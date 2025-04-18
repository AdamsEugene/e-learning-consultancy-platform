<?php 
/**
* Verify if a string parsed is a valid date
*
* @param String $date 		This is the date String that has been parsed by the user
* @param String $format 	This is the format for that date to use
*
* @return Bool
 */
function isValidDate($date, $format = 'Y-m-d') {
    
    if (empty($date) || (!empty($date) && strlen($date) < 10)) {
        return false;
    }

    $date = date($format, strtotime($date));

    if ($date === "1970-01-01") {
        return false;
    }

    $d = \DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Get the data tracking statuses
 * 
 * @return array
 */
function dataTrackingStatuses($status) {

    $statuses = [
        'begin' => 'tracking_in_progress',
        'hsr_created' => 'pages_created',
        'pageviews' => 'data_tracking_successful'
    ];

    return $statuses[$status] ?? null;
}

/**
 * Validate email address
 *
 * @param string $email
 * @param bool  $preFree
 *
 * @return bool|string
 */
function isValidEmail($email = '')
{

    if (empty($email)) {
        return true;
    }

    // get the domain name
    $domain = substr(strrchr($email, "@"), 1);

    // if the site is not local, then check if the email is disposable
    if(!configs('is_local')) {
        // get the temp emails
        $temp_email = APPPATH . '../app/ThirdParty/temp_emails.txt';
        if (!is_file($temp_email) || !file_exists($temp_email)) {
            return true;
        }

        $disposable_domains = file($temp_email, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $domain = mb_strtolower(explode('@', trim($email))[1]);

        // check if the domain is disposable
        if (in_array($domain, $disposable_domains)) {
            return false;
        }
    }

    // validate the mx record of the email address
    if(!in_array($domain, ['yahoo.com', 'gmail.com', 'ymail.com', 'rocketmail.com', 'outlook.com', 'hotmail.com', 'live.com', 'msn.com', 'aol.com'])) {
        if (!checkdnsrr($domain, "MX")) {
            return false;
        }
    }
    
    return true;
}

/**
 * Cheeck the days difference
 * 
 * @param string    $specificDate
 * @param bool      $returnDays
 * 
 * @return string
 */
function timeDifference($specificDate) {

    // Get the current datetime
    $currentDate = new \DateTime();

    // Create DateTime object for the specific date
    $targetDate = new \DateTime($specificDate);

    // Calculate the difference
    $interval = $currentDate->diff($targetDate);

    return [
        'years' => $interval->y,
        'months' => $interval->m,
        'days' => $interval->d,
        'hours' => $interval->h,
        'minutes' => $interval->i,
        'seconds' => $interval->s,
    ];

}

/**
 * Cheeck the days difference
 * 
 * @param string    $specificDate
 * @param bool      $returnDays
 * 
 * @return string
 */
function daysDifference($specificDate, $returnDays = false) {

    // if the date is not valid, then return 0
    if(!isValidDate($specificDate)) return 0;

    // Get the current datetime
    $currentDate = new \DateTime();

    // Create DateTime object for the specific date
    $targetDate = new \DateTime($specificDate);

    // Calculate the difference
    $interval = $currentDate->diff($targetDate);

    // Get the difference in months and days
    $monthsDifference = $interval->m + ($interval->y * 12);
    $daysDifference = $interval->d;

    if($returnDays) return $daysDifference;

    // Display the result
    return (!empty($monthsDifference) ? "{$monthsDifference} months and " : null) . $daysDifference . " days then billed monthly";

}

/**
 * Convert the date to timestamp
 * 
 * @param string $dateString
 * 
 * @return int
 */
function convertDateToTimestamp($dateString) {
    
    $datetime = empty($dateString) ? date('Y-m-d H:i:s') : $dateString;
    $date = new \DateTime($datetime, new \DateTimeZone('UTC'));
    $date->setTime(0, 0, 0);
    return $date->getTimestamp() * 1000;

}

/**
 * Convert seconds to hours and minutes
 * 
 * @param int $seconds
 * @return string
 */
function convertSecondsToHoursAndMinutes($seconds = 0) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return $hours . 'h ' . $minutes . 'm';
}

/**
 * Checks for a correctly formatted email address
 *
 * @param string|null $str
 */
function validEmail($str = null): bool
{
    if (! is_string($str)) {
        $str = (string) $str;
    }

    // @see https://regex101.com/r/wlJG1t/1/
    if (function_exists('idn_to_ascii') && defined('INTL_IDNA_VARIANT_UTS46') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches)) {
        $str = $matches[1] . '@' . idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46);
    }

    return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
}

/**
 * Convert the array column to numbers
 * 
 * @param array   $array
 * @param string  $column
 * 
 * @return array
 */
function convertArrayColumnToNumbers($array, $column = null) {

    // if the array is not an array, return an empty array
    if(!is_array($array)) return [];

    // filter the array
    $array = array_filter($column ? array_column($array, $column) : $array);

    // return the unique array of numbers
    return array_unique(array_map('intval', $array));
}

