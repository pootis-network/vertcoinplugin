<?php
include('config.php');

// Maximum error logging
ini_set('log_errors', true);
ini_set('error_log', getcwd().'/server_errors.log');
error_reporting(E_ALL);

// prevent hacker people from reading file
function requirePostMethod() {
    // require POST requests
    if ($_SERVER['REQUEST_METHOD'] && $_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Allow: POST', true, 405);
        throw new Exception("Invalid HTTP request method.");
    }
}

// idiot-proofing
if (!isset($_POST['action']) || $_POST['action'] != 'register' && $_POST['action'] != 'login' && $_POST['action'] != 'addpoints') {
    errorAndDie('Unknown Action.');
}


if (!isset($_POST['GAMESERVER_KEY']) || empty($_POST['GAMESERVER_KEY'])) {
   errorAndDie('No Gameserver Key sent.');
}

$request = file_get_contents('php://input');
if ($request === FALSE || empty($request)) {
    errorAndDie('Error reading POST data');
}

// create account
if ($_POST['action'] == 'register') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

}