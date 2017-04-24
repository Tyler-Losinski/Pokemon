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
		<TITLE>PC Storage System Move</TITLE>
		<LINK REL="stylesheet" TYPE="text/css" HREF="css/boxInterface.css"></LINK>
		<SCRIPT>var Session = { uid: <?PHP echo $_SESSION['uid']; ?> };  var firstBoxId = <?PHP echo $box1Id; ?></SCRIPT>
		<SCRIPT SRC="javascript/jquery-1.11.2.min.js"></SCRIPT>
		<SCRIPT SRC="javascript/boxInterface.js"></SCRIPT>
		<SCRIPT>
			function setCellClickHander() {
				$('td').click(function() {
					if ($selectedCell === null) {
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
					else if (selectedBoxNo != currentBoxNo || !$selectedCell.contentsEqual($(this))){
						var col = $(this).parent().children().index($(this));
						var row = $(this).parent().parent().children().index($(this).parent());
						secondaryPokemon = getPokemonFromCell(row, col);
						if (secondaryPokemon !== null)
							onMove(selectedPokemon.slotId, selectedPokemon.pokemonId, secondaryPokemon.slotId, secondaryPokemon.pokemonId);
						else {
							var boxId = firstBoxId + (currentBoxNo - 1);
							var slotNo = row*6 + col + 1;
							onMove2(selectedPokemon.slotId, selectedPokemon.pokemonId, boxId, slotNo);
						}
					}
					else {
						$(this).css({"background":"inherit"});
						$selectedCell = null;
						showPokemon(null);
					}
				});
			}
			function onMove(sId1, pId1, sId2, pId2) {
				if (selectedPokemon !== null) {
					$.ajax({
						url: "/~adhart/movePokemon.php",
						data: { slotId1: sId1, pId1: pId1, slotId2: sId2, pId2: pId2 },
						type: "POST",
						success: function (successMessage) {
							console.log(successMessage);
							selectedPokemon = null;
							$selectedCell.css({"background":"inherit"});
							$selectedCell = null;
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
			function onMove2(sId1, pId1, boxId2, slotNo2) {
				if (selectedPokemon !== null) {
					$.ajax({
						url: "/~adhart/movePokemon2.php",
						data: { slotId1: sId1, pId1: pId1, boxId2: boxId2, slotNo2: slotNo2 },
						type: "POST",
						success: function (successMessage) {
							console.log(successMessage);
							selectedPokemon = null;
							$selectedCell.css({"background":"inherit"});
							$selectedCell = null;
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
		<H4>Click on a Pokemon to move, and then click on where you want to move it</H4>
		<DIV ID="boxInterface"></DIV>
	</BODY>
</HTML>
