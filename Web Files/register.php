<?PHP
	$pcUsername = "";
	$pcPassword = "";
	$pcPassword2 = "";
	$errorMessage = "";

	// When the page gets a submit request
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$pcUsername = $_POST['username'];
		$pcPassword = $_POST['password'];
		$pcPassword2 = $_POST['confirmPassword'];

		// escape dangerous html characters
		$pcUsername = htmlspecialchars($pcUsername);
		$pcPassword = htmlspecialchars($pcPassword);
		$pcPassword2 = htmlspecialchars($pcPassword2);
		
		if (strlen($pcUsername) > 2 && strlen($pcPassword) > 6 && $pcPassword == $pcPassword2) {
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
				// Check for existing username
				$sql = "SELECT * FROM PCUsers WHERE username ='" . $pcUsername . "'";
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);

				// If found, error
				if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
					$errorMessage = "A user with that name already exists.";
				}
				// If not found, continue
				else {
					// Get the highest ID currently stored in the table
					$sql = "SELECT MAX(userId) AS NEXT FROM PCUsers";
					$stid = oci_parse($conn, $sql);
					oci_execute($stid);

					// If found, continue
					if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
						// Increment ID by one, so we know it's available
						$nextID = $row['NEXT'];
						$nextID++;
						
						// Insert new User from form data
						$sql = "INSERT INTO PCUsers VALUES(" . $nextID . ", '" . $pcUsername . "', '" . $pcPassword . "')";
						$stid = oci_parse($conn, $sql);
						$r = oci_execute($stid);
						
						// If insertion failed, print error
						if (!$r) {
							$e = oci_error($stid);
							print htmlentities($e['message']);
							print "\n<pre>\n";
							print htmlentities($e['sqltext']);
							printf("\n%".($e['offset']+1)."s", "^");
							print  "\n</pre>\n";
						}
						// If insertion succeeds, print success message
						else {
							// Get the highest ID currently stored in the table
							$sql = "SELECT MAX(bId) AS NEXT FROM Box";
							$stid = oci_parse($conn, $sql);
							oci_execute($stid);
							$rowBox = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
							$nextBox = $rowBox['NEXT'];
							
							$sql = "SELECT MAX(sId) AS NEXT FROM BoxSlot";
							$stid = oci_parse($conn, $sql);
							oci_execute($stid);
							$rowSlot = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
							$nextSlot = $rowSlot['NEXT'];
							
							for ($loop = 1; $loop < 21; $loop++) {
								$nextBox++;
								$sql = "INSERT INTO Box VALUES(" . $nextBox . ", " . $nextID . ", " . $loop . ")";
								$stid = oci_parse($conn, $sql);
								oci_execute($stid);
								
								for ($loop2 = 1; $loop2 < 31; $loop2++) {
									$nextSlot++;
									$sql = "INSERT INTO BoxSlot VALUES(" . $nextSlot . ", " . $nextBox . ", NULL, " . $loop2 . ")";
									$stid = oci_parse($conn, $sql);
									oci_execute($stid);
								}
							}
							
							session_start();
							$_SESSION['login'] = "1";
							$_SESSION['username'] = $pcUsername;
							$_SESSION['uid'] = $nextID;
							header ("Location: pchome.php");
							exit();
							
						}
					}
					// If not found, error
					else {
						$errorMessage = "An error occurred when creating new user.";
					}
				}
			}
			
			// Close the connection
			oci_close($conn);
		}
		else {
			$errorMessage = 'Invalid registration.<BR>Username must be longer than 2 characters.<BR>Password must be longer than 6 characters.';
		}
	}
?>

<HTML>
	<HEAD>
		<TITLE>PC Storage System Register</TITLE>
	</HEAD>
	<BODY>
		<FORM NAME ="form1" METHOD ="POST" ACTION ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<P>Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $pcUsername;?>" maxlength="20">
			<P>Password: <INPUT TYPE = 'TEXT' Name ='password'  value="<?PHP print $pcPassword;?>" maxlength="16">
			<P>Confirm Password: <INPUT TYPE = 'TEXT' Name ='confirmPassword'  value="<?PHP print $pcPassword2;?>" maxlength="16">
			<P align = center><INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Login"></P>
		</FORM>
		<P><?PHP print_r($errorMessage);?>
	</BODY>
</HTML>