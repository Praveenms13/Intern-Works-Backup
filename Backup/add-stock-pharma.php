<?php

/* 
Ajax File Called
				ajax/ajax_pharmacy_stock_avail.php (getListofDatas)
            	ajax/ajax_pharmacy_stock_avail.php (movenext)
	            ajax/ajax_pharmacy_stock_avail.php (moveprevious)
	            ajax/ajax_pharmacy_stock_avail.php (makesavechange)



Included Files
        header.php                         core/class.manageUsers.php                              close.php
        top-menu.php              left-nav.php
End */

?>


<?php 

include_once('header.php');
include_once('ohc_session.php');
include_once('top-menu.php');
include_once('left-nav.php');
include_once('core/class.manageUsers.php');
include_once('core/class.manageSettings.php');
$id=$_GET['id'];

$prescriptionDetailObj = new ManageUsers();
$prescriptions=$prescriptionDetailObj->listDirectQuery("SELECT psd.* FROM `pharmacy_stock_detail` psd 
WHERE psd.id='".$id."'");

if(!empty($_SESSION['ohc_loca'])){
	$gst=$prescriptionDetailObj->listDirectQuery("SELECT sgst,cgst,igst FROM `master_corporate`  WHERE id='".$_SESSION['ohc_loca']."'");
}else{

	$gst=$prescriptionDetailObj->listDirectQuery("SELECT sgst,cgst,igst FROM `master_pharmacy`  WHERE
	id='".$sessionlocation_id."' ");
}
$prescriptionDetailObj = null;
if($prescriptions!=0){
	$drug_name=$prescriptions[0]['drug_name'];
	$drug_ingredient=$prescriptions[0]['drug_ingredient'];
	$drug_manifaturer=$prescriptions[0]['drug_manifaturer'];
	$drug_manifaturer_date=explode("-",$prescriptions[0]['drug_manifaturer_date']);
	$drug_manifaturer_date=($drug_manifaturer_date[2]<>"")?($drug_manifaturer_date[2]."/".$drug_manifaturer_date[1]."/".$drug_manifaturer_date[0]):"";
	$drug_batch=$prescriptions[0]['drug_batch'];
	$drug_expiry_period=$prescriptions[0]['drug_expiry_period'];
	$drug_expiry_date=explode("-",$prescriptions[0]['drug_expiry_date']);
	$drug_expiry_date=($drug_expiry_date[2]<>"")?($drug_expiry_date[2]."/".$drug_expiry_date[1]."/".$drug_expiry_date[0]):"";
	$drug_type=$prescriptions[0]['drug_type'];
	$drug_strength=$prescriptions[0]['drug_strength'];
	$inventory=$prescriptions[0]['inventory'];
	$amount_per_tab=$prescriptions[0]['amount_per_tab'];
	$amount_per_strip=$prescriptions[0]['amount_per_strip'];
	$tablet_qty_strip=$prescriptions[0]['tablet_qty_strip'];
	$unit_per_tab=$prescriptions[0]['unit_per_tab'];
	$quantity=$prescriptions[0]['quantity'];
	$discount=$prescriptions[0]['discount'];
	$isactive=$prescriptions[0]['isactive'];
	$drugtype=$prescriptions[0]['drug_type'];
	$schedule=$prescriptions[0]['schedule'];
	
	$discount_settings=$prescriptions[0]['discount_settings'];
}
if(!empty($_REQUEST['id'])){
$sgst= $prescriptions['sgst'];
$cgst= $prescriptions['cgst'];
$igst= $prescriptions['igst'];
	}else{
echo $sgst= $gst[0]['sgst'];
$cgst= $gst[0]['cgst'];
$igst= $gst[0]['igst'];
	}
?>
<script>
function isAmount(evt,thisElement) {
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (
		(charCode != 45 || $(thisElement).val().indexOf('-') != -1) && 
		(charCode != 46 || $(thisElement).val().indexOf('.') != -1) &&
		(charCode < 48 || charCode > 57)
		)
	return false;
	return true;
}
function getDiscount(thisAttribute){
	$(".modal").css("display","block");
	if($(thisAttribute).is(":checked")){
		var action="getDiscount";
		$.ajax({
			url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_data.php",
			cache: false,
			data: 'action='+action,
			type: 'post',
			success: function(data) {
				$("#discount").val($.trim(data));
				$("#discount").attr("readonly","readonly")
				$(".modal").css("display","none");
			}
		});
	} else {
		$("#discount").val(0);
		$("#discount").removeAttr("readonly");
		$(".modal").css("display","none");
	}
}

function pharmacyUploadXls(thisAttr) {
	$(".modal").css("display","block");
	var file_data = $(thisAttr).prop('files')[0];
	if(file_data.name.toLowerCase().lastIndexOf(".csv")==-1){
		$(".modal").css("display","none");
		alert("Required .csv Format File");
	} else {
		$(thisAttr).css("disabled","disabled");
		var form_data = new FormData();
		if (form_data) {form_data.append("file", file_data);form_data.append("action","uploadXls");}
		$.ajax({
			url: 'ajax/ajax_pharmacy_data.php',
			dataType: 'text',
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function(data) {
				document.location.href="pharmacy_stock_avail_upload.php?id="+$.trim(data);
				$('.modal').css('display', 'none');
			},
			error: function(data) {
				alert("Sorry, Please try again");
				$('.modal').css('display', 'none');
			}
		});
	}
}
$(document).ready(function(){
	$(".modal").css("display","block");
	var action="existUploadXls";
	$.ajax({
		url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_data.php",
		cache: false,
		data: 'action='+action,
		type: 'post',
		success: function(data) {
			$("#existUploadXls").html(data);
			$('.modal').css('display', 'none');
		},
		error: function(data) {
			$('.modal').css('display', 'none');
		}
	});
});

function calculateAmount(){
	var amount_per_strip=$("#amount_per_strip").val();
	var tablet_qty_strip=$("#tablet_qty_strip").val();
	var totalTablet=(amount_per_strip/tablet_qty_strip).toFixed(2);
	if(totalTablet>0 && totalTablet!=Infinity){
		$("#amount_per_tab").val(totalTablet);
	}
}
function calculateExpiry(thisValue){
	if(thisValue!=""){
		var action="calculateExpiry";
		$(".modal").css("display","block");
		var manufacturDate=$('input#drug_manifaturer_date').val();
		if(manufacturDate==""){
			alert("Required Manufacturer Date");
			$('.modal').css('display', 'none');
			$('input#drug_manifaturer_date').focus();
		} else {
			var expiryPeriod=thisValue;
			var dataString ={manufacturDate:manufacturDate,expiryPeriod:expiryPeriod,action:action}
			$.ajax({
				url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_data.php",
				cache: false,
				data: dataString,
				type: 'post',
				success: function(data) {
					$("#drug_expiry_date").val($.trim(data));
					$('.modal').css('display', 'none');
				},
				error: function(data) {
					$('.modal').css('display', 'none');
				}
			});	
		}
	}
}





function bringDrug(searchElement,searchValue,e){
		var hiddenValToPass=$(".hiddenValToPass").val();
		if(!hiddenValToPass || hiddenValToPass!=searchValue){
			var searchId=$(searchElement).parent().closest("tr").attr("id");
			e.stopPropagation();
			$.ajax({
				url: "ajax/ajax_addohc-pres-temp.php",
				cache: false,
				data: 'searchValue='+searchValue+'&searchId='+searchId+'&action=bringDrug',
				type: 'post',
				success: function(data) {	
					if($.trim(data).match(/notfound/g)){
						splittingDataDrug=$.trim(data).split("^^^");
						$(searchElement).next().css("display","block");
						$(searchElement).next().html('Click here to add this drug');
						$(searchElement).next().attr("onClick","openDrugAdd('"+splittingDataDrug[2]+"','"+splittingDataDrug[1]+"',this)");
					} else {
						$(searchElement).next().css("display","block");
						$(searchElement).next().html(data);
						$(searchElement).next().attr("onClick","");
					}
					
					if(!hiddenValToPass){
						if($("body").next().hasClass("hiddenValToPass")){
							$(".hiddenValToPass").val(searchValue);
						} else {
							$("body").after("<input type='hidden' class='hiddenValToPass' value='"+searchValue+"'/>");
						}
					} else {
						$(".hiddenValToPass").val(searchValue);
					}
				}
			});
		}
	}

function bringmedcountp(dt,$rw){
var id=dt;
var action="bringdrugcount";
var dataString={dvalue:id,action:action};
 	$.ajax
		({ 
			type: "POST",
			url: "ajax/ajax_addohc-pres-temp.php",
			data : dataString,
			success: function(data){
             
                       //  alert(data); 
                          values=data.split('--');
                         // alert(values[0]);
                          // alert(values[1]);
                       	 $rw.find('.avalcount').html(values[0]);
                         $rw.find('.dtype').html(values[1]+'<input   type="hidden" id="hiddendrugtype" name="hiddendrugtype[]" value="'+values[1]+'" >');
                       //  $rw.find('.dintype').val(values[1]);
		 
			
}


});
}

















</script>
<div id="main" style="padding-top: 30px !important;">
	<div class="Top-Strip ">
					 <h5 style="font-weight:bold; color:#<? echo $_SESSION['tclr'] ?>;">Add Stock Availability</h5>
					 </div>
		
				
					<div class="errmsg"><?php if(is_array($_SESSION["errmsg"])){} else { echo $_SESSION["errmsg"]; } unset($_SESSION["errmsg"]); ?></div>
				
				<div class="box box-color">
			
						<div class="searchbar border-0">
							<div class="tab-pane w-100 active" id="inbox">
							<form method="post" name="f1_pharmacy_add_stock" action="pharmacy_stock_avail_save.php" onSubmit="return validatePharmacyForm()">
								<div class="row px-2">
								<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										Drug<br>
										<div class="drugname " title="drugname">
										<input type="hidden" id="drugname" onkeypress="return samblockSpecialChar(event)" name="drugname[]" class="mastrdrug"  />
										<?php if(isset($_GET['drug_name'])){ //onKeyUp="nxtassdt(this,this.value)" ?>
										<input type="text" id="hiddendrugname" name="hiddendrugname"  autocomplete="off" onKeyUp="return bringDrug(this,this.value,event)" value="<?php echo $_GET['drug_name']; ?>" class="hiddendrugname input-large capitalize" required />
										<?php }else{ ?>
										<input type="text" id="hiddendrugname" name="hiddendrugname"  autocomplete="off" onKeyUp="return bringDrug(this,this.value,event)" value="<?php echo $drug_name; ?>" class="hiddendrugname input-large capitalize" required />
										<?php } ?>
										<ul class="typeahead-custom dropdown-menu dropdown-menu-custom" style="position:absolute;display:none;top:auto;left:auto;"></ul>
										</div>
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
	<?php
							/*	$tablename ='doctype_static';
								$valuecol ='id';
								$displaycol ='doctype';
								$where ='doctypename_static_id="3"';
								$ischecked = '1';
								$selectedvalue = '184';
								$showDropDowndrugtype = new ManageSettings();
								echo $showDropDowndrugtype->createdropdownvalues($tablename,$valuecol,$displaycol,$ischecked,$selectedvalue,$where);
								$showDropDowndrugtype=null;*/
							?>        	
	
	
</select>
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2"> Quantity<br><input type="text" name="quantity" onkeypress="return samblockSpecialChar(event)" id="quantity" value="<?php echo $quantity; ?>" class="input-large " required /></div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
										M.R.P<br><input type="text" name="amount_per_strip" onkeypress="return samblockSpecialChar(event)" id="amount_per_strip" value="<?php echo $amount_per_strip; ?>" class="input-large " onKeyUp="calculateAmount()" onKeyPress="return isAmount(event,this)" required />
									</div>
									</div>
								<div class="row px-2">
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
										Package Unit<br><input type="text" name="tablet_qty_strip" id="tablet_qty_strip" value="<?php echo $tablet_qty_strip; ?>" class="input-large" onKeyUp="calculateAmount()" onKeyPress="return isAmount(event,this)" required /><br><font class="hint-1">Like no of tablet in a strip / 1 bottle syrup or suspension</font>
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										Unit<br><input type="text" name="unit_per_tab" id="unit_per_tab" value="<?php echo $unit_per_tab; ?>" class="input-large" required /><br><font class="hint-1">Like 1 tablet / 1 bottle syrup or suspension</font>
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										Manufacturer<br>
										<input type="text" name="drug_manifaturer" id="drug_manifaturer" onkeypress="return samblockSpecialChar(event)" value="<?php echo $drug_manifaturer; ?>" class="input-large" required />
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										M.R.P Per Unit<br><input type="text" name="amount_per_tab" id="amount_per_tab" value="<?php echo $amount_per_tab; ?>" class="input-large " readonly="readonly" required />  
									</div>
									</div>
						<div class="row px-2">
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
										Manufacturer Date<br><input type="text" name="drug_manifaturer_date" id="drug_manifaturer_date" value="<?php echo $drug_manifaturer_date; ?>" placeholder="DD/MM/YYYY" class="input-large mask_date_new " required />
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										Expiry Period<br><input type="text" name="drug_expiry_period" id="drug_expiry_period" value="<?php echo $drug_expiry_period; ?>" onBlur="calculateExpiry(this.value)" required class="input-large" placeholder="1 year / 1 month / 1 day" />
									</div>
								<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Expiry Date<br><input type="text" name="drug_expiry_date" id="drug_expiry_date" value="<?php echo $drug_expiry_date; ?>" class="input-large mask_date_new " placeholder="DD/MM/YYYY" required />
									</div>
                                                                <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Batch<br><input type="text" name="drug_batch" id="drug_batch" onkeypress="return samblockSpecialChar(event)" value="<?php echo $drug_batch; ?>" class="input-large " required />
									</div>
</div>
						<div class="row px-2">
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 p-2">
										Ingredient<br>
										<textarea name="drug_ingredient" id="drug_ingredient" onkeypress="return samblockSpecialChar(event)" class="input-large w-100 " rows="1"><?php echo $drug_ingredient; ?></textarea>
									</div>	
									<div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 p-2">
									Strength<br>
										<textarea name="drug_strength" id="drug_strength" class="input-large w-100" rows="1" ><?php echo $drug_strength; ?></textarea>
									</div>
									</div>
						<div class="row px-2">
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Inventory<br><input type="text" name="inventory" id="inventory" value="<?php echo $inventory; ?>" class="input-large " />
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Schedule<br><input type="text" name="schedule" id="schedule" onkeypress="return samblockSpecialChar(event)" value="<?php echo $schedule; ?>"  class="input-large " />
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">Discount<br>
<input type="checkbox" class="float-left mr-2" name="discount_settings" onkeypress="return samblockSpecialChar(event)" id="discount_settings" onChange="getDiscount(this)" value="1" <?php echo (($discount_settings=="1")?"checked":"")?>  /> &nbsp; <input type="text" name="discount" id="discount" value="<?php echo $discount; ?>" class="input-medium " <?php echo (($discount_settings=="1")?"readonly='readonly'":"")?> /><br>
 <p class="hint-1">Click checkbox, to apply overall discount.</p>									</div>	
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2"><br>
<input type="checkbox" name="isactive"  id="isactive" value="1" <?php echo ($isactive=="0")?"":"checked='checked'"; ?> />&nbsp;Active</div>
</div>
										
										<div class="row px-2">		
									<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />

									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
										Idno <br><input type="text" name="idno" onkeypress="return samblockSpecialChar(event)" id="idno" value="" class="input-large">
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										hsncode <br><input type="text" onkeypress="return samblockSpecialChar(event)" name="hsncode" id="hsncode" value="" class="input-large floatleft">
									</div>
									
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
										SGST % <br><input type="text" name="sgst" onkeypress="return samblockSpecialChar(event)" id="sgst" value="<?php echo $sgst; ?>" class="input-large "  />
									</div>
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3  p-2">
										CGST % <br><input type="text" name="cgst" onkeypress="return samblockSpecialChar(event)" id="cgst" value="<?php echo $cgst; ?>" class="input-large "  />
									</div>
								
									<div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 float-left p-2">
										IGST % <br><input type="text" onkeypress="return samblockSpecialChar(event)" name="igst" id="igst" value="<?php echo $igst; ?>" class="input-large "  />
									</div>
						</div>
								<div class="col-6 col-sm-6 col-md-12 col-lg-12 col-xl-12 float-left p-2">
								<?
								if(!empty($_SESSION['ohc_phar'])){?>
								<input type="hidden" name="ohcs" value="1" />
								<? }?>
									<input type="submit" class="btn btn-sm btn-primary" value="<?php echo isset($_GET['id'])?"Save":"Add Stock"; ?>" />
								</div>
							</form>
							</div>
						</div>
					
				</div>
			</div>	
<!--				<div id="existUploadXls"></div>-->
			 
<script>





function nxtassdt(){

		//id="+elementValue+"&action="+elementAction+"&na="+na
		//id="+ids
		var drugname=$('#hiddendrugname').val();
			 $.fn.colorbox({href:"get_prescription.php?id="+drugname,iframe:true,escKey:true, open:true, close:true, innerWidth:500,innerHeight:500}); 
		 }




function validatePharmacyForm() {
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
	if (frm.quantity.value=="") {
		alert("Required Quantity");
		frm.quantity.focus();
		return false;
	 }
	if (frm.amount_per_strip.value=="") {
		alert("Required Amount");
		frm.amount_per_strip.focus();
		return false;
	 }
	if (frm.amount_per_tab.value=="") {
		if (frm.amount_per_strip.value=="") {
			calculateAmount();
		} else {
			alert("Required Amount");
			frm.amount_per_tab.focus();
			return false;
		}
	 }
	if (frm.drug_manifaturer.value=="") {
		alert("Required Manufacturer");
		frm.drug_manifaturer.focus();
		return false;
	 }
	 if (frm.drug_manifaturer_date.value=="") {
		alert("Required Manufacturer Date");
		frm.drug_manifaturer_date.focus();
		return false;
	 }
	if (frm.drug_expiry_period.value=="" || frm.drug_expiry_date.value=="") {
		if(frm.drug_expiry_period.value=="" && frm.drug_expiry_date.value==""){
			alert("Required Expiry Period/Date");
			frm.drug_expiry_date.focus();
			return false;
		}
		if(frm.drug_expiry_period.value!="" || frm.drug_expiry_date.value==""){
			 var thisValue=frm.drug_expiry_period.value;
			 calculateExpiry(thisValue);
		} 
	 }
	 
	if (frm.drug_batch.value=="") {
		alert("Required Batch");
		frm.drug_batch.focus();
		return false;
	 }
	 if (frm.discount.value=="") {
		 frm.discount.value="0";
	}
	 
	 
	 frm.submit();
	 return true;
}

</script>
<script type="text/javascript">
    function samblockSpecialChar(e){
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
        }
</script>
<?php require_once('../close.php')?>
