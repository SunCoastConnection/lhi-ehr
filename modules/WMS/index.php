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
 * @author Naveen Muthusamy <kmnaveen101@gmail.com>
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
<!--ROOMS SECTION-->
<div id="rooms_screen" style="display: none;">
<div class="container col-xs-12">              
  <ol class="breadcrumb">
    <li><a href="#" id="refresh">Wards</a></li>
    <li class="active" id="ward_name_breadcrumb"></li>        
  </ol>
</div>
<div class="body_top col-xs-12">
<br/>
<h4><i class="fa fa-list-ul"></i>&nbsp;&nbsp;Available Rooms</h4>


</div>

<div class="col-xs-12"><br/></div>

<div class="col-xs-12">
<br/><br/>
</div>
<div class="row" id="rooms_place">
</div>

</div>

<!--WARDS SECTION-->
<div id="wards_screen">
<div class="toolbar col-xs-12">
	<a class="btn btn-primary" id="add"><i class="fa fa-plus"></i> Add</a>
	&nbsp;&nbsp;
	<a class="btn btn-danger" id="delete"><i class="fa fa-trash"></i> Delete</a>
	&nbsp;&nbsp;
	<a class="btn btn-warning" id="edit"><i class="fa fa-pencil"></i> Edit</a>
</div>

<div class="col-xs-12">
<br/><br/>
<h4><i class="fa fa-list-ul"></i>&nbsp;&nbsp;Available Wards</h4>
<br/><br/>
</div>

<div class="row" id="layout">

<?php 

$sql = "SELECT * FROM `wms_wards`";

$row = sqlStatement($sql);

$iterator = 0;

