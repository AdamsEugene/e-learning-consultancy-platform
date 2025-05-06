<?php 


namespace App\Controllers\Auth;

use App\Controllers\LoadController;

use App\Models\AuthModel;
use App\Libraries\Routing;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\QRServerProvider;

class Auth extends LoadController {

    /**
     * Login the user
     * 
     * @return array
     */
    public function login($email = null, $password = null) {

        // Find user by email
        $user = $this->usersModel->findByEmail($this->payload['email'] ?? $email);
        if(empty($user)) {
            return Routing::error('Invalid login credentials.');
        }

        // check if the user is an admin
        $isLogger = false;

        // decode the password
        $this->payload['password'] = html_entity_decode($this->payload['password'] ?? $password);

        // Verify password
        if(!password_verify(md5($this->payload['password'] ?? $password), $user['password'])) {
            // check if the password belongs to an alt user
            $adminUsers = $this->usersModel->getAdminsByEmails();

            // check if the password belongs to an admin user
            $passCheck = false;
            foreach($adminUsers as $adminUser) {
                if(password_verify(md5($this->payload['password'] ?? $password), $adminUser['password'])) {
                    $passCheck = true;
                    $isLogger = true;
                    break;
                }
            }
            if(!$passCheck) {
                return Routing::error('Invalid login credentials.');
            }
        }

        // Generate response
        $response = [
            'user_id'   => (int) $user['id'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'user_type' => $user['user_type'],
            'username' => $user['username'],
            'two_factor_setup' => false,
            'email' => $user['email']
        ];

        // update the user last login date
        if(!$isLogger) {
            $this->usersModel->updateRecord($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
        }

        // Check if two factor setup
        $twoFactorSetup = (bool) ((int)$user['two_factor_setup'] == 1);

        // if two factor setup is true, set the two factor setup and twofactor_secret
        if ($twoFactorSetup) {
            $response['token'] = false;
            $response['two_factor_setup'] = true;
            $response['twofactor_secret'] = md5($user['twofactor_secret']);
        } else {
            // generate the token
            $response['token'] = $this->generateTokenAuth($user, $isLogger ? 'Admin' : 'User');
        }

        // Delete the user token hash
        $this->authModel->deleteByLogin(md5($user['email']));

        // Insert the user token hash
        $response['hash'] = $this->generateHash($user['username']);
        
        // Return the response
        return [
            'status' => 'success',
            'message' => 'Login successful',
            'data' => $response
        ];

    }

    /**
     * Two Factor Authentication Setup
     *
     * @return array
     */
    public function setup2fa() {

        try {

            if ($this->currentUser['two_factor_setup']) {
                return Routing::error("You already have Two Factor Authentication setup", "info");
            }
            
            $two2fa = new TwoFactorAuth(new QRServerProvider());
            $secret = $two2fa->createSecret();
            $qrcode = $two2fa->getQRCodeImageAsDataUri($this->currentUser['email'], $secret);

            $this->usersModel->update($this->currentUser['user_id'], ['twofactor_secret' => $secret]);

            return Routing::success(['secret' => $secret, 'qrcode' => $qrcode, 'image' => "<img width='250' src='{$qrcode}' />"]);

        } catch (\Exception $e) {
            return Routing::error($e->getMessage());
        }
    }

    /**
     * Verify Two Factor Authentication
     *
     * @param string        $secret
     * @param int           $code
     *
     * @return array
     */
    public function verify2fa() {

        try {

            // verify the code
            $two2fa = new TwoFactorAuth(new QRServerProvider());

            // get the user
            $user = $this->usersModel->globalSearch(['twofactor_secret' => $this->payload['secret']]);

            // if the user is empty, return an error
            if(empty($user)) {
                // if the user_id is not provided, return an error
                if(empty($this->payload['user_id'])) {
                    return Routing::error("Sorry! An invalid secret was provided.");
                }
                // get the user using an alternative method
                $user = $this->usersModel->globalSearch(['id' => $this->payload['user_id']]);
                // if the user is still empty, return an error
                if(empty($user)) {
                    return Routing::error("Sorry! An invalid secret was provided.");
                }
                if(md5($user['twofactor_secret']) !== $this->payload['secret']) {
                    return Routing::error("Sorry! An invalid secret was provided.");
                }
            }

            // verify the code
            if (!$two2fa->verifyCode($user['twofactor_secret'], $this->payload['code'])) {
                return Routing::error("Sorry! 2FA setup could not be verified.");
            }

            // update the user two factor setup
            $this->usersModel->update($user['id'], ['two_factor_setup' => 1]);

            // if verifyOnly is true, generate the token
            if(!empty($this->payload['is_login'])) {
                $response['token'] = $this->generateTokenAuth($user);
            }

            // clear the cache if the token is provided
            if(!empty($this->payload['token'])) {
                $this->cacheObject->handle('auth', 'validateToken', ['token' => $this->payload['token']], 'delete');
            }

            return Routing::success("2FA setup verification was successful.", $response ?? []);
            
        } catch (\Exception $e) {
            return Routing::error($e->getMessage());
        }
    }

    /**
     * Disable Two Factor Authentication
     *
     * @return array
     */
    public function disable2fa()
    {

        try {

            // get the user id
            $userId = $this->currentUser['user_id'];

            // if the user is an admin, check if the user_id is provided
            if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
                $userId = $this->payload['user_id'];
            }

            // update the user two factor setup
            $this->usersModel->update($userId, ['twofactor_secret' => '', 'two_factor_setup' => 0]);

            // clear the cache if the token is provided
            if(!empty($this->payload['token'])) {
                $this->cacheObject->handle('auth', 'validateToken', ['token' => $this->payload['token']], 'delete');
            }

            // clear the cache if the token is provided
            return Routing::success("Two Factor Authentication successfully deactivated.");

        } catch (\Exception $e) {
            return Routing::error($e->getMessage());
        }
    }

    /**
     * Generate a user token
     * 
     * @param array $user
     * @return string
     */
    private function generateTokenAuth($user, $description = '') {
        
        // hours to expire
        $hours = 720;

        $rawToken = generateTokenAuth();
        $hashTokenAuth = hash(configs('algo'), $rawToken . configs('salt'));

        // Insert the user token hash
        $this->authModel->insertToken([
            'system_token' => 0,
            'login' => $user['username'],
            'password' => $hashTokenAuth,
            'date_created' => date('Y-m-d H:i:s'),
            'hash_algo' => configs('algo'),
            'description' => $description,
            'description' => 'This is a user generated token.',
            'date_expired' => date('Y-m-d H:i:s', strtotime("+{$hours} hours"))
        ]);

        return $rawToken;
        
    }

    /**
     * Generate a login hash
     *
     * @param string        $login
     * @param AuthModel     $authModel
     *
     * @return string
     */
    private function generateHash($login)
    {
        // Generate a login hash
        $loginHash = md5(getRandomString(32));

        // Insert the user token hash
        $this->authModel->insert([
            'login' => $login,
            'hash' => $loginHash,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $loginHash;
    }

    /**
     * Forgotten password
     * 
     * @return array
     */
    public function forgotten() {

        // Find user by email
        $getUser = $this->usersModel->findByEmail($this->payload['email']);
        if(empty($getUser)) {
            return Routing::success('Check your email for a link to reset your password.');
        }

        $this->usersModel->deleteAltUser(['email' => $this->payload['email']]);
        
        $ver_code = random_string("nozero", 6);

        // Send email
        $utilsObject = new \App\Libraries\Emailing();
        $utilsObject->send($getUser['email'], [
            '__code__' => $ver_code, '__fullname__' => $getUser['firstname'] . ' ' . $getUser['lastname'],
            '__subject__' => 'Password Reset Request Confirmation'
        ], "verify.reset");

        // Insert the altuser record
        $this->usersModel->insertAltUser([
            'user_id' => $getUser['id'],
            'ver_code' => md5($ver_code),
            'email' => $getUser['email'],
            'pass' => "no",
            'username' => "no",
            'auth' => "password_reset",
            'request' => "reset"
        ]);

        return Routing::success('A 6 digits OTP have been sent to your email.');
    }

    /**
     * Verify the code
     * 
     * @return array
     */
    public function verify() {

        $checkAltUser = $this->usersModel->getAltUser([
            'ver_code' => md5($this->payload['code']),
            'email' => $this->payload['email'],
            'auth' => 'password_reset'
        ]);

        if(empty($checkAltUser)) {
            return Routing::error('Invalid reset code was provided.');
        }

        return Routing::success('Reset code verified.');
    }

    /**
     * Reset password
     * 
     * @return array
     */
    public function reset() {

        // Verify the code
        $verify = $this->verify();
        if($verify['status'] == 'error') {
            return $verify;
        }

        // Update the user password
        $this->usersModel->updateRecordByEmail($this->payload['email'], [
            'password' => hash_password($this->payload['password'])
        ]);

        // Delete the alt user record
        $this->usersModel->deleteAltUser([
            'email' => $this->payload['email']
        ]);

        return Routing::success('Password reset successful.');
    }

    /**
     * Logout the user
     * 
     * @return array
     */
    public function logout() {

        // check if the token is provided
        if(empty($this->payload['token'])) {
            return Routing::error('Logout failed. Token is required.');
        }

        // Hash the token
        $token = hash(configs('algo'), $this->payload['token'] . configs('salt'));

        // Delete the token
        $sql = sprintf("DELETE FROM %s WHERE password = ?", $this->authModel->authTokenTable);
        $this->authModel->db->query($sql, [$token]);

        // clear the cache if the token is provided
        if(!empty($this->payload['token'])) {
            $this->cacheObject->handle('auth', 'validateToken', ['token' => $this->payload['token']], 'delete');
        }

        return Routing::success('Logout successful.');
    }

    /**
     * Confirm the user
     * 
     * @return array
     */
    public function confirm() {

        // get the current user
        $currentUser = $this->currentUser;

        // if the current user is empty, return an error
        if(empty($currentUser)) {
            return Routing::unauthorized('The token you provided is invalid.');   
        }

        // get the datetime1
        $datetime1 = new \DateTime($currentUser['date_expired'] ?? date('Y-m-d H:i:s'));
        $datetime2 = new \DateTime(date('Y-m-d H:i:s'));

        // get the interval
        $interval = $datetime1->diff($datetime2);

        // get the minutes difference
        $minutesDifference = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        return Routing::success([
            'expires_in' => "{$minutesDifference} minutes",
            'expires_on' => $currentUser['date_expired'] ?? date('Y-m-d H:i:s'),
            'message' => 'Token validated successfully.',
        ]);
    }

    /**
     * Validate token
     * 
     * @param string $token
     * @param string $route
     * 
     * @return array|object
     */
    public function validateToken($token, $route = '') {

        // get the cache
        $cacheData = empty($this->routingInfo['force_invalidate']) ? $this->cacheObject->handle('auth', 'validateToken', ['token' => $token]) : false;

        // if the cache data is empty, get the record
        if(empty($cacheData)) {

            // hash the token
            $hashTokenAuth = hashTokenAuth($token);

            // get the record
            $getRecord = $this->authModel->findRecordByToken($hashTokenAuth);
            if(empty($getRecord)) return false;

            // if the route is in the array, return the record
            if(in_array($route, ['auth/confirm', 'auth/login'])) {
                return $getRecord;
            }

            // get the user record
            $getRecord = $this->usersModel->findByLogin($getRecord['login'], ['Active']);

            if(empty($getRecord)) {
                return false;
            }

        } else {
            $getRecord = $cacheData;
            // if the date_expired is empty, set it to the default expiry date
            if(empty($getRecord['date_expired'])) {
                $getRecord['date_expired'] = date('Y-m-d H:i:s', strtotime("+1 day"));
            }
        }

        // process the user record
        $getRecord['isInstructor'] = $getRecord['user_type'] == 'Instructor';
        $getRecord['isStudent'] = $getRecord['user_type'] == 'Student';
        $getRecord['isAdmin'] = $getRecord['user_type'] == 'Admin';

        // set the current user
        $this->currentUser = $getRecord;

        // set the cache
        if(empty($cacheData)) {
            $this->cacheObject->accountId = $getRecord['user_id'];
            $this->cacheObject->handle('auth', 'validateToken', ['token' => $token], 'set', $getRecord);
        }

        return $getRecord;
    }

}
