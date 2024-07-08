<?php
require_once('header.php');
include_once('ohc_session.php');
require_once('top-menu.php');
require_once('left-nav.php');
include_once('core/class.manageUsers.php');
extract($_REQUEST);
$obj = new ManageUsers();
$tday = date("Y-m-d");

$sels = (!empty($_SESSION['ohc_loca'])) ? $_SESSION['ohc_loca'] : $_SESSION['currentlocation_id'];

$pharmsy = $obj->listDirectQuery("select GROUP_CONCAT(id) as id from ohc_pharmay where location_id='" . $sels . "' ");
$pharmsyid = $pharmsy[0]['id'];
if (empty($_SESSION['ohc_loca'])) {
    $whr .= " AND psd.r_pharmacy_id='" . $sessionlocation_id . "'";
    $joinm = "master_pharmacy";
} else {
    $whr .= " AND psd.r_pharmacy_id='" . $_SESSION['ohc_loca'] . "' AND psd.ohc='1' ";
    $joinm = "master_corporate";
}

$sql = "SELECT psd.*,mp.reminder_issue,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_quantity,DATE_FORMAT(psd.created_on,'%d %b %Y') as added,DATE_FORMAT(psd.modified_on,'%d %b %Y') as modified,ifnull(mp.reminder_expiry,0) as reminder_expiry,'-1' as total_count
				FROM `pharmacy_stock_detail` psd
				LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
				LEFT OUTER JOIN  " . $joinm . " mp ON mp.id = pssd.r_pharmacy_id
				WHERE 1=1 " . $whr . " AND psd.drug_expiry_date > '$tday' and psd.phar_ids in(" . $pharmsyid . ") GROUP BY psd.id  HAVING  balance_quantity > '0' ORDER BY psd.drug_name ";
$getSqlData = new ManageUsers();
$listingMaintenaces = $getSqlData->listDirectQuery($sql);

$max_count = $obj->listDirectQuery("SELECT  * FROM  `stock_alert` where corp_id='" . $_SESSION['ohc_loca'] . "' ");
foreach ($listingMaintenaces  as $listingMaintenacess) {
    $dname = $listingMaintenacess['drug_name'];
    $arr[$dname][] = $listingMaintenacess['balance_quantity'];
    $arr[$dname]['dtype'] = $listingMaintenacess['drug_type'];
    $arr[$dname]['manufacturer'] = $listingMaintenacess['drug_manifaturer'];
}


