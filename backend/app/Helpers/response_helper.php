<?php

function accessDenied() {
    return [
        'status' => 'error',
        'message' => 'You are not authorized to access this resource.'
    ];
}
?>