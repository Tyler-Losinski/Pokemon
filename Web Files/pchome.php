<?PHP
	// Start session so we can read cookie values
	session_start();
	
	// If the session is empty, the user has not logged in properly.  Redirect.
	if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
		header ("Location: wat.php");
		exit();
	}
	
	// Store session variables
	$uname = $_SESSION['username'];
	$uid = $_SESSION['uid'];
?>

<html>
	<head>
		<title>PC Storage System Home</title>
	</head>
	<body>
		<P>Welcome, <?PHP print $uname;?>! (User ID: <?PHP print $uid; ?>)
		<P>What would you like to do?
		<P>
			<a href="/~adhart/withdraw.php">Withdraw</a><BR>
			<a href="/~adhart/deposit.php">Deposit</a><BR>
			<a href="/~adhart/Move.php">Move</a>
		</P>
	</body>
</html>