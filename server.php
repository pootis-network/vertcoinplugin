<?php
include('config.php');

// Maximum error logging
ini_set('log_errors', true);
ini_set('error_log', getcwd().'/server_errors.log');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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

if ($gameserver_key != $_POST['gameserver_key']){
    errorAndDie('Incorrect Gameserver key sent');
}

// POST Data
$username = $_POST['username'];
if (isset($_POST['password'])) {
    $password = $_POST['password'];
}
if (isset($_POST['wallet'])) {
    $wallet = $_POST['wallet'];
}
if (isset($_POST['points'])){
    $points = $_POST['points'];
}

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
		user varchar(1000) NOT NULL, 
		pass varchar(1000) NOT NULL, 
		coin varchar(1000) default 'vertcoin' NOT NULL,
		coin_address varchar(1000) NOT NULL, 
		points int default 0 NOT NULL, 
		UNIQUE KEY user (user))";
    mysqli_query($link, $query);


    // Check if username already in database
    $query = "SELECT * FROM players WHERE user=?";
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
    $register_query = "INSERT INTO players ( user, pass) VALUES ( ?,?)";
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

// login account and edit wallet
if ($_POST['action'] == 'editWallet') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }

    // In case players database doesnt exist (ie wasnt imported), make it.
    $query = "CREATE TABLE IF NOT EXISTS players (  
		user varchar(1000) NOT NULL, 
		pass varchar(1000) NOT NULL, 
		coin varchar(1000) default 'vertcoin' NOT NULL,
		coin_address varchar(1000) NOT NULL, 
		points int default 0 NOT NULL, 
		UNIQUE KEY user (user))";
    mysqli_query($link, $query);


    //check if account exists
    $query = "SELECT * FROM players WHERE user=?";
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
    $query_password = "SELECT  user, pass FROM players WHERE user=?";
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, $query_password)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        // bind shit to variables
        mysqli_stmt_bind_result($stmt, $usrname, $pw);

        $User = mysqli_stmt_fetch($stmt);

    }
    mysqli_stmt_close($stmt);

    $validPassword = password_verify($password, $pw);
    if($validPassword){
        //All is good. Edit the wallet.

        // prepare the query.
        $query_edit = "UPDATE players SET coin_address = ? WHERE players.user = ?";

        $wallet_stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($wallet_stmt, $query_edit)) {
            mysqli_stmt_bind_param($wallet_stmt, "ss",$wallet, $username);
            mysqli_stmt_execute($wallet_stmt);
        }
        mysqli_stmt_close($wallet_stmt);

        mysqli_close($link);
        die("Successfully Edited Wallet");
        exit(0);
    }else {
        mysqli_close($link);
        die("Invalid Account Details");
        exit(0);
    }
}

// validate login
if ($_POST['action'] == 'login') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }

    // In case players database doesnt exist (ie wasnt imported), make it.
    $query = "CREATE TABLE IF NOT EXISTS players (  
		user varchar(1000) NOT NULL, 
		pass varchar(1000) NOT NULL, 
		coin varchar(1000) default 'vertcoin' NOT NULL,
		coin_address varchar(1000) NOT NULL, 
		points int default 0 NOT NULL, 
		UNIQUE KEY user (user))";
    mysqli_query($link, $query);


    //check if account exists
    $query = "SELECT * FROM players WHERE user=?";
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
    mysqli_close($link);
    die("Success");
    exit(0);

}


// todo: fix this dumb broken shit
// add points to account
if ($_POST['action'] == 'addPoints') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }

    //check if account exists
    $query = "SELECT * FROM players WHERE user=?";
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

    // obtain current credits
    $query_credits = "SELECT  user, points FROM players WHERE user=?";
    $credit_stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($credit_stmt, $query_credits)) {
        mysqli_stmt_bind_param($credit_stmt, "s", $username);
        mysqli_stmt_execute($credit_stmt);

        // bind shit to variables
        mysqli_stmt_bind_result($credit_stmt, $usrname, $current_credits);

        var_dump($current_credits);
    }
    mysqli_stmt_close($credit_stmt);


    $points2 = $points + $current_credits;

    // All is good. add the credits.
    $sql = "UPDATE players SET points = ? WHERE players.user = ?";
    $point_stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($point_stmt, $sql)) {
        mysqli_stmt_bind_param($point_stmt, "is", $points2,$username);
        mysqli_stmt_execute($point_stmt);
    }
    mysqli_stmt_close($point_stmt);

    mysqli_close($link);
    die("Successfully Edited Credits");
    exit(0);
}

// remove points to account
if ($_POST['action'] == 'removePoints') {

}

// get balance of account
if ($_POST['action'] == 'getBalance') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }

    // In case players database doesnt exist (ie wasnt imported), make it.
    $query = "CREATE TABLE IF NOT EXISTS players (  
		user varchar(1000) NOT NULL, 
		pass varchar(1000) NOT NULL, 
		coin varchar(1000) default 'vertcoin' NOT NULL,
		coin_address varchar(1000) NOT NULL, 
		points int default 0 NOT NULL, 
		UNIQUE KEY user (user))";
    mysqli_query($link, $query);


    //check if account exists
    $query = "SELECT * FROM players WHERE user=?";
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
    // obtain current credits
    $query_credits = "SELECT  user, points FROM players WHERE user=?";
    $credit_stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($credit_stmt, $query_credits)) {
        mysqli_stmt_bind_param($credit_stmt, "s", $username);
        mysqli_stmt_execute($credit_stmt);

        // bind shit to variables
        mysqli_stmt_bind_result($credit_stmt, $usrname, $current_credits);

        var_dump($current_credits);
    }
    mysqli_stmt_close($credit_stmt);

    mysqli_close($link);
    die($current_credits);
    exit(0);
}

if ($_POST['action'] == 'withdraw') {
    // Connect to database
    $link = mysqli_connect(SQLServer, SQLUsername, SQLPassword, SQLDatabase);
    if (!$link) {
        error_log("Failed to connect to the database: " . mysqli_connect_error());
        exit(0);
    }

    // In case players database doesnt exist (ie wasnt imported), make it.
    $query = "CREATE TABLE IF NOT EXISTS players (  
		user varchar(1000) NOT NULL, 
		pass varchar(1000) NOT NULL, 
		coin varchar(1000) default 'vertcoin' NOT NULL,
		coin_address varchar(1000) NOT NULL, 
		points int default 0 NOT NULL, 
		UNIQUE KEY user (user))";
    mysqli_query($link, $query);


    //check if account exists
    $query = "SELECT * FROM players WHERE user=?";
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
    $query_password = "SELECT  user, pass FROM players WHERE user=?";
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, $query_password)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        // bind shit to variables
        mysqli_stmt_bind_result($stmt, $usrname, $pw);

        $User = mysqli_stmt_fetch($stmt);

    }
    mysqli_stmt_close($stmt);

    $validPassword = password_verify($password, $pw);
    if($validPassword){
        //All is good. Withdraw.

        // todo: figure out how to grab INT's from SQL as php variables

        mysqli_close($link);
        die("Successfully Withdrawn");
        exit(0);
    }else {
        mysqli_close($link);
        die("Invalid Account Details");
        exit(0);
    }
}