<?php
include('../session.php');
//@ini_set( ‘upload_max_size’ , ’1024M’ ); @ini_set( ‘post_max_size’, ’1024M’); @ini_set( ‘max_execution_time’, ’1000′ );

include_once('../core/class.manageSettings.php');
include_once('../core/class.manageUsers.php');
include_once('../core/class.common.php');
$datetime = date("Y-m-d H:i:s");
extract($_REQUEST);
?>
<style>
    .cboxContentCls {
        background: none !important;
        border: none !important;
    }

    .outside {
        color: #<? echo $_SESSION['tclr'] ?> !important;
    }

    .box.box-bordered.box-color .box-content {
        border-color: #fff !important;
    }

    .dropdown-menu.dropdown-primary,
    #navigation .dropdown-menu,
    .box.box-bordered.box-color .box-title,
    .box.box-bordered.box-color .box-content {
        border-color: #fff !important;
    }
</style>
<link rel="stylesheet" href="<?php echo $sitepath; ?>css/plugins/select2/select2.css">
<link rel="stylesheet" href="<?php echo $sitepath; ?>css/colorbox.css">
<script src="<?php echo $sitepath; ?>js/custom.js"></script>
<script src="<?php echo $sitepath; ?>js/jquery.fs.selecter.js"></script>
<script src="<?php echo $sitepath; ?>js/plugins/select2/select2.min.js"></script>

<script src="<?php echo $sitepath; ?>js/bootstrap.min.js"></script>
<script src="<?php echo $sitepath; ?>js/eakroko.min.js"></script>
<script>
    function viewsubpharAjax() {

        var subphid = $('#subphid').val();
        var uservalue = $('#userid').val();
        var action = "subphar";
        var dataString = {
            uservalue: uservalue,
            subphid: subphid,
            action: action
        };
        $.ajax({
            type: "POST",
            url: "ajax/ajax_sub-pharmacy_request.php",
            data: dataString,
            success: function(data) {
                $("#sub-phar-div").html(data);
                $("#content-infinite").css("display", "none");



            }


        });
    }
</script>

