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