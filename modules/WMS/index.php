<?php
/**
 *  index.php main program for the ward management system
 *
 * Copyright (C) 2018 Naveen Muthusamy <kmnaveen101@gmail.com>
 *
 * LICENSE: This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 * See the Mozilla Public License for more details.
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package LibreHealth EHR
 * @author Naveen Muthusamy <kmnaveen101@gmail.com >
 * @link http://librehealth.io
 *
 * Please help the overall project by sending changes you make to the author and to the LibreHealth EHR community.
 *
 */
 ini_set("display_errors", "1");
  error_reporting(E_ALL);
require_once('../../interface/globals.php');
require_once('../../library/headers.inc.php');
require_once("../../library/sql.inc");

$library_array = array("bootstrap", "font-awesome", "jquery-ui", "jquery-min-3-1-1");
call_required_libraries($library_array);
?>

<!--UI CODE-->
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>Ward Management System</title>
<div class="body_top col-xs-12 text-center">
<h4><i class="fa fa-user"></i> Ward Management System</h4>
<br/><br/>
</div>

<div class="toolbar col-xs-12">
	<a class="btn btn-primary" id="add"><i class="fa fa-plus"></i> Add</a>
	&nbsp;&nbsp;
	<a class="btn btn-danger" id="delete"><i class="fa fa-trash"></i> Delete</a>
	&nbsp;&nbsp;
	<a class="btn btn-warning" id="edit"><i class="fa fa-pencil"></i> Edit</a>
</div>

<div class="col-xs-12">
<br/><br/>
<h4><i class="fa fa-list-ul"></i> Available Wards</h4>
<br/><br/>
</div>

<div class="col-xs-12" id="layout">

<?php 

$sql = "SELECT * FROM `wms_wards`";

$row = sqlStatement($sql);

$iterator = 0;

while ($r = sqlFetchArray($row)) {

$ward_name = $r['name'];

$ward_rooms = $r['rooms'];

//row id

$row_id = $r['id'];

//div id used for selection purpose
$id = "w".$r['id'];

$uid = $id."_uid";

//room id value, which is to be used in jquery
$id_room = "w".$r['id']."_room";

echo $ward_template = "<div class='col-xs-3 text-center col-xs-offset-1 ward' id='$id'>
					<input type='hidden' id='$uid' value='$row_id'>
					<input type='hidden' id='$id_room' value='$ward_rooms'>
					<br/><br/><br/>
					<b>$ward_name</b>
					</div>";
if ($iterator % 3 == 0 && $iterator != 0 or ($iterator == 3)) {
	echo "<div class='col-xs-12'><br/><br/></div>";

}					

$iterator = $iterator + 1;

}


?>
                  
<div id="dialog" title="Create a ward">
  <p>Ward Name</p>
  <input type="text" id="w_name">
  <br/><br/>
  <p>Number of rooms</p>
  <input type="number" id="w_rooms">
  <br/><br/>
  <div class="text-center">
  <br/>
  <input type="submit"  value="create ward" style="background-color: #234342;" id="c_ward">
  </div>
</div>

<div id="edit_wms" title="Edit ward">
  <p>Ward Name</p>
  <input type="text" id="wms_edit_name">
  <br/><br/>
  <p>Number of rooms</p>
  <input type="number" id="wms_edit_rooms">
  <br/><br/>
  <input type="hidden" id="wms_uid">
  <div class="text-center">
  <br/>
  <input type="submit"  value="update" style="background-color: #234342;" id="wms_edit_update">
  </div>
</div>

<div class='success_create' style='display:none'>Successfully ward created</div>
<div class='error_create' style='display:none'>Ward creation Failed</div>

</div>

<div style="display: none;" id="parsehtml">


</div>

<script type="text/javascript">
	//wards to be deleted
	var deletewards = [];
	var deletedwards_uid = [];
    //wards selected - this has to single selection for certain operation
	var selectedwards = [];
	var selectedward_name;
	var selectedward_room;
	var selectedward_room_id;
	var selectedward_name_id;

