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

$wms_mode = $_GET['wms_mode'];

if ($wms_mode == "add") {
	//ajax for add ward
	if (isset($_GET['wms_name']) && isset($_GET['wms_rooms'])) {
		if (!empty($_GET['wms_name']) && !empty($_GET['wms_rooms'])) {
			$wms_name = $_GET['wms_name'];
			$wms_rooms = $_GET['wms_rooms'];
			$wms_owner = "admin";
			$sql = "INSERT INTO `wms_wards` SET id=?,
			name=?,
			rooms=?,
			owner=?";
			$bindArray = array("", $wms_name, $wms_rooms, $wms_owner);
			echo sqlInsert($sql, $bindArray);
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
	if (isset($_GET['wms_name']) && isset($_GET['wms_rooms']) && isset($_GET['wms_id'])) {
		if (!empty($_GET['wms_name']) && !empty($_GET['wms_rooms']) && !empty($_GET['wms_id'])) {
	//ajax for edit
	$wms_name = $_GET['wms_name'];
	$wms_rooms = $_GET['wms_rooms'];
	$wms_id = $_GET['wms_id'];
	$sql = "UPDATE wms_wards SET name=?, rooms=?". " WHERE id=?";
	$binds = array($wms_name, $wms_rooms, $wms_id);
		try {
		 sqlStatement($sql, $binds);
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




?>