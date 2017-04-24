<?PHP
	// Start session so we can read cookie values
	session_start();
	
	// If the session is empty, the user has not logged in properly.  Redirect.
	if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
		header ("Location: login.php");
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
			<a href="/~adhart/move.php">Move</a><BR>
			<a href="/~adhart/logout.php">Log out</a>
		</P>
		<audio id="login" src="sound/pcOn.wav" preload="auto" style="display=none">
		<SCRIPT>
			if (document.referrer == "http://students.cs.ndsu.nodak.edu/~adhart/login.php" || document.referrer == "http://students.cs.ndsu.nodak.edu/~adhart/register.php")
				document.getElementById("login").play();
		</SCRIPT>
	</body>
</html>