<?PHP
	require("helper.php");
	sessionCheck();
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$uId = $_SESSION['uid'];
		$pId = $_POST['pId'];
		
		$conn = getConn();
		if (!conn) { printOciError(); }
		else {
			// Validate that the currently logged-in user has the specified Pokemon in his or her box
			$validateSql = "SELECT u.userId FROM PCUsers u 
							JOIN Box b ON b.owner = u.userId
							JOIN BoxSlot s ON b.bId = s.bId
							JOIN Pokemon p ON p.pId = s.pId
							WHERE p.pId = :pId";
			$validateStid = oci_parse($conn, $validateSql);
			oci_bind_by_name($validateStid, ":pId", $pId);
			oci_execute($validateStid);
	
			if ($row = oci_fetch_array($validateStid, OCI_ASSOC+OCI_RETURN_NULLS)) {
				if ($row['USERID'] != $uId) {
					echo "You do not have permission to modify this object";
				}
				else {
					$sql = "SELECT p.pId AS PokemonID,
								   p.nickname AS Nickname,
								   p.dexNo AS DexNumber,
								   ps.name AS SpeciesName,
								   t1.typeName AS Type1,
								   ps.type1 AS Type1ID,
								   t2.typeName AS Type2,
								   ps.type2 AS Type2ID,
								   p.gender AS Gender,
								   p.lvl AS Lvl,
								   u.username AS OriginalTrainer,
								   u.userId AS OriginalTrainerID,
								   a1.name AS Attack1,
								   p.attack1 AS Attack1ID,
								   a2.name AS Attack2,
								   p.attack2 AS Attack2ID,
								   a3.name AS Attack3,
								   p.attack3 AS Attack3ID,
								   a4.name AS Attack4,
								   p.attack4 AS Attack4ID,
								   b.bId AS BoxID,
								   b.boxNo AS BoxNumber,
								   s.sId AS SlotID,
								   s.slotNo AS SlotNumber
							FROM Pokemon p
							JOIN PCUsers u ON p.ot = u.userId
							JOIN BoxSlot s ON p.pId = s.pId
							JOIN Box b ON b.bId = s.bId
							JOIN PkmnSpecies ps ON p.dexNo = ps.dexNo
							JOIN PkmnType t1 ON ps.type1 = t1.typeId

							FULL JOIN PkmnType t2 ON ps.type2 = t2.typeId

							JOIN Attack a1 ON p.attack1 = a1.aId

							FULL JOIN Attack a2 ON p.attack2 = a2.aId
							FULL JOIN Attack a3 ON p.attack3 = a3.aId

							FULL JOIN Attack a4 ON p.attack4 = a4.aId
							WHERE p.pId = :pId";
					$stid = oci_parse($conn, $sql);
					oci_bind_by_name($stid, ":pId", $pId);
					oci_execute($stid);
			
					if ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
						$xml = new SimpleXMLElement("<Pokemon></Pokemon>");
						array_to_xml($row,$xml);
						echo $xml->asXML();
					}
					else {
						printQueryError($stid);
					}
				}
			}
			else {
				printQueryError($validateStid);
			}
		}
		oci_close($conn);
	}
