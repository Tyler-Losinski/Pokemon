<?PHP
	// Start session
	session_start();
	if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
		header ("Location: login.php");
		exit();
	}
	
	// If the page receives a submit request
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Escape dangerous HTML characters
		$fromBoxId = htmlspecialchars($_POST['fromBoxId']);
		$fromSlotNo = htmlspecialchars($_POST['fromSlotNo']);
		$toBoxId = htmlspecialchars($_POST['toBoxId']);
		$toSlotNo = htmlspecialchars($_POST['toSlotNo']);
		
		// Connect to server
		$user_name = 'adhart';
		$pass_word = 'Aug111995';
		$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
		$conn = oci_connect($user_name, $pass_word, $conn_string);
		
		// If connection failed, print error
		if (!conn) {
			$e = oci_error();
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
		else {
			// Get selected Pokemon's ID
			$sql = "SELECT p.pId from Pokemon p JOIN BoxSlot s ON p.pId = s.pId WHERE s.bId = " . $fromBoxId . " AND s.slotNo = " . $fromSlotNo;
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				$pId1 = $row['PID'];
				
				// Get second selected Pokemon's ID
				$sql = "SELECT p.pId from Pokemon p JOIN BoxSlot s ON p.pId = s.pId WHERE s.bId = " . $toBoxId . " AND s.slotNo = " . $toSlotNo;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);
				
				$pId2 = "";
				if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
					$pId2 = $row['PID'];
				}
				else {
					$pId2 = 'NULL';
				}
				
				$sql = "UPDATE BoxSlot SET pId = " . $pId2 . " WHERE bId = " . $fromBoxId . " AND slotNo = " . $fromSlotNo;
				$stid = oci_parse($conn, $sql);
				$r = oci_execute($stid);
			
				// If update failed, print error
				if (!$r) {
					$e = oci_error($stid);
					print htmlentities($e['message']);
					print "\n<pre>\n";
					print htmlentities($e['sqltext']);
					printf("\n%".($e['offset']+1)."s", "^");
					print  "\n</pre>\n";
				}
				
				$sql = "UPDATE BoxSlot SET pId = " . $pId1 . " WHERE bId = " . $toBoxId . " AND slotNo = " . $toSlotNo;
				$stid = oci_parse($conn, $sql);
				$r = oci_execute($stid);
			
				// If update failed, print error
				if (!$r) {
					$e = oci_error($stid);
					print htmlentities($e['message']);
					print "\n<pre>\n";
					print htmlentities($e['sqltext']);
					printf("\n%".($e['offset']+1)."s", "^");
					print  "\n</pre>\n";
				}
				// If update succeeds, print success message
				else {
					echo 'Pokemon moved successfully.<BR>';
				}
				
			}
			// If failed, print error
			else {
				echo 'An error occurred: Could not move.<BR>';
			}
		}
		// Close the connection
		oci_close($conn);
	}
	
	// Populates the box list for the current user
	function popBoxes() {
		// Connect to the database
		$user_name = 'adhart';
		$pass_word = 'Aug111995';
		$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
		$conn = oci_connect($user_name, $pass_word, $conn_string);
		
		// If connection failed, print error
		if (!conn) {
			$e = oci_error();
			print htmlentities($e['message']);
			print "\n<pre>\n";
			print htmlentities($e['sqltext']);
			printf("\n%".($e['offset']+1)."s", "^");
			print  "\n</pre>\n";
		}
		else {
			// Select box ID and box number from boxes table for the current user
			$sql = "SELECT bid, boxNo FROM box WHERE owner = " . $_SESSION['uid'];
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			// Spit out default option
			echo '<option value="">---</option>';
			// For each returned row, spit out drop-down code.  Value = box ID, Text = box number
			while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				echo '<option value="' . $row['BID'] . '">' . $row['BOXNO'] . '</option>';
			}
		}
		// Close connection
		oci_close($conn);
	}
?>

