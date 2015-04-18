<?PHP
	// Start session
	session_start();
	if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
		header ("Location: wat.php");
		exit();
	}
	
	// If the page receives a submit request
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Escape dangerous HTML characters
		$slotNo = htmlspecialchars($_POST['slotNo']);
		$boxId = htmlspecialchars($_POST['boxNo']);
		
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
			// Get the highest ID currently stored in the table
			$sql = "SELECT MAX(pId) AS NEXT FROM Pokemon";
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				// Get selected Pokemon's ID
				$sql = "SELECT p.pId from Pokemon p JOIN BoxSlot s ON p.pId = s.pId WHERE s.bId = " . $boxId . " AND s.slotNo = " . $slotNo;
				$stid = oci_parse($conn, $sql);
				oci_execute($stid);
				
				if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
					$pId = $row['PID'];
					
					$sql = "UPDATE BoxSlot SET pId = NULL WHERE bId = " . $boxId . " AND slotNo = " . $slotNo;
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
						echo 'Removal from box successful.<BR>';
					}
					
					$sql = "DELETE FROM Pokemon WHERE pId = " . $pId;
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
						echo 'Pokemon withdrawn.<BR>';
					}
					
				}
				// If failed, print error
				else {
					echo 'An error occurred: Could not withdraw.<BR>';
				}
			}
			// If could not get highest ID, print error
			else {
				echo "Error finding Pokemon to withdraw";
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
			function getBoxes(boxId) {
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
							if (dexNos[i].childNodes.length > 0) {
								var dn = parseInt(dexNos[i].childNodes[0].nodeValue);
								var s = "slot" + (i + 1);
								var si = "slotIcon" + (i + 1);
								
								if (dn > 386 && dn <= 413) dn += 3;
								else if (dn > 413 && dn <=479) dn += 6;
								else if (dn > 479 && dn <= 487) dn += 11;
								else if (dn > 487 && dn <= 641) dn += 12;
								else if (dn == 642) dn += 13;
								else if (dn > 642 && dn <= 645) dn += 14;
								else if (dn == 646) dn += 15;
								else if (dn == 647) dn += 17;
								else if (dn == 648) dn += 18;
								else if (dn > 648) dn += 19;
								
								document.getElementById(s).innerHTML = '<INPUT TYPE="RADIO" VALUE="' + (i+1) + '" NAME="slotNo" ONCLICK="enable_submit(this)">';
								document.getElementById(si).style.top = ((dn/16>>0) * -32) + "px";
								document.getElementById(si).style.left = (((dn % 16) - 1) * -32) + "px";
							}
						}
					}
				};
				xmlhttp.open("GET", "/~adhart/getbox.php?boxId=" + boxId, true);
				xmlhttp.send();
			}
			
			var i = 0;

			function enable_submit(obj) {
				var sb = document.getElementById("Withdraw");
				if (obj.checked === true){
					sb.disabled = false; i++;
				}
				else {
					i--;
					if (i == 0) {
						sb.disabled = true;
					}
				}
			}
		</SCRIPT>
	</HEAD>
	<BODY>
		<P>Box: <select id="boxNo" name="boxNo" form="withdrawForm" onchange = "getBoxes(this.value)"><?PHP popBoxes(); ?></select>
		<DIV>
			<FORM NAME="withdrawForm" ID="withdrawForm" METHOD="POST" ACTION="withdraw.php">
			<P><INPUT TYPE="SUBMIT" ID="Withdraw" NAME="Withdraw" VALUE="Withdraw" DISABLED="disabled">
				<TABLE BORDER=1>
					<TR>
						<TD CLASS="cell">
							<DIV ID="slot1" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon1" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot2" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon2" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot3" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon3" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot4" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon4" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot5" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="slotIcon5" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot6" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon6" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="slot7" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon7" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot8" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon8" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot9" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon9" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot10" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon10" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot11" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="slotIcon11" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot12" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon12" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="slot13" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon13" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot14" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon14" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot15" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon15" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot16" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon16" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot17" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="slotIcon17" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot18" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon18" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="slot19" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon19" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot20" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon20" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot21" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon21" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot22" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon22" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot23" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="slotIcon23" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot24" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon24" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
					<TR>
						<TD CLASS="cell">
							<DIV ID="slot25" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon25" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot26" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon26" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot27" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon27" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot28" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon28" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot29" CLASS="radio"></DIV>
							<DIV CLASS="sample"><img ID="slotIcon29" SRC="image/pkMontage.png"></DIV>
						</TD>
						<TD CLASS="cell">
							<DIV ID="slot30" CLASS="radio"></DIV>
							<DIV CLASS="sample"><IMG ID="slotIcon30" SRC="image/pkMontage.png"></DIV>
						</TD>
					</TR>
				</TABLE>
			</FORM>
		</DIV>
		<P><a href="/~adhart/pchome.php">Back to home</a>
	</BODY>
</HTML>