while ($r = sqlFetchArray($row)) {

$ward_name = $r['name'];

$ward_rooms = $r['rooms'];

//row id

$row_id = $r['wid'];

//div id used for selection purpose
$id = "w".$r['wid'];

$uid = $id."_uid";

//room id value, which is to be used in jquery
$id_room = "w".$r['wid']."_room";

//prefix of rooms
$prefix = $r['prefix'];

//id for prefix
$id_prefix = $id."_prefix";

//capacity of each room
$capacity = $r['rooms_capacity'];

//id for capacity
$id_capacity = $id."_capacity";

echo $ward_template = "<div class='col-xs-6 text-center col-xs-offset-1 ward' id='$id'>
					<input type='hidden' id='$uid' value='$row_id'>
					<input type='hidden' id='$id_room' value='$ward_rooms'>
          <input type='hidden' id='$id_prefix' value='$prefix'>
          <input type='hidden' id='$id_capacity' value='$capacity'>
					<br/><br/><br/>
					<b>$ward_name</b>
					</div>";
if ($iterator % 2 == 0 && $iterator != 0) {
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
  <p>Capactiy of each room</p>
  <input type="number" id="w_rooms_capacity" title="if each room has different capactiy then you can alter it later">
  <br/><br/>
  <p>Prefix for rooms</p>
  <input type="text" id="w_prefix" title="eg: R1, R2 where R is prefix">
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
  <p>Prefix for rooms</p>
  <input type="text" id="wms_edit_prefix">
  <br/><br/>
  <p>Rooms Capacity</p>
  <input type="number" id="wms_edit_rooms_capacity">
  <br/><br/>
  <div class="text-center">
  <br/>
  <input type="submit"  value="add patient" style="background-color: #234342;" id="wms_patient_add">
  </div>
</div>

<div id="add_patients">
<div id = "tabs-1">
         <ul>
            <li><a href = "#tabs-2">Add New Patient</a></li>
            <li><a href = "#tabs-3">Add Existing Patient</a></li>
            <li><a href="#tabs-4">Patients in this room</a></li>
         </ul>
<div id = "tabs-2">

  <p>Name of the Patient </p>
  <table>
  <tr>
  <td><input type="text" name="fname" id="wms_patient_fname" style="width: 60px;"></td>
  <td><input type="text" name="mname" id="wms_patient_mname" style="width: 60px;"></td>
  <td><input type="text" name="lname" id="wms_patient_lname" style="width: 60px;"></td>
  <input type="hidden" name="form_create" value="true" id="form_create">
  <input type="hidden" name="from_wms" value="true" id="from_wms">
  </tr>
  </table>
  <br/>
  <p>Sex</p>
  <select name="sex" id="wms_patient_sex">
    <option value="">Unassigned</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
  </select>
  <br/><br/>
  <p>DOB</p>
  <input type="date" id="wms_patient_dob" min="1900-01-01" max=<?php echo date("Y-m-d");?> name="DOB">
  <br/><br/>
  <input type="button" value="Add patient" style="background-color: #234342; color: white;" id="wms_patient_add_submit">
  <input type="hidden" id="wms_patient_add_ward_id">
</div>
<div id = "tabs-3">
<b>search patient</b>
<h6>
<input type="text" id="wms_search_patient" placeholder="patient name">
<br/><br/>
<div id="search_patient_results"></div>
</h6>
</div>
</div>
<div id="tabs-4">
<div id="patient_view"></div>
</div>


<div class='success_create' style='display:none'>Successfully ward created</div>
<div class='error_create' style='display:none'>Ward creation Failed</div>

</div>

<div id="search_box_add_patients" title="confirm add this patient?">
Do you really want to add this patient?
</div>

<div id="search_box_add_failed" title="Cant add this patient.">
This patient already exist in ward.
</div>
<div id="search_box_add_passed" title="successfully added the patient">
The patient has been successfully added.
</div>
<div id="patient_view_delete_patient" title="Confirm delete this patient?">
Do you really want to remove this patient from this room?
</div>
</div>
<div id="sc" style="display: none;"></div>
<div id="fc" style="display: none;"></div>
<script type="text/javascript">

  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  //                      WARDS GLOBALS                        // 
  var deletewards = [];                                        //
  var deletedwards_uid = [];                                   //
  var selectedwards = [];                                      //
  var selectedward_name;                                       //
  var selectedward_room;                                       //
  var selectedward_room_id;                                    //
  var selectedward_name_id;                                    //
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////



  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  //                      ROOM GLOBALS                         //       
  var deletedrooms = [];
  var selectedroom;                                            //                                                             //
  var selectedroom_id;                                         //
  var selectedroom_name; 
  var ward_id_with_room_id;                                      //
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
//check whether a patient exists
function checkpatientAlreadyExists() {
  var bool = "";
  var pid = $('#search_box_add_patients').data('pid');
  var url = "../../library/ajax/wms_ajax.php?wms_mode=check_patient_search_box&pid=" + pid;

    $.ajax({url: url, async:false, success: function(result){
        bool = result;
    }});
  return bool;
} 

//delete a patient from room
function deletePatient(pid,wid) {
  var bool = "";
  var url = "../../library/ajax/wms_ajax.php?wms_mode=delete_patient&pid=" + pid + "&wid=" + wid;
    $.ajax({url: url, async:false, success: function(result){
        bool = result;
    }});
  return bool;
} 

function insertPatient(pid, wid) {
var url = "../../library/ajax/wms_ajax.php?wms_mode=add_patient&pid=" + pid + "&wid=" + wid;
var bool = "";
$.ajax({url: url, async:false, success: function(result){
  bool = result;
    }});
return bool;
}

function checkPatient(wid) {
var url = "../../library/ajax/wms_ajax.php?wms_mode=patients_in_room&wid=" + wid;
var bool = "";
$.ajax({url: url, async:false, success: function(result){
  bool = result;
    }});
return bool;

}

function refreshPatientCountBadge(condition) {
//REFRESH THE BADGE, NUMBER OF PATIENTS IN ROOM
var number_of_patients = $("#" + ward_id_with_room_id).find("span").text();
number_of_patients = parseInt(number_of_patients);
if (condition == "add") {
number_of_patients = number_of_patients + 1;
}
else {
  number_of_patients = number_of_patients - 1;
}
$("#" + ward_id_with_room_id).find("span").text(number_of_patients);
}      

function refreshPatientView() {
              //REFRESH THE PATIENT VIEW
  var url = "../../library/ajax/wms_ajax.php?wms_mode=view_patient&wid=" + ward_dbl_click_select + "&rid=" + ward_id_with_room_id;
  $.get(url, function(data, status){
    $('#patient_view').html(data);
  });
}  


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


  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  //  WHEN A USER DOUBLE CLICKS ON WARDS, DISPLAY THE ROOMS    //
  //  PRESENT IN THE WARD.HIDE THE WARD INTERFACE              //
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
$('.ward').dblclick(function () {
  id = $(this).attr('id');
  selectedward_uid = "#" + id + "_uid";
  ward_dbl_click_select = $(selectedward_uid).val();
	//reset globlas to prevent conflicts
	deletewards = [];
	deletedwards_uid = [];
	selectedwards = [];
	var id = $(this).attr('id');
  var name_of_ward = $("#" + id).find('b').text();
  var uid = "#" + id + "_uid";
  var wms_ward_id = $(uid).val();
	$('#wards_screen').hide();
  $('#rooms_screen').css('display', 'block');
  $('#ward_name_breadcrumb').text(name_of_ward);
    var url = "../../library/ajax/wms_ajax.php?wms_mode=get_rooms&wms_ward_id=" + wms_ward_id;
  $.get(url, function(data, status){
    $('#rooms_place').html(data);
  });

});

});


  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  //  CREATES A CREATE WARD DIALOG, WHERE USER CAN ENTER DETAIL//
  //  AND CREATE THE WARD                                      //
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
$(function() {
  $("#dialog").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400 }
  });
  $("#add").click(function() {
    $("#dialog").dialog("open");
    return false;
  });
});


  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  // VALUES FROM CREATE WARD DIALOG IS FETCHED AND AJAX REQUEST//
  // IS MADE IN THE BELOW CODE                                 //
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
$('#c_ward').click(function () {
  var ward_name = $('#w_name').val();
  var ward_rooms = $('#w_rooms').val();
  var w_rooms_capacity = $('#w_rooms_capacity').val();
  var w_prefix = $('#w_prefix').val();
  var url = "../../library/ajax/wms_ajax.php?wms_mode=add&wms_name=" + ward_name  + "&wms_rooms=" + ward_rooms + "&wms_rooms_capacity=" + w_rooms_capacity + "&wms_prefix=" + w_prefix;
  $.get(url, function(data, status){
  if (data == 2) {
    $('.error_create').text("Ward with same name already exists");
    $('.error_create').fadeIn(400).delay(3000).fadeOut(400);
  }
  if(data > 0 && data != 2){
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


  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  // UPDATE THE WARD DETAILS FROM THE UPDATE WARD DIALOG       //
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
$('#wms_edit_update').click(function () {
  var ward_name = $('#wms_edit_name').val();
  var ward_rooms = $('#wms_edit_rooms').val();
  var wms_uid = $('#wms_uid').val();
  var wms_rooms_capacity = $('#wms_edit_rooms_capacity').val();
  var wms_prefix = $('#wms_edit_prefix').val();
  $('#edit_wms').dialog( "close" );
   var url = "../../library/ajax/wms_ajax.php?wms_mode=edit&wms_name=" + ward_name  + "&wms_rooms=" + ward_rooms + "&wms_id=" + wms_uid + "&wms_rooms_capacity=" + wms_rooms_capacity + "&wms_prefix=" + wms_prefix;
    $.get(url, function(data, status){
  if(data == 1){
  	//success ward creation
  	$(selectedward_name_id).find('b').text(ward_name);
  	$(selectedward_room_id).val(ward_rooms);
  	$('.success_create').text("ward successfully updated");
   $('.success_create').fadeIn(400).delay(3000).fadeOut(400);
  }
  if (data == 2) {
    $('.error_create').text("The ward with same name already exists");
    $('.error_create').fadeIn(400).delay(3000).fadeOut(400);
  }
  if (data != 1 && data != 2){
  	//error in ward creation
  	$('.error_create').text("error in updating ward details");
  	$('.error_create').fadeIn(400).delay(3000).fadeOut(400);

  }
  });
});



//edit dialog for ward
$(function() {
  $("#edit_wms").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400}
  });
  $("#edit").click(function() {
  	if (selectedwards.length > 0) {
  	$('.ward').css("background-color", "#b9cd6d");
  	deletewards = [];
    $("#edit_wms").dialog("open");
    $('#wms_edit_name').val(selectedward_name);
    $('#wms_edit_rooms').val(selectedward_room);
    $('#wms_edit_prefix').val($('#w'+selectedward_uid+"_prefix").val());
    $('#wms_edit_rooms_capacity').val($('#w'+selectedward_uid+"_capacity").val());
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
var user_authorization = confirm("Do you really want to delete the wards selected?");
if (user_authorization) {
    var valid_delete = 0;
    var invalid_delete = 0;
    var hide;
     for (i=0; i<deletedwards_uid.length; i++) {
     	var wms_uid = deletedwards_uid[i];
     	 hide = '#' + deletewards[i];
     	var url = "../../library/ajax/wms_ajax.php?wms_mode=delete&wms_id=" + wms_uid;
    	$.get(url, function(data, status){
            if (data == 1){
            	valid_delete = valid_delete + 1;
            }
            else {
            	invalid_delete = invalid_delete + 1;
            }

    	});

    }
    deletewards = [];
    deletedwards_uid = [];
    if (alert("Delete operation complete")) {

    }
    else {
     location.reload(true);     
    }


}
else {
  deletewards = [];
  deletedwards_uid = [];
  selectedwards = [];
  $('.ward').css("background-color", "#b9cd6d");
}

}
else {
	alert("please choose a ward to delete");
}
});


//refresh function for wms
$('#refresh').click(function () {
  location.reload(true);
});

$('#rooms_place').on('click', '.room', function (){
      var id = $(this).attr('id');
      $(this).css("background-color", "yellow");
      if ($.inArray(id,deletedrooms) != -1) {
      //it means the item is clicked again
      var index = deletedrooms.indexOf(id);

      if (index !== -1) {
        deletedrooms.splice(index, 1);
      }
      $(this).css('background-color', '#b9cd6d');
    }
    else {
      //it means the item is selected new
      deletedrooms.push(id);
      $(this).css('background-color', 'yellow');
      }
});

//edit dialog for ward
$(function() {
  $("#add_patients").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400}
  });
});

$('#rooms_place').on('dblclick', '.room', function () {
  var id = $(this).attr('id');
  ward_id_with_room_id = id;
  var room_name = $(this).find("b").text();
  //assign room id
  var rid = id;
  var url = "../../library/ajax/wms_ajax.php?wms_mode=view_patient&wid=" + ward_dbl_click_select + "&rid=" + rid;
  $.get(url, function(data, status){
    $('#patient_view').html(data);
  });
  $('#add_patients').dialog("option", "title", room_name);
  $('#wms_patient_add_ward_id').val(id);
  $('#add_patients').dialog("option", "width", "600");
  $('#add_patients').dialog("open");
  var tabs_priority = checkPatient(ward_id_with_room_id);
  console.log(tabs_priority);
  if (tabs_priority == 1) {
    tab_index = 2;
  }
  else {
    tab_index = 0;
  }
  $(function() {
  $('#tabs-1').tabs({active: tab_index});
  $( "#tabs-1" ).tabs({ activate: function(event, ui) {
    if (ui.newTab.index() == 2) {
      $('#tabs-4').css('display', 'block');
    }
    else {
      $('#tabs-4').css('display', 'none');
    }
        }});
});
});


//when patient is newly created
$('#wms_patient_add_submit').click(function () {
//Before adding a patient we need to check for room capacity.
  $('#add_patients').dialog("close");
  //wid  = ward id
  var wid = $('#wms_patient_add_ward_id').val();
  var fname = $('#wms_patient_fname').val();
  var mname = $('#wms_patient_mname').val();
  var lname = $('#wms_patient_lname').val();
  var DOB = $('#wms_patient_dob').val();
  var sex = $('#wms_patient_sex').val();
  var form_create = $('#form_create').val();
  var from_wms = $('#from_wms').val();
  $.post("../../interface/new/new_patient_save.php", {fname: fname, mname:mname, lname:lname, DOB:DOB, sex:sex, form_create:form_create, from_wms:from_wms}, function(result){
    pid = parseInt(result);
          var bool = "";
          var url = "../../library/ajax/wms_ajax.php?wms_mode=add_patient&wid=" + wid + "&pid=" + pid;
          $.ajax({url: url, async:false, success: function(result){
              bool = result;
          }});
          if (bool) {
            $('#search_box_add_passed').text("patient has been successfully added");
            $('#search_box_add_passed').dialog("open");
            refreshPatientView();
            refreshPatientCountBadge("add");
          }
          else {
            $('#search_box_add_failed').text("failure");
            $('#search_box_add_failed').dialog("open");

          }

    });
});

//DELETE PATIENTS IN A PARTICULAR ROOM
$('#patient_view').on('dblclick', '.deletable', function () {
var pid = $(this).attr('href');
var wid = ward_dbl_click_select;
var url = "../../library/ajax/wms_ajax.php?wms_mode=delete_patients&wid=" + wid + "";
});

//SEARCH PATIENTS FROM SEARCH BOX
$('#wms_search_patient').keyup(function () {
  var searchQuery = $('#wms_search_patient').val();
  //we will search for fname, mname, lname for now.
  var url = "../../library/ajax/wms_ajax.php?wms_mode=search_patient&search_patient_name=" + searchQuery;
  $.get(url, function(data, status){
    $('#search_patient_results').html(data);
  });
});
 
//add patient dialog for search area
$(function() {
  $("#search_box_add_patients").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400}
  });  
  $('#search_box_add_patients').dialog({
       buttons : {
        "Confirm" : function() {
          var boolean =  checkpatientAlreadyExists();
          if (boolean == 1) {
            $(this).dialog("close");
            var check_insert = insertPatient($('#search_box_add_patients').data('pid'), ward_id_with_room_id);
            if (check_insert) {
            $("#search_box_add_passed").dialog("open");
            refreshPatientView();
            refreshPatientCountBadge("add");
            }
            else {
              $("#search_box_add_failed").text("Error in adding patient");
              $("#search_box_add_failed").dialog("open");
            }
          }
          else{
            $(this).dialog("close");
            $("#search_box_add_failed").dialog("open");
          }
        },
        "Cancel" : function() {
          $(this).dialog("close");
        }
  }
});

});