<HTML>
	<HEAD>
		<TITLE>PC Storage System Withdraw</TITLE>
		<STYLE>
			.sample{
				width:32px;
				height:32px;
				overflow:hidden;
				position: relative;
				display: inline-block;
			}

			.sample img {
				position: absolute;
				top: -1472px;
				left: -160px;
			}
			
			.cell {
				display: table-cell;
				vertical-align: middle;
			}
			
			.radio {
				display: inline-block;
			}
		</STYLE>
		<SCRIPT>
			function getBoxes(boxId, toOrFrom) {
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						var xmlDoc;
						if (window.DOMParser) {
							parser=new DOMParser();
							xmlDoc=parser.parseFromString(xmlhttp.responseText,"text/xml");
						}
						else { // code for IE
							xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
							xmlDoc.async=false;
							xmlDoc.loadXML(xmlhttp.responseText);
						}
						
						var slots = xmlDoc.getElementsByTagName("SLOTNO");
						var dexNos = xmlDoc.getElementsByTagName("DEXNO");
						
						for (i=0; i<slots.length; i++) {
							var s = toOrFrom + "Slot" + (i + 1);
							var si = toOrFrom + "SlotIcon" + (i + 1);
							if (dexNos[i].childNodes.length > 0) {
								var dn = parseInt(dexNos[i].childNodes[0].nodeValue);
								
								if (dn > 386 && dn <= 413) dn += 3;
								else if (dn > 413 && dn <=479) dn += 5;
								else if (dn > 479 && dn <= 487) dn += 10;
								else if (dn > 487 && dn <= 492) dn += 11;
								else if (dn > 492 && dn <= 555) dn += 12;
								else if (dn > 555 && dn < 641) dn += 13;
								else if (dn == 642) dn += 14;
								else if (dn > 642 && dn <= 645) dn += 15;
								else if (dn == 646) dn += 16;
								else if (dn == 647) dn += 18;
								else if (dn == 648) dn += 19;
								else if (dn > 648) dn += 20;
								
								document.getElementById(si).style.top = ((dn/16>>0) * -32) + "px";
								document.getElementById(si).style.left = (((dn % 16) - 1) * -32) + "px";
								if (toOrFrom == "from")
									document.getElementById(s).innerHTML = '<INPUT TYPE="RADIO" VALUE="' + (i+1) + '" NAME="' + toOrFrom + 'SlotNo" ONCLICK="enable_submit(this)">';
							}
							if (toOrFrom == "to")
								document.getElementById(s).innerHTML = '<INPUT TYPE="RADIO" VALUE="' + (i+1) + '" NAME="' + toOrFrom + 'SlotNo" ONCLICK="enable_submit(this)">';
						}
					}
				};
				xmlhttp.open("GET", "/~adhart/getbox.php?boxId=" + boxId, true);
				xmlhttp.send();
			}
			
			var q = 0;

			function enable_submit(obj) {
				var sb = document.getElementById("Move");
				if (obj.checked === true){
					q++;
					if (q >= 2) {
						sb.disabled = false;
					}
				}
				else {
					q--;
					if (q < 2) {
						sb.disabled = true;
					}
				}
				document.getElementById("debug").innerHTML = q;
			}
		</SCRIPT>
	</HEAD>
	<BODY>
		<DIV>
			<FORM NAME="moveForm" ID="moveForm" METHOD="POST" ACTION="move.php">
				<P><INPUT TYPE="SUBMIT" ID="Move" NAME="Move" VALUE="Move" DISABLED="disabled">
				<P>Move from:
				<P>Box: <select id="fromBoxId" name="fromBoxId" form="moveForm" onchange = "getBoxes(this.value, 'from')"><?PHP popBoxes(); ?></select>
				<TABLE BORDER=1>
					<TR>
						<TD CLASS="cell">
							<DIV ID="fromSlot1" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon1" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot2" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon2" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot3" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon3" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot4" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon4" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot5" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="fromSlotIcon5" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot6" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon6" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="fromSlot7" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon7" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot8" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon8" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot9" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon9" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot10" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon10" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot11" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="fromSlotIcon11" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot12" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon12" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="fromSlot13" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon13" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot14" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon14" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot15" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon15" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot16" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon16" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot17" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="fromSlotIcon17" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot18" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon18" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="fromSlot19" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon19" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot20" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon20" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot21" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon21" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot22" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon22" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot23" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="fromSlotIcon23" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot24" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon24" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="fromSlot25" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon25" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot26" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon26" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot27" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon27" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot28" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon28" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot29" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="fromSlotIcon29" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="fromSlot30" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="fromSlotIcon30" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
				</TABLE>
				
				<DIV CLASS="initiallyHidden">
					<P>Move to:
					<P>Box: <select id="toBoxId" name="toBoxId" form="moveForm" onchange = "getBoxes(this.value, 'to')"><?PHP popBoxes(); ?></select>
					<TABLE BORDER=1>
						<TR>
							<TD CLASS="cell">
								<DIV ID="toSlot1" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon1" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot2" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon2" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot3" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon3" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot4" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon4" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot5" CLASS="radio"></DIV>
								<DIV CLASS="sample"><img ID="toSlotIcon5" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot6" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon6" SRC="image/pkMontage.png"></DIV>
							</TD>
						</TR>
						<TR>
							<TD CLASS="cell">
								<DIV ID="toSlot7" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon7" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot8" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon8" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot9" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon9" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot10" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon10" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot11" CLASS="radio"></DIV>
								<DIV CLASS="sample"><img ID="toSlotIcon11" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot12" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon12" SRC="image/pkMontage.png"></DIV>
							</TD>
						</TR>
						<TR>
							<TD CLASS="cell">
								<DIV ID="toSlot13" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon13" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot14" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon14" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot15" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon15" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot16" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon16" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot17" CLASS="radio"></DIV>
								<DIV CLASS="sample"><img ID="toSlotIcon17" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot18" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon18" SRC="image/pkMontage.png"></DIV>
							</TD>
						</TR>
						<TR>
							<TD CLASS="cell">
								<DIV ID="toSlot19" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon19" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot20" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon20" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot21" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon21" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot22" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon22" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot23" CLASS="radio"></DIV>
								<DIV CLASS="sample"><img ID="toSlotIcon23" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot24" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon24" SRC="image/pkMontage.png"></DIV>
							</TD>
						</TR>
						<TR>
							<TD CLASS="cell">
								<DIV ID="toSlot25" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon25" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot26" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon26" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot27" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon27" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot28" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon28" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot29" CLASS="radio"></DIV>
								<DIV CLASS="sample"><img ID="toSlotIcon29" SRC="image/pkMontage.png"></DIV>
							</TD>
							<TD CLASS="cell">
								<DIV ID="toSlot30" CLASS="radio"></DIV>
								<DIV CLASS="sample"><IMG ID="toSlotIcon30" SRC="image/pkMontage.png"></DIV>
							</TD>
						</TR>
					</TABLE>
				</DIV>
			</FORM>
		</DIV>
		<P><a href="/~adhart/pchome.php">Back to home</a>
	</BODY>
</HTML>