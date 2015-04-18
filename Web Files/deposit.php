<?PHP
	// Start session
	session_start();
	if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
		header ("Location: login.php");
		exit();
	}

	// If the page receives a submit request
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Escape dangerous HTML characters
		$dexNo = htmlspecialchars($dexNo = $_POST['dexNo']);
		$lvl = htmlspecialchars($_POST['lvl']);
		$sex = htmlspecialchars($_POST['sex']);
		$move1 = htmlspecialchars($_POST['move1']);
		$move2 = htmlspecialchars($_POST['move2']);
		$move3 = htmlspecialchars($_POST['move3']);
		$move4 = htmlspecialchars($_POST['move4']);
		$nickname = htmlspecialchars($_POST['nickname']);
		$slotId = htmlspecialchars($_POST['slotNo']);
		
		// Connect to server
		$user_name = 'adhart';
		$pass_word = 'Aug111995';
		$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
		$conn = oci_connect($user_name, $pass_word, $conn_string);
		
		// If connection failed, print error
		if (!conn) {
			$e = oci_error();
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
		else {
			// Get the highest ID currently stored in the table
			$sql = "SELECT MAX(pId) AS NEXT FROM Pokemon";
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				// Increment ID by one, so we know it's available
				$nextID = $row['NEXT'];
				$nextID++;
				
				// Insert new Pokemon from form data
				$sql = "INSERT INTO Pokemon VALUES(" . $nextID . ", " . $dexNo . ", '" . $sex . "', " . $lvl . ", " . $_SESSION['uid'] . ", "
					. $move1 . ", " . $move2 . ", " . $move3 . ", " . $move4 . ", '" . $nickname . "')";
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
					echo 'Pokemon created<BR>';
				}
				
				// Insert newly-created Pokemon into the specified box slot
				$sql = "UPDATE BoxSlot SET pId=" . $nextID . " WHERE sId=" . $slotId;
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
					echo 'Deposit successful<BR>';
				}
			}
			// If could not get highest ID, print error
			else {
				echo "Error getting next value";
			}
		}
		// Close the connection
		oci_close($conn);
	}
	
	// Populates the "Select Pokemon" drop-down list
	function popDropDown() {
		// Connect to database
		$user_name = 'adhart';
		$pass_word = 'Aug111995';
		$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
		$conn = oci_connect($user_name, $pass_word, $conn_string);
		
		// If connection failed, print error
		if (!conn) {
			$e = oci_error();
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
		else {
			// Select National Dex number (PK) and species name from list, in Nat Dex order.
			$sql = "SELECT dexNo, name FROM PkmnSpecies ORDER BY dexNo";
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			// Spit out default option
			echo '<option value="">---</option>';
			// For each returned row, spit out drop-down code.  Value = NatDex No, Text = species name
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				echo '<option value="' . $row['DEXNO'] . '">#' . $row['DEXNO'] . ' ' . $row['NAME'] . '</option>';
			}
		}
		// Close connection
		oci_close($conn);
	}

	// Populates the box list for the current user
	function popBoxes() {
		// Connect to the database
		$user_name = 'adhart';
		$pass_word = 'Aug111995';
		$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
		$conn = oci_connect($user_name, $pass_word, $conn_string);
		
		// If connection failed, print error
		if (!conn) {
			$e = oci_error();
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
		else {
			// Select box ID and box number from boxes table for the current user
			$sql = "SELECT bid, boxNo FROM box WHERE owner = " . $_SESSION['uid'];
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			// Spit out default option
			echo '<option value="">---</option>';
			// For each returned row, spit out drop-down code.  Value = box ID, Text = box number
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				echo '<option value="' . $row['BID'] . '">' . $row['BOXNO'] . '</option>';
			}
		}
		// Close connection
		oci_close($conn);
	}
?>

<html>
	<head>
		<style>
			.initiallyHidden { display: none; }
		</style>
		<script>
			// Populates the Move drop-down lists based on the Pokemon selected
			function pokemonSelected(dexNo) {
				if (dexNo != 0) {
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							document.getElementById("move1").innerHTML = xmlhttp.responseText;
							document.getElementById("move2").innerHTML = xmlhttp.responseText;
							document.getElementById("move3").innerHTML = xmlhttp.responseText;
							document.getElementById("move4").innerHTML = xmlhttp.responseText;
						}
					}
					xmlhttp.open("GET", "/~adhart/getmoves.php?dexNo=" + dexNo, true);
					xmlhttp.send();
					
					document.getElementById("Form").style.display = "inline";
				}
				else {
					document.getElementById("Form").style.display = "none";
				}
			}
			
			// Populates the Box slot drop-down list based on the Box selected.  Only returns OPEN slots.
			function boxSelected(id)
			{
				if (id != 0) {
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							document.getElementById("slotNo").innerHTML = xmlhttp.responseText;
						}
					}
					xmlhttp.open("GET", "/~adhart/getemptyslots.php?boxId=" + id, true);
					xmlhttp.send();
					
					document.getElementById("slotNoPar").style.display = "inline";
				}
				else {
					document.getElementById("slotNoPar").style.display = "none";
				}
			}
		</script>
	</head>
	<body>
		<P><select name="dexNo" form="depositForm" onchange="pokemonSelected(this.value)"><?php popDropDown(); ?></select>
		
		<div ID = "Form" CLASS="initiallyHidden">
			<FORM NAME ="depositForm" ID="depositForm" METHOD ="POST" ACTION ="deposit.php">
				<P><INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Deposit">
				
				<P>
					Nickname: <INPUT TYPE = 'TEXT' NAME = 'nickname' maxlength='12'><BR>
					Level: <INPUT TYPE = 'TEXT' Name ='lvl' maxlength="3"><BR>
					Gender: <INPUT TYPE = 'RADIO' Name ='sex' value="m" checked> Male <INPUT TYPE = 'RADIO' Name  = 'sex' value = "f"> Female
				</P>
			</FORM>
			
			<P>
				Move 1: <select form="depositForm" id="move1" name="move1"></select><BR>
				Move 2: <select form="depositForm" id="move2" name="move2"></select><BR>
				Move 3: <select form="depositForm" id="move3" name="move3"></select><BR>
				Move 4: <select form="depositForm" id="move4" name="move4"></select>
			</P>
			
			<P>
				Box: <select id="boxNo" name="boxNo" form="depositForm" onchange = "boxSelected(this.value)"><?PHP popBoxes(); ?></select> <span id = slotNoPar class = "initiallyHidden">Slot: <select form="depositForm" id="slotNo" name="slotNo"></select></span>
			</P>
		</div>
		<P><a href="/~adhart/pchome.php">Back to home</a>
	</body>
</html>