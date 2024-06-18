<?php
session_start();
$config = include('config.php');
$DATABASE_HOST = $config['host'];
$DATABASE_USER = $config['user'];
$DATABASE_PASS = $config['password'];
$DATABASE_NAME = $config['database'];
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

//$con->exec("SET NAMES 'utf8';");
$con -> set_charset("utf8");
//$con->exec("use `$database`");
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	die ('Please fill both the username and password field!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT mandant, password FROM tblAccount WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();

	if ($stmt->num_rows > 0) {
		$stmt->bind_result($mandant, $password);
		$stmt->fetch();
		// Account exists, now we verify the password.
		// Note: remember to use password_hash in your registration file to store the hashed passwords.
		if (password_verify($_POST['password'], $password)) {
			// Verification success! User has loggedin!
			// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
			session_regenerate_id();
			$_SESSION['mandant'] = $mandant;
			$_SESSION['periode'] = 0;
			
			header('Location: ../kassa.php');
		} else {
			echo 'Passwort falsch!';
		}
	} else {
		echo 'Benutzername existiert nicht!';
	}

	$stmt->close();
}
?>