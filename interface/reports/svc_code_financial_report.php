<?php
/*
 * Financial Summary by Service Code
 *
 * This is a summary of service code charge/pay/adjust and balance,
 * with the ability to pick "important" codes to either highlight or
 * limit to list to. Important codes can be configured in
 * Administration->Service section by assigning code with
 * 'Service Reporting'.
 *
 * Copyright (C) 2016-2017 Terry Hill <teryhill@librehealth.io>
 * Copyright (C) 2006-2015 Rod Roark <rod@sunsetsystems.com>
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * LICENSE: This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0
 * See the Mozilla Public License for more details.
 * If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package LibreHealth EHR
 * @author  Visolve
 * @author Rod Roark <rod@sunsetsystems.com>
 * @link    http://librehealth.io
 */

$sanitize_all_escapes=true;
$fake_register_globals=false;

require_once("../globals.php");
require_once("../../library/report_functions.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formatting.inc.php");
require_once "$srcdir/options.inc.php";
require_once "$srcdir/formdata.inc.php";
require_once "$srcdir/appointments.inc.php";
/** Current format date */
$DateFormat = DateFormatRead();
$DateLocale = getLocaleCodeForDisplayLanguage($GLOBALS['language_default']);

$grand_total_units  = 0;
$grand_total_amt_billed  = 0;
$grand_total_amt_paid  = 0;
$grand_total_amt_adjustment  = 0;
$grand_total_amt_balance  = 0;


  if (! acl_check('acct', 'rep')) die(xlt("Unauthorized access."));

if (isset($_POST['form_from_date']) && isset($_POST['form_to_date']) && !empty($_POST['form_to_date']) && $_POST['form_from_date']) {
    $from_date = fixDate($_POST['form_from_date'], date(DateFormatRead(true)));
    $to_date   = fixDate($_POST['form_to_date']  , date(DateFormatRead(true)));
}
  $form_facility  = $_POST['form_facility'];
  $form_provider  = $_POST['form_provider'];

  if ($_POST['form_csvexport']) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=svc_financial_report_".attr($from_date)."--".attr($to_date).".csv");
    header("Content-Description: File Transfer");
    // CSV headers:
    } // end export
  else {
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<?php html_header_show();?>
<style type="text/css">
/* specifically include & exclude from printing */
@media print {
    #report_parameters {
        visibility: hidden;
        display: none;
    }
    #report_parameters_daterange {
        visibility: visible;
        display: inline;
    }
    #report_results {
       margin-top: 30px;
    }
}

/* specifically exclude some from the screen */
@media screen {
    #report_parameters_daterange {
        visibility: hidden;
        display: none;
    }
}
</style>

<title><?php echo xlt('Financial Summary by Service Code') ?></title>

<script language="JavaScript">

 $(document).ready(function() {
  var win = top.printLogSetup ? top : opener.top;
  win.printLogSetup(document.getElementById('printbutton'));
 });

</script>

</head>
<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' class="body_top">
<span class='title'><?php echo xlt('Report'); ?> - <?php echo xlt('Financial Summary by Service Code'); ?></span>
<form method='post' action='svc_code_financial_report.php' id='theform'>
<div id="report_parameters">
<input type='hidden' name='form_refresh' id='form_refresh' value=''/>
<input type='hidden' name='form_csvexport' id='form_csvexport' value=''/>
<table>
 <tr>
  <td width='70%'>
    <div style='float:left'>
    <table class='text'>
        <tr>
            <td class='label'>
                <?php echo xlt('Facility'); ?>:
            </td>
            <td>
            <?php dropdown_facility($form_facility, 'form_facility', true); ?>
            </td>
                        <td><?php echo xlt('Provider'); ?>:</td>
                        <td>
                  <?php // Build a drop-down list of providers.
                    dropDownProviders();
                                ?>
                </td>
        </tr>
        <tr>
          <?php showFromAndToDates(); ?>
                        <td>
                           <input type='checkbox' name='form_details'<?php  if ($_POST['form_details']) echo ' checked'; ?>>
                           <?php echo xlt('Important Codes'); ?>
                        </td>
        </tr>
    </table>
    </div>
  </td>
  <?php showSubmitPrintButtons('form_csvexport'); ?>
 </tr>
