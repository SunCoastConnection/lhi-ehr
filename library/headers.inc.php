<?php

/** 
 *  Common include pathing and related native LHEHR functions
 * 
 * Copyright (C) 2017 Art Eaton
 * SOURCE:  Ken Chapple at mi-squared.com
 * 
 * LICENSE: This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0
 * See the Mozilla Public License for more details. 
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * 
 * @package Librehealth EHR 
 * @author Art Eaton <art@suncoastconnection.com>
 * @link http://librehealth.io
 *  
 * Please help the overall project by sending changes you make to the author and to the LibreEHR community.
 * 
 */
?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<?php

function include_js_library($path)
{
?>
<script type="text/javascript" src="<?php echo $GLOBALS['standard_js_path'].$path;?>"></script>
<?php
}function include_css_library($path)
{
?>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['css_path'].$path;?>" media="screen" />
<?php
}
?>

<?php 
/*  
    This function can be used to call various frequently used libraries.
    Parameters for this function are boolean. Use true or false for including the required libraries.   
*/
function call_required_libraries($bootstrap,$fancybox,$knockout,$datepicker){?>
    <!-- All these libraries require jQuery to be loaded initially for best performance -->
    <script type="text/javascript" src="<?php echo $GLOBALS['standard_js_path']; ?>jquery-min-3-1-1/index.js"></script>
    <?php 
    if($bootstrap===true){   ?>
        <link rel="stylesheet" href="<?php echo $GLOBALS['standard_js_path']; ?>bootstrap-3-3-4/dist/css/bootstrap.min.css" type="text/css">
        <script type="text/javascript" src="<?php echo $GLOBALS['standard_js_path']; ?>bootstrap-3-3-4/dist/js/bootstrap.min.js"></script>
    <?php
    }
    if($fancybox===true){   ?>
        <link rel="stylesheet" href="<?php echo $GLOBALS['css_path']; ?>fancybox/jquery.fancybox-1.2.6.css" media="screen" />
        <script type="text/javascript" src="<?php echo $GLOBALS['standard_js_path']; ?>fancybox/jquery.fancybox-1.2.6.js"></script>
    <?php
    }
    if($knockout===true){   ?>
        <script type="text/javascript" src="<?php echo $GLOBALS['standard_js_path']; ?>knockout/knockout-3.4.0.js"></script>
    <?php
    }
    if($datepicker===true){ ?>
        <link rel="stylesheet" href="<?php echo $GLOBALS['css_path']; ?>jquery-datetimepicker/jquery.datetimepicker.css" media="screen" />
        <script type="text/javascript" src="<?php echo $GLOBALS['standard_js_path']; ?>jquery-datetimepicker/jquery.datetimepicker.full.min.js"></script>
    <?php
    }
}
?>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/textformat.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
