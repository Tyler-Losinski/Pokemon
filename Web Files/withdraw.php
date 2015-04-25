<?PHP
	require("helper.php");
	sessionCheck();
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$pId = htmlspecialchars($_POST['pId']);
		$boxId = htmlspecialchars($_POST['boxId']);
		$slotNo = htmlspecialchars($_POST['slotNo']);
		
		$conn = getConn();
		if (!conn) { printOciError(); }
		else {
			$sql = "UPDATE BoxSlot SET pId = NULL WHERE bId = " . $boxId . " AND slotNo = " . $slotNo;
			$stid = oci_parse($conn, $sql);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
			else { echo 'Removal from box successful.<BR>';	}
			
			$sql = "DELETE FROM Pokemon WHERE pId = " . $pId;
			$stid = oci_parse($conn, $sql);
			$r = oci_execute($stid);
		
			if (!$r) { printQueryError($stid); }
			else { echo 'Pokemon withdrawn.<BR>'; }
		}
		oci_close($conn);
	}
?>

<!DOCTYPE html>
<HTML>
	<HEAD>
		<META CONTENT="text/html;charset=utf-8" HTTP-EQUIV="Content-Type">
		<META CONTENT="utf-8" HTTP-EQUIV="encoding">
		<TITLE>PC Storage System Withdraw</TITLE>
		<LINK REL="stylesheet" TYPE="text/css" HREF="css/box.css"></LINK>
		<LINK REL="stylesheet" TYPE="text/css" HREF="css/pokemonInfo.css"></LINK>
		<STYLE>
			#pokemonInfo {
				float: left;
				padding: 10px;
				width: 350px;
			}
		</STYLE>
		<SCRIPT>
			var Session = { uid: <?PHP echo $_SESSION['uid'] ?> };
		</SCRIPT>
		<SCRIPT SRC="javascript/box.js"></SCRIPT>
	</HEAD>
	<BODY>
		<P><a href="/~adhart/pchome.php">Back to home</a>
			<DIV ID="boxDiv">
				<DIV ID="boxSelectDiv">
					<DIV ID="boxNameDiv">
						<H2 ID="boxName" ID="boxName"></H2>
					</DIV>
					<IMG ID="lArrow" SRC="image/arrowL.png" ONCLICK="javascript:scrollBoxes(-1)"></IMG>
					<IMG ID="rArrow" SRC="image/arrowR.png" ONCLICK="javascript:scrollBoxes(1)"></IMG>
				</DIV>
				<TABLE ID="boxTable">
					<!-- JavaScript will fill in the table -->
				</TABLE>
			</DIV>
		<DIV ID="pokemonInfo"></DIV>
	</BODY>
</HTML>