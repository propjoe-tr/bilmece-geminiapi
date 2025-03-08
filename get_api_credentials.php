<?php
require_once 'database.php';

header('Content-Type: application/json');

$credentials = getApiCredentials();
echo json_encode($credentials); 