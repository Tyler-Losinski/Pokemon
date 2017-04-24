<?PHP
	require("helper.php");
	sessionCheck();
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$slotId1 = $_POST['slotId1'];
		$pId1 = $_POST['pId1'];
		$slotId2 = $_POST['slotId2'];
		$pId2 = $_POST['pId2'];
		
		$conn = getConn();
		if (!conn) { printOciError(); }
		else {
			$sql = "UPDATE BoxSlot SET pId = :pId2 WHERE sId = :slotId1";
			$stid = oci_parse($conn, $sql);
			oci_bind_by_name($stid, ":slotId1", $slotId1);
			oci_bind_by_name($stid, ":pId2", $pId2);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
			
			$sql = "UPDATE BoxSlot SET pId = :pId1 WHERE sId = :slotId2";
			$stid = oci_parse($conn, $sql);
			oci_bind_by_name($stid, ":slotId2", $slotId2);
			oci_bind_by_name($stid, ":pId1", $pId1);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
		}
		oci_close($conn);
	}
