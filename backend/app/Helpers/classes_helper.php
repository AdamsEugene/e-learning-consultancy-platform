<?php

function formatClassesResponse($classes, $single = false) {
    
    if(empty($classes)) {
        return [];
    }

    $result = [];
    foreach($classes as $class) {
        $class['course'] = json_decode($class['course'], true);

        foreach(['user', 'materials'] as $key) {
            $class[$key] = json_decode($class[$key], true);
            $class[$key] = empty($class[$key]) ? null : $class[$key];
        }
        
        $result[] = $class;
    }

    return $single ? $result[0] : $result;
}