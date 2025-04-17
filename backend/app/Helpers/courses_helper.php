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
?>