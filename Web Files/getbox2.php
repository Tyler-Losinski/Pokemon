<?PHP
	// Connect to database
    $user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	// Select all Slot IDs and Slot numbers from BoxSlot table that match the given Box ID
	$sql = "SELECT p.dexNo, s.slotNo FROM Box b JOIN BoxSlot s ON b.bId = s.bId LEFT JOIN Pokemon p ON s.pId = p.pId WHERE b.bId = " . $_GET["boxId"] . " ORDER BY sId";
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	
	for ($i = 1; $i <= 5; $i++) {
		echo '<TR>';
		for ($j = 1; $j <= 6; $j++) {
			echo '<TD CLASS="cell" ONCLICK="selectSlot(' . $i . ',' . $j . ',' . $_GET["boxId"] . ')" ID="cell_' . $i . ',' . $j . '">';
			$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
			if ($row['DEXNO']) {
				//echo '<INPUT TYPE="RADIO" VALUE="' . ($i * $j) . '" NAME="slotNo" ONCLICK="enable_submit(this,' . $_GET["boxId"] . ',' . $row['SLOTNO'] . ')">';
				echo '<DIV CLASS="iconDiv"><IMG CLASS="icon" SRC="image/pkmnicons/' . $row['DEXNO'] . '"></DIV>';
			}
			else {
				echo '<DIV CLASS="iconDiv"></DIV>';
			}
			echo '</TD>';
		}
		echo '</TR>';
	}
	
	oci_close($conn);


	
