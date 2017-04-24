var currentBoxNo = 1;
var currentBox;
var $selectedCell = null;
var selectedBoxNo = 1;
var selectedPokemon = null;

$.fn.contentsEqual = function(compareTo) {
	return compareTo && this.length === compareTo.length && this.length === this.filter(compareTo).length;
};

$(function(){
	$("#boxInterface")
		.load("boxInterface.html", setUp);
});

function setUp() {
	var $loading = $("#overlay").hide();
	$(document)
		.ajaxStart(function () {
			$loading.show();
			console.log("Ajax sent; Overlay shown");
		})
		.ajaxStop(function () {
			$loading.hide();
			console.log("Ajax stopped; Overlay hidden");
		});
	console.log("First Box ID: " + firstBoxId);
	getBox(firstBoxId);
	$("#lArrow").click(function(){scrollBoxes(-1);});
	$("#rArrow").click(function(){scrollBoxes(1);});
	
	setCellClickHander();
}

function showPokemon(selPokemon) {
	if (selPokemon === null) {
		$("#pkmnImageDiv").html("");
		$("#pkmnName").html("");
		$("#pkmnDexNo").html("");
		$("#pkmnSpecies").html("");
		$("#pkmnType1").html("");
		$("#pkmnType2").html("");
		$("#pkmnGender").html("");
		$("#pkmnLevel").html("");
		$("#pkmnOT").html("");
		$("#pkmnMove1").html("");
		$("#pkmnMove2").html("");
		$("#pkmnMove3").html("");
		$("#pkmnMove4").html("");
	}
	else {
		$("#pkmnImageDiv").html("<IMG ID='pkmnImage' SRC='image/pkmnsprites/" + selPokemon.species.toLowerCase() + ".gif'></IMG>");
		$("#pkmnName").html(selPokemon.nickname);
		$("#pkmnDexNo").html("#" + selPokemon.dexNo);
		$("#pkmnSpecies").html(selPokemon.species);
		$("#pkmnType1").html("<IMG SRC='image/typeicons/" + selPokemon.type1Id + ".png'></IMG>");
		//$("#pkmnType1").html("test");
		if (selPokemon.type2Id !== null) {
			$("#pkmnType2").html("<IMG SRC='image/typeicons/" + selPokemon.type2Id + ".png'></IMG>");
		}
		else {
			$("#pkmnType2").html("");
		}
		$("#pkmnGender").html("<IMG SRC='image/" + selPokemon.gender + ".png'></IMG>");
		$("#pkmnLevel").html("Lvl: " + selPokemon.lvl);
		$("#pkmnOT").html("OT: " + selPokemon.ot);
		$("#pkmnMove1").html(selPokemon.attack1);
		$("#pkmnMove2").html(selPokemon.attack2);
		$("#pkmnMove3").html(selPokemon.attack3);
		$("#pkmnMove4").html(selPokemon.attack4);
	}
}

function getBox(sendBoxId) {
	$.ajax({
		url: "/~adhart/getbox.php",
		data: { boxId: sendBoxId },
		type: "POST",
		dataType: "xml",
		success: function (xml) {
			currentBox = new Box(xml);
			printBox();
		},
		error: function( xhr, status, errorThrown ) {
			alert( "Sorry, there was a problem!" );
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
		}
	});
}

function printBox() {
	var table = document.getElementById("boxTable");
	for (var i = 1; i <= table.rows.length; i++) {
		for (var j = 1; j <= table.rows[i-1].cells.length; j++) {
			var q = (i-1)*6 + j;
			var slotNo = 'slot' + q;
			if (currentBox[slotNo]) {
				table.rows[i-1].cells[j-1].children[0].innerHTML = '<IMG CLASS="icon" SRC="image/pkmnicons/' + currentBox[slotNo].dexNo + '.png"></IMG>';
			}
			else {
				table.rows[i-1].cells[j-1].children[0].innerHTML = '';
			}
			$("#boxName").html("Box " + currentBoxNo);
		}
	}
	if (selectedBoxNo != currentBoxNo && $selectedCell !== null) {
		$selectedCell.css({"background":"inherit"});
	}
	else if ($selectedCell !== null) {
		$selectedCell.css({"background":"#AAF"});
	}
}