$(document).ready(function () {
	$('.ward').click(function () {
		var id = $(this).attr('id');
			selectedwards = [];
			selectedwards.push(id);
			selectedward_name = $(this).find('b').text();
			selectedward_room = "#" + id + "_room";
			//used to update the values of room of particular ward in same screen
			selectedward_room_id = "#" + id + "_room";
			selectedward_name_id = "#" + id;
			selectedward_room = $(selectedward_room).val();
			selectedward_uid = "#" + id + "_uid";
			selectedward_uid = $(selectedward_uid).val();

		dw_uid = "#" + id + "_uid";
		dw_uid = $(dw_uid).val();
		//delete wards part
		if ($.inArray(id,deletewards) != -1) {
			//it means the item is clicked again
			var index = deletewards.indexOf(id);
			var uid_index = deletedwards_uid.indexOf(dw_uid);

			if (index !== -1) {
				deletewards.splice(index, 1);

			}
			if (uid_index !== -1) {
				deletedwards_uid.splice(uid_index,1);
			}
			$(this).css('background-color', '#b9cd6d');
		}
		else {
			//it means the item is selected new
			deletewards.push(id);
			deletedwards_uid.push(dw_uid);
			$(this).css('background-color', 'yellow');
	    }

	});

});



//add dialog for ward
$(function() {
  $("#dialog").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400 }
  });
  $("#add").click(function() {
    $("#dialog").dialog("open");
    return false;
  });
});

$('#c_ward').click(function () {
  var ward_name = $('#w_name').val();
  var ward_rooms = $('#w_rooms').val();
  var url = "../../library/ajax/wms_ajax.php?wms_mode=add&wms_name=" + ward_name  + "&wms_rooms=" + ward_rooms;
  $.get(url, function(data, status){
  if(data > 0){
   $('.success_create').fadeIn(400).delay(3000).fadeOut(400);
   location.reload(true);
 
  }
  else {
  	//error in ward creation
  	$('.error_create').fadeIn(400).delay(3000).fadeOut(400);

  }
  });
  $('#dialog').dialog( "close" );
});


//update the ward details by clicking update button
$('#wms_edit_update').click(function () {
  var ward_name = $('#wms_edit_name').val();
  var ward_rooms = $('#wms_edit_rooms').val();
  var wms_uid = $('#wms_uid').val();
  $('#edit_wms').dialog( "close" );
   var url = "../../library/ajax/wms_ajax.php?wms_mode=edit&wms_name=" + ward_name  + "&wms_rooms=" + ward_rooms + "&wms_id=" + wms_uid;
    $.get(url, function(data, status){
  if(data == 1){
  	//success ward creation
  	$(selectedward_name_id).find('b').text(ward_name);
  	$(selectedward_room_id).val(ward_rooms);
  	$('.success_create').text("ward successfully updated");
   $('.success_create').fadeIn(400).delay(3000).fadeOut(400);
  }
  else {
  	//error in ward creation
  	$('.error_create').text("error in updating ward details");
  	$('.error_create').fadeIn(400).delay(3000).fadeOut(400);

  }
  });
});



//edit dialog for ward
$(function() {
  $("#edit_wms").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 40}
  });
  $("#edit").click(function() {
  	if (selectedwards.length > 0) {
  	$('.ward').css("background-color", "#b9cd6d");
  	deletewards = [];
    $("#edit_wms").dialog("open");
    $('#wms_edit_name').val(selectedward_name);
    $('#wms_edit_rooms').val(selectedward_room);
    $('#wms_uid').val(selectedward_uid);
    return false;
    }
    else {
    	alert("please choose a ward to edit");
    }
  });
});


//delete wards
$('#delete').click(function () {
//used to delete wards.
if (deletewards.length > 0 && deletedwards_uid.length > 0) {
var valid_delete = 0;
var invalid_delete = 0;
 for (i=0; i<deletedwards_uid.length; i++) {
 	var wms_uid = deletedwards_uid[i];
 	var hide = '#' + deletewards[i];
 	var url = "../../library/ajax/wms_ajax.php?wms_mode=delete&wms_id=" + wms_uid;
 	console.log(url);
	$.get(url, function(data, status){
        if (data == 1){
        	valid_delete = valid_delete + 1;
        	$(hide).hide();
        }
        else {
        	invalid_delete = invalid_delete + 1;
        }

	});
}
deletewards = [];
deletedwards_uid = [];
}
else {
	alert("please choose a ward to delete");
}
});


</script>