<?php 

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;

class Emailing {

    public $replacements = [];
    public $emailObject;

    public function __construct() {
        $this->replacements = [
            '__APPLOGO__' => 'https://c.heatmap.com/assets/logo.avif',
            '__TITLE__' => 'Heatmap.com',
            '__TEAM__' => 'Team - Heatmap.com',
            '__INVITE_URL__' => rtrim(configs('app_url'), '/') . '/auth/signup'
        ];

        $this->emailObject = new PHPMailer(true);

        $this->initialize();
    }

    /**
     * Initialize the email object
     * 
     * @return void
     */
    public function initialize()
    {

        // set the timeout to 15 seconds
        $timeout = 15;

        // get the email config
        $emailConfig = config('Email');

        // set the time limit to 15 seconds
        set_time_limit($timeout);

        // set the email configs object
        $this->emailObject->isSMTP();
        $this->emailObject->SMTPDebug = 0;
        $this->emailObject->Timeout = $timeout;
        $this->emailObject->Host = configs('email.host') ? configs('email.host') : $emailConfig?->SMTPHost;
        $this->emailObject->SMTPAuth = true;
        $this->emailObject->Username = configs('email.user') ? configs('email.user') : $emailConfig?->SMTPUser;
        $this->emailObject->Password = configs('email.pass') ? configs('email.pass') : $emailConfig?->SMTPPass;
        $this->emailObject->SMTPSecure = configs('email.crypto') ? configs('email.crypto') : $emailConfig?->SMTPCrypto;
        $this->emailObject->Port = configs('email.port') ? configs('email.port') : $emailConfig?->SMTPPort;
        $this->emailObject->setFrom($this->emailObject->Username , "Heatmap.com", false);
        $this->emailObject->isHTML(true);
    }

    /**
     * Send email
     * 
     * @param string $email
     * @param array $message
     * @param string $template
     * 
     * @return bool
     */
    public function send($email, array $message, string $template) {

        try {

            // load the email template
            $template = loadEmailTemplate($template);

            // replace the placeholders with the actual values
            foreach(array_merge($message, $this->replacements) as $key => $value) {
                $template = str_replace($key, $value, $template);
            }

            if(configs('is_local')) {
                return true;
            }

            // Initialize email
            $this->emailObject->addAddress($email);

            // Set email details
            $this->emailObject->Subject = ($message['__subject__'] ?? 'Heatmap.com');
            $this->emailObject->Body = $template;
            
            return  $this->emailObject->send();

        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Send email
     *
     * @param string $email
     * @param array $data
     * @param string $template
     *
     * @return false
     */
    public function sendRaw(string $email, array $data,  string $template) {
        // Todo:: validate the template
        try {
            if(configs('is_local')) {
                return true;
            }

            // Initialize email
            $this->emailObject->addAddress($email);

            // Set email details
            $this->emailObject->Subject = ($message['__subject__'] ?? 'Heatmap.com');
            $this->emailObject->Body = $template;

            return  $this->emailObject->send();

        } catch (\Exception $e) {
            return false;
        }

    }
}
