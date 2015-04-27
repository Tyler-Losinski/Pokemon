<?PHP
	$user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	$fromBoxId = htmlspecialchars($_GET['fromBoxId']);
	$fromSlotNo = htmlspecialchars($_GET['fromSlotNo']);
	$toBoxId = htmlspecialchars($_GET['toBoxId']);
	$toSlotNo = htmlspecialchars($_GET['toSlotNo']);
	
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
		// Get selected Pokemon's ID
		$sql = "SELECT p.pId from Pokemon p JOIN BoxSlot s ON p.pId = s.pId WHERE s.bId = " . $fromBoxId . " AND s.slotNo = " . $fromSlotNo;
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		
		if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			$pId1 = $row['PID'];
			
			// Get second selected Pokemon's ID
			$sql = "SELECT p.pId from Pokemon p JOIN BoxSlot s ON p.pId = s.pId WHERE s.bId = " . $toBoxId . " AND s.slotNo = " . $toSlotNo;
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			$pId2 = "";
			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				$pId2 = $row['PID'];
			}
			else {
				$pId2 = 'NULL';
			}
			
			$sql = "UPDATE BoxSlot SET pId = " . $pId2 . " WHERE bId = " . $fromBoxId . " AND slotNo = " . $fromSlotNo;
			$stid = oci_parse($conn, $sql);
			$r = oci_execute($stid);
		
			// If update failed, print error
			if (!$r) {
				$e = oci_error($stid);
				print htmlentities($e['message']);
				print "\n<pre>\n";
				print htmlentities($e['sqltext']);
				printf("\n%".($e['offset']+1)."s", "^");
				print  "\n</pre>\n";
			}
			
			$sql = "UPDATE BoxSlot SET pId = " . $pId1 . " WHERE bId = " . $toBoxId . " AND slotNo = " . $toSlotNo;
			$stid = oci_parse($conn, $sql);
			$r = oci_execute($stid);
		
			// If update failed, print error
			if (!$r) {
				$e = oci_error($stid);
				print htmlentities($e['message']);
				print "\n<pre>\n";
				print htmlentities($e['sqltext']);
				printf("\n%".($e['offset']+1)."s", "^");
				print  "\n</pre>\n";
			}
			// If update succeeds, print success message
			else {
				echo 'Pokemon moved successfully.<BR>';
			}
			
		}
		// If failed, print error
		else {
			echo 'An error occurred: Could not move.<BR>';
		}
	}
	// Close the connection
	oci_close($conn);