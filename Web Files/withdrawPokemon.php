<?PHP
	require("helper.php");
	sessionCheck();
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$slotId = $_POST['slotId'];
		$pId = $_POST['pId'];
		$pName = $_POST['pName'];
		
		$conn = getConn();
		if (!conn) { printOciError(); }
		else {
			$sql = "UPDATE BoxSlot SET pId = NULL WHERE sId = :slotId";
			$stid = oci_parse($conn, $sql);
			oci_bind_by_name($stid, ":slotId", $slotId);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
			
			$sql = "DELETE FROM Pokemon WHERE pId = :pId";
			$stid = oci_parse($conn, $sql);
			oci_bind_by_name($stid, ":pId", $pId);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
			else { echo $pName . ' was taken out.'; }
		}
		oci_close($conn);
	}
