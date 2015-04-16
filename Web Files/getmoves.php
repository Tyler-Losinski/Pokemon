<?PHP
	// Connect to database
    $user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	// Select all Attack IDs and Attack names from Attack table where they match the given Dex number given in the CanLearn table, ordered by Attack name.
	$sql = "SELECT a.aid, a.name FROM CanLearn c JOIN Attack a ON c.aid = a.aId WHERE c.dexno = " . $_GET["dexNo"] . " ORDER BY a.name";
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	
	// Spit out default option
	echo '<option value="">---</option>';
	// For each returned row, spit out drop-down code.  Value = Attack ID, Text = Attack name
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		echo '<option value="' . $row['AID'] . '">' . $row['NAME'] . '</option>';
	}
	// Close connection
	oci_close($conn);