<?php

include_once('session.php');
include_once('core/class.manageUsers.php');
include_once('dateconvert.php');
include_once('sendmail/conform_registration.php');

$datetime = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$days = date('N', strtotime(date('Y-m-d')));
extract($_REQUEST);

echo "<pre>";
print_r($_POST);
echo "</pre>";

$sqlDataObj = new ManageUsers();

if ($close == "Medicine Issue") {
    $tid_array = $_POST['tid'];
    $quanty_array = $_POST['quanty'];
    // $quanty_array = array(
    //     0 => 25,
    //     1 => 30,
    // );
    $phrid = intval($_POST['phrid']);
    $prescriptiondetail = $sqlDataObj->listDirectQuery("UPDATE prescription SET fav_pharmacy_order='2' WHERE id=" . $presid);

    for ($i = 0; $i < count($tid_array); $i++) {
        $template_id = intval($tid_array[$i]);
        $quantity_needed = intval($quanty_array[$i]);
        $sql = "SELECT * FROM pharmacy_stock_detail WHERE drug_template_id = $template_id AND phar_ids = $phrid ORDER BY id";
        $result = $sqlDataObj->listDirectQuery($sql);

        if (!$result) {
            echo "No stock found for template ID $template_id.<br>";
            continue;
        }

        echo "Processing template ID $template_id with quantity needed $quantity_needed<br>";
        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        foreach ($result as $res) {
            $current_availability = $res['cur_availability'];
            echo "Current availability: $current_availability . <br>";

            if ($quantity_needed <= 0) {
                break;
            }
            if ($current_availability >= $quantity_needed) {
                echo "Reducing $quantity_needed from current availability of $current_availability.<br>";
                $new_availability = $current_availability - $quantity_needed;
                $quantity_to_insert = $quantity_needed;
                $quantity_needed = 0;
            } else {
                echo "Reducing $current_availability from current availability (not enough to fulfill $quantity_needed).<br>";
                $new_availability = 0;
                $quantity_to_insert = $current_availability;
                $quantity_needed -= $current_availability;
            }

            $sql = "INSERT INTO pharmacy_sold_stock_detail (
                `qty`,
                `r_prescription_id`,
                `r_doctor_id`,
                `created_role`,
                `r_stock_id`,
                `r_pharmacy_id`,
                `r_user_id`,
                `amount`,
                `ohc`,
                `phar_ids`,
                `created_on`,
                `r_appuser_walkin_id`,
                `stock_generate_id`,
                `day`,
                `datereport`,
                `ppid`
            ) VALUES (
                '" . $quantity_to_insert . "',
                '" . $presid . "',
                '" . $_SESSION['userid'] . "',
                '" . $created_role . "',
                '" . $res['id'] . "',
                '" . $_SESSION['ohc_loca'] . "',
                '$uid',
                '$prc',
                '1',
                '" . $phrid . "',
                '$datetime',
                '1',
                '1',
                '$days',
                '$today',
                '" . $phrid . "'
            )";
            $result = $sqlDataObj->listDirectQuery($sql);

            $sql = "UPDATE pharmacy_stock_detail SET cur_availability = '$new_availability' WHERE id = " . $res['id'];
            $result = $sqlDataObj->listDirectQuery($sql);

            $sql = "UPDATE pharmacy_stock_detail SET reduced_qty = '" . ($res['reduced_qty'] + $quantity_to_insert) . "' WHERE id = " . $res['id'];
            $result = $sqlDataObj->listDirectQuery($sql);
            $remind = $remaind[$i] - $quanty_array[$i];
            $sql = "UPDATE prescription_detail SET remained_medicine = " . $remind . " WHERE id = " . $_POST['rowid'][$i];
            $result = $sqlDataObj->listDirectQuery($sql);

            echo "New availability is $new_availability.<br><hr>";
        }

        if ($quantity_needed > 0) {
            echo "Not enough stock available for template ID $template_id. Short by $quantity_needed units.<br>";
        }
    }
}


?>
<script>
    document.location.href = 'request-ohc-pharma.php';
</script>




<?php
exit();

