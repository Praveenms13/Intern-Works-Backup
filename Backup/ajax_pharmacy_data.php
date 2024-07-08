<?php
 
include('../session.php');
include_once('../core/class.manageSettings.php');
include_once('../core/class.manageUsers.php');
$datetime=date("Y-m-d H:i:s");

if(isset($_POST) && !empty($_POST)){
    
    
	$searchValue=$_POST['searchValue'];
	$action=$_POST['action'];
	$count=0;
	
	 $drug_tempname = $_POST['drug_name'];
	 
	if($drug_tempname){
	    	$drug_temp = new ManageUsers();
	    	$sql= "UPDATE drug_template SET schedule='hello' Where drug_name='$drug_tempname';
	    	$drud=$drug_temp->listDirectQuery($sql);
	    
	}
	if($action=="aboutDrug"){
	    
		$prescriptionMainid=$_POST['searchId'];
				 $tday=date("Y-m-d");
			 $getSqlData = new ManageUsers();
		$stopissues= $getSqlData->listDirectQuery("select reminder_issue from master_pharmacy where id='".$_SESSION['currentlocation_id']."' ");
		$getSqlData = null;
					 $stp="+".$stopissues[0]['reminder_issue']."days";
			$stopiss=date("Y-m-d",strtotime($tday." ".$stp));
		$limit=($searchValue=="")?" LIMIT 5":"";
		echo '<div class="row-fluid row-fluid-style" style="margin:0px; background-color: #fff !important;">';
		$pharma_id=$_POST['pharma_id'];
		if(empty($_SESSION['ohc_loca'])){
		    if(isset($_POST['pharma_id']) && $_POST['pharma_id']!=''){
		        $sql="SELECT psd.r_pharmacy_id,psd.id,psd.drug_name,psd.drug_manifaturer,psd.drug_batch,psd.drug_manifaturer_date,psd.drug_expiry_period,psd.drug_expiry_date,psd.drug_type,psd.drug_strength,psd.inventory,psd.amount_per_tab,psd.isactive,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_qty
		FROM `pharmacy_stock_detail` psd
		LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
		GROUP BY psd.id
		HAVING r_pharmacy_id='".$_POST['pharma_id']."' AND psd.drug_name LIKE '".$searchValue."%' AND psd.drug_expiry_date>='$stopiss' AND psd.isactive=1 AND ifnull(balance_qty,0)>0".$limit;
		    }else{
		$sql="SELECT psd.r_pharmacy_id,psd.id,psd.drug_name,psd.drug_manifaturer,psd.drug_batch,psd.drug_manifaturer_date,psd.drug_expiry_period,psd.drug_expiry_date,psd.drug_type,psd.drug_strength,psd.inventory,psd.amount_per_tab,psd.isactive,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_qty
		FROM `pharmacy_stock_detail` psd
		LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
		GROUP BY psd.id
		HAVING r_pharmacy_id='".$sessionlocation_id."' AND psd.drug_name LIKE '".$searchValue."%' AND psd.drug_expiry_date>='$stopiss' AND psd.isactive=1 AND ifnull(balance_qty,0)>0".$limit;
		    }
		}else{
		    
			$sql="SELECT psd.r_pharmacy_id,psd.id,psd.drug_name,psd.drug_manifaturer,psd.drug_batch,psd.drug_manifaturer_date,psd.drug_expiry_period,psd.drug_expiry_date,psd.drug_type,psd.drug_strength,psd.inventory,psd.amount_per_tab,psd.isactive,psd.ohc,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_qty
		FROM `pharmacy_stock_detail` psd
		LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
		GROUP BY psd.id
		HAVING r_pharmacy_id='".$_SESSION['ohc_loca']."' AND psd.drug_name LIKE '".$searchValue."%' AND psd.drug_expiry_date>='$stopiss' AND psd.ohc='1' and psd.isactive=1 AND ifnull(balance_qty,0)>0".$limit;	
		}
		$drugSearchObj = new ManageUsers();
		$drugSearchs=$drugSearchObj->listDirectQuery($sql);
		$drugSearchObj=null; 
		
		$dep_arr=array();
		
                        $ProfileDetails = new ManageUsers();
                        $user=$ProfileDetails->listDirectQuery("select * from userdependanceaccessrights where accessto_user_id='".$_SESSION["userid"]."'");
                        											if($user!=0){
                        												foreach($user as $key){
                        													$id=$key['accountof_user_id'];
                        													array_push($dep_arr,$id);
                        												}
                        											}
                        
                        $user=$ProfileDetails->listDirectQuery("select * from master_user_details where dependent_of='".$_SESSION["userid"]."'");
                        											if($user!=0){
                        												foreach($user as $key){
                        													$dep_of=$key['dependent_of'];
                        												}
                        											}
                        
                        $user1=$ProfileDetails->listDirectQuery("SELECT * from master_user_details m where m.dependent_of='".$_SESSION["userid"]."' LIMIT 5");
                        												if($user1!=0) {
                        													foreach($user1 as $key){
                        														$id=$key['id'];
                        														array_push($dep_arr,$id);
                        													}
                        												}
                        											
                        											//if(@$dep_of==""){
                        												array_push($dep_arr,$_SESSION["userid"]);
                        										//	}
                        											
                        											$arr_1=array_unique($dep_arr);
                        											$set=implode(", ",$arr_1);
                        	$sql="select *,if(year(now())-year(mu.dob)=0, concat(month(now())-month(dob), ' month'), EXTRACT(YEAR FROM (FROM_DAYS(DATEDIFF(NOW(),dob))))) as masterage from master_user_details mu where id IN($set)";
												//echo $sql;
												$ProfileQ = $ProfileDetails->listDirectQuery($sql);
												$ProfileDetail = new ManageUsers();										
                        											
                        											
						
						if(isset($_POST['pharma_id']) && $_POST['pharma_id']!=''){
						    
						    
						    ?>
						    <div class="content-table float-left">
						        <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 float-left p-1">
        						<select name="link" id="user_id" class='select2-me input-medium' style="width: 90%; float:left; padding: 5px 5px;" onchange="userchange(this.value);">
        								
        								<? foreach($ProfileQ as $singleProfileQ){
        									
        									$ids=$singleProfileQ['id'];
        									$name=ucfirst($singleProfileQ['first_name'])." ".$singleProfileQ['last_name'];
        									echo'<option value="'.$ids.'">'.$name.'</option>';
        									
        								} ?>
        								<option value="others">Others</option>
        								</select>
        						</div>
        						<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 float-left p-1">		
        							<input placeholder="Patient Name" style="display:none; width: 90%; float:left; padding: 5px 5px; height:30px !important;" type="text" name="patient_name" id="patient_name"  />	
        						</div>	
        						<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 float-left p-1">
        							<input placeholder="Doctor Name" style="width: 90%; float:left; padding: 5px 5px; height:30px !important;" type="text" name="doctor_name" id="doctor_name"  />	
        						</div>
        						<div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 float-left p-1">	
        						    <span class="btn btn-sm btn-file" style="width:100%; background-color:#1e74c5;">
                                     <span class="fileupload-new"><i class="glyphicon-paperclip" style="font-size:10px;" class="floatleft"></i> Attach Prescription</span><input onclick="attachmultipresc()" class="btn btn-sm btn-primary" /></span>	
        					    </div>
        					</span>		
        					</div>	
        					
        					
        					<div style="position:fixed;width:450px; top:-440%;left:30%;text-align:center;z-index:99999;" class="openitAttached">
                        	<div class="row-fluid">
                        		<div class="box box-color box-bordered" style="padding:0px 10px;">
                        			<div class="box-title" style="width: 100% !important;
                            background-color: #<? echo $_SESSION['tclr'] ?> !important;
                            border-color: #<? echo $_SESSION['tclr'] ?> !important;">
                        				<h3 style="color:#fff;"><i class="glyphicon-paperclip" style="font-size:10px"></i> Attach</h3>
                        				<div style="float: right; margin-right: 45px;"><a href="javascript:void('0')" class="icon icon-popupcls" onClick="wholedatatopaste()">&nbsp;</a></div>
                        			</div>
                        			<div class="box-content" style="height: 460px;overflow-y:scroll;border-color: #<? echo $_SESSION['tclr'] ?> !important;">
                        				<div>
                        					<div class="control-group wholedatatopaste">
                        						<div style="min-height:150px;">
                        							<ul id="fileListPrescriptionAttachmentId"></ul>
                        							<div class="uploadDataPrescriptionAttachmentId"></div>
                        							<div id="container">
                        								<a id="browsePrescriptionAttachmentId">Browse...</a> 
                        								<a id="uploadPrescriptionAttachmentId" style="color: #<? echo $_SESSION['tclr']; ?>" href="javascript:;">Start Upload</a>
                        							</div>
                        							<pre id="consolePrescriptionAttachmentId"></pre>
                        						</div>
                        					</div>
                        				</div>
                        			</div>
                        		</div>
                        	</div>
                        </div>
        					
        					
		               <?php } ?>
		                     
		
		<?php	echo '<div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 float-left p-1"> <table class="table table-nomargin table-mail" id="tbl-heading" cellpadding=5>
						<tr>
							<th>Drug Name</th>
							<th>Manufacturer & Dates</th>
							<th style="text-align:center;">Available (Rate/pcs)</th>
							<th style="text-align:center;">Order / Value</th>
							<th style="text-align:center;">Add</th>
						</tr>';
			if(is_array($drugSearchs)){	
				foreach($drugSearchs as $drugSearch){ 
					$count=$count+1;
					$drugStockId=$drugSearch['id'];
					$drugName=$drugSearch['drug_name'];
					$balance_qty=$drugSearch['balance_qty'];
					$drug_batch=$drugSearch['drug_batch'];
					$drug_manifaturer=$drugSearch['drug_manifaturer'];
					$drug_manifaturer_date=date("d M Y",strtotime($drugSearch['drug_manifaturer_date']));
					$drug_expiry_period=$drugSearch['drug_expiry_period'];
					$drug_expiry_date=date("d M Y",strtotime($drugSearch['drug_expiry_date']));
					$drug_type=$drugSearch['drug_type'];
					if(strtolower($drug_type)=="capsule" || strtolower($drug_type)=="tablet"){
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico7.png' style='width:18px;' /> ".$drug_type;
					} else if(strtolower($drug_type)=="syrup"){
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico13.png' style='width:18px;' /> ".$drug_type;
					} else if(strtolower($drug_type)=="injection"){
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico5.png' style='width:18px;' /> ".$drug_type;
					} else {
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico15.png' style='width:18px;' /> ".$drug_type;
					}
					$drug_strength=$drugSearch['drug_strength'];
					$inventory=$drugSearch['inventory'];
					$amount_per_tab=round($drugSearch['amount_per_tab'],2);
					$active=($count==1)?"active":"";
					echo '<tr>
							<td data-content="Drug" width=25%><p><b>'.$drugName.'</b></p><p>'.$drug_type.' / <b>'.$drug_batch.'</b></p></td>
							<td width=25%><p><b>'.$drug_manifaturer.'</b></p><p>'.$drug_manifaturer_date.' / '.$drug_expiry_date.'</p></td>
							
							<!--<td data-content="Amt" width=15% align=center>'.$amount_per_tab.'</td>
							<input type="hidden" id="stockCosth'.$drugStockId.'-'.$prescriptionMainid.'" value='.$amount_per_tab.'>
							<td data-content="Qty"><span id="stockBalQty'.$drugStockId.'">'.$balance_qty.'</span></td>-->
							
							<td data-content="Amt" width=15% align=center><span id="stockBalQty'.$drugStockId.'">'.$balance_qty.'</span> ('.$amount_per_tab.')</td>
							<input type="hidden" id="stockCosth'.$drugStockId.'-'.$prescriptionMainid.'" value='.$amount_per_tab.'>
							<td data-content="Qty" align=center>
							<input style="width:30px; text-align:center;" type="text" id="stockQty'.$drugStockId.'-'.$prescriptionMainid.'" value="" class="input-minix" onKeyup=bringPharmacyCalculateQty(event,'.$drugStockId.','.$prescriptionMainid.','.$pharma_id.') /><input type="hidden" id="prescriptionMainid'.$drugStockId.'-'.$prescriptionMainid.'" value="'.$prescriptionMainid.'" class="input-minix"  /><br>
							Rs <span id="stockCost'.$drugStockId.'-'.$prescriptionMainid.'"></span>
							</td>
							<td data-content="Action" align=center>
							';
							if(basename($_SERVER['HTTP_REFERER'])=="pharmacy_billing_add.php"){
								echo '<p><input type="button" value="Add to Cart" class="btn btn-primary" onclick="bringPharmacyAddWalkinUser('.$drugStockId.','.$prescriptionMainid.')" style="margin-top:5px; font-size: 14px; padding: 5px 10px;" /></p>';
							}
							elseif(basename($_SERVER['HTTP_REFERER'])=="corp_pharma_billing_add.php"){
								echo '<p><input type="button" value="Add to Cart" class="btn btn-primary" onclick="bringPharmacyAddWalkinUser('.$drugStockId.','.$prescriptionMainid.')" style="margin-top: 5px; font-size: 14px; padding: 5px 10px;" /></p>';
							}
							else {
								echo '<p><input type="button" value="Add to Cart" class="btn btn-primary" onclick="bringPharmacyAdd('.$drugStockId.','.$prescriptionMainid.','.$pharma_id.')"  style="margin-top:5px; font-size: 14px; padding: 5px 10px;" /></p>';								
							}
							echo '</td>
						 </tr>';
				}
			} else {
				echo "<tr class=\"test\"><td class=\"table-fixed-medium\" colspan=10>No Data to Display.</td></tr>";
			}
		echo '</table>
		</div></div>';
	}
	
	
	if($action=="alternateDrug"){
	    $prescriptionid=$_POST['prescriptionid'];
		$prescriptionMainid=$_POST['searchId'];
				 $tday=date("Y-m-d");
			 $getSqlData = new ManageUsers();
		$stopissues= $getSqlData->listDirectQuery("select reminder_issue from master_pharmacy where id='".$_SESSION['currentlocation_id']."' ");
		$getSqlData = null;
					 $stp="+".$stopissues[0]['reminder_issue']."days";
			$stopiss=date("Y-m-d",strtotime($tday." ".$stp));
		$limit=($searchValue=="")?" LIMIT 5":"";
		echo '<div class="row-fluid row-fluid-style" style="margin:0px; background-color: #fff !important;">';
		if(empty($_SESSION['ohc_loca'])){
		$sql="SELECT psd.r_pharmacy_id,psd.id,psd.drug_name,psd.drug_manifaturer,psd.drug_batch,psd.drug_manifaturer_date,psd.drug_expiry_period,psd.drug_expiry_date,psd.drug_type,psd.drug_strength,psd.inventory,psd.amount_per_tab,psd.isactive,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_qty
		FROM `pharmacy_stock_detail` psd
		LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
		GROUP BY psd.id
		HAVING r_pharmacy_id='".$sessionlocation_id."' AND psd.drug_name LIKE '".$searchValue."%' AND psd.drug_expiry_date>='$stopiss' AND psd.isactive=1 AND ifnull(balance_qty,0)>0".$limit;
		}else{
			$sql="SELECT psd.r_pharmacy_id,psd.id,psd.drug_name,psd.drug_manifaturer,psd.drug_batch,psd.drug_manifaturer_date,psd.drug_expiry_period,psd.drug_expiry_date,psd.drug_type,psd.drug_strength,psd.inventory,psd.amount_per_tab,psd.isactive,psd.ohc,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_qty
		FROM `pharmacy_stock_detail` psd
		LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
		GROUP BY psd.id
		HAVING r_pharmacy_id='".$_SESSION['ohc_loca']."' AND psd.drug_name LIKE '".$searchValue."%' AND psd.drug_expiry_date>='$stopiss' AND psd.ohc='1' and psd.isactive=1 AND ifnull(balance_qty,0)>0".$limit;	
		}
		$drugSearchObj = new ManageUsers();
		//echo $sql;
		$drugSearchs=$drugSearchObj->listDirectQuery($sql);
		$drugSearchObj=null; 
			echo '<table class="table table-mail" id="tbl-heading" cellpadding=5>
						<tr>
							<th>Drug / Manufacturer</th>
							<th>Batch & Expiry</th>
							<th style="text-align:center;">Available</th>
							
						</tr>';
			if(is_array($drugSearchs)){	
				foreach($drugSearchs as $drugSearch){ 
					$count=$count+1;
					$drugStockId=$drugSearch['id'];
					$drugName=$drugSearch['drug_name'];
					$balance_qty=$drugSearch['balance_qty'];
					$drug_batch=$drugSearch['drug_batch'];
					$drug_manifaturer=$drugSearch['drug_manifaturer'];
					$drug_manifaturer_date=date("d M Y",strtotime($drugSearch['drug_manifaturer_date']));
					$drug_expiry_period=$drugSearch['drug_expiry_period'];
					$drug_expiry_date=date("d M Y",strtotime($drugSearch['drug_expiry_date']));
					$drug_type=$drugSearch['drug_type'];
					if(strtolower($drug_type)=="capsule" || strtolower($drug_type)=="tablet"){
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico7.png' style='width:18px;' /> ".$drug_type;
					} else if(strtolower($drug_type)=="syrup"){
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico13.png' style='width:18px;' /> ".$drug_type;
					} else if(strtolower($drug_type)=="injection"){
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico5.png' style='width:18px;' /> ".$drug_type;
					} else {
						$drug_type="<img src='".$sitepath."img/pharma_ico/pharma_ico15.png' style='width:18px;' /> ".$drug_type;
					}
					$drug_strength=$drugSearch['drug_strength'];
					$inventory=$drugSearch['inventory'];
					$amount_per_tab=round($drugSearch['amount_per_tab'],2);
					$active=($count==1)?"active":"";
					echo '<tr>
							<td data-content="Drug" width=40%><p>'.$drugName.'</p><p>'.$drug_type.'</p><p>'.$drug_manifaturer.'</p></td> 
							<td width=40%><form action="pharmacy_pending_request.php" method="post" id="frm"><p><b>'.$drug_batch.'</b></p><p>'.$drug_manifaturer_date.'<br>'.$drug_expiry_date.'</p></td>
							<td data-content="Action" align=center><b><span id="stockBalQty'.$drugStockId.'">'.$balance_qty.'</span></b><Br>';
							if(basename($_SERVER['HTTP_REFERER'])=="pharmacy_billing_add.php"){
								echo '<p><input type="button" value="Add" class="btn btn-primary" onclick="bringPharmacyAddWalkinUser('.$drugStockId.','.$prescriptionMainid.')" style="margin-top:5px;" /></p>';
							}

							elseif(basename($_SERVER['HTTP_REFERER'])=="corp_pharma_billing_add.php"){
									echo '<p><input type="button" value="Add" class="btn btn-primary" onclick="bringPharmacyAddWalkinUser('.$drugStockId.','.$prescriptionMainid.')" style="margin-top:5px;" /></p>';
							}
							else {
								echo '<p><input type="button" value="Select" class="btn btn-primary"  onclick="alternateDrugAdd(\''.$drugName.'\','.$balance_qty.','.$prescriptionid.')" style="margin-top:5px; font-size: 14px; padding: 5px 10px;" /></p>';
								//onclick="alternateDrugAdd(\''.$drugName.'\','.$balance_qty.','.$prescriptionid.')"
							}
							echo '</form></td>
						 </tr>';
				}
			} else {
				echo "<tr class=\"test\"><td class=\"table-fixed-medium\" colspan=10>No Data to Display.</td></tr>";
			}
		echo '</table>
		</div>';
	}
	
	
	
	
	
	if($action=="stockCalculateQty"){
		$stockId=$_POST['stockId'];
		$stockQty=$_POST['stockQty'];
		
		$qtyGetObj = new ManageUsers();
		if(isset($_POST['pharma_id']) && $_POST['pharma_id']!=''){
		    $sql="SELECT psd.amount_per_tab,(psd.quantity - IFNULL(SUM(pssd.qty),0)-".$stockQty.") as balance_qty FROM `pharmacy_sold_stock_detail` pssd
	    	LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
		    WHERE psd.r_pharmacy_id='".$_POST['pharma_id']."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1 AND psd.id='".$stockId."'";
		}else{
		    $sql="SELECT psd.amount_per_tab,(psd.quantity - IFNULL(SUM(pssd.qty),0)-".$stockQty.") as balance_qty FROM `pharmacy_sold_stock_detail` pssd
	    	LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
		    WHERE psd.r_pharmacy_id='".$sessionlocation_id."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1 AND psd.id='".$stockId."'";
		}
		
		
		//echo $sql;
		$qtyGet=$qtyGetObj->listDirectQuery($sql);
		$qtyGetObj=null;
		$amount=($qtyGet[0]['amount_per_tab'])*$stockQty;
		$balance_qty=$qtyGet[0]['balance_qty'];
		
		if($balance_qty>=0){
			echo $balance_qty."-".round($amount,2);
		}
		if($qtyGet[0]['amount_per_tab']==''){
		    exit;
		}
	}

	
	
	
	
	if($action=="SeacrhAndAll"){
		$id=$_POST['presId'];
		$sql="SELECT md.drug_name
			FROM `prescription_detail` pd
			LEFT OUTER JOIN `master_drugs` md ON md.id = pd.drugs_id
			WHERE pd.prescription_id='".$id."'";
		$prescriptionDetail = new ManageUsers();			
		$prescriptionsWanted=$prescriptionDetail->listDirectQuery($sql);
		$prescriptionDetail=null;
		$drugs_name_wanted=$prescriptions[0]['drugs_name_wanted'];
		foreach($prescriptionsWanted as $prescriptionsWantedList){
			$sql="SELECT GROUP_CONCAT(psd.drug_name) as drugs_name_having
				FROM `pharmacy_stock_detail` psd
				WHERE psd.r_pharmacy_id='".$sessionlocation_id."' AND psd.drug_name LIKE '".$prescriptionsWantedList['drug_name']."%'";
			$prescriptionDetail = new ManageUsers();			
			$prescriptions=$prescriptionDetail->listDirectQuery($sql);
			$prescriptionDetail=null;
			if($prescriptions[0]['drugs_name_having']<>""){
				$relavent[]=$prescriptions[0]['drugs_name_having'];
			}
		}
		$relaventDrugs=implode(", ",$relavent);
		echo "<div style='min-height:80px;'>
			<i style='margin-left:10px;'> We have Relevant Medicines: ".$relaventDrugs."</i>";
		?>
			<input type="text" onkeyup="bringPharmacyData(this.value,'aboutDrug','<?php echo (($id=="")?"0":$id); ?>')" style="width: 81%; text-align: left; margin: 0px 0px 10px 10px; height: 35px ! important; background-color: rgb(255, 255, 255) ! important; float: left;" placeholder="<?php echo ($relaventDrugs<>"")?$relaventDrugs:"Search Drug "; ?>" />
			<img style="background-color: rgb(204, 204, 204); padding: 10px; width: 23px; float: left; border: 1px solid rgb(170, 170, 170); margin-left: -2px;" src="img/plus_zoom.png"><br/><br/>
		</div>
			
		<div class="pharmacyDataDetail-<?php echo $id; ?>"></div>
		<?php 
	}
	
	
	
	
	if($action=="stockSoldAdd" || $action=="stockSoldAddWalkinUser"){
		$stockId=$_POST['stockId'];
		$stockQty=($_POST['stockQty']=="")?"0":$_POST['stockQty'];
		$totalQty=($_POST['totalQty']=="")?"0":$_POST['totalQty'];
		$remainingqty=$totalQty-$stockQty;
		$prescriptionMainid=$_POST['prescriptionMainid'];
		
		$qtyGetObj = new ManageUsers();
		if(empty($_SESSION['ohc_loca'])){
		$sql="SELECT psd.amount_per_tab,psd.drug_name,psd.discount,(psd.quantity - IFNULL(SUM(pssd.qty),0)-".$stockQty.") as balance_qty FROM `pharmacy_sold_stock_detail` pssd
		LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
		WHERE psd.r_pharmacy_id='".$sessionlocation_id."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1 AND psd.id='".$stockId."'";
		//echo $sql; 
		
		}else{
			 $sql="SELECT psd.amount_per_tab,psd.drug_type,psd.drug_name,psd.discount,(psd.quantity - IFNULL(SUM(pssd.qty),0)-".$stockQty.") as balance_qty FROM `pharmacy_sold_stock_detail` pssd
		LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
		WHERE psd.r_pharmacy_id='".$_SESSION['ohc_loca']."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1 AND psd.id='".$stockId."' and psd.ohc='1'"; 
		}
		$qtyGet=$qtyGetObj->listDirectQuery($sql);
		//echo $qtyGet[0]['balance_qty'];
		//$qtyGetObj=null;
		$amount=$qtyGet[0]['amount_per_tab'];
		$balance_qty=$qtyGet[0]['balance_qty'];
		$discount=$qtyGet[0]['discount'];
		$qtyGetObj = new ManageUsers();
	
$sqlcount="SELECT psd.r_pharmacy_id,psd.id,psd.drug_manifaturer,psd.drug_name,psd.drug_manifaturer,psd.drug_batch,psd.drug_manifaturer_date,psd.drug_expiry_period,psd.drug_expiry_date,psd.drug_type,psd.drug_strength,psd.inventory,psd.amount_per_tab,psd.isactive,psd.ohc,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_qty FROM `pharmacy_stock_detail` psd LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id GROUP BY psd.id HAVING r_pharmacy_id='1'AND psd.drug_type='".$qtyGet[0]['drug_type']."' AND psd.drug_name LIKE '".$qtyGet[0]['drug_name']."' AND psd.drug_expiry_date>=date(now())  AND psd.ohc='1' and psd.isactive=1 AND ifnull(balance_qty,0)>0"; 
		//print_r ($_SESSION);
		//echo $sqlcount;	
		$corp_nam_location=$qtyGetObj->listDirectQuery("select first_name,last_name,email from master_corporate_user where id=".$_SESSION['userid']);
		$qtyGetcount=$qtyGetObj->listDirectQuery($sqlcount);
		$cnted=0;
		foreach($qtyGetcount as $blnsdrg){
		$cnted=$cnted+$blnsdrg['balance_qty'];

		}
		$remaining_stocks=$cnted-$stockQty; 
		$res_drugname=$qtyGetcount[0]['drug_name']; 
		
		$max_count=$qtyGetObj->listDirectQuery("SELECT  * FROM  `stock_alert` where corp_id='".$_SESSION['parent_id']."' ");
		
		if(!empty($max_count)){
		if ($qtyGet[0]['drug_type']=="Capsule"){
		$drug_count=$max_count[0]['Capsule'];
		}
		else if ($qtyGet[0]['drug_type']=="Cream"){
		$drug_count=$max_count[0]['Cream'];
		}
		else if ($qtyGet[0]['drug_type']=="Drops"){
		$drug_count=$max_count[0]['Drops'];
		}
		else if ($qtyGet[0]['drug_type']=="Foam"){
		$drug_count=$max_count[0]['Foam'];
		}
		else if ($qtyGet[0]['drug_type']=="Gel"){
		$drug_count=$max_count[0]['Gel'];
		}
		else if ($qtyGet[0]['drug_type']=="Inhaler"){
		$drug_count=$max_count[0]['Inhaler'];
		}
		else if ($qtyGet[0]['drug_type']=="Injecion"){
		$drug_count=$max_count[0]['Injecion'];
		}
		else if ($qtyGet[0]['drug_type']=="Lotion"){
		$drug_count=$max_count[0]['Lotion'];
		}
		else if ($qtyGet[0]['drug_type']=="Ointment"){
		$drug_count=$max_count[0]['Ointment'];
		}
		else if ($qtyGet[0]['drug_type']=="Powder"){
		$drug_count=$max_count[0]['Powder'];
		}
		else if ($qtyGet[0]['drug_type']=="Shampoo"){
		$drug_count=$max_count[0]['Shampoo'];
		}
		else if ($qtyGet[0]['drug_type']=="Syringe"){
		$drug_count=$max_count[0]['Syringe'];
		}
		else if ($qtyGet[0]['drug_type']=="Syrup"){
		$drug_count=$max_count[0]['Syrup'];
		}
		else if ($qtyGet[0]['drug_type']=="Tablet"){
		$drug_count=$max_count[0]['Tablet'];
		}
		else if ($qtyGet[0]['drug_type']=="Toothpaste"){
		$drug_count=$max_count[0]['Toothpaste'];
		}
		else if ($qtyGet[0]['drug_type']=="Spray"){
		$drug_count=$max_count[0]['Spray'];
		}
		
		if ($remaining_stocks<$drug_count){
		$to = $corp_nam_location[0]['email'];
		$subject = "Restock alert";
		
		$txt = "Dear  ".$corp_nam_location[0]['first_name']."  ".$corp_nam_location[0]['last_name'].", <br><br> The following drugs/items have gone below the threshold level as on ". date("d-m-Y") .".The current available stock displayed is the total available stock across both the Main Pharmacy as well as all Sub-Pharmacies.Please initiate restocking process at the earliest.<br><br><br><table class='table' border='1' width='80%' cellpadding='5' cellspacing='1'> <thead> <tr> <th width='25%'>Drug / Item Name</th> <th width='30%'>Manufacturer Name</th> <th width='10%'>Type</th><th width='25%'>Current Available Stock</th> <th width='10%'>Date</th> </tr> </thead> <tbody> <tr> <td align='center'>".$res_drugname."</td> <td align='center'>".$qtyGetcount[0]['drug_manifaturer']."</td> <td align='center'>".$qtyGet[0]['drug_type']."</td><td align='center'>".$remaining_stocks."</td><td align='center'>". date("d-m-Y") ."</td> </tr></table><br><br><b>Note:</b> For the full list of drugs/items that require restocking, please view the <b>Restock List</b> report from the reports section.<br><br>Regards,<br> myHealthvalet Team" ;
		
		$headers = "From: myhealthvalet@mhv.softlayer.com" . "\r\n" ;
		
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		mail($to,$subject,$txt,$headers);
		
		}}
		//$remainingtabs=" ";
		if($balance_qty>=0){
			if(!isset($_SESSION['stock_generate_id'])){
				$sql="SELECT ifnull(max(stock_generate_id),0)+1 as stock_generate_id FROM `pharmacy_sold_stock_detail` WHERE r_pharmacy_id='".$sessionlocation_id."'";
				$sqlDataObj = new ManageUsers();
				$sqlData = $sqlDataObj->listDirectQuery($sql);
				$sqlDataObj=null;
				$_SESSION['stock_generate_id']=$sqlData[0]["stock_generate_id"];
			} 
			
			$stockGenerationId=$_SESSION['stock_generate_id'];
			if($action=="stockSoldAdd"){
				$qtyGetObj = new ManageUsers();
				$sql="SELECT user_id,doctor_id,master_hcsp_user_id as hosp_id FROM `prescription` p WHERE p.id='".$prescriptionMainid."'";
				$qtyGetUserDetails=$qtyGetObj->listDirectQuery($sql);
				$qtyGetObj=null;
				if(isset($_POST['user_id'])){
				    $user_id=$_POST['user_id'];
				}else{
				    $user_id=$qtyGetUserDetails[0]['user_id'];
				}
				$doctor_id=$qtyGetUserDetails[0]['doctor_id'];
				$hosp_id=$qtyGetUserDetails[0]['hosp_id'];
			} else {
				$user_id = $_POST['user_id'];
				$doctor_id = $_POST['doctor_id'];
				$hosp_id = $_POST['hosp_id'];
			}
			$r_appuser_walkin_id="1";
			if($action=="stockSoldAddWalkinUser"){
				$r_appuser_walkin_id="0";
			}
			$addDrugSearchObj = new ManageUsers();
			if($amount==0){
			    $amount=$_POST["stockCost"];
			}
			if(empty($_SESSION['ohc_loca'])){
			    $pharma_id='';
			    if(isset($_POST['pharmaid']) && $_POST['pharmaid']!=''){
			       $pharma_id=$_POST['pharmaid']; 
			       $user_order=1;
			       
			       $qtyGetObj = new ManageUsers();
			       $sqlselect="SELECT max(id) as maxid FROM `order`";
				   $getorders=$qtyGetObj->listDirectQuery($sqlselect);
			    	$qtyGetObj=null;
			    	if($getorders!=''){
			    	    
			    	    $nextnum=$getorders[0]["maxid"]+1;
			    	    $ordid="ord".$nextnum;
			    	}else{
			    	     
			    	    $ordid="ord1";
			    	}
			    	
			       $qtyGetObj = new ManageUsers();
			       $sqlorder="INSERT INTO `order`(`order_id`,`link_id`,`order_cat`, `category_id`, `user_id`, `amount`, `status`) VALUES ('".$ordid."','".$stockGenerationId."','pharma','".$_POST['pharmaid']."','".$user_id."','".$amount."','In Process')";
			       $orderresult=$qtyGetObj->AddDirectQuery($sqlorder);
			       $qtyGetObj=null;
			        
			    }else{
			        $pharma_id=$sessionlocation_id; 
			        $user_order=0;
			    }
			    $patientname='';
			    if(is_numeric($user_id)){
			        
			    }else{
			        $patientname=$user_id;
			    }
			$sql="INSERT INTO `pharmacy_sold_stock_detail`(r_pharmacy_id,r_user_id,r_appuser_walkin_id,r_prescription_id,stock_generate_id,r_stock_id,qty,amount,discount,r_doctor_id,r_hosp_id,created_by,created_role,created_on,user_order,remaining_qty,patient_name,doctor_name,upload_file) VALUES ('".$pharma_id."','".$user_id."','".$r_appuser_walkin_id."','".$prescriptionMainid."','".$stockGenerationId."','".$stockId."','".$stockQty."','".$amount."','".$discount."','".$doctor_id."','".$hosp_id."','".$tbs_userid."','".$tbs_role."','".$datetime."','".$user_order."','".$remainingqty."','".$patientname."','".$_POST["doctor_name"]."','".$_POST["uploadfile"]."')";
			
			 //echo $sql;   
			}else{
			    if(isset($_POST['pharmaid']) && $_POST['pharmaid']!=''){
			       $pharma_id=$_POST['pharmaid']; 
			       $user_order=1;
			       
			       $qtyGetObj = new ManageUsers();
			       $sqlselect="SELECT max(id) as maxid FROM `order`";
				   $getorders=$qtyGetObj->listDirectQuery($sqlselect);
			    	$qtyGetObj=null;
			    	if(!empty($getorders)){
			    	    
			    	    $nextnum=$getorders[0]["maxid"]+1;
			    	    $ordid="ord".$nextnum;
			    	}else{
			    	     
			    	    $ordid="ord1";
			    	}
			    	
			      $qtyGetObj = new ManageUsers();
			       $sqlorder="INSERT INTO `order`(`order_id`,`link_id`,`order_cat`, `category_id`, `user_id`, `amount`, `status`) VALUES ('".$ordid."','".$stockGenerationId."','pharma','".$_POST['pharmaid']."','".$user_id."','".$amount."','In Process')";
			       $orderresult=$qtyGetObj->AddDirectQuery($sqlorder);
			       $qtyGetObj=null;
			    }else{
			        $pharma_id=$_SESSION['ohc_loca'];
			        $user_order=0;
			    }
			    
			    $patientname='';
			    if(is_numeric($user_id)){
			        
			    }else{
			        $patientname=$user_id;
			    }
				$sql="INSERT INTO `pharmacy_sold_stock_detail`(r_pharmacy_id,r_user_id,r_appuser_walkin_id,r_prescription_id,stock_generate_id,r_stock_id,qty,amount,discount,r_doctor_id,created_by,created_role,created_on,ohc,user_order,remaining_qty,patient_name,doctor_name,upload_file) VALUES ('".$pharma_id."','".$user_id."','".$r_appuser_walkin_id."','".$prescriptionMainid."','".$stockGenerationId."','".$stockId."','".$stockQty."','".$amount."','".$discount."','".$tbs_role."','".$tbs_userid."','".$tbs_role."','".$datetime."','1','".$user_order."','".$remainingqty."','".$patientname."','".$_POST["doctor_name"]."','".$_POST["uploadfile"]."')";
			}
			//echo $sql;
			$addDrugSearchStock=$addDrugSearchObj->AddDirectQuery($sql);
			$addDrugSearchObj=null;
			
			$qtyGetObj = new ManageUsers();
			$sql="SELECT SUM(pssd.qty) as qty,SUM(pssd.amount) as amount FROM `pharmacy_sold_stock_detail` pssd
			LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
			WHERE pssd.stock_generate_id='".$stockGenerationId."' AND psd.r_pharmacy_id='".$sessionlocation_id."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1";
			$qtyGetStokLists=$qtyGetObj->listDirectQuery($sql);
			$qtyGetObj=null;
			foreach($qtyGetStokLists as $qtyGetStokList){
				$qty=$qtyGetStokList['qty'];
				$amount=$qtyGetStokList['amount'];
				echo $qty;
			}
		 } else {
			 $_SESSION['errmsg']="Insufficient Quantity";
		 }
		
	}

	
	
	
	if($action=="stockinViewDetails"){
		if(isset($_SESSION['stock_generate_id'])){
			$stockGenerationId=$_SESSION['stock_generate_id'];
			$qtyGetObj = new ManageUsers();

			if(empty($_SESSION['ohc_loca'])){
			     if($_POST["tbsrole"]==1){
			        $sql="SELECT pssd.id,psd.drug_name,pssd.qty,pssd.amount,pssd.discount,pssd.r_stock_id FROM `pharmacy_sold_stock_detail` pssd
			LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
			WHERE  pssd.r_user_id='".$_POST["userid"]."' and pssd.stock_generate_id='".$stockGenerationId."'  and psd.drug_expiry_date>=date(now()) AND psd.isactive=1";
			    }else{
			$sql="SELECT pssd.id,psd.drug_name,pssd.qty,pssd.amount,pssd.discount,pssd.r_stock_id FROM `pharmacy_sold_stock_detail` pssd
			LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
			WHERE psd.r_pharmacy_id='".$sessionlocation_id."' AND pssd.stock_generate_id='".$stockGenerationId."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1";
			    }
			}else{
			   
			    if($_POST["tbsrole"]==1){
			        $sql="SELECT pssd.id,psd.drug_name,pssd.qty,pssd.amount,pssd.discount,pssd.r_stock_id FROM `pharmacy_sold_stock_detail` pssd
			LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
			WHERE  pssd.r_user_id='".$_POST["userid"]."' and pssd.stock_generate_id='".$stockGenerationId."'  and psd.drug_expiry_date>=date(now()) AND psd.isactive=1";
			    }else{
					$sql="SELECT pssd.id,psd.drug_name,pssd.qty,pssd.amount,pssd.discount,pssd.r_stock_id FROM `pharmacy_sold_stock_detail` pssd
			LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
			WHERE psd.r_pharmacy_id='".$_SESSION['ohc_loca']."' AND pssd.stock_generate_id='".$stockGenerationId."' AND psd.ohc='1' and psd.drug_expiry_date>=date(now()) AND psd.isactive=1";
			    }
			}
			//echo $sql;
			$qtyGetStokLists=$qtyGetObj->listDirectQuery($sql);
			$qtyGetObj=null;
			//<div class="span12" ><img src="'.$sitepath.'img/export-to-csv.png" align="right"/></div>
			
			if(isset($_POST["pharmaid"]) && $_POST["pharmaid"]!=''){
			    $pharmaid=$_POST["pharmaid"];
			}else{
			    $pharmaid='';
			}
			echo '
			<div class="row-fluid row-fluid-style tablecart" style="margin:0px; background-color: #fff !important;">

				<table class="table table-nomargin table-mail">
					<tr>
						<th>Drug</th>
						<th style="width: 50px; text-align:center">Qty</th>
						<th style="width: 50px; text-align:center">CPU</th>
						<th style="width: 50px; text-align:center">M.R.P.</th>
						<th style="width: 100px; text-align:center">M.R.P After Discount</th>
						<th>Action</th>
					</tr>';
					$totalAmt=0;
					$totalDiscAmt=0;
					$totalCountQty=0;
					foreach($qtyGetStokLists as $qtyGetStokList){
						$drug_name=$qtyGetStokList['drug_name'];
						$qty=$qtyGetStokList['qty'];
						$amount=round($qtyGetStokList['amount'],2);
						$stockId=$qtyGetStokList['id'];
						$netAmount=round(($qtyGetStokList['amount']*$qtyGetStokList['qty']),2);
						$rateAfterDiscount=round(($qtyGetStokList['amount'])-(($qtyGetStokList['amount'])*($qtyGetStokList['discount'])/100),2);
						$discountNetAmount=$rateAfterDiscount*$qtyGetStokList['qty'];
						
						
						echo "<tr>
						<td>".$drug_name."</td>
						<td style=text-align:center>".$qty."</td>
						<td style=text-align:center>".$amount."</td>
						<td style=text-align:center>".$netAmount."</td>
						<td style=text-align:center>".$discountNetAmount."</td>
						<td style=text-align:center><a onclick='stockOutDel(".$stockId.",".$pharmaid.")'><img src='".$sitepath."/img/cancel.png' style='width:15px;' /></td>
						</tr>";
						
						$totalCountQty=$totalCountQty+$qty;
						$totalAmt=$totalAmt+$netAmount;
						$totalDiscAmt=$totalDiscAmt+$discountNetAmount;
					}
			echo '</table>
			<div style="float:left;">Total Qty: '.$totalCountQty.' </div> <div style="float:right;text-align:right;"><p>Total: '.round($totalAmt,2).'</p><p>Total After Discount: '.round($totalDiscAmt,2).'</p></div>';
			
			if(basename($_SERVER['HTTP_REFERER'])=="pharmacy_billing_add.php"){
				echo '<div class="clear" style="float:right;"><input type="button" class="btn btn-primary" value="Continue Billing" onclick="stockOutGenerateBill('.$stockGenerationId.',\'stockOutGenerateBillWalkin\')" /></div>';
			} else {
				echo '<div class="clear" style="float:right;"><input type="button" class="btn btn-primary" value="Continue Billing" onclick="stockOutGenerateBill('.$stockGenerationId.',\'stockOutGenerateBill\','.$pharmaid.')" /></div>';
			}
			echo '</div>';
		} else {
			echo '<p style="width: 200px;text-align:center; padding-top:13%;color:#f00;">You have not added any medicines/items to the cart.<p>';
		}
	}
	
	
	
	
	
	if($action=="stockOutDel"){
	    
		$stockId=$_POST['stockId'];
		$stockGenerationId=$_SESSION['stock_generate_id'];
		if($stockGenerationId<>"" || $stockId<>""){
			$qtyGetObj = new ManageUsers();
			if(isset($_POST["pharmaid"]) && $_POST["pharmaid"]!=''){
			    $sql="DELETE FROM `pharmacy_sold_stock_detail` WHERE id='".$stockId."' AND r_pharmacy_id='".$_POST["pharmaid"]."' AND stock_generate_id='".$stockGenerationId."'";
		    	
			}else{
			    $sql="DELETE FROM `pharmacy_sold_stock_detail` WHERE id='".$stockId."' AND r_pharmacy_id='".$sessionlocation_id."' AND stock_generate_id='".$stockGenerationId."'";
			}
			$qtyGet=$qtyGetObj->listDirectQuery($sql);
			$qtyGetObj=null;
					
			$qtyGetObj = new ManageUsers();
			$sql="SELECT ifnull(SUM(pssd.qty),0) as qty FROM `pharmacy_sold_stock_detail` pssd
					LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
					WHERE psd.r_pharmacy_id='".$sessionlocation_id."' AND pssd.stock_generate_id='".$stockGenerationId."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1";
			$qtyGetStokLists=$qtyGetObj->listDirectQuery($sql);
			$qtyGetObj=null;
			echo $qtyGetStokLists[0]['qty'];
		} else {
			echo "error";
		}
	}
	
	
	
	
	
	if($action=="bringCountQtyInCart"){
		$stockGenerationId=$_SESSION['stock_generate_id'];
		$qtyGetObj = new ManageUsers();
		$sql="SELECT ifnull(SUM(pssd.qty),0) as qty FROM `pharmacy_sold_stock_detail` pssd
					LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
					WHERE psd.r_pharmacy_id='".$sessionlocation_id."' AND pssd.stock_generate_id='".$stockGenerationId."' AND psd.drug_expiry_date>=date(now()) AND psd.isactive=1 and r_appuser_walkin_id!='3'";
		$qtyGetStokLists=$qtyGetObj->listDirectQuery($sql);
		$qtyGetObj=null;
		foreach($qtyGetStokLists as $qtyGetStokList){
			$qty=$qtyGetStokList['qty'];
			$amount=$qtyGetStokList['amount'];
			echo $qty;
		}
	}
			
			
			
		if($action=="stockOutGenerateBill" || $action=="stockOutGenerateBillWalkin"){
				$stockGenerationId=$_POST['stockGenerationId'];
				if($stockGenerationId<>""){
					 if($sessionlocation_id==""){
					      $sessionlocation_id=$_POST["pharm_id"];
					  } 
					$sql="SELECT ifnull( max(bill_generation_id),0)+1 as bill_generation_id FROM `pharmacy_billing_history` WHERE r_pharmacy_id='".$sessionlocation_id."'";
					$sqlDataObj = new ManageUsers();
					$sqlData = $sqlDataObj->listDirectQuery($sql);
					$sqlDataObj=null;
					$billGenerationId=$sqlData[0]["bill_generation_id"];
					
					
					$qtyGetObj = new ManageUsers();
					if(empty($_SESSION['ohc_loca'])){
					  
					$sql="
						INSERT INTO `pharmacy_billing_history`(r_pharmacy_id,r_pharmacy_num,r_pharmacy_name,r_pharmacy_logo,dlno,tinno,store_id,phone,tax_invoice_no,patient_name,patient_age,patient_gender,doctor_name,doctor_regno,drug_name,manufaturer_name,schedule,batch_no,expiry_date,mrp,rate_after_discount,quantity,amount,amount_saved,vat_rate,vat_amount,total_amount,r_pharmacy_sold_stock_detail_id,r_prescription_id,r_prescription_display_id,r_prescription_detail_id,status,bill_generation_id,address1,address2,pincode,area,city,state,country,print_notes,created_on,created_by,created_role) 
						SELECT '".$sessionlocation_id."',mp.pharmacy_num,mp.pharmacy_name,mp.prof_image,mp.dlno,mp.tinno,mp.store_id,mp.phone,mp.tax_invoice_no,
						CONCAT(mud.first_name,' ',mud.last_name) as patient_name,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' Month'), concat(TIMESTAMPDIFF(year,mud.dob,NOW()), ' Year')) as patient_age,
						mud.gender as patient_gender,CONCAT(md.first_name,' ',md.last_name) as doctor_name,md.registrationno,psd.drug_name,psd.drug_manifaturer,psd.schedule,psd.drug_batch,psd.drug_expiry_date,psd.amount_per_tab,psd.discount,pssd.qty,pssd.amount,'' as amount_saved,mp.vat_rate,'' as vat_amount,'' as total_amount,pssd.id,pssd.r_prescription_id,p.prescription_id,pssd.r_prescription_detail_id,2,'".$billGenerationId."',mp.address1,mp.address2,pincode.doctype,area.doctype,city.doctype,state.doctype,country.doctype,mp.print_notes,'".$datetime."','".$tbs_userid."','".$tbs_role."'
						FROM `pharmacy_sold_stock_detail` pssd
						LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
						LEFT OUTER JOIN `master_pharmacy` mp ON mp.id=pssd.r_pharmacy_id
						LEFT OUTER JOIN `prescription` p ON p.id=pssd.r_prescription_id
						LEFT OUTER JOIN `master_doctor` md ON md.id=pssd.r_doctor_id
						LEFT OUTER JOIN `doctype` pincode ON pincode.id=mp.pincode
						LEFT OUTER JOIN `doctype` area ON area.id=pincode.parent_id 
						LEFT OUTER JOIN `doctype` city ON city.id=area.parent_id 
						LEFT OUTER JOIN `doctype` state ON state.id=city.parent_id 
						LEFT OUTER JOIN `doctype` country ON country.id=state.parent_id
						LEFT OUTER JOIN `master_user_details` mud ON mud.id=pssd.r_user_id
						WHERE pssd.r_pharmacy_id='".$sessionlocation_id."' AND pssd.stock_generate_id='".$stockGenerationId."'";
					    //echo "ifff";
					}else{
					    
							$sql="
						INSERT INTO `pharmacy_billing_history`(r_pharmacy_id,r_pharmacy_name,patient_name,patient_age,patient_gender,doctor_name,drug_name,manufaturer_name,schedule,batch_no,expiry_date,mrp,rate_after_discount,quantity,amount,amount_saved,total_amount,r_pharmacy_sold_stock_detail_id,r_prescription_id,r_prescription_display_id,r_prescription_detail_id,status,bill_generation_id,address1,address2,pincode,area,city,state,country,created_on,created_by,created_role,ohc) 
						SELECT '".$_SESSION['ohc_loca']."',mp.corporate_name,
						CONCAT(mud.first_name,' ',mud.last_name) as patient_name,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' Month'), concat(TIMESTAMPDIFF(year,mud.dob,NOW()), ' Year')) as patient_age,
						mud.gender as patient_gender,CONCAT(md.first_name,' ',md.last_name) as doctor_name,psd.drug_name,psd.drug_manifaturer,psd.schedule,psd.drug_batch,psd.drug_expiry_date,psd.amount_per_tab,psd.discount,pssd.qty,pssd.amount,'' as amount_saved,'' as total_amount,pssd.id,pssd.r_prescription_id,p.prescription_id,pssd.r_prescription_detail_id,2,'".$billGenerationId."',mp.address1,mp.address2,pincode.doctype,area.doctype,city.doctype,state.doctype,country.doctype,'".$datetime."','".$tbs_userid."','".$tbs_role."','1'
						FROM `pharmacy_sold_stock_detail` pssd
						LEFT OUTER JOIN `pharmacy_stock_detail` psd ON psd.id=pssd.r_stock_id
						LEFT OUTER JOIN `master_corporate` mp ON mp.id=pssd.r_pharmacy_id
						LEFT OUTER JOIN `prescription` p ON p.id=pssd.r_prescription_id
						LEFT OUTER JOIN `master_corporate_user` md ON md.id=pssd.r_doctor_id
						LEFT OUTER JOIN `doctype` pincode ON pincode.id=mp.pincode
						LEFT OUTER JOIN `doctype` area ON area.id=pincode.parent_id 
						LEFT OUTER JOIN `doctype` city ON city.id=area.parent_id 
						LEFT OUTER JOIN `doctype` state ON state.id=city.parent_id 
						LEFT OUTER JOIN `doctype` country ON country.id=state.parent_id
						LEFT OUTER JOIN `master_user_details` mud ON mud.id=pssd.r_user_id
						WHERE pssd.r_pharmacy_id='".$_SESSION['ohc_loca']."' AND pssd.ohc='1' and  pssd.stock_generate_id='".$stockGenerationId."'";
						
					}
					//echo $sql;	
					$qtyGetStokLists=$qtyGetObj->AddDirectQuery($sql);
					$qtyGetObj=null;
					if($qtyGetStokLists){
						unset($_SESSION['stock_generate_id']);
						echo $billGenerationId;
					}
					
				} else {
					echo "error";
				}
			}
			
			
			
	if($action=="getDiscount"){
		$qtyGetObj = new ManageUsers();
		$sql="SELECT ifnull(mp.discount,0) as discount FROM `master_pharmacy` mp
			WHERE mp.id='".$sessionlocation_id."'";
		$qtyGetStokLists=$qtyGetObj->listDirectQuery($sql);
		$qtyGetObj=null;
		echo $qtyGetStokLists[0]['discount'];
	}
	
	
	
	
	
	if($action=="orderStatus"){
		
		$pharmaNameObj = new ManageUsers();
		$pharmaName=$pharmaNameObj->listDirectQuery("SELECT mp.pharmacy_name,area.doctype as areaName FROM `master_pharmacy` mp
		LEFT OUTER JOIN `doctype` pincode ON pincode.id=mp.pincode
		LEFT OUTER JOIN `doctype` area ON area.id=pincode.parent_id
		WHERE mp.id='$sessionlocation_id'");
		$pharmaNameObj=null;
		$pharmacyName=$pharmaName[0]['pharmacy_name'];
		$pharmacyAreaName=$sqlData[0]["areaName"];
		
		$presId=$_POST['presId'];
		$status=$_POST['status'];
		$sql="SELECT p.user_id,p.doctor_id,p.prescription_id,mud.first_name as patient_first_name,mud.last_name as patient_last_name FROM `prescription` p
		LEFT OUTER JOIN `master_user_details` mud ON mud.id=p.user_id
		WHERE p.id='".$presId."'";
		$qtyGetObj = new ManageUsers();
		$qtyGetNotifiList=$qtyGetObj->listDirectQuery($sql);
	
		$user_id=$qtyGetNotifiList[0]['user_id'];
		$doctor_id=$qtyGetNotifiList[0]['doctor_id'];
		$prescription_id=$qtyGetNotifiList[0]['prescription_id'];
		$patientName=ucfirst($qtyGetNotifiList[0]['patient_first_name'])." ".ucfirst($qtyGetNotifiList[0]['patient_last_name']);
		
		$HomeScreensets = $qtyGetObj->listDirectQuery("SELECT * FROM homescreensettings WHERE user_id='$sessionlocation_id' and role_id='$tbs_role'");	
			$qtyGetObj=null;
			foreach($HomeScreensets as $homeset){
				$setid=$homeset['homescreensettings_doctype_static'];
				$hmst[$setid]=$homeset['homevalues'];
			}
		if($status=="2"){
			//Request will move to complete request
			$updateOrderStatusObj = new ManageUsers();
			$updateOrderStatus=$updateOrderStatusObj->listDirectQuery("UPDATE `prescription` SET fav_pharmacy_order=2 WHERE id='".$presId."'");
			$updateOrderStatusObj=null;
		}
		if(!empty($_SESSION['ohc_phar'])){
				if($status=="2"){
					$gcmMessageReason="5";
				$messageSend="Dear Mr/Ms. ".$patientName.", Your Order related to the prescription ".$prescription_id." has been delivered.\n-Sent by ".$pharmacyName.".";
				} else {
					$gcmMessageReason="6";
				$messageSend="Dear Mr/Ms. ".$patientName.", Your Order related to the prescription ".$prescription_id." is ready for delivery/collection.\n-Sent by ".$pharmacyName.".";
				}
				
				include("../cron_notification.php");			
		}
if( ($hmst['253']=='1' && $status=='1')  || ($hmst['254']=='1' && $status=='2') ){
		//TO SEND USER NOTIFICATION
		$getGCMUserObj = new ManageUsers();
		
		$getGCMUsers=$getGCMUserObj->listDirectQuery("SELECT gcm.*,gcm.user_id as user,gcm.role_id as role
		FROM gcm_users gcm 
		WHERE gcm.token!='' AND (gcm.user_id='$user_id' AND gcm.role_id=1 || gcm.user_id='$doctor_id' AND gcm.role_id=2)");
		$getGCMUserObj=null;
		if(is_array($getGCMUsers)){
			foreach($getGCMUsers as $getGCMUser){ /*GCM User Start*/
				$suser=$getGCMUser['user_id'];
				$srole=$getGCMUser['role_id'];
				$gcmRegID=$getGCMUser['token'];
				
				$gcmUserId=$getGCMUser['user'];
				$gcmRoleId=$getGCMUser['role'];
				if($status=="2"){
					$gcmMessageReason="5";
					$messageSend="Dear Mr/Ms. ".$patientName.", Your Order related to the prescription ".$prescription_id." has been delivered.\n-Sent by ".$pharmacyName.".";
				} else {
					$gcmMessageReason="6";
					$messageSend="Dear Mr/Ms. ".$patientName.", Your Order related to the prescription ".$prescription_id." is ready for delivery/collection.\n-Sent by ".$pharmacyName.".";
				}
				
				include("../cron_notification.php");
			}
		}
		
		
}
		if( ($hmst['251']=='1' && $status=='1')  || ($hmst['252']=='1' && $status=='2') ){
		//TO SEND SMS
		$getSMSUserObj = new ManageUsers();
		$getSMSUsers=$getSMSUserObj->listDirectQuery("SELECT first_name,last_name,mob_num,1 as role,id as user FROM `master_user_details` WHERE mob_num!='' AND id='$user_id'");
		$getSMSUserObj=null;
		if(is_array($getSMSUsers)){
			foreach($getSMSUsers as $getSMSUser){ /*SMS User Start*/
				$mobileNumberSMS=$getSMSUser['mob_num'];
				$smsUserId=$getSMSUser['user'];
				$smsRoleId=$getSMSUser['role'];
				$prescriptionEncoded=urlencode($prescription_id);
				if($status=="2"){
					$smsMessageReason="5";
					$messageSMS="Dear Mr/Ms. ".$patientName.", Your Order related to the prescription ".$prescriptionEncoded." has been delivered.\n-Sent by ".$pharmacyName;
				} else {
					$smsMessageReason="6";
					$messageSMS="Dear Mr/Ms. ".$patientName.", Your Order related to the prescription ".$prescriptionEncoded." is ready for delivery/collection.\n-Sent by ".$pharmacyName;
				}
				include("../sms_notification.php");
			}
		}
		}
	}
	
	
	
	if($action=="uploadXls"){
		function getExtension1($str) {
			$i = strrpos($str, ".");
			if (!$i) { return ""; }
			$l = strlen($str) - $i;
			$ext = substr($str,$i+1,$l);
			return $ext;
		}
		
		$file=$_FILES['file']['name'];
		$filename = stripslashes($file);
		$extension = getExtension1($filename);
		$extension = strtolower($extension);
		
		$getMaxUploadIdObj = new ManageUsers();
		$sql="SELECT ifnull(max(upload_generate_id),0)+1 as upload_generate_id FROM `pharmacy_stock_detail` WHERE r_pharmacy_id='".$sessionlocation_id."'";
		$getMaxUploadId=$getMaxUploadIdObj->listDirectQuery($sql);
		$getMaxUploadIdObj=null;
		$uploadGenerateId=$getMaxUploadId[0]['upload_generate_id'];
		
		if ($extension == "csv"){
			$csv_file = $_FILES['file']['tmp_name'];
			$csvfile = fopen($csv_file, 'r');
			$theData = fgets($csvfile);
			$uploadXls=0;
			while (!feof($csvfile)) {
				$csv_data[] = fgets($csvfile, 2000);
				$csv_array = explode(",", $csv_data[$uploadXls]);
				//if(count($csv_array)=="17"){
					$insert_csv = array();
					$id=$csv_array[0];
					$insert_csv['drug_name']=trim($csv_array[1]);
					$insert_csv['drug_ingredient']=trim($csv_array[2]);
				//	$insert_csv['schedule']=trim($csv_array[3]);
					$insert_csv['drug_manifaturer']=trim($csv_array[3]);
					$insert_csv['drug_batch']=trim($csv_array[4]);
					$insert_csv['drug_manifaturer_date']=date("Y-m-d",strtotime($csv_array[5]));
					$insert_csv['drug_expiry_period']=trim($csv_array[6]);
				
					//Expiry Date Calculation
					if(trim($csv_array[7])==""){
						if(is_numeric($csv_array[6])){ $expiryPeriod=" + ".$csv_array[6]." months"; } else {$expiryPeriod=" + ".$csv_array[6];}
						$drugExpiryDate=date("Y-m-d",strtotime($insert_csv['drug_manifaturer_date']." ".$expiryPeriod));
					} else {
						$drugExpiryDate=date("Y-m-d",strtotime($csv_array[7]));
					}
					$insert_csv['drug_expiry_date']=$drugExpiryDate;
					
					$insert_csv['drug_type']=trim($csv_array[8]);
					$insert_csv['drug_strength']=trim($csv_array[9]);
					$insert_csv['quantity']=trim($csv_array[10]);
					$insert_csv['amount_per_strip']=trim($csv_array[11]);
					$insert_csv['amount_per_tab']=($csv_array[12]=="")?(round((trim($csv_array[11])/trim($csv_array[14])),2)):trim($csv_array[12]);
					$insert_csv['unit_per_tab']=trim($csv_array[13]);
					$insert_csv['tablet_qty_strip']=trim($csv_array[14]);
					$insert_csv['discount']=trim($csv_array[15]);
	                $insert_csv['ohc']=trim($csv_array[16]);
					$insert_csv['created_type']=2;
					
					if($insert_csv['drug_name']<>"" && is_numeric($insert_csv['quantity']) && is_numeric($insert_csv['amount_per_tab'])){
$locss=(!empty($_SESSION['ohc_loca']))?$_SESSION['ohc_loca']:$sessionlocation_id;
						$sql="INSERT INTO `pharmacy_stock_detail` (r_pharmacy_id,drug_name,drug_ingredient,drug_manifaturer,drug_batch,drug_manifaturer_date,drug_expiry_period,drug_expiry_date,drug_type,drug_strength,inventory,amount_per_tab,amount_per_strip,tablet_qty_strip,unit_per_tab,quantity,discount,schedule,isactive,upload_generate_id,created_type,created_on,created_role,created_by,ohc,phar_ids) VALUES('".$locss."','".$insert_csv['drug_name']."','".$insert_csv['drug_ingredient']."','".$insert_csv['drug_manifaturer']."','".$insert_csv['drug_batch']."','".$insert_csv['drug_manifaturer_date']."','".$insert_csv['drug_expiry_period']."','".$insert_csv['drug_expiry_date']."','".$insert_csv['drug_type']."','".$insert_csv['drug_strength']."','".$insert_csv['inventory']."','".$insert_csv['amount_per_tab']."','".$insert_csv['amount_per_strip']."','".$insert_csv['tablet_qty_strip']."','".$insert_csv['unit_per_tab']."','".$insert_csv['quantity']."','".$insert_csv['discount']."','".$insert_csv['schedule']."','0','".$uploadGenerateId."','".$insert_csv['created_type']."','".$datetime."','".$tbs_role."','".$tbs_userid."','".$insert_csv['ohc']."','".$_SESSION['phrmcy_id']."')";
				
						$insertSqlObj = new ManageUsers();
						$id = $insertSqlObj->AddDirectQuery($sql);
						$insertSqlObj=null;
						if($id==0){
							$_SESSION['errmsg'][]="The line of ".($uploadXls+1)." is not added. Please check the row.";
						}
					} else {
						$_SESSION['errmsg'][]="The line of ".($uploadXls+1)." is not added. Please check the row.";
					}
				//}
				$uploadXls++;
			}
			/*if(count($csv_array)=="18"){
			} else {
				$_SESSION['errmsg'][]="Column is invalid";
			}*/
			fclose($csvfile);
		}
		echo $uploadGenerateId;
	}

if($action=="ohcuploaded"){
	$phrs=$_REQUEST['phrss'];
		function getExtension1($str) {
			$i = strrpos($str, ".");
			if (!$i) { return ""; }
			$l = strlen($str) - $i;
			$ext = substr($str,$i+1,$l);
			return $ext;
		}
		
		$file=$_FILES['file']['name'];
		$filename = stripslashes($file);
		$extension = getExtension1($filename);
		$extension = strtolower($extension);
		
		$getMaxUploadIdObj = new ManageUsers();
		$sql="SELECT ifnull(max(upload_generate_id),0)+1 as upload_generate_id FROM `pharmacy_stock_detail` WHERE r_pharmacy_id='".$sessionlocation_id."'";
		$getMaxUploadId=$getMaxUploadIdObj->listDirectQuery($sql);
		$getMaxUploadIdObj=null;
		$uploadGenerateId=$getMaxUploadId[0]['upload_generate_id'];
		
		if ($extension == "csv"){
			$csv_file = $_FILES['file']['tmp_name'];
			$csvfile = fopen($csv_file, 'r');
			$theData = fgets($csvfile);
			$uploadXls=0;
			while (!feof($csvfile)) {
				$csv_data[] = fgets($csvfile, 2000);
				$csv_array = explode(",", $csv_data[$uploadXls]);
				//if(count($csv_array)=="17"){
					$insert_csv = array();
					$stid=$csv_array[0];
					$insert_csv['drug_name']=trim($csv_array[1]);
					$insert_csv['drug_ingredient']=trim($csv_array[2]);
					$insert_csv['schedule']=trim($csv_array[3]);
					$insert_csv['drug_manifaturer']=trim($csv_array[4]);
					$insert_csv['drug_batch']=trim($csv_array[5]);
					$insert_csv['drug_manifaturer_date']=date("Y-m-d",strtotime($csv_array[6]));
					$insert_csv['drug_expiry_period']=trim($csv_array[7]);
				
					//Expiry Date Calculation
					if(trim($csv_array[8])==""){
						if(is_numeric($csv_array[7])){ $expiryPeriod=" + ".$csv_array[7]." months"; } else {$expiryPeriod=" + ".$csv_array[7];}
						$drugExpiryDate=date("Y-m-d",strtotime($insert_csv['drug_manifaturer_date']." ".$expiryPeriod));
					} else {
						$drugExpiryDate=date("Y-m-d",strtotime($csv_array[8]));
					}
					$insert_csv['drug_expiry_date']=$drugExpiryDate;
					
					$insert_csv['drug_type']=trim($csv_array[9]);
					$insert_csv['drug_strength']=trim($csv_array[10]);
					$insert_csv['quantity']=trim($csv_array[11]);
					$insert_csv['amount_per_strip']=trim($csv_array[12]);
					$insert_csv['amount_per_tab']=($csv_array[13]=="")?(round((trim($csv_array[12])/trim($csv_array[15])),2)):trim($csv_array[13]);
					$insert_csv['unit_per_tab']=trim($csv_array[14]);
					$insert_csv['tablet_qty_strip']=trim($csv_array[15]);
					$insert_csv['discount']=trim($csv_array[16]);
	$insert_csv['ohc']=trim($csv_array[17]);
		//$insert_csv['qanty']=trim($csv_array[18]);
					$insert_csv['created_type']=2;
					
					if($insert_csv['drug_name']<>"" && is_numeric($insert_csv['quantity']) && is_numeric($insert_csv['amount_per_tab'])){
//'".$sessionlocation_id."'
$locss=(!empty($_SESSION['ohc_loca']))?$_SESSION['ohc_loca']:$sessionlocation_id;
						$sql="INSERT INTO `pharmacy_stock_detail` (r_pharmacy_id,drug_name,drug_ingredient,drug_manifaturer,drug_batch,drug_manifaturer_date,drug_expiry_period,drug_expiry_date,drug_type,drug_strength,inventory,amount_per_tab,amount_per_strip,tablet_qty_strip,unit_per_tab,quantity,discount,schedule,isactive,upload_generate_id,created_type,created_on,created_role,created_by,ohc,phar_ids) VALUES('$locss','".$insert_csv['drug_name']."','".$insert_csv['drug_ingredient']."','".$insert_csv['drug_manifaturer']."','".$insert_csv['drug_batch']."','".$insert_csv['drug_manifaturer_date']."','".$insert_csv['drug_expiry_period']."','".$insert_csv['drug_expiry_date']."','".$insert_csv['drug_type']."','".$insert_csv['drug_strength']."','".$insert_csv['inventory']."','".$insert_csv['amount_per_tab']."','".$insert_csv['amount_per_strip']."','".$insert_csv['tablet_qty_strip']."','".$insert_csv['unit_per_tab']."','".$insert_csv['quantity']."','".$insert_csv['discount']."','".$insert_csv['schedule']."','0','".$uploadGenerateId."','".$insert_csv['created_type']."','".$datetime."','".$tbs_role."','".$tbs_userid."','".$insert_csv['ohc']."','".$_REQUEST['phrss']."')";						$insertSqlObj = new ManageUsers();
						$id = $insertSqlObj->AddDirectQuery($sql);
						$sql1="INSERT INTO `pharmacy_sold_stock_detail`(r_pharmacy_id,r_user_id,r_appuser_walkin_id,r_stock_id,qty,amount,discount,created_by,created_role,created_on,ohc,phar_ids,stock_generate_id) VALUES ('".$locss."','".$user_id."','3','".$stid."','".$insert_csv['quantity']."','".$insert_csv['amount_per_tab']."','".$insert_csv['discount']."','".$tbs_userid."','".$tbs_role."','".$datetime."','1','".$_REQUEST['phrmcy_id']."','".$uploadGenerateId."')";
								$updates = $insertSqlObj->AddDirectQuery($sql1);
						$insertSqlObj=null;
						if($id==0){
							//$_SESSION['errmsg'][]="The line of ".($uploadXls+1)." is not added. Please check the row.";
						}
					} else {
						//$_SESSION['errmsg'][]="The line of ".($uploadXls+1)." is not added. Please check the row.";
					}
				//}
				$uploadXls++;
			}
			/*if(count($csv_array)=="18"){
			} else {
				$_SESSION['errmsg'][]="Column is invalid";
			}*/
			fclose($csvfile);
		}
			echo $uploadGenerateId;
	}


	
	if($action=="existUploadXls"){
		$getListStocksIdObj = new ManageUsers();
		if(empty($_SESSION['ohc_phar'])){
		$sql="SELECT psd.upload_generate_id,psd.isactive,mpu.first_name,mpu.last_name,mpu.user_id,psd.created_on FROM `pharmacy_stock_detail` psd 
		LEFT OUTER JOIN `master_pharmacy_user` mpu ON mpu.id=psd.created_by
		WHERE  psd.r_pharmacy_id='".$sessionlocation_id."' AND psd.created_type=2 GROUP BY psd.upload_generate_id";
		}
		else{
	$sql="SELECT psd.upload_generate_id,psd.isactive,mpu.first_name,mpu.last_name,mpu.user_id,psd.created_on,psd.phar_ids FROM `pharmacy_stock_detail` psd 
	LEFT OUTER JOIN `master_corporate_user` mpu ON mpu.id=psd.created_by WHERE  psd.r_pharmacy_id='".$_SESSION['ohc_loca']."' and psd.ohc='1' AND 
        psd.created_type=2 GROUP BY psd.upload_generate_id order by psd.created_on desc";
			
		}

		$getListStocksIds=$getListStocksIdObj->listDirectQuery($sql);
		$getListStocksIdObj=null;
		if($getListStocksIds[0]['upload_generate_id']<>""){
			echo '
			<div style="width:100%; padding: 10px 10%;">
				<table class="table table-nomargin table-mail" width="80%" align="center" cellpadding="5" cellspaving="3">';
				echo '<tr><th>User</th><th>Date</th><th style="text-align:center;">Action</th></tr>';
				foreach($getListStocksIds as $getListStocksId){
$opr="";
if(!empty($_SESSION['ohc_phar'])){
$opr="&phr=".$getListStocksId['phar_ids'];
}

					echo '<tr><td>'.$getListStocksId['first_name'].' '.$getListStocksId['last_name'].'/'.$getListStocksId['user_id'].'</td><td>'.date("d M Y D, H:i:s",strtotime($getListStocksId['created_on'])).'</td><td style="text-align:center;">';
					if($getListStocksId['isactive']=='0'){
				echo	'<a class="btn btn-primary" href="'.$sitepath.'pharmacy_stock_avail_upload.php?id='.$getListStocksId['upload_generate_id'].''.$opr.'">Check List</a>';
					}else{
						echo	'<b style="color:#199BBF">Updated</b>';
					}
				echo	'</td></tr>';
				}
				echo '</table>
			</div>';
		}
	}
	
	
	
	
	if($action=="confirmActive"){
		$uploadGenerateId=$_POST["uploadGenerateId"];
$locss=(!empty($_SESSION['ohc_loca']))?$_SESSION['ohc_loca']:$sessionlocation_id;
		$sql="UPDATE `pharmacy_stock_detail` SET isactive=1 WHERE upload_generate_id='".$uploadGenerateId."' AND r_pharmacy_id='".$locss."' AND created_type=2";
		$getListStocksIdObj = new ManageUsers();
		$getListStocksIds=$getListStocksIdObj->listDirectQuery($sql);
		$getListStocksIdObj=null;
	}
	
	if($action=="confirmDiscard"){
$locss=(!empty($_SESSION['ohc_loca']))?$_SESSION['ohc_loca']:$sessionlocation_id;
		$uploadGenerateId=$_POST["uploadGenerateId"];
		$sql="DELETE FROM `pharmacy_stock_detail` WHERE upload_generate_id='".$uploadGenerateId."' AND r_pharmacy_id='".$locss."' AND created_type=2";
		$getListStocksIdObj = new ManageUsers();
		$getListStocksIds=$getListStocksIdObj->listDirectQuery($sql);
		$getListStocksIdObj=null;
	}
	
	
	
	
	
	if($action=="calculateExpiry"){
		$manufacturDates=explode("/",$_POST["manufacturDate"]);
		$manufacturDate=$manufacturDates[2]."/".$manufacturDates[1]."/".$manufacturDates[0];
		$expiryPeriod=$_POST["expiryPeriod"];
		if(is_numeric($expiryPeriod)){ $expiryPeriod=" + ".$expiryPeriod." months"; } else {$expiryPeriod=" + ".$expiryPeriod;}
		echo date("d/m/Y",strtotime($manufacturDate." ".$expiryPeriod));
	}
	
	
	
	
	
	/*if($action=="walkinUser"){
		$id=$_POST["id"];
		if(strstr($id,"walkin")){
			$id=str_replace("walkin","",$id);
			$sql="SELECT mud.*,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' Month'), concat(TIMESTAMPDIFF(year,mud.dob,NOW()), ' Year')) as age,pincode.doctype as pincodeName FROM `prescription` p
			LEFT OUTER JOIN `master_user_details` mud ON mud.id=p.user_id
			LEFT OUTER JOIN `doctype` pincode ON pincode.id=mud.pincode
			WHERE mud.id='".$id."' AND p.fav_pharmacy=".$sessionlocation_id." GROUP BY mud.id";
		} else {
			$sql="SELECT pwu.*,pincode.doctype as pincodeName FROM `pharmacy_walkin_user` pwu 
			LEFT OUTER JOIN `doctype` pincode ON pincode.id=pwu.pincode
			WHERE pwu.id='".$id."' AND pwu.r_pharmacy_id='".$sessionlocation_id."'";
		}
		$getListUserIdObj = new ManageUsers();
		$getListUserIds=$getListUserIdObj->listDirectQuery($sql);
		$getListUserIdObj=null;
		echo $first_name=$getListUserIds[0]['first_name']."***//***";
		echo $last_name=$getListUserIds[0]['last_name']."***//***";
		echo $mob_num=$getListUserIds[0]['mob_num']."***//***";
		echo $gender=$getListUserIds[0]['gender']."***//***";
		echo $age=$getListUserIds[0]['age']."***//***";
		echo $dob=date("d-m-Y",strtotime($getListUserIds[0]['dob']))."***//***";
		echo $email=$getListUserIds[0]['email']."***//***";
		echo $address=$getListUserIds[0]['address1']."***//***";
		echo $pincode=$getListUserIds[0]['pincode']."***//***";
		echo $pincodeName=$getListUserIds[0]['pincodeName']."***//***";
	}*/
	
	
	
	
	//Popup in center screen
	if($action=="bringCountRequest"){
		$dateTime=$_POST['dateTime'];
if(empty($_SESSION['ohc_loca'])){
		$sql="SELECT COUNT(id) as did FROM `prescription` WHERE fav_pharmacy='".$sessionlocation_id."' AND created_on>='".$dateTime."'";
}else{
			$da=date("Y-m-d");
		 $min=date("H:i:s", strtotime('-1 minutes'));
			$dateTime=$da." ".$min;
			$sql="SELECT COUNT(id) as did FROM `prescription` WHERE fav_pharmacy='".$_SESSION['ohc_loca']."' AND ohc='1' and created_on>='".$dateTime."'";
		}
		$countTkObj = new ManageUsers();
		$countTk=$countTkObj->listDirectQuery($sql);
		$countTkObj=null;
	/*	$count=$countTk[0]['did'];
		if($count>0){
			echo '<img src="'.$sitepath.'img/close.png" style="height:25px;width:25px;" onclick="closecronCountLoadPagePharmacyRequest()"> You have received '.$count.' new request/s.';
		} else {
			//Dont return anythin
		}*/
	}
	
	
/*	if($action=="bringCountpRequest"){
	$countTkObj = new ManageUsers();
		 $dateTime=$_POST['dateTime'];
if(empty($_SESSION['ohc_loca'])){
		$sql="SELECT COUNT(id) as did FROM `prescription` WHERE fav_pharmacy='".$sessionlocation_id."' AND created_on>='".$dateTime."'";
}else{
			$pharmacyright=$countTkObj->listDirectQuery("SELECT phrmacy_id from ohc_rights where corp_id=".$_SESSION['parent_id']." and user_id=".$_SESSION['userid']);

			$da=date("Y-m-d");
		 $min=date("H:i:s", strtotime('-1 minutes'));
			$dateTime=$da." ".$min;
			
		 	$sql="SELECT COUNT(id) as did FROM `prescription` WHERE fav_pharmacy in (".$pharmacyright[0]['phrmacy_id'].") AND ohc='1' and created_on>='".$dateTime."'";
		}
		$countTk=$countTkObj->listDirectQuery($sql);
		$countTkObj=null;
	$count=$countTk[0]['did'];
		if($count>0){
			echo '<img src="'.$sitepath.'img/close.png" style="height:25px;width:25px;" onclick="closecronCountLoadPagePharmacyRequest()"> You have received '.$count.' new request/s.';
		} else {
			//Dont return anythin
		}
	}*/

if($action=="addalternatedrug"){
	$countTkObj = new ManageUsers();
    	$updateOrderStatusObj = new ManageUsers();
    	
			$updateOrderStatus=$updateOrderStatusObj->listDirectQuery("UPDATE `prescription_detail` SET alternate_drug='".$_POST['drugname']."',alternate_qty='".$_POST['drugqty']."' WHERE id='".$_POST['prescriptionrowid']."'");
			$updateOrderStatusObj=null;
}
if($action=="alternateclose"){

    	$updateOrderStatusObj = new ManageUsers();
    
			$updateOrderStatus=$updateOrderStatusObj->listDirectQuery("UPDATE `prescription_detail` SET alternate_drug='',alternate_qty='' WHERE id='".$_POST['prescriptionid']."'");
			$updateOrderStatusObj=null;
}
	
	
}


?>




