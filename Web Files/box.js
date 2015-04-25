var currentBox = 1;
var box1Id = 0;

window.onload = function getFirstBox() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			box1Id = parseInt(xmlhttp.responseText);
			scrollBoxes(0);
		}
	};
	xmlhttp.open("GET", "/~adhart/getInitBox.php?uid=" + Session.uid, true);
	xmlhttp.send();
}

function scrollBoxes(changeAmt) {
	currentBox += parseInt(changeAmt);
	if (currentBox < 1) { currentBox = 20; }
	if (currentBox > 20) { currentBox = 1; }
	var newBoxId = (currentBox - 1 + box1Id);
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("boxTable").innerHTML = xmlhttp.responseText;
			document.getElementById("boxName").innerHTML = "Box " + currentBox;
		}
	};
	xmlhttp.open("GET", "/~adhart/getbox2.php?boxId=" + newBoxId, true);
	xmlhttp.send();
}

var currentRow=-1;
var currentCell=-1;

function selectSlot(row, column, boxId) {
	if (row != currentRow || currentCell != column) {
		if (currentRow != -1 && currentCell != -1) {
			document.getElementById('cell_' + currentRow + ',' + currentCell).style.background = 'inherit';
		}
		document.getElementById('cell_' + row + ',' + column).style.background='#AAF';
		currentRow = row;
		currentCell = column;
	}
	else {
		document.getElementById('cell_' + currentRow + ',' + currentCell).style.background = 'inherit';
		currentRow = -1;
		currentCell = -1;
	}
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("pokemonInfo").innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET", "/~adhart/getpokemon.php?boxId=" + boxId + "&slotNo=" + ((currentRow-1)*6 + currentCell), true);
	xmlhttp.send();
}