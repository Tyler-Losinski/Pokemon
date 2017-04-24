<?php
    $user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	if (!conn) {
		$e = oci_error();
		echo 'failure';
	}
	else {
		$sql = "SELECT bid, boxNo FROM box WHERE owner = " . $_GET['userId'];
		
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		
		echo '<option value="0">---</option>';
		while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			echo '<option value="' . $row['BID'] . '">' . $row['BOXNO'] . '</option>';
		}
	}

	oci_close($conn);