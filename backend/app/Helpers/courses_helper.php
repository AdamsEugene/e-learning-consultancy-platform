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

        // get the section duration
        $totalDuration = (int) $section['totalDuration'];
        $lessonsDuration = 0;

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
                // lesson keys
                $lesson['title'] = $lesson['title'] ?? 'Untitled Lesson';
                $section['title'] = $section['title'] ?? 'Untitled Section';

                // return the error message
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

            $lessonsDuration += (int) $lesson['duration'];

        }

        if($lessonsDuration > $totalDuration) {
            $diffHours = convertSecondsToHoursAndMinutes($lessonsDuration - $totalDuration);
            return "Section {$curKey} - '{$section['title']}' total duration is less than the sum of the lessons duration by {$diffHours})";
        }
    }

    return $sections;
}

/**
 * Format the discussions
 * 
 * @param array $discussions
 * @return array
 */
function formatDiscussions($discussions = []) {

    // return empty array if no discussions
    if(empty($discussions)) return [];
    
    $regroupByParent = [];

    // loop through the discussions
    foreach($discussions as $key => $value) {
        // format the created by
        $value['created_by'] = json_decode($value['created_by'], true);

        foreach(['course_id', 'lesson_id', 'user_id', 'upvotes', 'downvotes', 'discussion_hash'] as $field) {
            if(isset($value[$field])) {
                unset($value[$field]);
            }
        }
        
        // set the parent comment and attach the children as a property
        if(!isset($regroupByParent[$value['id']]) && $value['parent_id'] == 0) {
            $regroupByParent[$value['id']] = $value;
        } else {

            if($value['parent_id'] == 0) {
                $regroupByParent[$value['parent_id']] = $value;
            }

            $regroupByParent[$value['parent_id']]['comments'][] = $value;
        }

    }

    // finally get the values of the regrouped by parent
    $result = array_values($regroupByParent);

    if(count($result) == 1 && empty($result[0]['created_at'])) {
        $result = $result[0]['comments'][0];
    }

    return $result;
}

/**
 * Format the course response
 * 
 * @param array $course
 * @param bool $minified
 * @param bool $single
 * @return array
 */
function formatCourseResponse($courses = [], $minified = false, $single = false) {
    
    // return empty array if no courses
    if(empty($courses)) return [];

    // format the course response
    foreach($courses as $key => $value) {

        // format the course response
        foreach(['tags', 'features', 'description', 'requirements', 'created_by'] as $field) {
            if(!empty($value[$field])) {
                $list = json_decode($value[$field], true);
                $value[$field] = empty($list) ? html_entity_decode($value[$field]) : $list;
            }
        }

        if($minified) {
            foreach(['created_by', 'category_id', 'subcategory_id', 'originalPrice', 'updated_at', 'enrollmentCount'] as $field) {
                unset($value[$field]);
            }
        }

        $value['is_featured'] = $value['is_featured'] == 'yes' ? true : false;

        $result[] = $value;
    }

    return $single ? $result[0] : $result;
}

/**
 * Format the enrolled courses
 * 
 * @param array $enrollments
 * @param bool $single
 * 
 * @return array
 */
function formatEnrolledCourses($enrollments = [], $single = false) {

    // return empty array if no enrollments
    if(empty($enrollments)) return [];
    
    foreach($enrollments as $key => $value) {

        // format the course
        if(!empty($value['course'])) {
            $value['course'] = json_decode($value['course'], true);
            // format the course response
            foreach(['tags', 'features', 'description', 'requirements'] as $field) {
                if(!empty($value['course'][$field])) {
                    $list = json_decode($value['course'][$field], true);
                    $value['course'][$field] = empty($list) ? html_entity_decode($value['course'][$field]) : $list;
                }
            }
        }

        if(!empty($value['user'])) {
            $value['user'] = json_decode($value['user'], true);
        }

        // format the course
        $result[] = $value;
    }

    return $single ? $result[0] : $result;
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
                $value[$field] = empty($list) ? html_entity_decode($value[$field]) : $list;
            }
        }

        if(!empty($value['lessons'])) {
            foreach($value['lessons'] as $key => $lesson) {
                $value['lessons'][$key]['formatDuration'] = convertSecondsToHoursAndMinutes($lesson['duration']);
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
 * Format the course reviews
 * 
 * @param array $reviews
 * @return array
 */
function formatCourseReviews($reviews = []) {

    // return empty array if no reviews
    if(empty($reviews)) return [];
    
    foreach($reviews as $key => $value) {

        unset($value['updated_at']);

        // format the course
        $value['entity'] = !empty($value['entity']) ? json_decode($value['entity'], true) : [];
        $value['user'] = !empty($value['user']) ? json_decode($value['user'], true) : [];

        // format the user
        $result[] = $value;
    }

    return $result ?? [];
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