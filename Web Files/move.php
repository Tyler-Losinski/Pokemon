<?PHP
	// Start session
	session_start();
	if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
		header ("Location: login.php");
		exit();
	}
	
	
?>

<HTML>
	<HEAD>
		<META CONTENT="text/html;charset=utf-8" HTTP-EQUIV="Content-Type">
		<META CONTENT="utf-8" HTTP-EQUIV="encoding">
		<TITLE>PC Storage System Move</TITLE>
		<LINK REL="stylesheet" TYPE="text/css" HREF="css/box.css"></LINK>
		<LINK REL="stylesheet" TYPE="text/css" HREF="css/pokemonInfo.css"></LINK>
		<STYLE>
			.pokemonInfo {
				float: left;
				padding: 10px;
				margin-top: 30px;
				width: 350px;
			}
			
			.moveDiv {
				float: left;
				height: 378px;
				margin: 10px;
				padding:5px;
			}
		</STYLE>
		<SCRIPT>
			var Session = { uid: <?PHP echo $_SESSION['uid'] ?> };
			
			function onMove() {
				if (boxTables[0].currentCell != -1 && boxTables[1].currentCell != -1) {
					var cbId1 = (boxTables[0].box1Id + (boxTables[0].currentBox - 1));
					var ccId1 = ((boxTables[0].currentRow-1)*6 + boxTables[0].currentCell);
					var cbId2 = (boxTables[1].box1Id + (boxTables[1].currentBox - 1));
					var ccId2 = ((boxTables[1].currentRow-1)*6 + boxTables[1].currentCell);
					console.log(cbId1);
					console.log(ccId1);
					console.log(cbId2);
					console.log(ccId2);
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							scrollBoxes(boxTables[0].tableId, 0);
							scrollBoxes(boxTables[1].tableId, 0);
							document.getElementById('error').innerHTML = xmlhttp.responseText;
						}
					};
					xmlhttp.open("GET", "/~adhart/movePokemon.php?fromBoxId=" + cbId1 + "&fromSlotNo=" + ccId1 + "&toBoxId=" + cbId2 + "&toSlotNo=" + ccId2, true);
					xmlhttp.send();
				}
			}
		</SCRIPT>
		<SCRIPT SRC="javascript/box2.js"></SCRIPT>
	</HEAD>
	<BODY>
		<P ID="error"></P>
		<P><a href="/~adhart/pchome.php">Back to home</a>
			<DIV CLASS="boxDiv">
				<DIV CLASS="boxSelectDiv">
					<DIV CLASS="boxNameDiv">
						<H2 ID="boxName_from" CLASS="boxName"></H2>
					</DIV>
					<IMG CLASS="lArrow" SRC="image/arrowL.png" ONCLICK="javascript:scrollBoxes('from', -1)"></IMG>
					<IMG CLASS="rArrow" SRC="image/arrowR.png" ONCLICK="javascript:scrollBoxes('from', 1)"></IMG>
				</DIV>
				<TABLE ID="boxTable_from" CLASS="boxTable">
					<!-- JavaScript will fill in the table -->
				</TABLE>
				<DIV ID="pokemonInfo_from" CLASS="pokemonInfo"></DIV>
			</DIV>
			<DIV CLASS="moveDiv">
				<BUTTON ID="move" ONCLICK="onMove()">Move</BUTTON>
			</DIV>
			<DIV CLASS="boxDiv">
				<DIV CLASS="boxSelectDiv">
					<DIV CLASS="boxNameDiv">
						<H2 ID="boxName_to" CLASS="boxName"></H2>
					</DIV>
					<IMG CLASS="lArrow" SRC="image/arrowL.png" ONCLICK="javascript:scrollBoxes('to', -1)"></IMG>
					<IMG CLASS="rArrow" SRC="image/arrowR.png" ONCLICK="javascript:scrollBoxes('to', 1)"></IMG>
				</DIV>
				<TABLE ID="boxTable_to" CLASS="boxTable">
					<!-- JavaScript will fill in the table -->
				</TABLE>
				<DIV ID="pokemonInfo_to" CLASS="pokemonInfo"></DIV>
			</DIV>
		
		
	</BODY>
</HTML>