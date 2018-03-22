<?php 
/** 
 * Ajax Handler for wms
 * 
 * Copyright (C) 2018 Naveen Muthusamy
 * 
 * LICENSE: This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0
 * See the Mozilla Public License for more details. 
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * 
 * @package Librehealth EHR 
 * @author Naveen Muthusamy(kmnaveen101@gmail.com)
 * @link http://librehealth.io
 *  
 * Please help the overall project by sending changes you make to the author and to the LibreEHR community.
 * 
 */ 
include_once("../../interface/globals.php");
include_once("{$GLOBALS['srcdir']}/sql.inc");

/**
*	AJAX MODULE FOR THE WARDS SCREEN
*/



$wms_mode = $_GET['wms_mode'];

if ($wms_mode == "add") {
	//ajax for add ward
	if (isset($_GET['wms_name']) && isset($_GET['wms_rooms'])) {
		if (!empty($_GET['wms_name']) && !empty($_GET['wms_rooms'])) {
			$wms_name = $_GET['wms_name'];
			$wms_rooms = $_GET['wms_rooms'];
			$wms_rooms_capacity = $_GET['wms_rooms_capacity'];
			$wms_prefix = $_GET['wms_prefix'];
			$wms_owner = "admin";
			$sql = "SELECT * FROM wms_wards WHERE name='$wms_name'";
			$query = sqlQ($sql);
			$row_count = sqlNumRows($query);
			if ($row_count == 0) {

			
			$sql = "INSERT INTO `wms_wards` SET id=?,
			name=?,
			rooms=?,
			rooms_capacity = ?,
			prefix = ?,
			owner=?";
			$random = str_shuffle("098765432198765432109876543210987654321");
			$bindArray = array($random, $wms_name, $wms_rooms, $wms_rooms_capacity, $wms_prefix, $wms_owner);
			echo sqlInsert($sql, $bindArray);
			}
			else {
				//error code to show the ward with name already exists.
				echo "2";
			}
	    }
	    else {
	    	echo "0";
	    }
	}
	else {
		echo "0";
	}
}

if ($wms_mode == "edit") {
	if (isset($_GET['wms_name']) && isset($_GET['wms_rooms']) && isset($_GET['wms_id']) && isset($_GET['wms_rooms_capacity']) && isset($_GET['wms_prefix'])) {
		if (!empty($_GET['wms_name']) && !empty($_GET['wms_rooms']) && !empty($_GET['wms_id']) && !empty($_GET['wms_prefix']) && !empty($_GET['wms_rooms_capacity'])) {
			$wms_name = $_GET['wms_name'];
			$wms_rooms = $_GET['wms_rooms'];
			$wms_id = $_GET['wms_id'];
			$wms_prefix = $_GET['wms_prefix'];
			$wms_rooms_capacity = $_GET['wms_rooms_capacity'];
			$sql = "SELECT * FROM wms_wards WHERE name='$wms_name'";
			$query = sqlQ($sql);
			$array = sqlFetchArray($query);
			$wms_check_id = $array['id'];
			$row_count = sqlNumRows($query);
		if (($row_count == 1 && $wms_check_id==$wms_id) OR $row_count == 0){	
			$sql = "UPDATE wms_wards SET name=?, rooms=?, rooms_capacity=?, prefix=? WHERE id=?";
			$binds = array($wms_name, $wms_rooms, $wms_rooms_capacity, $wms_prefix, $wms_id);
				try {
				 sqlStatement($sql, $binds);
				 echo "1";
				}
				catch (Exception $e) {
				 echo "0";

				}
			}
			else {
				echo "2";
			}

    }
    else {
    echo "0";
    }
}
else {
    echo "0";
}

}

if ($wms_mode == "delete") {
	// ajax for delete
	if (isset($_GET['wms_id']) && !empty($_GET['wms_id'])){
		$wms_id = $_GET['wms_id'];
		$statement = "DELETE FROM wms_wards WHERE id=$wms_id";
		try {
		 sqlStatement($statement);
		 echo "1";
		}
		catch (Exception $e) {
		 echo "0";

		}

	}
	else {
    echo "0";
    }
}
/**
*	AJAX MODULE FOR THE ROOMS SCREEN
*/

if ($wms_mode == "get_rooms"){
	//Get Rooms in the particular ward
	if (isset($_GET['wms_ward_id'])) {
		$wms_ward_id = $_GET['wms_ward_id'];
		$sqlStatement = "SELECT * FROM wms_wards WHERE id=$wms_ward_id";
		$r = sqlStatement($sqlStatement);
		$row = sqlFetchArray($r);
		$rooms = $row['rooms'];
		$rooms_capacity = $row['rooms_capacity'];
		$prefix = $row['prefix'];
		$i = 1;
		$J = 0;
		while ($i <= $rooms) {
			echo $room_template = "<div class='room col-xs-3 text-center col-xs-offset-1' id='$wms_ward_id-$i'>
			<input type='hidden' id='' value='$rooms_capacity'>
			<br/><div class='text-right'><span class='badge'>2/$rooms_capacity</span></div>
					<br/>
					<b>$prefix $i</b>
					</div>";
	        $i = $i + 1;
	        $j = $j + 1;
	        if ($j % 3 == 0 && $j != 0) {
				echo "<div class='col-xs-12'><br/><br/></div>";
 			}		
		}
	}
}

?>