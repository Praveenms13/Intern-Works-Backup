<?php
include_once("session.php");
include_once("core/class.manageUsers.php");
$obj = new ManageUsers();
extract($_REQUEST);
$insertdata = $obj->listDirectQuery("select * from pharmacy_stock_detail where id='$ids'");
$id = $insertdata[0]['id'];
$drug_name = $insertdata[0]['drug_name'];
$drug_ingredient = $insertdata[0]['drug_ingredient'];
$drug_manifaturer = $insertdata[0]['drug_manifaturer'];
$drug_batch = $insertdata[0]['drug_batch'];
$drug_manifaturer_date = $insertdata[0]['drug_manifaturer_date'];
//$drug_manifaturer_date=($drug_manifaturer_date[2]<>"")?($drug_manifaturer_date[2]."-".$drug_manifaturer_date[1]."-".$drug_manifaturer_date[0]):"";
$drug_expiry_period = $insertdata[0]['drug_expiry_period'];
$drug_expiry_date = $insertdata[0]['drug_expiry_date'];
//$drug_expiry_date=($drug_expiry_date[2]<>"")?($drug_expiry_date[2]."-".$drug_expiry_date[1]."-".$drug_expiry_date[0]):"";
$drug_type = $insertdata[0]['drug_type'];
$drug_strength = $insertdata[0]['drug_strength'];
$inventory = $insertdata[0]['inventory'];
$quantity = $mqty;
$discount = $insertdata[0]['discount'];
$schedule = $insertdata[0]['schedule'];
$sgst = $insertdata[0]['sgst'];
$cgst = $insertdata[0]['cgst'];
$igst = $insertdata[0]['igst'];
$isactive = $insertdata[0]['isactive'];
$tablet_qty_strip = $insertdata[0]['tablet_qty_strip'];
$unit_per_tab = $insertdata[0]['unit_per_tab'];
$amount_per_strip = $insertdata[0]['amount_per_strip'];
$amount_per_tab = $amount_per_strip / $tablet_qty_strip;
$discount_settings = $insertdata[0]['discount_settings'];
$ohc = $insertdata[0]['ohcs'];
$locs = (empty($ohc)) ? $sessionlocation_id : $_SESSION['ohc_loca'];
$phr_id = $mphar;
$created_type = 1;
$hsncode = $insertdata[0]['hsncode'];
$drug_template_id = $insertdata[0]['drug_template_id']; 
//if($drug_name<>"" && $drug_type<>""){
//if($id==0){
if ($insertdata[0]['cur_availability'] > 0) {
    $cur_availability = $insertdata[0]['cur_availability'] - $mqty;
} else if ($insertdata[0]['cur_availability'] == 0) {
    $cur_availability = $mqty;
}
$reducedQuantity = $insertdata[0]['reduced_qty'] + $mqty;
// print_r($_POST); 
// echo "<br> Current Avail: " . $cur_availability;
// echo "Reduced Quantity: " . $reducedQuantity;
$sql = "UPDATE `pharmacy_stock_detail` SET cur_availability = '$cur_availability', reduced_qty = '$reducedQuantity' WHERE id = '$ids'";
// echo $sql;
$insertSqlObj = new ManageUsers();
$insertSqlObj->AddDirectQuery($sql); 
$sql = "INSERT INTO `pharmacy_stock_detail` 
        (r_pharmacy_id, drug_template_id, drug_name, drug_ingredient, drug_manifaturer, drug_batch, drug_manifaturer_date, drug_expiry_period, drug_expiry_date, drug_type, drug_strength, inventory, amount_per_tab, tablet_qty_strip, unit_per_tab, amount_per_strip, quantity, cur_availability, discount, discount_settings, schedule, isactive, created_type, created_on, created_role, created_by, ohc, sgst, cgst, igst, phar_ids, hsncode) 
        VALUES 
        ('$locs', '$drug_template_id', '$drug_name', '$drug_ingredient', '$drug_manifaturer', '$drug_batch', '$drug_manifaturer_date', '$drug_expiry_period', '$drug_expiry_date', '$drug_type', '$drug_strength', '$inventory', '$amount_per_tab', '$tablet_qty_strip', '$unit_per_tab', '$amount_per_strip', '$quantity', '$quantity', '$discount', '$discount_settings', '$schedule', '$isactive', '$created_type', '$datetime', '$tbs_role', '$tbs_userid', '1', '$sgst', '$cgst', '$igst', '$phr_id', '$hsncode')";
$_SESSION["errmsg"] = "Added Successfully";
$id = $insertSqlObj->AddDirectQuery($sql);
$sql1 = "INSERT INTO `pharmacy_sold_stock_detail`(r_pharmacy_id,r_user_id,r_appuser_walkin_id,stock_generate_id,r_stock_id,qty,amount,discount,created_by,created_role,created_on,ohc) 
		VALUES ('" . $_SESSION['ohc_loca'] . "','" . $user_id . "','3','" . $stockGenerationId . "','" . $ids . "','" . $quantity . "','" . $amount_per_tab . "','" . $discount . "','" . $tbs_userid . "','" . $tbs_role . "','" . $datetime . "','1')";
$id = $insertSqlObj->AddDirectQuery($sql1);
/*	} else {
if(empty($_SESSION['ohc_loca'])){
$lc=$sessionlocation_id;
}else{
$lc=$_SESSION['ohc_loca'];
}
		$sql="UPDATE `pharmacy_stock_detail` SET drug_name='".$drug_name."',drug_ingredient='".$drug_ingredient."',drug_manifaturer='".$drug_manifaturer."',drug_batch='".$drug_batch."',drug_manifaturer_date='".$drug_manifaturer_date."',drug_expiry_period='".$drug_expiry_period."',drug_expiry_date='".$drug_expiry_date."',drug_type='".$drug_type."',drug_strength='".$drug_strength."',inventory='".$inventory."',amount_per_tab='".$amount_per_tab."',tablet_qty_strip='".$tablet_qty_strip."',unit_per_tab='".$unit_per_tab."',amount_per_strip='".$amount_per_strip."',quantity='".$quantity."',discount='".$discount."',discount_settings='".$discount_settings."',schedule='".$schedule."',isactive='".$isactive."',modified_type='".$created_type."',modified_on='".$datetime."',modified_role='".$tbs_role."',modified_by='".$tbs_userid."',sgst='$sgst',cgst='$cgst',igst='$igst' WHERE id='".$id."' AND r_pharmacy_id='".$lc."'";
		$_SESSION["errmsg"]="Updated Successfully";
		$insertSqlObj = new ManageUsers();
		$insertSql = $insertSqlObj->listDirectQuery($sql);
		$insertSqlObj=null;
	}
} else {
	$_SESSION["errmsg"]="Required Fields";
}*/
?>
<script>
    document.location.href = "stock-move-pharma.php";
</script>