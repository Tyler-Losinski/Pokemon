<?PHP
	// Connect to database
    $user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	// Select all Slot IDs and Slot numbers from BoxSlot table that match the given Box ID
	$sql = "SELECT s.sId, s.slotNo FROM Box b JOIN BoxSlot s ON b.bId = s.bId WHERE b.bId = " . $_GET["boxId"] . "AND s.pId IS NULL";
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	
	// Spit out default option
	echo '<option value="">---</option>';
	// For each returned row, spit out drop-down code.  Value = Slot ID, Text = Slot number
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		echo '<option value="' . $row['SID'] . '">' . $row['SLOTNO'] . '</option>';
	}
	// Close connection
	oci_close($conn);