if ($close == "Medicine Issue") {

    $prescriptiondetail = $sqlDataObj->listDirectQuery("update prescription set fav_pharmacy_order='2' where id=" . $presid);
}
$sql = "SELECT ifnull(max(stock_generate_id),0)+1 as stock_generate_id FROM `pharmacy_sold_stock_detail` WHERE r_pharmacy_id='" . $phrid . "'";


$sqlData = $sqlDataObj->listDirectQuery($sql);
$stockid = $sqlData[0]["stock_generate_id"];

$nms = $sqlDataObj->listDirectQuery("SELECT first_name,last_name from master_user_details where id='$uid'");

$sqlDataObj = null;
$nm = $nms[0]['first_name'] . " " . $nms[0]['last_name'];
$messageSMS .= "Dear " . $nm . " , The below medicines have been issued";

//print_r($medc);


$tabidsarr = implode(",", $tabids);
$tabidsid = explode(",", $tabidsarr);

for ($i = 0; $i < count($tabidsid); $i++) {
    if ($i % 2 != 0) {
        $blnc[] = $tabidsid[$i];
    } else {
        $tabid[] = $tabidsid[$i];
    }
}

/*echo "<br><br><br><br>";
print_r($blnc);echo "<br><br><br><br><br>";
print_r($tabid);echo "<br><br><br><br><br>";*/

$quanty = [2];
// print_r($quanty);
// print_r($remaind);
// exit();

