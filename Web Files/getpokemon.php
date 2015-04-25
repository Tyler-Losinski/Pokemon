<?PHP
	// Connect to database
    $user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	// Get selected Pokemon's ID
	$sql = "SELECT p.pId from Pokemon p JOIN BoxSlot s ON p.pId = s.pId WHERE s.bId = " . $_GET['boxId'] . " AND s.slotNo = " . $_GET['slotNo'];
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	
	if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		$pId = $row['PID'];

		// Select all Slot IDs and Slot numbers from BoxSlot table that match the given Box ID
		$sql = "SELECT * FROM Pokemon p JOIN PkmnSpecies s ON p.dexNo = s.dexNo WHERE p.pId = " . $pId;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);

		$sql2 = "SELECT username FROM PCUsers WHERE userId = " . $row['OT'];
		$stid2 = oci_parse($conn, $sql2);
		oci_execute($stid2);
		$row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS);

		echo '<DIV ID="pkmnImageDiv"><IMG ID="pkmnImage" SRC="image/pkmnsprites/' . strtolower($row['NAME']) . '.gif"></DIV>';
		if ($row['NICKNAME']) { echo 'Name: ' . $row['NICKNAME'] . '<BR>'; }
		else { echo 'Name: ' . $row['NAME'] . '<BR>'; }
		echo 'Dex NO. ' . $row['DEXNO']. '<span style="padding-left:2em">' . $row['NAME'] . '</span><BR>';
		if ($row['TYPE2']) { echo 'Type: <IMG SRC="image/typeicons/' . $row['TYPE1'] . '.png"><IMG SRC="image/typeicons/' . $row['TYPE2'] . '.png"><BR>'; }
		else { echo 'Type: <IMG SRC="image/typeicons/' . $row['TYPE1'] . '.png"><BR>'; }
		echo 'Gender: ' . $row['G'] . '<BR>';
		echo 'Level: ' . $row['LVL'] . '<BR>';
		echo 'OT: ' . $row2['USERNAME'] . '<BR><BR>';

		$sqlAtk = "SELECT name FROM Attack WHERE aId = " . $row['ATTACK1'];
		$stidAtk = oci_parse($conn, $sqlAtk);
		oci_execute($stidAtk);
		$rowAtk = oci_fetch_array($stidAtk, OCI_ASSOC+OCI_RETURN_NULLS);

		echo 'Moves Learned: <BR>';
		echo '  - ' . $rowAtk['NAME'] . '<BR>';

		if ($row['ATTACK2']) {
			$sqlAtk = "SELECT name FROM Attack WHERE aId = " . $row['ATTACK2'];
			$stidAtk = oci_parse($conn, $sqlAtk);
			oci_execute($stidAtk);
			$rowAtk = oci_fetch_array($stidAtk, OCI_ASSOC+OCI_RETURN_NULLS);
			
			echo '  - ' . $rowAtk['NAME'] . '<BR>';
		}
		else {
			echo '<BR>';
		}

		if ($row['ATTACK3']) {
			$sqlAtk = "SELECT name FROM Attack WHERE aId = " . $row['ATTACK3'];
			$stidAtk = oci_parse($conn, $sqlAtk);
			oci_execute($stidAtk);
			$rowAtk = oci_fetch_array($stidAtk, OCI_ASSOC+OCI_RETURN_NULLS);
			
			echo '  - ' . $rowAtk['NAME'] . '<BR>';
		}
		else {
			echo '<BR>';
		}
		if ($row['ATTACK4']) {
			$sqlAtk = "SELECT name FROM Attack WHERE aId = " . $row['ATTACK4'];
			$stidAtk = oci_parse($conn, $sqlAtk);
			oci_execute($stidAtk);
			$rowAtk = oci_fetch_array($stidAtk, OCI_ASSOC+OCI_RETURN_NULLS);
			
			echo '  - ' . $rowAtk['NAME'] . '<BR>';
		}
		else {
			echo '<BR>';
		}
		echo '<DIV><FORM METHOD="POST" ACTION="withdraw.php"'
			. '<P><INPUT TYPE="SUBMIT" NAME="withdraw" VALUE="Withdraw"></P>'
			. '<INPUT TYPE="hidden" NAME="pId" VALUE="' . $row['PID'] . '">'
			. '<INPUT TYPE="hidden" NAME="boxId" VALUE="' . $_GET['boxId'] . '" />'
			. '<INPUT TYPE="hidden" NAME="slotNo" VALUE="' . $_GET['slotNo'] . '" /></FORM>';
	}
	else {
		echo '';
	}
	
	oci_close($conn);


	