var Pokemon = function(xmlDoc) {
	var attList = xmlDoc.childNodes[0].childNodes;
	this.pokemonId = attList[0].childNodes[0].data;
	this.nickname = nicknameOrNull(attList);
	this.dexNo = attList[2].childNodes[0].data;
	this.species = attList[3].childNodes[0].data;
	this.type1 = attList[4].childNodes[0].data;
	this.type1Id = attList[5].childNodes[0].data;
	this.type2 = dataOrNull(attList[6]);
	this.type2Id = dataOrNull(attList[7]);
	this.gender = dataOrNull(attList[8]);
	this.lvl = attList[9].childNodes[0].data;
	this.ot = attList[10].childNodes[0].data;
	this.otId = attList[11].childNodes[0].data;
	this.attack1 = attList[12].childNodes[0].data;
	this.attack1Id = attList[13].childNodes[0].data;
	this.attack2 = dataOrNull(attList[14]);
	this.attack2Id = dataOrNull(attList[15]);
	this.attack3 = dataOrNull(attList[16]);
	this.attack3Id = dataOrNull(attList[17]);
	this.attack4 = dataOrNull(attList[18]);
	this.attack4Id = dataOrNull(attList[19]);
	this.boxId = attList[20].childNodes[0].data;
	this.boxNo = attList[21].childNodes[0].data;
	this.slotId = attList[22].childNodes[0].data;
	this.slotNo = attList[23].childNodes[0].data;
	console.log(this);
};

var Box = function(xmlDoc) {
	var attList = xmlDoc.childNodes[0].childNodes;
	this.slot1 = pokemonOrNull(attList[0]);
	this.slot2 = pokemonOrNull(attList[1]);
	this.slot3 = pokemonOrNull(attList[2]);
	this.slot4 = pokemonOrNull(attList[3]);
	this.slot5 = pokemonOrNull(attList[4]);
	this.slot6 = pokemonOrNull(attList[5]);
	this.slot7 = pokemonOrNull(attList[6]);
	this.slot8 = pokemonOrNull(attList[7]);
	this.slot9 = pokemonOrNull(attList[8]);
	this.slot10 = pokemonOrNull(attList[9]);
	this.slot11 = pokemonOrNull(attList[10]);
	this.slot12 = pokemonOrNull(attList[11]);
	this.slot13 = pokemonOrNull(attList[12]);
	this.slot14 = pokemonOrNull(attList[13]);
	this.slot15 = pokemonOrNull(attList[14]);
	this.slot16 = pokemonOrNull(attList[15]);
	this.slot17 = pokemonOrNull(attList[16]);
	this.slot18 = pokemonOrNull(attList[17]);
	this.slot19 = pokemonOrNull(attList[18]);
	this.slot20 = pokemonOrNull(attList[19]);
	this.slot21 = pokemonOrNull(attList[20]);
	this.slot22 = pokemonOrNull(attList[21]);
	this.slot23 = pokemonOrNull(attList[22]);
	this.slot24 = pokemonOrNull(attList[23]);
	this.slot25 = pokemonOrNull(attList[24]);
	this.slot26 = pokemonOrNull(attList[25]);
	this.slot27 = pokemonOrNull(attList[26]);
	this.slot28 = pokemonOrNull(attList[27]);
	this.slot29 = pokemonOrNull(attList[28]);
	this.slot30 = pokemonOrNull(attList[29]);
};

function pokemonOrNull(attList) {
	if (attList.childNodes.length > 0)
		return new Pokemon(attList);
	return null;
}

function dataOrNull(attList) {
	if (attList.childNodes.length > 0)
		return attList.childNodes[0].data;
	return null;
}

function nicknameOrNull(attList) {
	if (attList[1].childNodes.length > 0)
		return attList[1].childNodes[0].data;
	return attList[3].childNodes[0].data;
}

function scrollBoxes(changeAmt) {
	currentBoxNo += parseInt(changeAmt);
	if (currentBoxNo < 1) { currentBoxNo = 20; }
	if (currentBoxNo > 20) { currentBoxNo = 1; }
	getBox(currentBoxNo - 1 + firstBoxId);
}

function getPokemonFromCell(row, column) {
	cellId = 6*row + column;
	switch (cellId) {
		case 0: return currentBox.slot1;
		case 1: return currentBox.slot2;
		case 2: return currentBox.slot3;
		case 3: return currentBox.slot4;
		case 4: return currentBox.slot5;
		case 5: return currentBox.slot6;
		case 6: return currentBox.slot7;
		case 7: return currentBox.slot8;
		case 8: return currentBox.slot9;
		case 9: return currentBox.slot10;
		case 10: return currentBox.slot11;
		case 11: return currentBox.slot12;
		case 12: return currentBox.slot13;
		case 13: return currentBox.slot14;
		case 14: return currentBox.slot15;
		case 15: return currentBox.slot16;
		case 16: return currentBox.slot17;
		case 17: return currentBox.slot18;
		case 18: return currentBox.slot19;
		case 19: return currentBox.slot20;
		case 20: return currentBox.slot21;
		case 21: return currentBox.slot22;
		case 22: return currentBox.slot23;
		case 23: return currentBox.slot24;
		case 24: return currentBox.slot25;
		case 25: return currentBox.slot26;
		case 26: return currentBox.slot27;
		case 27: return currentBox.slot28;
		case 28: return currentBox.slot29;
		case 29: return currentBox.slot30;
	}
}
