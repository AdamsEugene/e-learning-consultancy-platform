<?php

/**
 * Get the list of course statuses
 * 
 * @return array
 */
function course_statuses() {
    return [
        'Published',
        'Draft',
        'Archived',
        'Under Review'
    ];
}

/**
 * Validate the course sections
 * 
 * @param array $sections
 * @param array $allSections
 * @param array $allLessons
 * @param array $acceptedLessonTypes
 * 
 * @return array|string
 */
function validateCourseSections($sections = [], $allSections = [], $allLessons = [], $acceptedLessonTypes = []) {

    // loop through the sections
    foreach($sections as $key => $section) {

        // get the section keys
        $sectionKeys = array_keys($section);

        // check if the section keys are all in the allSections array
        $missingKeys = array_diff($allSections, $sectionKeys);

        $curKey = $key + 1;

        // if there are missing keys, then return an error
        if(!empty($missingKeys)) {
            return "Section {$curKey} - '{$section['title']}' is missing some keys: " . implode(', ', $missingKeys);
        }

        // check if the section duration is a number
        if(!is_numeric($section['totalDuration'])) {
            return "Section {$curKey} - '{$section['title']}' duration is not a number";
        }

        // confirm if there are lessons in the section
        if(empty($section['lessons'])) {
            return "Section {$curKey} - '{$section['title']}' has no lessons";
        }

        // check if the lessons are an array
        if(!is_array($section['lessons'])) {
            return "Section {$curKey} - '{$section['title']}' lessons are not an array";
        }

        // loop through the lessons
        foreach($section['lessons'] as $lesson) {

            // get the lesson keys
            $lessonKeys = array_keys($lesson);

            // get the missing keys
            $missingKeys = array_diff($allLessons, $lessonKeys);

            if(!empty($missingKeys)) {
                return "Section {$curKey} - '{$section['title']}' lesson '{$lesson['title']}' is missing some keys: " . implode(', ', $missingKeys);
            }

            // confirm the accepted lesson types
            if(!in_array($lesson['type'], $acceptedLessonTypes)) {
                return "Section {$curKey} - '{$section['title']}' lesson '{$lesson['title']}' type is not accepted.";
            }

            // check if the duration is a number
            if(!is_numeric($lesson['duration'])) {
                return "Section {$curKey} - '{$section['title']}' lesson '{$lesson['title']}' duration is not a number.";
            }
        }
    }

    return $sections;
}

/**
 * Format the course response
 * 
 * @param array $course
 * @return array
 */
function formatCourseResponse($courses = []) {
    
    // return empty array if no courses
    if(empty($courses)) return [];

    // format the course response
    foreach($courses as $key => $value) {

        // format the course response
        foreach(['tags', 'features', 'description', 'requirements'] as $field) {
            if(!empty($value[$field])) {
                $list = json_decode($value[$field], true);
                $value[$field] = empty($list) ? $value[$field] : $list;
            }
        }

        $result[] = $value;
    }

    return $result;
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
 * Format the course sections
 * 
 * @param array $sections
 * @return array
 */
function formatCourseSections($sections = []) {
    
    // return empty array if no courses
    if(empty($sections)) return [];

    // format the course response
    foreach($sections as $key => $value) {

        // format the course response
        foreach(['lessons'] as $field) {
            if(!empty($value[$field])) {
                $list = json_decode($value[$field], true);
                $value[$field] = empty($list) ? $value[$field] : $list;
            }
        }

        unset($value['created_at']);
        unset($value['updated_at']);
        
        // format the duration
        $value['formatDuration'] = convertSecondsToHoursAndMinutes($value['totalDuration']);

        $result[] = $value;
    }

    return $result;

}

/**
 * Format the wishlist response
 * 
 * @param array $wishlists
 * @param bool $single
 * @return array
 */
function formatWishlistResponse($wishlists = [], $single = false) {
    
    // return empty array if no courses
    if(empty($wishlists)) return [];

    // format the course response
    foreach($wishlists as $key => $value) {

        unset($value['updated_at']);
        unset($value['user_id']);

        // format the course response
        foreach(['tags', 'features', 'description', 'requirements'] as $field) {
            if(!empty($value[$field])) {
                $list = json_decode($value[$field], true);
                $value[$field] = empty($list) ? $value[$field] : $list;
            }
        }

        // format the course response
        $result[] = $value;
    }

    return $single ? $result[0] : $result;

}
?>