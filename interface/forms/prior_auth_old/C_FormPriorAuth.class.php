<?php
/** 
 * 
 * No Copyright information in original file
 * 
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package LibreHealth EHR
 * @author  No author information in original file
 * @link    http://librehealth.io
*/


require_once ($GLOBALS['fileroot'] . "/library/classes/Controller.class.php");
require_once ($GLOBALS['fileroot'] . "/library/forms.inc");
require_once("FormPriorAuth.class.php");

class C_FormPriorAuth extends Controller {

    var $template_dir;
    var $form_action;
    var $dont_save_link;
    var $style;
    var $prior_auth;
    var $view;                    
    
    function __construct($template_mod = "general") {
        parent::__construct();
        $returnurl = 'encounter_top.php';
        $this->template_mod = $template_mod;
        $this->template_dir = dirname(__FILE__) . "/templates/prior_auth/";
        $this->form_action = $GLOBALS['web_root'];
        $this->dont_save_link = $GLOBALS['webroot'] . "/interface/patient_file/encounter/$returnurl";
        $this->style =  $GLOBALS['style'];
    }
    
    function default_action() {
        $prior_auth = new FormPriorAuth();
                    ob_start(); //Start output Buffer
                    require_once($this->template_dir . $this->template_mod . "_new.php");
                    $echoed_content = ob_get_clean(); // gets content, discards buffer
                    return $echoed_content;        
                    $this->prior_auth = $prior_auth;
                   
    }
    
    function view_action($form_id) {
        if (is_numeric($form_id)) {
            $prior_auth = new FormPriorAuth($form_id);
        }
        else {
            $prior_auth = new FormPriorAuth();
        }
        
                    $this->view = true;
                    $this->prior_auth = $prior_auth;
                    ob_start(); //Start output Buffer
                    require_once($this->template_dir . $this->template_mod . "_new.php");
                    $echoed_content = ob_get_clean(); // gets content, discards buffer
                    return $echoed_content;                    

    }
    
    function default_action_process() {
        if ($_POST['process'] != "true")
            return;
        $this->prior_auth = new FormPriorAuth($_POST['id']);
        parent::populate_object($this->prior_auth);
        
        
        $this->prior_auth->persist();
        if ($GLOBALS['encounter'] == "") {
            $GLOBALS['encounter'] = date("Ymd");
        }
        addForm($GLOBALS['encounter'], "Prior Authorization Form", $this->prior_auth->id, "prior_auth", $GLOBALS['pid'], $_SESSION['userauthorized']);
        $_POST['process'] = "";
        return;
    }
    
}



?>
