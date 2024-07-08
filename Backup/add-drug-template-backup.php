<?php 

include_once('header.php');
include_once('top-menu.php');
include_once('left-nav.php');
include_once('core/class.manageUsers.php');
$id=$_GET['id'];

$temDetailObj = new ManageUsers();
$template=$temDetailObj->listDirectQuery("SELECT * FROM `drug_template` 
WHERE id='".$id."'");




$temDetailObj = null;
if($template!=0){
	$drug_name=$template[0]['drug_name'];
	$drug_ingredient=$template[0]['drug_ingredient'];
	$drug_manifaturer=$template[0]['drug_manifaturer'];
	
	$drug_type=$template[0]['drug_type'];
	$drug_strength=$template[0]['drug_strength'];
	$inventory=$template[0]['inventory'];

	$crd=$template[0]['crd'];
	$idno=$template[0]['idno'];
	$hsncode=$template[0]['hsncode'];
	$schedule=$template[0]['schedule'];
	$amount_per_strip=$template[0]['amount_per_strip'];
	$tablet_qty_strip=$template[0]['tablet_qty_strip'];
	$amount_per_tab=$template[0]['amount_per_tab'];
	$sgst=$template[0]['sgst'];
	$igst=$template[0]['igst'];
     $cgst=$template[0]['sgst'];
	$unit_per_tab=$template[0]['unit_per_tab'];
	$discount_settings=$template[0]['discount_settings'];
	$bill_status=$template[0]['bill_status'];
	print_r($bill_status);
	


}

?>
<script>

	function calculateAmount() {
		var amount_per_strip = $("#amount_per_strip").val();
		var tablet_qty_strip = $("#tablet_qty_strip").val();
		var totalTablet = (amount_per_strip / tablet_qty_strip).toFixed(2);
		if (totalTablet > 0 && totalTablet != Infinity) {
			$("#amount_per_tab").val(totalTablet);
		}

</script>
<div id="main" style="padding-top: 50px !important;">
	<div class="Top-Strip ">
					 <h5 style="font-weight:bold; color:#<? echo $_SESSION['tclr'] ?>;">Add Drug Template</h5>
					 </div>
		
				
					<div class="errmsg"><?php if(is_array($_SESSION["errmsg"])){} else { echo $_SESSION["errmsg"]; } unset($_SESSION["errmsg"]); ?></div>
				
				<div class="box box-color">
			
						<div class="searchbar border-0">
							<div class="tab-pane w-100 active" id="inbox">
							<form method="post" name="drug_temp" action="drug_template_save.php" onSubmit="return validatedrugtemplate()">
							    <input type="hidden" name="pharma_id" value="<?php if(isset($_GET["pharma_id"])){ echo $_GET["pharma_id"];}?>">
								<div class="row px-2">
								<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										Drug<br>
										<input type="text" id="hiddendrugname" name="hiddendrugname"  autocomplete="off" onKeyUp="return bringDrug(this,this.value,event)" value="<?php echo $drug_name; ?>" class="input-large capitalize 
										" required />
									</div>
										<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Type<br>
										<!--input type="text" name="drug_type" id="drug_type" value="<?php echo $drug_type; ?>" class="input-large "  /-->
										<select title="Drug Type" name="drug_type" id="drug_type"  class='icon_90 select2-me ' required>
<option value="" >Drug Type</option>
<?
$deobj = new ManageUsers();
$corp=array("id","corp_id");
$injs = $deobj->listDirectQuery("SELECT `COLUMN_NAME` as type
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`='hhrfwxcz_UID' 
    AND `TABLE_NAME`='stock_alert' ");
    
foreach($injs as $inj){
		if (!in_array($inj['type'],$corp)) {
		    $sel='';
		    if($inj['type']==$drug_type) {
		        $sel='selected';
		    }
echo '<option value="'.$inj['type'].'" '.$sel.'>'.$inj['type'].'</option>"';
		}
    }
	?>
</select>
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										Manufacturer<br>
										<input type="text" name="drug_manifaturer" id="drug_manifaturer" value="<?php echo $drug_manifaturer; ?>" class="input-large" required />
									</div>
										<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										HSN Code <br><input type="text" name="hsncode" id="hsncode" value="<?php echo $hsncode; ?>" class="input-large floatleft">
									</div>
									</div>
							
						<div class="row px-2">
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 p-2">
										Ingredient<br>
										<select name="drug_ingredient[]" id="drug_ingredient" class="select2-me input-medium floatleft  acc" title="Ingredient"  multiple placeholder=" Select Ingredient" style=" width:95% !important;" >
										
<?
$ing = new ManageUsers();
$ings=$ing->listDirectQuery(" select *  from ingredients where status='0' ");
$drug_ing=explode("~",$drug_ingredient);
foreach($ings as $ins){
	$sel="";
	if(in_array($ins['id'],$drug_ing)){
		$sel="selected";
	}
	echo $sel;

?>	
<option value="<? echo $ins['id']; ?>"  <? echo $sel; ?> ><? echo $ins['name']; ?></option>
<? } ?>
</select>
										
										
									</div>	
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 p-2">
									Strength<br>
										<textarea name="drug_strength" id="drug_strength" class="input-large w-100" rows="1" ><?php echo $drug_strength; ?></textarea>
									</div>
									</div>
						<div class="row px-2">
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Restock Count<br><input type="text" name="inventory" id="inventory" value="<?php echo $inventory; ?>" class="input-large " />
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Schedule<br><input type="text" name="schedule" id="schedule" value="<?php echo $schedule; ?>"  class="input-large " />
									</div>
									<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />

									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
										Idno <br><input type="text" name="idno" id="idno" value="<?php echo $idno; ?>" class="input-large">
									</div>
										
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2"><br>
<input type="checkbox" name="crd"  id="crd" value="1" <?php echo ($crd=="0")?"":"checked='checked'"; ?> />&nbsp;CRD</div>
</div>
									
										<div class="row px-2">
								<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
							M.R.P<br><input type="text" name="amount_per_strip"
							 id="amount_per_strip"
								value="<?php echo $amount_per_strip; ?>" class="input-large "
								 required />
						</div>
							    <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
							Package Unit<br><input type="text" name="tablet_qty_strip" id="tablet_qty_strip"
								value="<?php echo $tablet_qty_strip; ?>" class="input-large" 
								 required /><br>
							<font class="hint-1">Like no of tablet in a strip / 1 bottle syrup or suspension</font>
						</div>
						<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
							Unit<br><input type="text" name="unit_per_tab" id="unit_per_tab"
								value="<?php echo $unit_per_tab; ?>" class="input-large" required /><br>
							<font class="hint-1">Like 1 tablet / 1 bottle syrup or suspension</font>
						</div>
						
						<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
							M.R.P Per Unit<br><input type="text" name="amount_per_tab" id="amount_per_tab"
								value="<?php echo $amount_per_tab; ?>" class="input-large " readonly="readonly"
								required />
						</div>
							
						</div>
						
							<div class="row px-2">
						<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
							SGST % <br><input type="text" name="sgst" onkeypress="return samblockSpecialChar(event)"
								id="sgst" value="<?php echo $sgst; ?>" class="input-large " />
						</div>
						<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
							CGST % <br><input type="text" name="cgst" onkeypress="return samblockSpecialChar(event)"
								id="cgst" value="<?php echo $cgst; ?>" class="input-large " />
						</div>

						<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 float-left p-2">
							IGST % <br><input type="text" onkeypress="return samblockSpecialChar(event)" name="igst"
								id="igst" value="<?php echo $igst; ?>" class="input-large " />
						</div>
						<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
        Discount<br>
        <input type="text" name="discount" id="discount" value="<?php echo $discount_settings; ?>" class="input-large">
    </div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 p-2">
        Bill<br>
        <input type="radio" name="bill_status" id="bill_yes" value="1" <?php echo ($bill_status == "1") ? "checked" : ""; ?>>Bill
        <input type="radio" name="bill_status" id="bill_no" value="0" <?php echo ($bill_status == "0") ? "checked" : ""; ?>> Not Bill
    </div>
							</div>
						
									
									
									
									
									
									
									
										<div class="row px-2">		
									<?
								if(!empty($_SESSION['ohc_phar'])){?>
								<input type="hidden" name="ohcs" value="1" />
								<? }?>
										<div class="col-6 col-sm-6 col-md-12 col-lg-12 col-xl-12 float-left p-2">
							
								
									<input type="submit" class="btn btn-sm btn-primary" value="<?php echo isset($_GET['id'])?"Save":"Add Template"; ?>" />
								</div>
									
						</div>
					
						
								
							</form>
							</div>
						</div>
					
				</div>
			</div>	
<!--				<div id="existUploadXls"></div>-->
			 
<script>
function validatedrugtemplate() {
	var frm = this.document.f1_pharmacy_add_stock;
	if (frm.hiddendrugname.value=="") {
		alert("Required Drug");
		frm.hiddendrugname.focus();
		return false;
	 }
	if (frm.drug_type.value=="") {
		alert("Required Drug Type");
		frm.drug_type.focus();
		return false;
	 }
	
	if (frm.drug_manifaturer.value=="") {
		alert("Required Manufacturer");
		frm.drug_manifaturer.focus();
		return false;
	 }
	
	 
	 frm.submit();
	 return true;
}

</script>
<?php require_once('../close.php')?>