foreach ($medc as $key => $val) {

    $qtys = "";
    $prescriptionDetailObj = new ManageUsers();

    $dsq = $prescriptionDetailObj->listDirectQuery("select drug_name from  pharmacy_stock_detail where id='" . $tabid[$val] . "'");

    if ($close == "Medicine Issue" || ($getrowcounts == "1" && $remaind[$key] == $quanty[$key])) {

        $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("update prescription set fav_pharmacy_order='2' where id=" . $presid);
    }
    if (!empty($quanty[$key]) && !empty($blnc[$val])) {

        if ($quanty[$key] > 0 && $tobeissued[$key] > 0) {
            $remain = $tobeissued[$key] - $quanty[$key];
        }

        $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("update prescription_detail set remained_medicine='" . $remain . "' where prescription_id=" . $presid . " and drugs_name='" . $dsq[0]['drug_name'] . "'");
        if ($quanty[$key] > $blnc[$val]) {
            $qtys = $blnc[$val];
        } else {
            $qtys = $quanty[$key];
        }




        if ($quanty[$key] <= $blnc[$val]) {
            //    echo "<br>sun";
            //echo "insert into pharmacy_sold_stock_detail (`qty`,`r_prescription_id`,`r_doctor_id`,`created_role`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('".$qtys."','".$presid."','".$_SESSION['userid']."','".$created_role."','".$tabid[$val]."','".$_SESSION['ohc_loca']."','$uid','$prc','1','".$phrid."','$datetime','1','$stockid','$days',$today,'".$phrid."')" ;
            $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("insert into pharmacy_sold_stock_detail (`qty`,`r_prescription_id`,`r_doctor_id`,`created_role`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('" . $qtys . "','" . $presid . "','" . $_SESSION['userid'] . "','" . $created_role . "','" . $tabid[$val] . "','" . $_SESSION['ohc_loca'] . "','$uid','$prc','1','" . $phrid . "','$datetime','1','$stockid','$days',$today,'" . $phrid . "')");
            $drugs = $prescriptionDetailObj->listDirectQuery("select drug_name from pharmacy_stock_detail where id='" . $tabid[$val] . "'");
            $drnm = trim($drugs[0]['drug_name']);
            $sql = "SELECT psd.*,mp.reminder_issue,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_quantity FROM `pharmacy_stock_detail` psd LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id LEFT OUTER JOIN `master_corporate` mp ON mp.id = pssd.r_pharmacy_id WHERE  psd.isactive='1' and psd.phar_ids='" . $phrid . "' and psd.ohc='1' and psd.drug_expiry_date > '$today' and psd.id='" . $tabid[$val] . "' and psd.drug_name like '%$drnm%' GROUP BY psd.id having balance_quantity > '0' order by psd.drug_expiry_date ASC ";


            $nxt1 = $prescriptionDetailObj->listDirectQuery($sql);
            if (!empty($nxt1)) {
                $remaining_stocks = $nxt1[0]['balance_quantity'];
                $res_drugname = $nxt1[0]['drug_name'];
                $sqlcountfor_stock = "select * from stock_alert where corp_id='" . $_SESSION['parent_id'] . "'";
                $max_count = $prescriptionDetailObj->listDirectQuery($sqlcountfor_stock);
                $corp_nam_location = $prescriptionDetailObj->listDirectQuery("select first_name,last_name,email from master_corporate_user where id=" . $_SESSION['userid']);

                if (!empty($max_count)) {
                    if ($nxt1[0]['drug_type'] == "Capsule") {
                        $drug_count = $max_count[0]['Capsule'];
                    } else if ($nxt1[0]['drug_type'] == "Cream") {
                        $drug_count = $max_count[0]['Cream'];
                    } else if ($nxt1[0]['drug_type'] == "Drops") {
                        $drug_count = $max_count[0]['Drops'];
                    } else if ($nxt1[0]['drug_type'] == "Foam") {
                        $drug_count = $max_count[0]['Foam'];
                    } else if ($nxt1[0]['drug_type'] == "Gel") {
                        $drug_count = $max_count[0]['Gel'];
                    } else if ($nxt1[0]['drug_type'] == "Inhaler") {
                        $drug_count = $max_count[0]['Inhaler'];
                    } else if ($nxt1[0]['drug_type'] == "Injecion") {
                        $drug_count = $max_count[0]['Injecion'];
                    } else if ($nxt1[0]['drug_type'] == "Lotion") {
                        $drug_count = $max_count[0]['Lotion'];
                    } else if ($nxt1[0]['drug_type'] == "Ointment") {
                        $drug_count = $max_count[0]['Ointment'];
                    } else if ($nxt1[0]['drug_type'] == "Powder") {
                        $drug_count = $max_count[0]['Powder'];
                    } else if ($nxt1[0]['drug_type'] == "Shampoo") {
                        $drug_count = $max_count[0]['Shampoo'];
                    } else if ($nxt1[0]['drug_type'] == "Syringe") {
                        $drug_count = $max_count[0]['Syringe'];
                    } else if ($nxt1[0]['drug_type'] == "Syrup") {
                        $drug_count = $max_count[0]['Syrup'];
                    } else if ($nxt1[0]['drug_type'] == "Tablet") {
                        $drug_count = $max_count[0]['Tablet'];
                    } else if ($nxt1[0]['drug_type'] == "Toothpaste") {
                        $drug_count = $max_count[0]['Toothpaste'];
                    } else if ($nxt1[0]['drug_type'] == "Spray") {
                        $drug_count = $max_count[0]['Spray'];
                    }

                    if ($remaining_stocks < $drug_count) {
                        $to = $corp_nam_location[0]['email'];
                        $subject = "Restock alert";

                        $txt = "Dear  " . $corp_nam_location[0]['first_name'] . "  " . $corp_nam_location[0]['last_name'] . ", <br><br> The following drugs/items have gone below the threshold level as on " . date("d-m-Y") . ".The current available stock displayed is the total available stock across both the Main Pharmacy as well as all Sub-Pharmacies.Please initiate restocking process at the earliest.<br><br><br><table class='table' border='1' width='80%' cellpadding='5' cellspacing='1'> <thead> <tr> <th width='25%'>Drug / Item Name</th> <th width='30%'>Manufacturer Name</th> <th width='10%'>Type</th><th width='25%'>Current Available Stock</th> <th width='10%'>Date</th> </tr> </thead> <tbody> <tr> <td align='center'>" . $res_drugname . "</td> <td align='center'>" . $nxt1[0]['drug_manifaturer'] . "</td> <td align='center'>" . $nxt1[0]['drug_type'] . "</td><td align='center'>" . $remaining_stocks . "</td><td align='center'>" . date("d-m-Y") . "</td> </tr></table><br><br><b>Note:</b> For the full list of drugs/items that require restocking, please view the <b>Restock List</b> report from the reports section.<br><br>Regards,<br> myHealthvalet Team";

                        $headers = "From: myhealthvalet@mhv.softlayer.com" . "\r\n";

                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        //mail($to,$subject,$txt,$headers);

                    }
                }
            } else {
            }
        } else {


            $qnty = $quanty[$key] - $blnc[$val];
            $drugs = $prescriptionDetailObj->listDirectQuery("select drug_name from pharmacy_stock_detail where id='" . $tabid[$val] . "'");
            $drnm = trim($drugs[0]['drug_name']);
            $qtys = $qnty;
            //	echo "<br>mon";
            //echo "insert into pharmacy_sold_stock_detail (`qty`,`r_prescription_id`,`r_doctor_id`,`created_role`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('".$blnc[$val]."','".$presid."','".$_SESSION['userid']."','".$created_role."','".$tabid[$val]."','".$_SESSION['ohc_loca']."','$uid','$prc','1','".$phrid."','$datetime','1','$stockid','$days',$today,'".$phrid."')";
            $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("insert into pharmacy_sold_stock_detail (`qty`,`r_prescription_id`,`r_doctor_id`,`created_role`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('" . $blnc[$val] . "','" . $presid . "','" . $_SESSION['userid'] . "','" . $created_role . "','" . $tabid[$val] . "','" . $_SESSION['ohc_loca'] . "','$uid','$prc','1','" . $phrid . "','$datetime','1','$stockid','$days',$today,'" . $phrid . "')");


            $sql = "SELECT psd.*,mp.reminder_issue,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_quantity FROM `pharmacy_stock_detail` psd LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id LEFT OUTER JOIN `master_corporate` mp ON mp.id = pssd.r_pharmacy_id
WHERE  psd.isactive='1' and psd.phar_ids='" . $phrid . "' and psd.ohc='1' and psd.drug_expiry_date > '$today' and psd.id!='" . $tabid[$val] . "' and psd.drug_name like '%$drnm%' GROUP BY psd.id having balance_quantity > '0' order by psd.drug_expiry_date ASC";

            $nxt = $prescriptionDetailObj->listDirectQuery($sql);
            foreach ($nxt as $n) {
                if (!empty($qnty) && $qnty > 0) {
                    $prcs = $qnty * $n['amount_per_tab'];
                    if ($qnty > $n['balance_quantity']) {
                        //   echo "<br>tue";
                        //echo "insert into pharmacy_sold_stock_detail (`qty`,`r_prescription_id`,`r_doctor_id`,`created_role`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('".$qnty."','".$presid."','".$_SESSION['userid']."','".$created_role."','".$n['id']."','".$_SESSION['ohc_loca']."','$uid','$prcs', '1','".$phrid."','$datetime','1','$stockid','$days',$today,'".$phrid."')";
                        $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("insert into pharmacy_sold_stock_detail (`qty`,`r_prescription_id`,`r_doctor_id`,`created_role`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('" . $qnty . "','" . $presid . "','" . $_SESSION['userid'] . "','" . $created_role . "','" . $n['id'] . "','" . $_SESSION['ohc_loca'] . "','$uid','$prcs', '1','" . $phrid . "','$datetime','1','$stockid','$days',$today,'" . $phrid . "')");
                        $qnty = $qnty - $n['balance_quantity'];
                    } else {
                        //   echo "<br>wed";
                        //echo "insert into pharmacy_sold_stock_detail (`qty`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('".$qnty."','".$n['id']."','".$_SESSION['ohc_loca']."','$uid','$prcs', '1','".$_SESSION['phrmcy_id']."','$datetime','0','$stockid','$days',$today,'".$phrid."')";
                        $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("insert into pharmacy_sold_stock_detail (`qty`,`r_stock_id`,`r_pharmacy_id`, `r_user_id`, `amount`,`ohc`,`phar_ids`,created_on,r_appuser_walkin_id,stock_generate_id,day,datereport,ppid) values ('" . $qnty . "','" . $n['id'] . "','" . $_SESSION['ohc_loca'] . "','$uid','$prcs', '1','" . $_SESSION['phrmcy_id'] . "','$datetime','0','$stockid','$days',$today,'" . $phrid . "')");
                        $qnty = $qnty - $n['balance_quantity'];
                    }
                }
            }
        }
        $messageSMS .= $dsq[0]['drug_name'] . " - " . $quanty[$key] . "";
    }
}
$messageSMS .= $crp[0]['corporate_name'] . "-" . $crp[0]['displayname'];
$mobileNumberSMS = $mobnms;
$smsMessageReason = "15";
include_once("sms_notification.php");
?>

<script>
    document.location.href = 'request-ohc-pharma.php';
</script>