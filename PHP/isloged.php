<?php
session_start();
header('Content-Type: application/json');

$response = array('isLogged' => false);

if (isset($_SESSION['user_id'])) {
    $response['isLogged'] = true;
}

echo json_encode($response);
