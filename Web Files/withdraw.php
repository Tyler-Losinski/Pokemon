<?PHP
	require("helper.php");
	sessionCheck();
	
	$box1Id;
	
	$conn = getConn();
	if (!conn) { printOciError(); }
	else {
		$sql = "SELECT bid FROM box WHERE owner = :PCuserId ORDER BY boxNo";
		$stid = oci_parse($conn, $sql);
		oci_bind_by_name($stid, ":PCuserId", $_SESSION['uid']);
		$r = oci_execute($stid);
	
		if (!$r) { printQueryError($stid); }
		else {
			$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
			$box1Id = $row['BID'];
		}
	}
	oci_close($conn);
?>
<!DOCTYPE html>
<HTML>
	<HEAD>
		<META CONTENT="text/html;charset=utf-8" HTTP-EQUIV="Content-Type">
		<META CONTENT="utf-8" HTTP-EQUIV="encoding">
		<TITLE>PC Storage System Withdraw</TITLE>
		<LINK REL="stylesheet" TYPE="text/css" HREF="css/boxInterface.css"></LINK>
		<SCRIPT>var Session = { uid: <?PHP echo $_SESSION['uid']; ?> };  var firstBoxId = <?PHP echo $box1Id; ?></SCRIPT>
		<SCRIPT SRC="javascript/jquery-1.11.2.min.js"></SCRIPT>
		<SCRIPT SRC="javascript/boxInterface.js"></SCRIPT>
		<SCRIPT>
			function setCellClickHander() {
				$('td').click(function() {
					if ($selectedCell === null || selectedBoxNo != currentBoxNo || !$selectedCell.contentsEqual($(this))) {
						if ($selectedCell !== null) {
							$selectedCell.css({"background":"inherit"});
						}
						$(this).css({"background":"#AAF"});
						var col = $(this).parent().children().index($(this));
						var row = $(this).parent().parent().children().index($(this).parent());
						selectedPokemon = getPokemonFromCell(row, col);
						$selectedCell = $(this);
						selectedBoxNo = currentBoxNo;
						showPokemon(selectedPokemon);
					}
					else {
						$(this).css({"background":"inherit"});
						$selectedCell = null;
						showPokemon(null);
					}
				});
			}
			function onWithdraw() {
				if (selectedPokemon !== null) {
					$.ajax({
						url: "/~adhart/withdrawPokemon.php",
						data: { slotId: selectedPokemon.slotId, pId: selectedPokemon.pId, pName: selectedPokemon.nickname },
						type: "POST",
						success: function (successMessage) {
							$("#successMessage").html(successMessage);
							selectedPokemon = null;
							showPokemon(null);
							getBox(currentBoxNo - 1 + firstBoxId);
						},
						error: function( xhr, status, errorThrown ) {
							alert( "Sorry, there was a problem!" );
							console.log( "Error: " + errorThrown );
							console.log( "Status: " + status );
							console.dir( xhr );
						}
					});
				}
				else {
					$("#successMessage").html("");
				}
			}
		</SCRIPT>
	</HEAD>
	<BODY>
		<P><a href="/~adhart/pchome.php">Back to home</a>
		<DIV ID="boxInterface"></DIV>
		<BUTTON ID="withdraw" ONCLICK="onWithdraw()">Withdraw</BUTTON>
		<P ID="successMessage">
	</BODY>
</HTML>
