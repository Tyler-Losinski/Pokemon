<?PHP
	require("helper.php");
	sessionCheck();
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$slotId1 = $_POST['slotId1'];
		$pId1 = $_POST['pId1'];
		$boxId2 = $_POST['boxId2'];
		$slotNo2 = $_POST['slotNo2'];
		
		echo $slotId1 . "  ";
		echo $pId1 . "  ";
		echo $boxId2 . "  ";
		echo $slotNo2 . "  ";
		
		$conn = getConn();
		if (!conn) { printOciError(); }
		else {
			$sql = "UPDATE BoxSlot SET pId = null WHERE sId = :slotId1";
			$stid = oci_parse($conn, $sql);
			oci_bind_by_name($stid, ":slotId1", $slotId1);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
			
			$sql = "UPDATE BoxSlot SET pId = :pId1 WHERE bId = :boxId2 AND slotNo = :slotNo2";
			$stid = oci_parse($conn, $sql);
			oci_bind_by_name($stid, ":slotNo2", $slotNo2);
			oci_bind_by_name($stid, ":boxId2", $boxId2);
			oci_bind_by_name($stid, ":pId1", $pId1);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
		}
		oci_close($conn);
	}
