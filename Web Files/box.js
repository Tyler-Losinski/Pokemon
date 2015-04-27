var boxTables = [];

window.onload = function initTables() {
	var inputs = document.getElementsByTagName("table");
	for (var i = 0; i < inputs.length; i++) {
		console.log(inputs[i].id);
		if (inputs[i].id.indexOf("boxTable_") == 0) {
			var id = inputs[i].id.substr(9);
			console.log(id);
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var firstBoxId = parseInt(xmlhttp.responseText);
					var table = { tableId:id, currentBox:1, box1Id:firstBoxId, currentRow:-1, currentCell:-1 }
					console.log( JSON.stringify(table) );
					boxTables.push(table);
					scrollBoxes(id, 0);
				}
			};
			xmlhttp.open("GET", "/~adhart/getInitBox.php?uid=" + Session.uid, false);
			xmlhttp.send();
		}
	}
	console.log( JSON.stringify(boxTables) );
}

function getTableById(id) {
	for (var i = 0; i < boxTables.length; i++) {
		if (boxTables[i].tableId === id) {
			return i;
		}
	}
}

function scrollBoxes(tableId, changeAmt) {
	console.log(tableId);
	var tableIndex = getTableById(tableId);
	var table = boxTables[tableIndex];
	table.currentBox += parseInt(changeAmt);
	if (table.currentBox < 1) { table.currentBox = 20; }
	if (table.currentBox > 20) { table.currentBox = 1; }
	boxTables[tableIndex] = table;
	var newBoxId = (table.currentBox - 1 + table.box1Id);
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("boxTable_" + table.tableId).innerHTML = xmlhttp.responseText;
			document.getElementById("boxName_" + table.tableId).innerHTML = "Box " + table.currentBox;
		}
	};
	xmlhttp.open("GET", "/~adhart/getbox2.php?tableId=" + table.tableId + "&boxId=" + newBoxId, true);
	xmlhttp.send();
}

function selectSlot(tableId, row, column, boxId, pkmnInfoId) {
	var tableIndex = getTableById(tableId);
	var table = boxTables[tableIndex];
	if (row != table.currentRow || table.currentCell != column) {
		if (table.currentRow != -1 && table.currentCell != -1) {
			document.getElementById('cell_' + table.tableId + ',' + table.currentRow + ',' + table.currentCell).style.background = 'inherit';
		}
		document.getElementById('cell_' + table.tableId + ',' + row + ',' + column).style.background='#AAF';
		table.currentRow = row;
		table.currentCell = column;
		boxTables[tableIndex] = table;
	}
	else {
		document.getElementById('cell_' + table.tableId + ',' + table.currentRow + ',' + table.currentCell).style.background = 'inherit';
		table.currentRow = -1;
		table.currentCell = -1;
		boxTables[tableIndex] = table;
	}
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById("pokemonInfo_" + table.tableId).innerHTML = xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET", "/~adhart/getpokemon.php?boxId=" + boxId + "&slotNo=" + ((table.currentRow-1)*6 + table.currentCell), true);
	xmlhttp.send();
}