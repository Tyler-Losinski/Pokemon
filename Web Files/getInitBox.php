<?PHP
	// Connect to the database
	$user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	// Select box ID and box number from boxes table for the current user
	$sql = "SELECT bid FROM box WHERE owner = " . $_GET['uid'] . " ORDER BY boxNo";
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
	echo $row['BID'];
		
	// Close connection
	oci_close($conn);