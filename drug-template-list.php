<? ob_start();
include_once('core/class.manageUsers.php');
include('session.php');
extract($_REQUEST);
$GtPatientSearch = new ManageUsers();
$whr.="";
if($drug_name!=""){$whr.=" and (drug_name like '".$drug_name."%')"; }
if($manufacturer!=""){$whr.=" and (drug_manifaturer like '".$manufacturer."%')"; }
$loc=$_SESSION['loc_id'];
$template=$GtPatientSearch ->listDirectQuery("select * from drug_template WHERE location_id=" . $loc );
$contents.="S.No,";
$contents.="Drug Template Id,";
$contents.="Drug Name,";
$contents.="Drug Ingredients,";
$contents.="Schedule,";
$contents.="Mfg name,";
$contents.="Batch No.,";
$contents.="Mfd Date,";
$contents.="Period to Expiry in months,";
$contents.="Expiry date,";
$contents.="Drug Type,";
$contents.="Drug Strength,";
$contents.="Availability (Nos),";
$contents.="M.R.P in INR,";
$contents.="M.R.P/Unit in INR,";
$contents.="Unit,";
$contents.="Package Unit,";
$contents.="Discount in %,";
$contents.="Discount Settings,";
$contents.="OHC,";
$contents.="\n";
	$i=0;

	
foreach($template as $row ){ 
$i=$i+1;
 $inju = new ManageUsers();
$ingredients=explode('~',$row['drug_ingredient'] );
	$arr=[];
	foreach($ingredients as $ins){
	if(!empty($ins)){
	$injus=$inju->listDirectQuery("  select *  from ingredients where id=".$ins." ");
	
	$arr[]=$injus[0]['name'];
	}
	}
  $ingredient=implode("~ ",$arr);

$contents.=$i.",";
$contents.=$row['id'].",";
$contents.=$row['drug_name'].",";
$contents.=$ingredient.",";
$contents.=$row['schedule'].",";
$contents.=$row['drug_manifaturer'].",";
$contents.=",";
$contents.=",";
$contents.=",";
$contents.=",";
$contents.=$row['drug_type'].",";
$contents.=$row['drug_strength'].",";
$contents.=",";
$contents.=$row['amount_per_strip'].",";
$contents.=$row['amount_per_tab'].",";
$contents.=$row['unit_per_tab'].",";
$contents.=$row['tablet_qty_strip'].",";
$contents.=$row['discount']. ",";
$contents.=$row['discount_settings']. ",";
$contents.=$row['ohc'].",";
$contents.="\n";

}	
$loc="Drug Template";
header('Content-type: application/csv');
header("Content-Disposition: attachment; filename=".$loc."-".date('dmY').".csv");
ob_clean();
print $contents;
?>