/**
 * Load the email template
 * 
 * @param string $template
 * 
 * @return string
 */
function loadEmailTemplate($template) {
    $loadFile = APPPATH . '../app/ThirdParty/EmailTemplates/' . $template . '.html';

    if (is_file($loadFile) && file_exists($loadFile)) {
        return file_get_contents($loadFile);
    }

    return '';
}

/**
 * Remove the configs cache
 *
 * @return array
 */
function remove_configs() {
    try {
        // set the path to use
        $path = str_ireplace('/app/', '', APPPATH) . '/writable/cache/';
        
        // remove the throttler cache
        @shell_exec('rm -rf ' . $path . 'throttler_*');
        
        // remove the factories cache
        @unlink($path . 'FactoriesCache_config');

        // remove the file locator cache
        @unlink($path . 'FileLocatorCache');
    } catch(\Exception $e) {
        return false;
    }
}

/**
 * Loading currencies
 *
 * @param $currency
 *
 * @return array
 */
function currencies($currency = null, $array = null)
{

    $loadFile = APPPATH . '../app/ThirdParty/currencies.json';

    $tFileData = [];
    if (is_file($loadFile) && file_exists($loadFile)) {
        $tFileData = json_decode(file_get_contents($loadFile), true);
    }

    if ($array) {
        return !empty($currency) ? ($tFileData[$currency] ?? []) : array_values($tFileData);
    }

    return !empty($currency) ? ($tFileData[$currency] ?? []) : $tFileData;

}

/**
* String to Array
*
* @param String        $string
* @param String        $delimiter
*
* @return Array
*/
function stringToArray($string, $delimiter = ",", $array = false)
{
    if (is_array($string) || empty($string)) {
        return $string;
    }

    $array = [];
    $expl = explode($delimiter, $string);

    foreach ($expl as $each) {
        $array[] = (trim($each) === "NULL") ? null : trim(trim($each), "\"");
    }

    return $array;
}

/**
 * Normalize a URL
 * 
 * @param string $url
 * 
 * @return string
 */
function normalizeUrl($url) {
    return str_replace(['http://', 'https://'], '', rtrim($url, '/'));
}

/**
 * Get the user ip address
 * 
 * @param boolean $return
 * 
 * @return string
 */
function get_ipaddress($return = false) {

    // calculate the users ip addreess
    $forwardedAddr = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['HTTP_CLOUDFRONT_VIEWER_ADDRESS'] ?? $_SERVER['REMOTE_ADDR']);
    $ipAddress = explode(",", $forwardedAddr)[0];
    if(isset($_SERVER['HTTP_CLOUDFRONT_VIEWER_ADDRESS'])) {
        $ipAddress = explode(":", $_SERVER['HTTP_CLOUDFRONT_VIEWER_ADDRESS'])[0];
    }
    
    // return the raw ip address
    if($return) return  trim($ipAddress);

    // return the md5 version of the ipaddress
    return md5(trim($ipAddress));
}

/**
 * Get the device type
 * 
 * @param int $device_type
 * 
 * @return int
 */
function get_device_type($device_type = 0) {

    $device_type = !empty($device_type) ? $device_type : 3;

    if(isset($_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'])) {
        $value = (string)$_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'];
        if(($value !== "false") || ($value == "1")) {
            $device_type = 3;
        }
    }

    if(isset($_SERVER['HTTP_CLOUDFRONT_IS_DESKTOP_VIEWER'])) {
        $value = (string)$_SERVER['HTTP_CLOUDFRONT_IS_DESKTOP_VIEWER'];
        if(($value !== "false") || ($value == "1")) {
            $device_type = 1;
        }
    }

    if(isset($_SERVER['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'])) {
        $value = (string)$_SERVER['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'];
        if(($value !== "false") || ($value == "1")) {
            $device_type = 2;
        }
    }

    return $device_type;

}

/**
 * Create a cache key
 * 
 * @param string $class
 * @param string $method
 * @param array $payload
 * 
 * @return string
 */
function create_cache_key($class, $method, $payload = []) {
    return md5($class . '_' . $method . '_' . json_encode($payload));
}

/**
 * List the days between two dates
 * 
 * @param string $startDate
 * @param string $endDate
 * 
 * @return array
 */
function listDaysBetweenDates($startDate, $endDate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $end->modify('+1 day'); // Include the end date in the result

    $interval = new DateInterval('P1D'); // Interval of 1 day
    $dateRange = new DatePeriod($start, $interval, $end);

    $days = [];
    foreach ($dateRange as $date) {
        $days[] = $date->format('Y-m-d');
    }

    return $days;
}

/**
 * Generate a UUID
 * 
 * @return string
 */
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // Set version to 0100 (UUID version 4)
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // Set variant to 10xx

    return vsprintf('%02x%02x%02x%02x-%02x%02x-%02x%02x-%02x%02x-%02x%02x%02x%02x%02x%02x', str_split(bin2hex($data), 2));
}