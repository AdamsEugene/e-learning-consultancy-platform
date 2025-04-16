<?php

function formatPermissions($permissions) {

    if(empty($permissions)) {
        return [];
    }

    $result = [];
    foreach($permissions as $key => $value) {
        $result[$key] = [
            "id" => $value['id'],
            "name" => $value['name'],
            "permissions" => !empty($value['permissions']) ? json_decode($value['permissions'], true) : [],
            // "created_at" => $value['created_at']
        ];
        if(!empty($value['user_id'])) {
            $result[$key]['user_id'] = $value['user_id'];
        }
    }

    return $result;
}