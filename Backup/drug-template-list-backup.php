<?php


ob_start();
ini_set("display_errors","1");
require_once('header.php'); 
require_once('top-menu.php'); 
require_once('left-nav.php');
include_once('core/class.manageUsers.php');
extract($_REQUEST);
//print_r($_REQUEST);
$obj= new ManageUsers();
$loc=$_SESSION['loc_id'];
$template=$obj->listDirectQuery("select * from drug_template   ");





?>
 <link href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
 <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>



</script>
<div class="container-fluid" id="content">

<div id="main">
<div class="Top-Strip">
<h5 style="font-weight:bold;color:#<? echo $_SESSION['tclr'] ?>;">Drug Template List</h5>
</div>


<div class="content-table float-left" style="border-bottom: 1px #ccc dashed;">
    			<div class="col-xl-4 col-lg-4 col-md-4 float-left p-2">
<input type="text" id="drug_name" class="icon_90" name="search" value="" placeholder="Drug Name" onKeyUp="search()"  /></div>
						<div class="col-xl-4 col-lg-4 col-md-4 float-left p-2">					
<input type="text" id="manufacture" class="icon_90" name="search" value="" placeholder="Manufacturer" onKeyUp="search()" /></div>



</div>
	<div class="content-table pb-0 float-left" style=" color:#199BBF; font-size: 16px;"> 
							
							
										
							<div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-4 float-left text-center">
							<img src="<? echo $sitepath?>/img/export-to-csv.png" class="export" onclick="export_template()" alt="Export" title="Export" /></div>

							<div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-4 float-right text-right">
							    <a href="add-drug-template.php" class="btn btn-sm btn-primary">Add Template</a>
						
							</div>
							
							
							
										
	

							</div>
				
							
							
							

<div class="content-table pb-0 float-left" style=" color:#199BBF; font-size: 16px;"> 


<div class="content-table float-left staticdata">
    
<table width="100%" cellpadding="0" cellspacing="2" border="1" align="center" class="display nowrap tableborder table  table-striped " id="result" style="font-size:14px">
<thead>
<tr style="color:white;background-color:#<? echo $_SESSION['tclr'] ?>; ">

<th  style="text-align:left;width:25%;">Drug Name</th>
<th style="text-align:left;width:10%;">Drug Type</th>
<th  style="text-align:left;width:15%;">Manufacturer</th>
<th  style="text-align:left;width:10%;">HSN Code</th>
<th  style="text-align:left;width:15%;">Ingredient</th>
<th  style="text-align:left;width:15%;">Schedule</th>
<th  style="text-align:left;width:15%;">Restock Count</th>
<th  style="text-align:center;width:5%;">Edit</th>

</tr>
</thead>
<tbody>
<? 

 foreach($template as $l){
$inju = new ManageUsers();
$ingredients=explode('~',$l['drug_ingredient'] );
	$arr=[];
	foreach($ingredients as $ins){
	if(!empty($ins)){
	$injus=$inju->listDirectQuery("  select *  from ingredients where id=".$ins." ");
	
	$arr[]=$injus[0]['name'];
	}
	}
  $ingredient=implode(", ",$arr);
?>
<tr style="color:black;">


<td style="text-align:left;"><?=$l['drug_name']?></td>
<td style="text-align:left;"><?=$l['drug_type']?></td>
<td style="text-align:left;"><?=$l['drug_manifaturer']?></td>
<td style="text-align:left;"><?=$l['hsncode']?></td>
<td style="text-align:left;"><?=$ingredient?></td>
<td style="text-align:left;"><?=$l['schedule']?></td>
<td style="text-align:left;"><?=$l['inventory']?></td>
<td style="text-align:center;">
    <!--<a href="add_stock_pharma_template.php?id=<?=$l['id']?>&drug_name=<?=urlencode($l['drug_name'])?>&drug_type=<?=urlencode($l['drug_type'])?>&drug_manifaturer=<?=urlencode($l['drug_manifaturer'])?>&hsncode=<?=urlencode($l['hsncode'])?>&ingredient=<?=urlencode($ingredient)?>&schedule=<?=urlencode($l['schedule'])?>&inventory=<?=urlencode($l['inventory'])?>">-->
    <!--    <i class="fa fa-eye" aria-hidden="true"></i>-->
    <!--</a>-->
    
    <a href="add_stock_pharma_template.php?id=<?=urlencode($l['id'])?>&drug_name=<?=urlencode($l['drug_name'])?>&drug_type=<?=urlencode($l['drug_type'])?>&drug_manifaturer=<?=urlencode($l['drug_manifaturer'])?>&hsncode=<?=urlencode($l['hsncode'])?>&ingredient=<?=urlencode($ingredient)?>&schedule=<?=urlencode($l['schedule'])?>&inventory=<?=urlencode($l['inventory'])?>&amount_per_strip=<?=urlencode($l['amount_per_strip'])?>&unit_per_tab=<?=urlencode($l['unit_per_tab'])?>&tablet_qty_strip=<?=urlencode($l['tablet_qty_strip'])?>&amount_per_tab=<?=urlencode($l['amount_per_tab'])?>&discount_settings=<?=urlencode($l['discount_settings'])?>&sgst=<?=urlencode($l['sgst'])?>&cgst=<?=urlencode($l['cgst'])?>&igst=<?=urlencode($l['igst'])?>&bill_status=<?=urlencode($l['bill_status'])?>">
    <i class="fa fa-eye" aria-hidden="true"></i>
</a>

</td>
<td style="text-align:center;"><a href="add-drug-template.php?id=<? echo $l['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i>	</a></td>

</tr>

<?}?>

</tbody>
</table>

</div>
<div class="content-table float-left searchdata">
</div>
</div>
</div>
<script>
/*----------------------search functionality Starts here ----------------------------*/
/*In this function we call search  details according to their department,search,colorfilter etc */
$(document).ready(function() {
    $('#example').DataTable( {
        dom: 'Bfrtip',
        buttons: [
             'csv', 'excel'
        ]
    } );
} );
function search(){
	var drug_name=$('#drug_name').val();
	var manufacturer=$('#manufacture').val();
	
	var action ="search";
	var datastring={drug_name:drug_name,manufacturer:manufacturer,action:action };
	$.ajax({
		url:"ajax/ajax_drug_template_list.php",
		type:"POST",
		data:datastring,
		success:function(data){
		$('.searchdata').html(data);
		$('.staticdata').hide();
		}
	});
}
/*----------------------search functionality ends here ----------------------------*/


 function export_template(){
			$('.modal').css('display', 'block');
			var drug_name=$('#drug_name').val();
	var manufacturer=$('#manufacturer').val();


var data="drug_name="+drug_name+"&manufacturer="+manufacturer;
		window.location="<? echo $sitepath;?>drug-template-export.php?"+data;
	$('.modal').css('display', 'none');
		 }

</script>

