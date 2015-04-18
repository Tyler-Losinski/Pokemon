<?PHP
	// Start session
	session_start();
	
	// Murder the session
	session_unset();
	session_destroy();
?>

<html>
	<head>
		<title>PC Storage System Logout</title>
	</head>
	<body>
		<P>You have successfully logged out.
		<P>Click <a href="/~adhart/login.php">here</a> to log back in.
	</body>
</html>