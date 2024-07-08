<?php
/*
Description: Maintenance Screen
Created By:
Created Date:
Modified By:Punitha Subramani
Modified Date: 16-Jan-2015
*/
include_once("../session.php");
require_once("../core/class.manageUsers.php");
require_once("../core/class.getDetails.php");
$chphr=$_POST['chphr'];?>


									
				
<?	$tday=date("Y-m-d");
function limit_char($tchar,$tlimit){
	if(strlen($tchar)<=$tlimit){
		echo $tchar;
	} else {
		$ychar=substr($tchar,0,$tlimit) . '...';
		echo $ychar;
	}
}
$obj = new ManageUsers();
$fliter3= $obj->listDirectQuery("select reminder_issue from master_corporate where id='".$_SESSION['currentlocation_id']."' ");
		
	
					 $remindissue="+".$fliter3[0]['reminder_issue']."days";
					 
			$nearexpiry=date("Y-m-d",strtotime($tday." ".$remindissue));

if(isset($_POST) && !empty($_POST)){
	$action = $_POST['action'];
	$datetime=date("Y-m-d H:i:s");
	$datetimeDisplay=date("d M Y");

	if($action=="editGrid"){ //To edit the grid
		$doctype = $_POST['doctype'];
		$id = $_POST['id'];
		if($id==0){
			$sql="INSERT INTO `pharmacy_stock_detail` (r_pharmacy_id,drug_name,drug_ingredient,drug_manifaturer,drug_batch,drug_manifaturer_date,drug_expiry_period,drug_expiry_date,drug_type,drug_strength,inventory,amount_per_tab,quantity,isactive,created_type,created_on,created_role,created_by) VALUES('".$sessionlocation_id."','".$drug_name."','".$drug_ingredient."','".$drug_manifaturer."','".$drug_batch."','".$drug_manifaturer_date."','".$drug_expiry_period."','".$drug_expiry_date."','".$drug_type."','".$drug_strength."','".$inventory."','".$amount_per_tab."','".$quantity."','".$isactive."','1','".$datetime."','".$tbs_role."','".$tbs_userid."')";	
		} else {
			$sql="UPDATE `pharmacy_stock_detail` SET drug_name='".$drug_name."',drug_ingredient='".$drug_ingredient."',drug_manifaturer='".$drug_manifaturer."',drug_batch='".$drug_batch."',drug_manifaturer_date='".$drug_manifaturer_date."',drug_expiry_period='".$drug_expiry_period."',drug_expiry_date='".$drug_expiry_date."',drug_type='".$drug_type."',drug_strength='".$drug_strength."',inventory='".$inventory."',amount_per_tab='".$amount_per_tab."',quantity='".$quantity."',isactive='".$isactive."',modified_type='1',modified_on='".$datetime."',modified_role='".$tbs_role."',modified_by='".$tbs_userid."' WHERE id='".$id."' AND r_pharmacy_id='".$sessionlocation_id."'";	
		}
		$updateSqlData = new ManageUsers();
		$querySqlData = $updateSqlData->listDirectQuery($sql);
		
			
		$updateSqlData=null;
		
		if($id==0){
			$sqlCount="SELECT count(id) as did FROM `pharmacy_stock_detail` WHERE isactive=1 AND r_pharmacy_id='".$sessionlocation_id."'".$whr;
			$details = new ManageUsers();
			$detailsObj = $details->listDirectQuery($sqlCount);
			$details = null;
			$count = $detailsObj[0]['did'];
			echo $x =  ceil(intVal($count) / intVal($_POST['recsPerPage']));
		}
	} else { //To display the grid
		$pageNo = $_POST['pageNo'];
		$recsPerPage = $_POST['recsPerPage'];
		$searchValue = $_POST['searchValue'];
		$manufa=$_POST['manufa'];
$filt=$_POST['filt'];
		$whereClass="";

		if($pageNo=="" || $pageNo==0 ){
			$pageNo=1;
		}
		if($recsPerPage=="" || $recsPerPage==0 ){
			//$recsPerPage=($defaultPageDisplay=="")?10:$defaultPageDisplay;
		}
		if(!isset($pageNo)){$pageNo=1;}
		$startLimitSql=($pageNo-1)*$recsPerPage;
		$startLimit=($pageNo-1)*$recsPerPage+1;
		
		 
		if($searchValue!=""){
			$whr.=" AND psd.drug_name LIKE '".$searchValue."%'";
		}
		if($manufa!=""){
			$whr.=" AND psd.drug_manifaturer LIKE '".$manufa."%'";
		}
if($filt=='1'){
    $hav=" HAVING  balance_quantity > '0' ";
$whr.=" AND psd.drug_expiry_date < '$tday' ";
}
if($filt=='2'){
$hav=" HAVING  balance_quantity < '1' ";
}
if($filt=='3'){
   $hav=" HAVING  balance_quantity > '0' ";
$whr.=" AND psd.drug_expiry_date <= '$nearexpiry' and   psd.drug_expiry_date >= '$tday'";
}
if(empty($filt) && !isset($_POST['uploadGenerateId'])){
$hav=" HAVING  balance_quantity > '0' ";
$whr.=" AND psd.drug_expiry_date > '$nearexpiry' ";

}

		$referLink=explode("?",basename($_SERVER["HTTP_REFERER"]));
		$pageNameHalf=$referLink[0];
		if(basename($_SERVER["HTTP_REFERER"])=="pharmacy_stock_avail_upload.php" || $pageNameHalf=="pharmacy_stock_avail_upload.php"){
			if(isset($_POST['uploadGenerateId'])){
				$whr.=" AND psd.upload_generate_id=".$_POST['uploadGenerateId'];
			
			}
	if(isset($_POST['phr']) && !empty($_POST['phr'])){
				$whr.=" and psd.phar_ids='".$_POST['phr']."'";
				}
			$whr.=" AND psd.isactive=0";
		} else {
			$whr.=" AND psd.isactive=1";
		}
		if(empty($_SESSION['ohc_loca'])){
			$whr.=" AND psd.r_pharmacy_id='".$sessionlocation_id."'";
$joinm="master_pharmacy";
		}
		else{
			$whr.=" AND psd.r_pharmacy_id='".$_SESSION['ohc_loca']."' AND psd.ohc='1' ";
$joinm="master_corporate";
		}
		if(!empty($_SESSION['ohc_loca']) && !isset($_POST['uploadGenerateId'])){
		if(!empty($chphr)){
			$whr.=" and psd.phar_ids='$chphr' ";
		}else{
			$whr.=" and psd.phar_ids='".$_SESSION['phrmcy_id']."' ";
		}
		}
		$sortField=($_POST['sortField']=="")?"psd.drug_name":$_POST['sortField'];
		$sortType=($_POST['sortType']=="")?"ASC":$_POST['sortType'];
		
	
		
$sql="SELECT SQL_CALC_FOUND_ROWS * FROM 
				(SELECT psd.*,mp.reminder_issue,(psd.quantity-IFNULL(SUM(pssd.qty),0)) as balance_quantity,DATE_FORMAT(psd.created_on,'%d %b %Y') as added,DATE_FORMAT(psd.modified_on,'%d %b %Y') as modified,ifnull(mp.reminder_expiry,0) as reminder_expiry,'-1' as total_count
				FROM `pharmacy_stock_detail` psd
				LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
				LEFT OUTER JOIN  ".$joinm." mp ON mp.id = pssd.r_pharmacy_id
				WHERE 1=1 ".$whr." GROUP BY psd.id ".$hav." ORDER BY ".$sortField." ".$sortType.") AS countTble 
		  UNION (SELECT '','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',FOUND_ROWS() as total_count)";
//echo $sql	
		$getSqlData = new ManageUsers();
		$listingMaintenaces = $getSqlData->listDirectQuery($sql);
		$getSqlData = null;
		$count=$listingMaintenaces[(count($listingMaintenaces)-1)]["total_count"];
		$endLimit=($count<=(($startLimit-1)+$recsPerPage))?$count:(($startLimit-1)+$recsPerPage);
		if(!empty($_SESSION['ohc_loca'])){
		if(!empty($chphr)){
		 $obj = new ManageUsers();
	$pharmsy=$obj->listDirectQuery(" select * from ohc_pharmay where id='$chphr' ");
		}
		} //if($_SESSION['phrmcy_id']==$chphr){
		?>


<div class="w-100 float-left">
    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 float-left top-pad">
<b style="font-size:16px;color:#<? echo $_SESSION['tclr'] ?>;"> Stock Availability</b></div>
 <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 float-left top-pad" style="text-align:center;">
<a href="<? echo $sitepath?>export_tablets.php?phar=<? echo $chphr;?>"><img src="<? echo $sitepath?>/img/export-to-csv.png" style="height:30px; cursor:pointer;" alt="Export" title="Export" /></a></div>

<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 float-right top-pad text-right adstock">
 <? 
 $main=$obj->listDirectQuery(" select id from ohc_pharmay where mainpharmacy='1' and location_id=".$_SESSION['ohc_loca']."");
 if(($chphr==$main[0]['id']) ){  ?>
		<a href="add-stock-pharma.php?pharma_id=<?php echo $chphr;?>" class="btn btn-sm btn-primary">Add Stock</a>
<?   }

else if(empty($_SESSION['ohc_loca'])){?>
	<a href="add-stock-pharma.php" class="btn btn-sm btn-primary">Add Stock</a> <? } ?>
				</div>	
<? //}?>
</div>
<div>
		<!--<table class="table table-striped table-nomargin table-mail">-->
		<table width="100%" cellpadding="5" cellspacing="3" border="1" class="table tableborder table-striped">
			<thead>
				<tr style="color:white;background-color:#<? echo $_SESSION['tclr'] ?>; ">
					<th style="text-align:center;">#</th>
					<th>
							<div style="width: 15%; float:left; text-align:left; color: #fff;">
								<label onclick="getListofDatas('','','','drug_name','asc');" style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-up" aria-hidden="true"></i></label>
								<label onclick="getListofDatas('','','','drug_name','desc');" style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-down" aria-hidden="true"></i></label>
							</div>
							<div style="width:85%; float:left; text-align:left; color: #fff;">
								Drug Name (Strength)<br> Type
							</div>
					</th>
					<th>
							<div style="width: 10%; float:left; text-align:left; color: #fff;"> 
								<label onclick="getListofDatas('','','','drug_batch','asc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-up" aria-hidden="true"></i></label>
								<label onclick="getListofDatas('','','','drug_batch','desc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-down" aria-hidden="true"></i></label>
							</div>
							<div style="width: 90%; float:left; text-align:left; color: #fff;">
								Manufacturer<br>
								Batch Number / Quantity
							</div>
					</th>
					<th>
						<div style="width:15%; float:left; text-align:left; color: #fff;"> 
							<label onclick="getListofDatas('','','','drug_expiry_date','asc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-up" aria-hidden="true"></i></label>
							<label onclick="getListofDatas('','','','drug_expiry_date','desc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-down" aria-hidden="true"></i></label>
						</div>
						<div style="width:85%; float:left; text-align:left; color: #fff;">
							Mfg Date<br>Expiry Date
						</div>
					</th>
					<th>
						<div style="width:15%; float:left; text-align:left; color: #fff;">
							<label onclick="getListofDatas('','','','amount_per_tab','asc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-up" aria-hidden="true"></i></label>
						 	<label onclick="getListofDatas('','','','amount_per_tab','desc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-down" aria-hidden="true"></i></label>
						</div>
						<div style="width:85%; float:left; text-align:left; color: #fff;">
							MRP / CPU<br>Discount
						</div>
					</th>
					<th>
						<div style="width:15%; float:left; text-align:left; color: #fff;">
							<label onclick="getListofDatas('','','','created_on','asc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-up" aria-hidden="true"></i></label>
							<label onclick="getListofDatas('','','','created_on','desc');"  style="height:5px; padding: 0 5px 5px 0;"><i class="fa fa-arrow-down" aria-hidden="true"></i></label>
						</div>
						<div style="width:85%; float:left; text-align:left; color: #fff;">Added Date <br> Modified Date</div>
					</th>
					<th>View</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$countQual=0;
			 $tday=date("Y-m-d");
			 $getSqlData = new ManageUsers();
		$stopissues= $getSqlData->listDirectQuery("select reminder_issue,reminder_expiry from ".$joinm." where id='".$_SESSION['currentlocation_id']."' ");
		//echo "select reminder_issue from ".$joinm." where id='".$_SESSION['currentlocation_id']."'";
		$getSqlData = null;
					 $stp="+".$stopissues[0]['reminder_expiry']."days";
					 
			$stopiss=date("Y-m-d",strtotime($tday." ".$stp));
			
			if($listingMaintenaces!="" && count($listingMaintenaces)>1){
				foreach($listingMaintenaces as $listingMaintenace){
					if($listingMaintenace["total_count"]=="-1"){
						$countQual=$countQual+1;
				
						$drugName = $listingMaintenace['drug_name'];
						$balance_quantity = $listingMaintenace['balance_quantity'];
						$id = $listingMaintenace['id'];
						$added = $listingMaintenace['added'];
						$modified = $listingMaintenace['modified'];
						$drug_expiry_date = $listingMaintenace['drug_expiry_date'];
						$reminder_expiry = $listingMaintenace['reminder_expiry'];
						$drug_type = $listingMaintenace['drug_type'];
						$isactive = $listingMaintenace['isactive'];
						$listingMaintenace['drug_expiry_date']." - ".$reminder_expiry;
						$expirydate=date("d-m-Y",strtotime($listingMaintenace['drug_expiry_date']));
						if($reminder_expiry==""){
							$expiryDateCheck=date("d-m-Y",strtotime($listingMaintenace['drug_expiry_date']));
						} else {
							$expiryDateCheck=date("d-m-Y",strtotime("-".$reminder_expiry,strtotime($listingMaintenace['drug_expiry_date'])));
						}
						$today=date("d-m-Y");
					    
						if(strtolower($drug_type)=="capsule" || strtolower($drug_type)=="tablet"){
							$drug_type="<img src='".$sitepath."/img/pharma_ico/pharma_ico7.png' style='width:18px;' /> ".$drug_type;
						} else if(strtolower($drug_type)=="syrup"){
							$drug_type="<img src='".$sitepath."/img/pharma_ico/pharma_ico13.png' style='width:18px;' /> ".$drug_type;
						} else if(strtolower($drug_type)=="injection"){
							$drug_type="<img src='".$sitepath."/img/pharma_ico/pharma_ico5.png' style='width:18px;' /> ".$drug_type;
						} else {
							$drug_type="<img src='".$sitepath."/img/pharma_ico/pharma_ico15.png' style='width:18px;' /> ".$drug_type;
						}
						
						$style="";
			//echo $today."<".$expiryDateCheck."&&".$expiryDateCheck."<".$stopiss."</br>";
			//echo ($tday)."<".($listingMaintenace['drug_expiry_date'])."&&".($stopiss).">".($listingMaintenace['drug_expiry_date']);
						if(strtotime($today)>=strtotime($expirydate) || $isactive=="0"){$style="color:#f00;";}
						elseif(strtotime($tday) < strtotime($listingMaintenace['drug_expiry_date']) && strtotime($stopiss) > strtotime($listingMaintenace['drug_expiry_date'])){
						    if($filt=='3'){
							$style="color:#42BAA3;";
						    } else {
						        $style="color:#42BAA3;";
						    }
						}
						if(isset($_POST['uploadGenerateId'])){
							$style="";
						}
						if($filt=='2'){
							$style="";
						}
						
						
		
						echo '<tr id="tr'.$id.'" style="'.$style.'">
								<td valign="top" style="text-align:center;">'.$countQual.' </td>
								<td valign="top"><b>'.$listingMaintenace['drug_name'].'</b><br> '.$drug_type.'</td>
								<td valign="top">'.$listingMaintenace['drug_manifaturer'].'<br>'.$listingMaintenace['drug_batch'].' / '.$listingMaintenace['balance_quantity'].'</td>
									<td valign="top">'.date("d-m-Y",strtotime($listingMaintenace['drug_manifaturer_date'])).'<br>'.date("d-m-Y",strtotime($listingMaintenace['drug_expiry_date'])).'</td>
								<td align="left" valign="top">'.$listingMaintenace['amount_per_strip'].' / '.$listingMaintenace['amount_per_tab'].'<br>'.$listingMaintenace['discount'].'%</td>
								<td valign="top">'.$added.'<br> '.$modified.'</td>
<td align="center" valign="top">';
	 if(!empty($_SESSION['mainphr']) || empty($_SESSION['ohc_loca'])){							
 echo '<a href="add-stock-pharma.php?id='.$id.'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'; 
	 }echo '</td>
							</tr><tr><td style="border-bottom: 1px #ccc Dashed;"></td><td colspan="6" style="border-bottom: 1px #ccc Dashed;"><i>';
								limit_char($listingMaintenace['drug_ingredient'],57);
						echo '</i></td></tr>';
						}
					}
					?>
					</tbody>
				</table></div>|*|*|*|*|<?php echo $pageNo; ?>|*|*|*|*|<?php echo $startLimit;?>|*|*|*|*|<?php echo $endLimit; ?>|*|*|*|*|<?php echo $count;?>|*|*|*|*|<?php echo $pharmsy[0]['name']; 
			} else {
			
				echo "<table class=\"table table-striped table-nomargin table-mail\" ><tr class=\"test\"><td class=\"table-fixed-medium\">No Data to Display.</td></tr></table>";?>
|*|*|*|*|<?php echo " "; ?>|*|*|*|*|<?php echo " ";?>|*|*|*|*|<?php echo " "; ?>|*|*|*|*|<?php echo " ";?>|*|*|*|*|<?php echo $pharmsy[0]['name']; ?>
			<?}
	}
?>
<?php } ?>