<?
$prescriptionDetail = new ManageUsers();
if ($action == "prescription") {
    if ($phrid != "") {
        $mphsql = "SELECT * from ohc_pharmay where  mainpharmacy='1' and corp_id=" . $_SESSION['parent_id'] . " and location_id=" . $_SESSION['ohc_loca'];
        $mainphr = $prescriptionDetail->listDirectQuery($mphsql);

        if ((!empty($mainphr) && $phrid == $mainphr[0]['id'])) {
            $remphr = $prescriptionDetail->listDirectQuery("SELECT group_concat(id) as pid from ohc_pharmay where corp_id=" . $mainphr[0]['corp_id'] . " and location_id=" . $mainphr[0]['location_id'] . " and  mainpharmacy!='1'");
            $whrPres .= "and p.fav_pharmacy in (" . $remphr[0]['pid'] . ")";
?>
            <div class="w-100 float-left px-3">
                <select name="subphid" id="subphid" class="select2-me w-25 input-large nomargin texttransformcap checkvalid float-right" onchange="viewsubpharAjax()">

                    <?php

                    $AppointmentDetail = new ManageUsers();
                    echo '<option value="' . $remphr[0]['pid'] . '" >Select Sub pharmacy</option>';

                    $phaname = $AppointmentDetail->listDirectQuery("SELECT name,id from ohc_pharmay where id in (" . $remphr[0]['pid'] . ")");
                    if ($phaname != 0) {
                        foreach ($phaname as $phanames) {


                            $phid = $phanames['id'];
                            $pharnames = $phanames['name'];



                            echo '<option value="' . $phid . '" >' . $pharnames . '</option>';
                        }
                    }
                    $AppointmentDetail = null;

                    ?>
                </select>
            </div>


        <?

        } else {
            $whrPres .= "and p.fav_pharmacy=" . $phrid;
        }
    }
    if ($uservalue != "") {
        $whrPres .= " AND p.user_id=$uservalue";
        echo '<input type="hidden" id="userid" value=".$uservalue.">';
    }
    if (!empty($_SESSION['ohc_phar'])) {
        $whrPres .= " and p.ohc='1'";
    }
    $prescriptionDetail = new ManageUsers();
    echo '<div id="sub-phar-div"></div>';
    echo '<div class="w-100 float-left" id="content-infinite">';
    $sql = "SELECT p.*,mud.first_name  as username,mud.last_name  as lastname,mud.gender,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mud.dob,now()),'month'),TIMESTAMPDIFF(year,mud.dob,NOW())) as age,LEFT(monthname(p.from_date),3) as monthname,LEFT(DAYNAME(p.from_date),3) as dayname,DAYOFMONTH(p.from_date)as dayofmonth,cum.emp_id FROM `prescription` p
LEFT OUTER JOIN `master_user_details` mud on mud.id=p.user_id LEFT OUTER JOIN `corporate_user_mapping` cum on mud.id=cum.r_user_id
 WHERE p.status='1' AND p.isdraft='0'  AND IFNULL(p.fav_pharmacy,0)!=0   and IFNULL(p.fav_pharmacy_order,0)!=2  " . $whrPres . " GROUP BY p.id ORDER BY p.from_date DESC,p.id DESC limit 0,20";

    //echo $sql;

    $prescriptionLists = $prescriptionDetail->listDirectQuery($sql);

    //var_dump($prescriptionLists);
    $countListPrescription = 0;

    if (!empty($prescriptionLists)) {

        include_once('../pharmacy_stock_common.php');
        foreach ($prescriptionLists as $listprescription) {
            $countListPrescription = $countListPrescription + 1;
            $id = $listprescription['id'];
            $prescriptionMainid = $listprescription['id'];
            $emp_id = $listprescription['emp_id'];
            $user_id = $listprescription['user_id'];
            $from_date = $listprescription['from_date'];
            $created_by = $listprescription['created_by'];
            $created_role = $listprescription['created_role'];
            //$med_condition=$listprescription['med_condition'];
            //$is_conformance=$listprescription['is_conformance'];
            $user_code = $listprescription['user_code'];
            $from_dates = $listprescription['from_date'];
            //$master_hcsp_user_id=$listprescription['master_hcsp_user_id'];
            $doctornotes = $listprescription['doctornotes'];
            $usernotes = $listprescription['usernotes'];
            $attachment_id = $listprescription['attachment_id'];
            $username = $listprescription['username'];
            $lastname = $listprescription['lastname'];
            $gender = $listprescription['gender'];
            $usernotes = $listprescription['usernotes'];
            $sharewithdoctor = $listprescription['sharewithdoctor'];
            $age = $listprescription['age'];
            $isdraft = $listprescription['isdraft'];
            $test_modified = $listprescription['test_modified'];
            $monthname = $listprescription['monthname'];
            $dayname = $listprescription['dayname'];
            $dayofmonth = $listprescription['dayofmonth'];
            $fav_pharmacy_order = $listprescription['fav_pharmacy_order'];
            $from_dates1 = explode("-", $from_dates);
            $from_datep = $from_dates1[2] . $from_dates1[1] . $from_dates1[0];
            if ($created_role == "2") {
                $GetCreatedByName = new ManageUsers();
                $GetCreatedByNames = $GetCreatedByName->listDirectQuery("SELECT * from master_doctor where id=" . $created_by);
                if ($GetCreatedByNames) {
                    foreach ($GetCreatedByNames as $GetCreatedByNamesL) {
                        $user_code = $GetCreatedByNamesL['user_id'];
                    }
                }
                $GetCreatedByName = null;
            } else if ($created_role == "3") {
                $GetCreatedByNameq = new ManageUsers();
                $GetCreatedByNameqs = $GetCreatedByNameq->listDirectQuery("SELECT * from master_hcsp_user where id=" . $created_by);
                if ($GetCreatedByNameqs) {
                    foreach ($GetCreatedByNameqs as $GetCreatedByNameqsL) {
                        $user_code = $GetCreatedByNameqsL['user_id'];
                    }
                }
                $GetCreatedByName = null;
            } else {
                $GetCreatedByName = new ManageUsers();
                $GetCreatedByNames = $GetCreatedByName->listDirectQuery("SELECT * from master_corporate_user where id=" . $created_by);
                if ($GetCreatedByNames) {
                    foreach ($GetCreatedByNames as $GetCreatedByNamesL) {
                        $user_code = $GetCreatedByNamesL['user_id'];
                    }
                }
                $GetCreatedByName = null;
            }

            /*if($listprescription['ohc']=="1"){
				$GetCreatedByName = new ManageUsers();
									$GetCreatedByNames=$GetCreatedByName->listDirectQuery("SELECT * from master_user_details where id='".$listprescription['doctor_id']."'");
									if($GetCreatedByNames){
										foreach($GetCreatedByNames as $GetCreatedByNamesL){
											$user_code=$GetCreatedByNamesL['user_id'];
										}
									}
									$GetCreatedByName=null;
}*/

        ?>


            <form name="frm" action="ohc-pharmacy_request_save.php" method="POST">
                <div class="w-100 float-left p-2" style=" background-color:#<? echo $_SESSION['tclr'] ?>; color:#fff; font-size:14px; margin-top: 10px;">
                    <div class="w-50 float-left" style="font-size:16px; "><?php echo '' . $prefix . " " . $username . " " . $lastname; ?>
                        <?php echo ' ' . " (" . $emp_id . ")"; ?></div>
                    <div class="w-25 float-left"><?php echo ucfirst($gender) . " " . $age; ?></div>
                    <div class="w-25 float-left"><!--<i class="glyphicon-user"></i> View Profile--></div>
                </div>

                <div class="request-lists box box-bordered box-color list-<?php echo $countListPrescription; ?>-<?php echo $prescriptionMainid; ?>">

                    <div class="w-100 float-left" style="background-color:#999 !Important; font-size:14px; color:#fff; ">

                        <div style="padding: 5px 1%; width: 50%; margin-top: 0 !important; border: 0 !important; float:left;">
                            Prescription ID: <?php echo $user_code . "/" . $from_datep . "/000" . $prescriptionMainid ?>
                        </div>
                        <div style="padding: 5px 1%; width: 10%; margin-top: 0 !important; border: 0 !important; float:right;">
                            <? if ($listprescription['case_id'] != '') {
                            ?>


                                <a onclick="stockreviews('<?php echo $listprescription['case_id']; ?>')"><i class="fa fa-eye" title="View Outpatient Details" alt="Outpatient" style="color:white;"></i></a>

                            <? } ?>
                        </div>
                    </div>
                </div>

                <div class="request-lists box box-bordered box-color list-<?php echo $countListPrescription; ?>-<?php echo $prescriptionMainid; ?>">

                    <div class="border-0">
                        <div class="tab-pane active" id="user">
                            <div class="prescription-table">
                                <?php
                                $tableHeading = '<div class="prescription-tr-table" style="width:100%; padding: 5px 0; float:left; border-bottom: 1px #ad235e solid;color:
 			#' . $_SESSION['tclr'] . '">
	        <div class="prescription-th-table" style="width: 3%;">&nbsp;</div>
            <div class="prescription-th-table" style="width: 20%;">Drug Name</div>
            <div class="prescription-th-table" style="width: 4%; text-align:center;">Days</div>
            <div class="prescription-th-table" style="width: 12%; padding-right:3%">
		    <div style="width: 25%; text-align:center !important; float:left;">
			<img src="https://login.myhealthvalet.in/img/Morning.png" align="absmiddle"></div>
			<div style="width: 25%; text-align:center !important; float:left;">
			<img src="https://login.myhealthvalet.in/img/Noon.png" align="absmiddle"></div>
			<div style="width: 25%; text-align:center !important; float:left;">
			<img src="https://login.myhealthvalet.in/img/Evening.png" align="absmiddle"> </div>
			<div style="width: 25%; text-align:center !important; float:left;">
			<img src="https://login.myhealthvalet.in/img/Night.png" align="absmiddle"></div>
			';
                                $tableHeading .= '</div>

		  <div class="prescription-th-table" style="width: 8%; ">AF/BF</div>
		  <div class="prescription-th-table" style="width: 10%;">Remarks</div>
		  <div class="prescription-th-table" style="width: 8%;">To Issue</div>
		  <div class="prescription-th-table" style="width: 8%;">Available</div>
		  <div class="prescription-th-table" style="width: 12%;">Issued</div>
                  </div>';
                                echo $tableHeading;
                                $prescriptionDetailObj = new ManageUsers();
                                //	echo "select p.*,pp.id as pid,oh.name as phname,md.drug_name,md1.drug_name as substitude_drugname,ds.doctype as drugtypes,dss.doctype as drugintakeconditions from prescription_detail p left outer join master_drugs md on md.id=p.drugs_id left outer join master_drugs md1 on md1.id=p.substitude_drug left outer join doctype_static ds on ds.id=p.drugtype left outer join doctype_static dss on dss.id=p.drugintakecondition left outer join prescription pp on pp.id=p.prescription_id left outer join ohc_pharmay oh on oh.id=pp.fav_pharmacy
                                //where p.prescription_id =".$id." ";
                                $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("select p.*,pp.id as pid,oh.name as phname,md.drug_name,md1.drug_name as substitude_drugname,ds.doctype as drugtypes,dss.doctype as drugintakeconditions from prescription_detail p left outer join master_drugs md on md.id=p.drugs_id left outer join master_drugs md1 on md1.id=p.substitude_drug left outer join doctype_static ds on ds.id=p.drugtype left outer join doctype_static dss on dss.id=p.drugintakecondition left outer join prescription pp on pp.id=p.prescription_id left outer join ohc_pharmay oh on oh.id=pp.fav_pharmacy
where p.prescription_id =" . $id . "  ");

                                if ($prescriptiondetail != 0) {

                                    $getrowcount = count($prescriptiondetail);
                                    echo '<input type="hidden" id="getrowcounts" name="getrowcounts" value="' . $getrowcount . '">';
                                    $countpres = 0;
                                    // print_r($prescriptiondetail);
                                    foreach ($prescriptiondetail as $listprescriptiondetail) {
                                        $pres_id = $listprescriptiondetail['prescription_id'];
                                        $result = $prescriptionDetailObj->listDirectQuery("SELECT * FROM prescription WHERE id = " . $pres_id);
                                        $phar_id = $result[0]["fav_pharmacy"];
                                        $sql = "SELECT * FROM pharmacy_stock_detail WHERE drug_template_id = " . $listprescriptiondetail['drug_template_id'] . " AND phar_ids = " . $phar_id;
                                        //echo $sql;
                                        $result = $prescriptionDetailObj->listDirectQuery($sql);

                                        $mergedResult = [];
                                        foreach ($result as $row) {
                                            $key = $row['drug_template_id'] . '_' . $row['drug_name'];
                                            if (!isset($mergedResult[$key])) {
                                                $mergedResult[$key] = $row;
                                            } else {
                                                $mergedResult[$key]['cur_availability'] += $row['cur_availability'];
                                            }
                                        }

                                        // Convert merged result back to an indexed array
                                        $finalResult = array_values($mergedResult);

                                        // print_r($finalResult);

                                        $countpres = $countpres + 1;
                                        $bg = ($countpres % 2) ? "odd-row" : "even-row";
                                        echo '<input type="hidden" id="remaind" name="remaind[]" value="' . $listprescriptiondetail['remained_medicine'] . '">';
                                        echo '<input type="hidden" name="phrid" value="' . $phrid . '"/>';
                                        echo '<input type="hidden" name="uid" value="' . $user_id . '"/><input type="hidden" name="created_role" value="' . $created_role . '"/>'; ?>

                                        <div class="prescription-tr-table <?php echo $bg; ?>" style="width: 100%; float: left; padding: 3px 0; border-bottom: 1px #ccc dashed;">
                                            <div class="prescription-td-table" style="width:3%; text-align: center !important;"><input type="hidden" name="presid" id="presid" value="<? echo $id; ?>" />
                                                <input type="hidden" name="rowid[]" id="rowid" value="<?php echo $listprescriptiondetail['id']; ?>" />&nbsp;<?php echo $countpres; ?>
                                            </div>
                                            <input type="hidden" name="tid[]" value="<?php echo $listprescriptiondetail['drug_template_id'] ?>">
                                            <div class="prescription-td-table" style="width:20%;">
                                                <?php $listprescriptiondetail['prescription_type'];
                                                echo ($listprescriptiondetail['drugs_name'] == "" ? "N/A" : $listprescriptiondetail['drugs_name']); ?>
                                                <?php echo ($listprescriptiondetail['substitude_drugname'] != "") ? ('<i> (sub: ' . $listprescriptiondetail['substitude_drugname'] . ')</i>') : "";  ?>
                                            </div>
                                            <div class="prescription-td-table" style="width: 4%; text-align: center !important;"><?php echo $listprescriptiondetail['prescribed_for_days']; ?>&nbsp;
                                            </div>
                                            <div class="prescription-th-table" style="width: 12%; color: #ad235e; padding-right:3%">

                                                <div class="prescription-td-table" style="width: 25%; text-align:center !important;" title="Morning (AM)">
                                                    <?php echo ($listprescriptiondetail['drugmorning'] == "" ? "0" : $listprescriptiondetail['drugmorning']); ?>
                                                </div>

                                                <div class="prescription-td-table" style="width: 25%; text-align:center !important;" title="Noon">
                                                    <?php echo ($listprescriptiondetail['drugafternoon'] == "" ? "0" : $listprescriptiondetail['drugafternoon']); ?>
                                                </div>

                                                <div class="prescription-td-table" style="width: 25%; text-align:center !important;" title="Evening">
                                                    <?php echo ($listprescriptiondetail['drugevening'] == "" ? "0" : $listprescriptiondetail['drugevening']); ?>
                                                </div>
                                                <div class="prescription-td-table" style="width: 25%; text-align:center !important;" title="Night (PM)">
                                                    <?php echo ($listprescriptiondetail['drugnight'] == "" ? "0" : $listprescriptiondetail['drugnight']); ?>
                                                </div>
                                            </div>
                                            <div class="prescription-td-table" style="width:8%;">
                                                <?php echo ($listprescriptiondetail['drugintakeconditions'] == "" ? "N/A" : $listprescriptiondetail['drugintakeconditions']) ?></div>
                                            <div class="prescription-td-table" style="width:10%;">
                                                <?php echo ($listprescriptiondetail['remarks'] == "" ? "N/A" : $listprescriptiondetail['remarks']); ?></div>
                                            <? if ($listprescriptiondetail['prescription_type'] == "1" || $listprescriptiondetail['prescription_type'] == "0") { ?>
                                                <div class="prescription-td-table" style="width:8%;">

                                                    <?php $countp = $listprescriptiondetail['drugmorning'] + $listprescriptiondetail['drugafternoon'] + $listprescriptiondetail['drugevening'] + $listprescriptiondetail['drugnight']; ?>

                                                    <?php

                                                    $today = date('Y-m-d');
                                                    $medicines = "SELECT psd.id,psd.drug_name,psd.r_pharmacy_id,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_quantity FROM `pharmacy_stock_detail` psd LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id LEFT OUTER JOIN `master_corporate` mp ON mp.id = pssd.r_pharmacy_id WHERE  psd.isactive='1' and psd.phar_ids='" . $phrid . "' and psd.ohc='1'  and psd.drug_expiry_date > '$today' GROUP BY psd.id having psd.r_pharmacy_id='" . $_SESSION['ohc_loca'] . "' AND ifnull(balance_quantity,0)>0  order by psd.drug_expiry_date,psd.id ASC";

                                                    $tablets = $prescriptionDetail->listDirectQuery($medicines);

                                                    // foreach ($tablets as $key => $lists) {

                                                    //     $tabnms[$key] = trim($lists['drug_name']);
                                                    //     $balser[$key] = trim($lists['balance_quantity']);
                                                    //     $tabserid[$key] = trim($lists['id']);
                                                    //     $idbal = $lists['id'] . "," . $lists['balance_quantity'];
                                                    //     echo '<input type="hidden" name="tabids[' . $key . ']" value="' . $idbal . '"/>';

                                                    //     //	echo'	<input type="hidden" name="blnc['.$key.']" value="'.$lists['balance_quantity'].'"/>';
                                                    //     //	echo '	<input type="hidden" name="amt['.$key.']" value="'.$lists['amount_per_tab'].'"/>';

                                                    // }
                                                    //echo "<pre>";

                                                    $tabnm = array_unique($tabnms); //print_r($tabnm);
                                                    if ($listprescriptiondetail['remained_medicine'] != "0") {
                                                        $totmedcount = $listprescriptiondetail['remained_medicine'];
                                                    } else {
                                                        $totmedcount = 0;
                                                    }

                                                    echo $totmedcount;

                                                    ?></div>
                                                <div class="prescription-td-table" style="width:8%;">
                                                    <?  //if ($listprescriptiondetail['remained_medicine']!="0"){
                                                    $drugs_name1 = explode("--", $listprescriptiondetail['drugs_name']);


                                                    $msql = "SELECT psd.id,psd.drug_name,psd.r_pharmacy_id,mp.reminder_issue,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_quantity
				FROM `pharmacy_stock_detail` psd
				LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
				LEFT OUTER JOIN `master_corporate` mp ON mp.id = pssd.r_pharmacy_id
				WHERE  psd.isactive='1' and psd.phar_ids='" . $phrid . "' and psd.ohc='1' and psd.drug_name LIKE '%" . trim($drugs_name1[0]) . "'  and psd.drug_expiry_date > '$today' GROUP BY psd.id having psd.r_pharmacy_id='" . $_SESSION['ohc_loca'] . "' AND ifnull(balance_quantity,0)>0  order by psd.drug_expiry_date,psd.id ASC";

                                                    // $drugdetails = $prescriptionDetailObj->listDirectQuery($msql);
                                                    // if (!empty($drugdetails)) {
                                                    //     $arr = array();

                                                    //     // Initialize the hidden input value
                                                    //     $mekey = "";

                                                    //     // Loop through drug details
                                                    //     foreach ($drugdetails as $list) {
                                                    //         $dname = $list['drug_name'];
                                                    //         $b_qty = $list['balance_quantity'];

                                                    //         // Store balance quantities in the array
                                                    //         if (!isset($arr[$dname])) {
                                                    //             $arr[$dname] = array();
                                                    //         }
                                                    //         $arr[$dname][] = $b_qty;

                                                    //         // Find the key in the table names array
                                                    //         $mekey = array_search($dname, $tabnm);
                                                    // 
                                                    ?>
                                                    <!-- <input type="hidden" name="medc[]" value="<?php echo htmlspecialchars($mekey); ?>" /> -->
                                                    <?php
                                                    //     }

                                                    //     // Initialize total quantity
                                                    //     $total_qty = 0;

                                                    //     // Sum up quantities for each drug
                                                    //     foreach ($arr as $aky => $aval) {
                                                    //         $total_qty = array_sum($aval);
                                                    //         // Output the total quantity (if you want to echo it per drug)
                                                    //         echo $total_qty;
                                                    //     }

                                                    //     // Set the total quantity to the last processed balance quantity
                                                    //     $total_qty = $b_qty;
                                                    //     echo $total_qty;

                                                    //     echo '<input type="hidden" name="tobeissued[]" id="tobeissued" value="' . $totmedcount . '">';
                                                    // } else {
                                                    //     echo $total_qty = 0;
                                                    // }
                                                    echo $finalResult[0]['cur_availability'];
                                                    ?>
                                                </div>
                                                <div class="prescription-td-table" style="width:8%;">
                                                    <?php if ($finalResult[0]['cur_availability'] > 0) {
                                                        if ($finalResult[0]['cur_availability'] < $totmedcount) {
                                                            $totVal = $finalResult[0]['cur_availability'];
                                                        } else if ($totmedcount < $finalResult[0]['cur_availability']) {
                                                            $totVal = $totmedcount;
                                                        }
                                                    ?>
                                                        <input type="number" name="quanty[]" id="quanty" min="1" max="<? echo $totVal; ?>" style="width:40%;" oninput="validateInput(this)">

                                                        <script>
                                                            function validateInput(input) {
                                                                if (input.value > parseInt(input.max)) {
                                                                    alert("The entered value exceeds the maximum allowed quantity.");
                                                                    input.value = '';
                                                                }
                                                            }
                                                        </script> <?php } ?>
                                                </div>
                                                <!-- <?
                                                        if ($total_qty < $totmedcount) {
                                                            $totmedcount = $total_qty;
                                                        }

                                                        if ($mainphr[0]['id'] != $phrid && $total_qty != 0 && $totmedcount != 0) { ?>
                                                        <input type="number" name="quanty[]" id="quanty" min="1" max="<? echo $totmedcount; ?>" style="width:40%;"> <? } ?>

                                                </div>
                                            <? } else { ?>
                                                <div class="prescription-td-table" style="width:24%;">
                                                    <b>Outside Prescription</b>
                                                </div>
                                            <? } ?> -->
                                        </div>

                                    <?
                                    } ?>
                                    <div class="prescription-td-table" style="float:right;">
                                        <? if ($mainphr[0]['id'] != $phrid) { ?>
                                            <!--<input type="submit" name="issuetab-<? echo $id; ?>" value="Issue Partly"  style="color:white;background-color:#0088cc;" class="btn btn-sm">-->
                                            <input type="submit" name="close" value="Medicine Issue" style="color:white;background-color:#0088cc;" class="btn btn-sm">
                        <? }
                                    }

                                    echo '</div></div></div></div></div></form>';
                                }
                            } else {
                                echo "No data";
                            }
                            echo '</div>';
                        } ?>
                        <script>
                            function stockreviews(ohc_id) {

                                $.fn.colorbox({
                                    href: "outpatient_details.php?sid=" + ohc_id,
                                    iframe: true,
                                    open: true,
                                    innerWidth: 1000,
                                    innerHeight: 1000,
                                    onOpen: function() {
                                        $('#cboxContent').removeClass("cboxContentCls");
                                    }
                                });
                            }
                        </script>