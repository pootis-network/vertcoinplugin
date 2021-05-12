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
function errorAndDie($error_msg) {
    die('Server Error: '.$error_msg);
}

$request = file_get_contents('php://input');
if ($request === FALSE || empty($request)) {
    errorAndDie('Error reading POST data');
}

// todo: fix idiot fixing
// idiot-proofing
if (!isset($_POST['action']) || $_POST['action'] != 'register' && $_POST['action'] != 'editWallet'  && $_POST['action'] != 'addpoints') {
    //  errorAndDie('Unknown Action.');
}


if (!isset($_POST['gameserver_key']) || empty($_POST['gameserver_key'])) {
    errorAndDie('No Gameserver Key sent.');
}


// POST Data
$username = $_POST['username'];
$password = $_POST['password'];


// create account
if ($_POST['action'] == 'register') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }



    // In case players database doesnt exist (ie wasnt imported), make it.
    $query = "CREATE TABLE IF NOT EXISTS players (  
		username varchar(1024) NOT NULL, 
		password varchar(2048) NOT NULL, 
		coin varchar(1024) default 'vertcoin' NOT NULL,
		coin_address varchar(2048) NOT NULL, 
		points int default 0 NOT NULL, 
		UNIQUE KEY username (username))";
    mysqli_query($link, $query);


    // Check if username already in database
    $query = "SELECT * FROM players WHERE username=?";
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        if(mysqli_stmt_fetch($stmt)){
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            die("Username already exists in database!");
            error_log("Username already exists in database {$username}");
            exit(0);
        }
    }
    mysqli_stmt_close($stmt);

    // todo:fix this
    // add user to database
    $register_query = "INSERT INTO 'players' ( username, password)";
    $register_stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($register_stmt,$register_query)){
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
        mysqli_stmt_bind_param($register_stmt, "ss",
            $username,
            $passwordHashed
        );
        mysqli_stmt_execute($register_stmt);
    }
    mysqli_stmt_close($register_stmt);
    echo 'Success';
    mysqli_close($link);
}

// login account
if ($_POST['action'] == 'editWallet') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }

    //check if account exists
    $query = "SELECT * FROM players WHERE username=?";
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        if(!mysqli_stmt_fetch($stmt)){
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            die("Account does not exist!");
            exit(0);
        }
    }
    mysqli_stmt_close($stmt);

    // check if same password
    $query_password = "SELECT  username, password FROM players WHERE username=?";
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $User = mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);

    $validPassword = password_verify($password, $User['password']);
    if($validPassword){
        //All is good. Edit the wallet.
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        die("Successfully Edited Wallet");
        exit(0);
    }
}