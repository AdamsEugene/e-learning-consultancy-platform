<?php
/**
 * @param string $key
 * 
 * @return mixed
 */
function configs($key) {

    $configuration = [
        'db_group' => config('Database')?->defaultGroup,
        'algo' => config('Security')?->algo,
        'salt' => config('Security')?->salt,
        'testing_mode' => config('General')?->testing_mode,
        'stripe_test_key' => getenv('STRIPE_API_KEY'),
        'hubspot_key' => getenv('HUBSPOT_KEY'),
        'amplitude' => getenv('AMPLITUDE_KEY'),
        'max_limit' => getenv('MAX_WEBSITES'),
        'membership' => getenv('MEMBERSHIP_URL'),
        'tracking_ttl' => 6,
        'app_url' => getenv('APP_URL'),
        'heatmaps_ttl' => (60 * 30 * 1),
        'is_local' => config('Database')?->defaultGroup == 'tests',

        // email config
        'email.port' => getenv('email.SMTP_PORT'),
        'email.host' => getenv('email.SMTP_HOST'),
        'email.user' => getenv('email.SMTP_USER'),
        'email.pass' => getenv('email.SMTP_PASSWORD'),
        'email.crypto' => getenv('email.SMTP_CRYPTO'),
    ];

    return $configuration[$key] ?? null;

}

/**
 * Get the event types
 * 
 * @return array
 */
function getEventTypes() {
    return [
        "course" => "text-blue-500",
        "meeting" => "text-purple-500",
        "assignment" => "text-amber-500",
        "reminder" => "text-green-500",
        "consultation" => "text-indigo-500",
        "deadline" => "text-red-500"
    ];
}

/**
 * Get the class types
 * 
 * @return array
 */
function getClassTypes() {
    return [
        "lecture" => "Lecture",
        "workshop" => "Workshop",
        "qa" => "Q&A Session",
        "review" => "Review Session",
        "office_hours" => "Office Hours",
        "assessment" => "Assessment"
    ];
}

/**
 * Get the industries
 * 
 * @return array
 */
function getIndustries() {
    return [
        "Warehousing",
        "Wholesale",
        "Wine & Spirits",
        "Wireless",
        "Writing & Editing"
    ];
}

/**
 * Get the levels
 * 
 * @return array
 */
function getLevels() {
    return [
        "Beginner",
        "Intermediate",
        "Advanced",
        "All Levels"
    ];
}
?>