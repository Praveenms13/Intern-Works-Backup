<?php

include_once("session.php");
include_once("core/class.manageUsers.php");
extract($_REQUEST);
$phar_ids = $_POST['phar_ids'];
$locs = $_POST['location_id'];
$ohc = 1;
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit;
$quantity = $_POST['quantity'];
if (!preg_match('/^[+-]\d+$/', $quantity)) {
    $quantity_instead = 0;
}
$corp_id = $_POST['corp_id'];
$drug_template_id = $_POST['drugtemplateid'];
$drug_name = $_POST['drug-name'];
$drug_type = $_POST['drug_type'];
$inventory = $_POST['inventory'];
$hsncode = $_POST['hsncode'];
$created_on = $_POST['created_on'];
$created_role = $_POST['created_role'];
$created_by = $_POST['created_by'];
$drug_strength = $_POST['drug_strength'];
$schedule = $_POST['schedule'];
$idno = $_POST['idno'];
$crd = $_POST['crd'];
$bill_status = $_POST['bill_status'];
$drug_manifaturer = $_POST['drug_manifaturer'];
$cur_availability = $_POST['quantity'];
$tbs_userid = 2;
$tbs_role = $_POST['created_role'];
$created_type = $_POST['created_on'];
$drug_ingredient = $_POST['drug_ingredient'];
$quantity = $_POST['quantity'];
$drug_manifaturerdate = $_POST['drug_manifaturer_date'];
$drug_expirydate = $_POST['drug_expiry_date'];
$drug_batch = $_POST['drug_batch'];
$amount_per_strip = $_POST['amount_per_strip'];
$amount_per_tab = $_POST['amount_per_tab'];
$tablet_qty_strip = $_POST['tablet_qty_strip'];
$unit_per_tab = $_POST['unit_per_tab'];
$sgst = $_POST['sgst'];
$cgst = $_POST['cgst'];
$igst = $_POST['igst'];
$discount = $_POST['discount'] ? $_POST['discount'] : 0;
$discount_settings = $_POST['discount_settings'];
$isactive = 1;
$pharma_id = $_SESSION['ohc_loc'];
$date = DateTime::createFromFormat('d/m/Y', $drug_manifaturerdate);
$drug_manifaturer_date = $date->format('Y-m-d');
$dateer = DateTime::createFromFormat('d/m/Y', $drug_expirydate);
$drug_expiry_date = $dateer->format('Y-m-d');

//$created_on=date('Y-m-d');
$submit = $_POST['submit'];
$update = $_POST['update'];
$record_id = $_POST['record_id']; // Assuming you get the record ID from the form
if (isset($submit)) {
    $sql = "INSERT INTO `pharmacy_stock_detail`
(cur_availability, r_pharmacy_id, drug_name, drug_ingredient, drug_manifaturer, drug_batch, drug_manifaturer_date, drug_expiry_date, drug_type, drug_strength, inventory, amount_per_tab, tablet_qty_strip, unit_per_tab, amount_per_strip, quantity, discount, discount_settings, schedule, isactive, created_type, created_on, created_role, created_by, ohc, sgst, cgst, igst, phar_ids, idno, hsncode,drug_template_id)
VALUES
('$cur_availability', '$pharma_id', '$drug_name', '$drug_ingredient', '$drug_manifaturer', '$drug_batch', '$drug_manifaturer_date', '$drug_expiry_date', '$drug_type', '$drug_strength', '$inventory', '$amount_per_tab', '$tablet_qty_strip', '$unit_per_tab', '$amount_per_strip', '$quantity', '$discount', '$discount_settings', '$schedule', '$isactive', 1, now(), '$created_role', '$created_by', '$ohc', '$sgst', '$cgst', '$igst', '$phar_ids', '$idno', '$hsncode','$drug_template_id')";
    $_SESSION["errmsg"] = "Added Successfully";
    $insertSqlObj = new ManageUsers();
    $id = $insertSqlObj->AddDirectQuery($sql); 

    $insertSqlObj = null;
    ?>
    <script>
        window.location.href = "add-stock-pharma.php";
    </script>
    <?php
    // header("Location:/add-stock-pharma.php");
} elseif (isset($update)) { 
    // take the existing quantity and save it in a variable
    $sql = "SELECT quantity, cur_availability FROM `pharmacy_stock_detail` WHERE id='$record_id'";
    $selectSqlObj = new ManageUsers();
    $result = $selectSqlObj->listDirectQuery($sql);
    $quantity_sign = substr($quantity, 0, 1);
    if ($quantity_sign == '+' || $quantity_sign == '-') {
        $quantity = substr($quantity, 1);
        $cur_availability = $result[0]['cur_availability'];
        $new_Quantity = $quantity_sign == '+' ? $result[0]['quantity'] + $quantity : $result[0]['quantity'] - $quantity;
        $new_cur_availability = $quantity_sign == '+' ? $result[0]['cur_availability'] + $quantity : $result[0]['cur_availability'] - $quantity;
        $quantity = $new_Quantity;
        $cur_availability = $new_cur_availability;
    } else {
        $cur_availability = $result[0]['cur_availability'];
        $quantity = $quantity;
    }
    // echo "New Quantity: " . $quantity . "<br>";
    // echo "New Availability: " . $cur_availability . "<br>";
    // exit;
    $sql = "UPDATE `pharmacy_stock_detail` SET
    quantity='$quantity',
    cur_availability='$new_cur_availability',
    drug_manifaturer_date='$drug_manifaturer_date',
    drug_expiry_date='$drug_expiry_date',
    drug_batch='$drug_batch',
    amount_per_strip='$amount_per_strip',
    amount_per_tab='$amount_per_tab',
    tablet_qty_strip='$tablet_qty_strip',
    unit_per_tab='$unit_per_tab',
    sgst='$sgst',
    cgst='$cgst',
    igst='$igst',
    discount = '$discount',
    discount_settings='$discount_settings',
    modified_type=1,
    modified_on=now(),
    modified_role='$created_role',
    modified_by='$created_by'
    WHERE id='$record_id'";


    $_SESSION["errmsg"] = "Updated Successfully";
    $updateSqlObj = new ManageUsers();
    $id = $updateSqlObj->AddDirectQuery($sql);

    $updateSqlObj = null;
    ?>
    <script>
        window.location.href = "add-stock-pharma.php";
    </script><?php
    // header("Location:/add-stock-pharma.php?id=$record_id");
}
