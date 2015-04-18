<?PHP
	// Connect to database
    $user_name = 'adhart';
	$pass_word = 'Aug111995';
	$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
	$conn = oci_connect($user_name, $pass_word, $conn_string);
	
	// Select all Slot IDs and Slot numbers from BoxSlot table that match the given Box ID
	$sql = "SELECT * FROM Box b JOIN BoxSlot s ON b.bId = s.bId LEFT JOIN Pokemon p ON s.pId = p.pId WHERE b.bId = " . $_GET["boxId"] . " ORDER BY sId";
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	
	$arr = array();
	// For each returned row, spit out drop-down code.  Value = Slot ID, Text = Slot number
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		$arr[] = $row;
		
		// creating object of SimpleXMLElement
		
		
		// function call to convert array to xml
		

		
		//saving generated xml file
		//$xml_student_info->asXML('file path and name');
	}
	//$xml = new SimpleXMLElement("<?xml version=\"1.0\"><box></box>");
	$xml = new SimpleXMLElement("<BOX></BOX>");
	array_to_xml($arr,$xml);
	echo $xml->asXML();
	// Close connection
	oci_close($conn);


	// function defination to convert array to xml
	function array_to_xml($student_info, &$xml_student_info) {
		foreach($student_info as $key => $value) {
			if(is_array($value)) {
				if(!is_numeric($key)){
					$subnode = $xml_student_info->addChild("$key");
					array_to_xml($value, $subnode);
				}
				else{
					$subnode = $xml_student_info->addChild("item$key");
					array_to_xml($value, $subnode);
				}
			}
			else {
				$xml_student_info->addChild("$key",htmlspecialchars("$value"));
			}
		}
	}
