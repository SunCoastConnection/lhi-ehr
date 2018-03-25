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

			
			$sql = "INSERT INTO `wms_wards` SET wid=?,
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
			$sql = "UPDATE wms_wards SET name=?, rooms=?, rooms_capacity=?, prefix=? WHERE wid=?";
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
		$statement = "DELETE FROM wms_wards WHERE wid=$wms_id";
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
		$sqlStatement = "SELECT * FROM wms_wards WHERE wid=$wms_ward_id";
		$r = sqlStatement($sqlStatement);
		$row = sqlFetchArray($r);
		$rooms = $row['rooms'];
		$rooms_capacity = $row['rooms_capacity'];
		$prefix = $row['prefix'];
		$i = 1; //iterator
		$J = 0; // variable used to leave space for each 3 wards created [UI part].
		//Now we query for wms_rooms table, which contain data about patients admitted in the ward, specifically on which room and pid of patient.
		while ($i <= $rooms) {
			$rid = $i;
			$sql = "SELECT * FROM wms_rooms WHERE rid=$rid AND wid=$wms_ward_id";
			$r_patient = sqlQ($sql);
			$number_of_patients = sqlNumRows($r_patient);
			echo $room_template = "<div class='room col-xs-3 text-center col-xs-offset-1' id='$wms_ward_id-$i'>
			<input type='hidden' id='' value='$rooms_capacity'>
			<br/><div class='text-right'><span class='badge'>$number_of_patients</span></div>
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



if ($wms_mode == "add_patient") {
	//this function is triggered whether a new patient is registered via wms, or a existing patient is added to a room, we move them to wms_rooms
	if (isset($_GET['pid']) && isset($_GET['wid'])) {
		//pid = patient id
		//wid = ward id
		//rid = room id
		$pid = $_GET['pid'];
		$wid = $_GET['wid'];
		$arr = explode("-", $wid);
		$wid = $arr[0];
		$rid = $arr[1];
		$sql = "SELECT * FROM wms_rooms WHERE wid = $wid AND pid = $pid";
		$query = sqlQ($sql);
		$patient_in_the_ward_before = sqlNumRows($query);
		if ($patient_in_the_ward_before == 0) {
			$sql = "INSERT INTO `wms_rooms` SET wid=?, pid=?, rid=?";
			$bindArray = array($wid, $pid, $rid);
			try {
			   sqlInsert($sql, $bindArray);
			   //1 is code for success
			   echo "1";
			}
			catch (Exception $e) {
				//code: failure
				echo "2";
			}
		}
		else {
			//code 3 is a failure caused if the patient is already in one room and added again to other room
			echo "3";
		}   

	}
	else {
		//code 2 is a failure, will trigger a error message in ui
		echo "2";
	}
}


if ($wms_mode == "view_patient") {
//used to view patients present in particular room
//rid - room id
//wid - ward id
if (isset($_GET['wid']) && isset($_GET['rid'])) {
	if (!empty($_GET['wid']) && !empty($_GET['rid'])) {
		$wid = $_GET['wid'];
		$rid = $_GET['rid'];
		$arr = explode("-", $rid);
		$rid = $arr[1];
		$sql = "SELECT * FROM wms_rooms WHERE wid = $wid AND rid = $rid";
		$query = sqlQ($sql);
		$patients_in_room = sqlNumRows($query);
		if ($patients_in_room == 0) {
			echo "No patients are in this room";
		}
		else {
			echo "<table class='table table-striped'>";
			echo "<th>Fname</th>";
			echo "<th>Lname</th>";
			echo "<th>DOB</th>";
			echo "<th>Sex</th>";
			echo "<th>Delete</th>";
			while ($r = sqlFetchArray($query)) {
				$pid = $r['pid'];
				$sql = "SELECT * FROM patient_data WHERE pid=$pid";
				$qry = sqlQ($sql);
				$patient_row = sqlFetchArray($qry);
				echo "<tr>";
				echo "<td><b>".$patient_row['fname']."</b></td>";
				echo "<td><b>".$patient_row['lname']."</b></td>";
				echo "<td><b>".$patient_row['DOB']."</b></td>";
				echo "<td><b>".$patient_row['sex']."</b></td>";
				echo "<td><b class='deletable' id='".$patient_row['pid']."'><i class='fa fa-trash-o'></i></b></td>";
				echo "</tr>";
			}
			echo "</table>";
		}
	}
	else {
		echo "2";
	}
}
else {
	echo "2";
}

}


if ($wms_mode == "delete_patient") {
	if (isset($_GET['wid']) && isset($_GET['pid'])) {
		if (!empty($_GET['wid']) && !empty($_GET['pid'])){
			$pid = $_GET['pid'];
			$wid = $_GET['wid'];
			$sql = "DELETE FROM wms_rooms WHERE wid=$wid AND pid=$pid";
			try {
				sqlQ($sql);
				echo "1";
			}
			catch (Exception $e) {
				echo "2";
			}
		}
		else {
			echo "2";
		}
	}
	else {
		echo "2";
	}
}


if ($wms_mode == "search_patient") {
	if (isset($_GET['search_patient_name'])) {
		if (!empty($_GET['search_patient_name'])) {
			// we search for fname, lname, mname for now
			$search_patient_name = $_GET['search_patient_name'];
			$sql = "SELECT * FROM patient_data WHERE fname LIKE '%$search_patient_name' OR lname LIKE '%$search_patient_name' OR mname LIKE '%$search_patient_name'";
			$query = sqlQ($sql);
			echo "<table class='table table-striped table-hover'>";
			echo "
				<th>fname</th>
				<th>mname</th>
				<th>lname</th>
				<th>DOB</th>
				<th>Add</th>
				";
			while ($row = sqlFetchArray($query)) {
				echo "<tr>";
				echo "<td>".$row['fname']."</td>";
				echo "<td>".$row['mname']."</td>";
				echo "<td>".$row['lname']."</td>";
				echo "<td>".$row['DOB']."</td>";
				echo "<td><i class='fa fa-plus-circle add_patients_plus_icon' id='". $row['pid']."'></td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		else {
			echo "No match found";
		}
	}
	else{ 
	echo "2";
	}
}

if ($wms_mode == "check_patient_search_box") {
//get the patient id and query the wms_rooms table
if (isset($_GET['pid'])) {
	if (!empty($_GET['pid'])) {
		$pid = $_GET['pid'];
		$sql = "SELECT * FROM wms_rooms WHERE pid = $pid";
		$query = sqlQ($sql);
		$occurences = sqlNumRows($query);
		if ($occurences == 0) {
			echo "1";
		}
		else {
			echo "2";
		}
	}
	else {
		echo "2";
	}
}	
else {
	echo "2";
}
}
?>