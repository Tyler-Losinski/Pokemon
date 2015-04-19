<?PHP
	$pcUsername = "";
	$pcPassword = "";
	$errorMessage = "";

	// When the page gets a submit request
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$pcUsername = $_POST['username'];
		$pcPassword = $_POST['password'];

		// escape dangerous html characters
		$pcUsername = htmlspecialchars($pcUsername);
		$pcPassword = htmlspecialchars($pcPassword);
		
		// SQL login credentials
		$uname = 'adhart';
		$pword = 'Aug111995';
		$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
		
		// Connect to database
		$conn = oci_connect($uname, $pword, $conn_string);
		
		// If failed to connect, print the error
		if (!conn) {
			$e = oci_error();
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
		else {
			// Select any rows that match the login credentials
			$sql = "SELECT userId FROM PCUsers WHERE username = '" . $pcUsername . "' AND password = '" . $pcPassword . "'";
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);

			// If found, create a session cookie and redirect
			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				session_start();
				$_SESSION['login'] = "1";
				$_SESSION['username'] = $pcUsername;
				$_SESSION['uid'] = $row['USERID'];
				header ("Location: pchome.php");
				exit();
			}
			// If not found, output an error
			else {
				$errorMessage = "Failed login attempt";
			}
		}
		
		// Close the connection
		oci_close($conn);
	}
?>

<html>
	<head>
		<title>PC Storage System Login</title>
	</head>
	<body>
		<FORM NAME ="form1" METHOD ="POST" ACTION ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<P>Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $pcUsername;?>" maxlength="20">
			<P>Password: <INPUT TYPE = 'TEXT' Name ='password'  value="<?PHP print $pcPassword;?>" maxlength="16">
			<P align = center><INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Login"></P>
		</FORM>
		<P><?PHP print_r($errorMessage);?>
		<P>Not a user yet?  <a href="/~adhart/register.php">Register!</a>
	</body>
</html>