$i = 1;
foreach ($arr as $ky => $val) {
    $total_qty = 0;
    $total_qty = array_sum($val);

    if (!empty($max_count)) {
        $drug_count = 0;
        if ($val['dtype'] === "Capsule") {

            $drug_count = $max_count[0]['Capsule'];
        }
        if ($val['dtype'] === "Tablet") {

            $drug_count = $max_count[0]['Tablet'];
        }
        if ($val['dtype'] === "Cream") {
            $drug_count = $max_count[0]['Cream'];
        }
        if ($val['dtype'] === "Drops") {
            $drug_count = $max_count[0]['Drops'];
        }
        if ($val['dtype'] === "Foam") {
            $drug_count = $max_count[0]['Foam'];
        }
        if ($val['dtype'] === "Gel") {
            $drug_count = $max_count[0]['Gel'];
        }
        if ($val['dtype'] === "Inhaler") {
            $drug_count = $max_count[0]['Inhaler'];
        }
        if ($val['dtype'] === "Injection") {

            $drug_count = $max_count[0]['Injecion'];
        }
        if ($val['dtype'] === "Lotion") {
            $drug_count = $max_count[0]['Lotion'];
        }
        if ($val['dtype'] === "Ointment") {
            $drug_count = $max_count[0]['Ointment'];
        }
        if ($val['dtype'] === "Powder") {
            $drug_count = $max_count[0]['Powder'];
        }
        if ($val['dtype'] === "Shampoo") {
            $drug_count = $max_count[0]['Shampoo'];
        }
        if ($val['dtype'] === "Syringe") {
            $drug_count = $max_count[0]['Syringe'];
        }
        if ($val['dtype'] === "Syrup") {
            $drug_count = $max_count[0]['Syrup'];
        }

        if ($val['dtype'] === "Toothpaste") {
            $drug_count = $max_count[0]['Toothpaste'];
        }
        if ($val['dtype'] === "Spray") {
            $drug_count = $max_count[0]['Spray'];
        }
    }

    if ($total_qty < $drug_count) {

        $checkstocks[] = $total_qty;

        $i++;
    }
}
$checkstock = array_sum($checkstocks);
?>
<!--responsive-->
<div class="container-fluid" id="content">
    <div id="main">

        <?php
        $getpharmObj = new ManageUsers();
        //echo "SELECT *  FROM ohc_rights where user_id='".$_SESSION['userid']."' ";
        $ohcsection = $getpharmObj->listDirectQuery("SELECT *  FROM ohc_rights where user_id='" . $_SESSION['userid'] . "' ");
        //echo "SELECT * from  `ohc_pharmay` where corp_id=".$_SESSION['parent_id']." and location_id=".$_SESSION['ohc_loca']." and mainpharmacy!='1' and id=".$ohcsection[0]['phrmacy_id']."";

        $Gtpharmacy = $getpharmObj->listDirectQuery("SELECT * from  `ohc_pharmay` where  id=" . $ohcsection[0]['phrmacy_id'] . "");



        $getpharmObj = null;
        ?>

        <div class="Top-Strip">
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 float-left">
                <h5 style="font-weight:bold;color:#<? echo $_SESSION['tclr'] ?>;">Pending Request</h5>
            </div>
            <div class="col-xl-3 col-lg-3 d-none d-lg-block float-left ">
                <h6 style="font-weight:bold;color:#<? echo $_SESSION['tclr'] ?>;"><? echo $userNameMenus . "-" . $Gtpharmacy[0]['name']; ?></h6>
            </div>

            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 float-left">
                <select name="phid" id="phid" class='uservalue a1 select2-me input-large w-100 nomargin texttransformcap checkvalid phhid'>

                    <?php

                    $AppointmentDetail = new ManageUsers();
                    //	echo "SELECT phrmacy_id from ohc_rights where corp_id=".$_SESSION['ohc_loca']." and user_id=".$_SESSION['userid'];
                    $Appointment = $AppointmentDetail->listDirectQuery("SELECT phrmacy_id from ohc_rights where corp_id=" . $_SESSION['ohc_loca'] . " and user_id=" . $_SESSION['userid']);
                    $pid = explode(",", $Appointment[0]['phrmacy_id']);
                    $phids = $pid[0];
                    //	echo "SELECT name,id from ohc_pharmay where location_id=".$_SESSION['ohc_loca']." and mainpharmacy='0' "; 
                    $phaname = $AppointmentDetail->listDirectQuery("SELECT name,id from ohc_pharmay where location_id=" . $_SESSION['ohc_loca'] . " and mainpharmacy='0' ");
                    if ($phaname != 0) {
                        foreach ($phaname as $phanames) {


                            $phid = $phanames['id'];
                            $pharnames = $phanames['name'];
                            $jbsel = "selected='select'";
                            if (in_array($phid, $pid)) {
                                if ($phids == $phid) {
                                    echo '<option value="' . $phid . '" ' . $jbsel . '>' . $pharnames . '</option>';
                                } else {
                                    echo '<option value="' . $phid . '" >' . $pharnames . '</option>';
                                }
                            }

                            //echo '<option value="'.$phid.'" >'.$pharnames.'</option>';

                        }
                    }
                    $AppointmentDetail = null;

                    ?>
                </select>
            </div>
            <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 float-left">
                <select checkvalid="1" name="uservalue" id="uservalue" class='uservalue a1 select2-me w-100 input-large nomargin texttransformcap checkvalid phhid'>
                    <option value="">Select Patient</option>
                    <?php

                    $AppointmentDetail = new ManageUsers();

                    $Appointment = $AppointmentDetail->listDirectQuery("SELECT p.user_id, mud.first_name, mud.last_name,mud.mob_num,cum.emp_id FROM
										 corporate_user_mapping cum inner JOIN master_user_details mud ON mud.id = cum.r_user_id inner JOIN 
										 prescription p on p.user_id=mud.id WHERE cum.location='" . $_SESSION['ohc_loca'] . "' and IFNULL(p.fav_pharmacy_order,0)!=2 AND cum.isactive='1'  
										 GROUP BY p.user_id ORDER BY mud.first_name	");
                    if ($Appointment != 0) {
                        foreach ($Appointment as $listAppointments) {

                            $Appid = $listAppointments['user_id'];
                            $first_name = $listAppointments['first_name'];
                            $last_name = $listAppointments['last_name'];
                            $user_id = $listAppointments['user_id'];
                            $mob_num = $listAppointments['mob_num'];
                            if (isset($_REQUEST['suser']) && $_REQUEST['suser'] == $Appid) {
                                $sel = " selected='selected'";
                            } else {
                                $sel = "";
                            }
                            if ($first_name != "" || $last_name != "") {
                                echo '<option value="' . $Appid . '" ' . $sel . '>' . ucfirst($first_name) . ' ' . ucfirst($last_name) . ' - ' . $user_id . ' / ' . $mob_num . '</option>';
                            }
                        }
                    }
                    $AppointmentDetail = null;

                    ?>
                </select>
            </div>
            <?php
            ?>

        </div>
        
        <div class="ajax-content content-table float-left"></div>
    </div>


    <script>
        $(document).ready(function() {
            var phid = $("#phid").val();
            var uservalue = $('#uservalue').val();

            viewPrescriptionByAjax();

            function viewPrescriptionByAjax() {
                var uservalue = $('#uservalue').val();
                var phid = $('#phid').val();

                var action = "prescription";
                var dataString = {
                    uservalue: uservalue,
                    phrid: phid,
                    action: action
                };
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_ohc-pharmacy_request.php",
                    data: dataString,
                    success: function(data) {
                        $(".ajax-content").html(data);

                    }


                });
            }
            $(".phhid").change(function() {

                var uservalue = $('#uservalue').val();
                var phid = $('#phid').val();

                var action = "prescription";
                var dataString = {
                    uservalue: uservalue,
                    phrid: phid,
                    action: action
                };
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_ohc-pharmacy_request.php",
                    data: dataString,
                    success: function(data) {
                        $(".ajax-content").html(data);

                    }


                });
            });
        });
        var stockdata = "<? echo $checkstock; ?>";
        if (stockdata != "") {
            window.onload = function() {
                restock();
            }
        }

        function restock() {
            var pid = 'stock';
            $.fn.colorbox({
                href: "restockreview.php?pid=" + pid,
                iframe: true,
                open: true,
                innerWidth: 700,
                innerHeight: 500,
                onOpen: function() {
                    $('#cboxContent').removeClass("cboxContentCls");
                }
            });
        }
    </script>