//DELETE PATIENT FROM PATIENT VIEW
$(function() {
  $("#patient_view_delete_patient").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400}
  });  
  $('#patient_view_delete_patient').dialog({
       buttons : {
        "Confirm" : function() {
          var pid = $('#patient_view_delete_patient').data('pid');
          var wid = ward_dbl_click_select;
          var boolean = deletePatient(pid, wid);
          if (boolean) {
            $('#patient_view_delete_patient').dialog("close");
            refreshPatientView();
            refreshPatientCountBadge("sub");
            $('#search_box_add_passed').text("patient deleted successfully");
            $('#search_box_add_passed').dialog("open");
          }
          else {
            $('#patient_view_delete_patient').dialog("close");
            $('#search_box_add_failed').text("error in deleting this patient");
            $('#search_box_add_failed').dialog("open");
          }
        },
        "Cancel" : function() {
          $(this).dialog("close");
        }
  }
});

});


//failure dialog for add patients
$(function() {
  $("#search_box_add_failed").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400}
  }).prev(".ui-dialog-titlebar").css("background","red");  
});

//success dialog for add patients
$(function() {
  $("#search_box_add_passed").dialog({
    autoOpen : false, modal : true, show : { effect: "explode", duration: 300 }, hide : { effect: "explode", duration: 400}
  }).prev(".ui-dialog-titlebar").css("background","green");  
});


//ADD PATIENT FROM SEARCH BOX
$('#search_patient_results').on('click', '.add_patients_plus_icon', function () {
  var pid = $(this).attr('id');
  $('#search_box_add_patients').data("pid", pid).dialog("open");
});

//DELETE PATIENT FROM ROOM
$('#patient_view').on('click', '.deletable', function () {
  var pid = $(this).attr('id');
  $('#patient_view_delete_patient').data("pid", pid).dialog("open");
});


//REDIRECT TO DEMOGRAPHICS SCREEN
$('#patient_view').on('click', '.redirect_demographics', function () {
  var pid = $(this).attr('id');
   top.restoreSession();
   top.RTop.location = "../../interface/patient_file/summary/demographics.php?set_pid=" + pid;
});
</script>
	