</table>
</div> <!-- end of parameters -->

<?php
}
   // end not export

  if ($_POST['form_refresh'] || $_POST['form_csvexport']) {
    $rows = array();
    $from_date = $from_date;
    $to_date   = $to_date;
    $sqlBindArray = array();
    $query = "select b.code,sum(b.units) as units,sum(b.fee) as billed,sum(ar_act.paid) as PaidAmount, " .
        "sum(ar_act.adjust) as AdjustAmount,(sum(b.fee)-(sum(ar_act.paid)+sum(ar_act.adjust))) as Balance, " .
        "c.financial_reporting " .
        "FROM form_encounter as fe " .
        "JOIN billing as b on b.pid=fe.pid and b.encounter=fe.encounter " .
        "JOIN (select pid,encounter,code,sum(pay_amount) as paid,sum(adj_amount) as adjust from ar_activity group by pid,encounter,code) as ar_act " .
        "ON ar_act.pid=b.pid and ar_act.encounter=b.encounter and ar_act.code=b.code " .
        "LEFT OUTER JOIN codes AS c ON c.code = b.code " .
        "INNER JOIN code_types AS ct ON ct.ct_key = b.code_type AND ct.ct_fee = '1' " .
        "WHERE b.code_type != 'COPAY' AND b.activity = 1 /* AND b.fee != 0 */ AND " .
        "fe.date >=  ? AND fe.date <= ?";
   array_push($sqlBindArray,"$from_date 00:00:00","$to_date 23:59:59");
    // If a facility was specified.
      if ($form_facility) {
        $query .= " AND fe.facility_id = ?";
       array_push($sqlBindArray,$form_facility);
      }
    // If a provider was specified.
      if ($form_provider) {
        $query .= " AND b.provider_id = ?";
        array_push($sqlBindArray,$form_provider);
      }
      // If selected important codes
      if($_POST['form_details']) {
        $query .= " AND c.financial_reporting = '1'";
      }
      $query .= " GROUP BY b.code ORDER BY b.code, fe.date, fe.id ";
      $res = sqlStatement($query,$sqlBindArray);
      $grand_total_units  = 0;
      $grand_total_amt_billed  = 0;
      $grand_total_amt_paid  = 0;
      $grand_total_amt_adjustment  = 0;
      $grand_total_amt_balance  = 0;

      while ($erow = sqlFetchArray($res)) {
      $row = array();
      $row['pid'] = $erow['pid'];
      $row['provider_id'] = $erow['provider_id'];
      $row['Procedure codes'] = $erow['code'];
      $row['Units'] = $erow['units'];
      $row['Amt Billed'] = $erow['billed'];
      $row['Paid Amt'] = $erow['PaidAmount'];
      $row['Adjustment Amt'] = $erow['AdjustAmount'];
      $row['Balance Amt'] = $erow['Balance'];
      $row['financial_reporting'] = $erow['financial_reporting'];
      $rows[$erow['pid'] . '|' . $erow['code'] . '|' . $erow['units']] = $row;
      }
              if ($_POST['form_csvexport']) {
                // CSV headers:
                if (true) {
                  echo '"Procedure codes",';
                  echo '"Units",';
                  echo '"Amt Billed",';
                  echo '"Paid Amt",';
          echo '"Adjustment Amt",';
                  echo '"Balance Amt",' . "\n";
                }
              } else {
?> <div id="report_results">
<table >
 <thead>
  <th>
   <?php echo xlt('Procedure Codes'); ?>
  </th>
  <th >
   <?php echo xlt('Units'); ?>
  </th>
  <th>
   <?php echo xlt('Amt Billed'); ?>
  </th>
  <th>
   <?php echo xlt('Paid Amt'); ?>
  </th>
  <th >
   <?php echo xlt('Adjustment Amt'); ?>
  </th>
  <th >
   <?php echo xlt('Balance Amt'); ?>
  </th>
 </thead>
 <?php
              }
     $orow = -1;

     foreach ($rows as $key => $row) {
$print = '';
$csv = '';

if($row['financial_reporting']){ $bgcolor = "#FFFFDD";  }else { $bgcolor = "#FFDDDD";  }
$print = "<tr bgcolor='$bgcolor'><td class='detail'>".text($row['Procedure codes'])."</td><td class='detail'>".text($row['Units'])."</td><td class='detail'>".text(oeFormatMoney($row['Amt Billed']))."</td><td class='detail'>".text(oeFormatMoney($row['Paid Amt']))."</td><td class='detail'>".text(oeFormatMoney($row['Adjustment Amt']))."</td><td class='detail'>".text(oeFormatMoney($row['Balance Amt']))."</td>";

$csv = '"' . text($row['Procedure codes']) . '","' . text($row['Units']) . '","' . text(oeFormatMoney($row['Amt Billed'])) . '","' . text(oeFormatMoney($row['Paid Amt'])) . '","' . text(oeFormatMoney($row['Adjustment Amt'])) . '","' . text(oeFormatMoney($row['Balance Amt'])) . '"' . "\n";

$bgcolor = ((++$orow & 1) ? "#ffdddd" : "#ddddff");
                                $grand_total_units  += $row['Units'];
                                                $grand_total_amt_billed  += $row['Amt Billed'];
                                                $grand_total_amt_paid  += $row['Paid Amt'];
                                                $grand_total_amt_adjustment  += $row['Adjustment Amt'];
                                                $grand_total_amt_balance  += $row['Balance Amt'];

        if ($_POST['form_csvexport']) { echo $csv; }
    else { echo $print;
 }
     }
       if (!$_POST['form_csvexport']) {
         echo "<tr bgcolor='#ffffff'>\n";
         echo " <td class='detail'>" . xlt("Grand Total") . "</td>\n";
         echo " <td class='detail'>" . text($grand_total_units) . "</td>\n";
         echo " <td class='detail'>" .
         text(oeFormatMoney($grand_total_amt_billed)) . "</td>\n";
         echo " <td class='detail'>" .
         text(oeFormatMoney($grand_total_amt_paid)) . "</td>\n";
         echo " <td class='detail'>" .
         text(oeFormatMoney($grand_total_amt_adjustment)) . "</td>\n";
         echo " <td class='detail'>" .
         text(oeFormatMoney($grand_total_amt_balance)) . "</td>\n";
         echo " </tr>\n";
          ?>
                </table>    </div>
        <?php
      }
    }

  if (! $_POST['form_csvexport']) {
       if ( $_POST['form_refresh'] && count($print) != 1)
    {
        echo "<span style='font-size:10pt;'>";
                echo xlt('No matches found. Try search again.');
                echo "</span>";
        echo '<script>document.getElementById("report_results").style.display="none";</script>';
        echo '<script>document.getElementById("controls").style.display="none";</script>';
        }

if (!$_POST['form_refresh'] && !$_POST['form_csvexport']) { ?>
<div class='text'>
    <?php echo xlt('Please input search criteria above, and click Submit to view results.' ); ?>
</div>
<?php } ?>
</form>
</body>

<!-- stuff for the popup calendar -->

<link rel='stylesheet' href='<?php echo $css_header ?>' type='text/css'>
<link rel="stylesheet" href="../../library/css/jquery.datetimepicker.css">
<script type="text/javascript" src="../../library/js/jquery.datetimepicker.full.min.js"></script>
<script>
    $(function() {
        $("#form_from_date").datetimepicker({
            timepicker: false,
            format: "<?= $DateFormat; ?>"
        });
        $("#form_to_date").datetimepicker({
            timepicker: false,
            format: "<?= $DateFormat; ?>"
        });
        $.datetimepicker.setLocale('<?= $DateLocale;?>');
    });
</script>
<script language="Javascript">
 top.restoreSession();
</script>
</html>
<?php
  } // End not csv export
?>
