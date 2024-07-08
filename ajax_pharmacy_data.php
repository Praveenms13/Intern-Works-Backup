<?php

/*
Ajax File Called
                ajax/ajax_pharmacy_stock_avail.php (getListofDatas)
                ajax/ajax_pharmacy_stock_avail.php (movenext)
                ajax/ajax_pharmacy_stock_avail.php (moveprevious)
                ajax/ajax_pharmacy_stock_avail.php (makesavechange)



Included Files
        header.php                         core/class.manageUsers.php                              close.php
        top-menu.php               left-nav.php
End */

?>

<?php
include_once('header.php');
include_once('ohc_session.php');
include_once('top-menu.php');
include_once('left-nav.php');
include_once('core/class.manageUsers.php');
$recsPerPage = 10;

?>
<div class="container-fluid" id="content">
<script type="text/javascript">
$(document).ready(function(){
	getListofDatas('','','','','');
	$("#no_recs_per_page").change(function(){
		getListofDatas('','','','','');
	});
});
window.onload = function(){ stockreviews(); }

function stockreviews(){
		var pid='stock';
		$.fn.colorbox({href:"stockreview.php?pid="+pid,iframe:true, open:true, innerWidth:700, innerHeight:500,onOpen:function(){$('#cboxContent').removeClass("cboxContentCls");}});  
	 }


function getListofDatas(type,thisElement,pageNo,sortField,sortType){
	$(".modal").css("display","block");
	/*if($.trim(type)=="search"){
		var searchValue=$('#search').val();
	} else {
		var searchValue="";
	}

	if(pageNo==""){
		pageNo=1;
	}*/
	var filt=$("#filt").val();
	var manufa=$("#manuf").val();
	var searchValue=$("#search").val();
	var chphr=$('#chphr').val();
	var recsPerPage=$('#no_recs_per_page option:selected').val();
	var dataString ={pageNo:pageNo,recsPerPage:recsPerPage,searchValue:searchValue,sortField:sortField,sortType:sortType,chphr:chphr,manufa:manufa,filt:filt};
    console.log(dataString);
	$.ajax({
			type: "POST",
			url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_avail.php",
			data: dataString,
			cache: false,
			success: function (html) {
				TT = html.split("|*|*|*|*|");

				$('#listPayments').html(TT[0]);
				$('#pageno').val(TT[1]);
				$('#start').html(TT[2]);
				$('#end').html(TT[3]);
				$('#tot').html(TT[4]);
$('.phrnm').html(TT[5]);


				$(".modal").css("display","none");
			}
		});
}
function moveNext(){
	$(".modal").css("display","block");
	var searchValue=$("#search").val();
	var recsPerPage=$('#no_recs_per_page option:selected').val();
	var pageno = parseInt($('#pageno').val())+1;
	var dataString ={pageNo:pageno,recsPerPage:recsPerPage,searchValue:searchValue};
	$.ajax({
		type: "POST",
		url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_avail.php",
		data: dataString,
		cache: false,
		success: function (html) {
			TT = html.split("|*|*|*|*|");
			$('#listPayments').html(TT[0]);
			$('#pageno').val(TT[1]);
			$('#start').html(TT[2]);
			$('#end').html(TT[3]);
			$('#tot').html(TT[4]);
			$(".modal").css("display","none");
		}
	});
}
function movePrevious(){
	$(".modal").css("display","block");
	var searchValue=$("#search").val();
	var recsPerPage=$('#no_recs_per_page option:selected').val();
	var pageno = parseInt($('#pageno').val())-1;
	var dataString ={pageNo:pageno,recsPerPage:recsPerPage,action:'listGrid',searchValue:searchValue};
	$.ajax({
		type: "POST",
		url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_avail.php",
		data: dataString,
		cache: false,
		success: function (html){
			TT = html.split("|*|*|*|*|");
			$('#listPayments').html(TT[0]);
			$('#pageno').val(TT[1]);
			$('#start').html(TT[2]);
			$('#end').html(TT[3]);
			$('#tot').html(TT[4]);
			$(".modal").css("display","none");
		}
	});
}
function makeChanges(thisElement,uniqueId){
	var doctype=$('#doctype'+uniqueId).val();
	var dataString ={doctype:doctype,action:'editGrid'};
	$(".inputData").hide();
	$(".labelData").show();
	$("#tr"+uniqueId+" .inputData").show();
	$("#tr"+uniqueId+" .labelData").hide();
}

<?php if($_SESSION["maintenance_edit"] == 1) { ?>
function makeSaveChanges(thisElement,uniqueId){
	var doctype=$('#doctype'+uniqueId).val();
	var recsPerPage=$('#no_recs_per_page option:selected').val();
	if(doctype==""){
		alert("Required Data");
	} else {
		var dataString ={doctype:doctype,recsPerPage:recsPerPage,id:uniqueId,action:'editGrid'};
		$.ajax({
			type: "POST",
			url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_avail.php",
			data: dataString,
			cache: false,
			success: function (html){
				if(uniqueId>0){
					$("#tr"+uniqueId+" label").each(function(){
						$(this).html($(this).prev().val());
					});
					$(".inputData").hide();
					$(".labelData").show();
				} else {
					$("tr#tr0").hide();
					getListofDatas('','',$.trim(html),'id','asc');
				}
			}
		});
	}
}
function makeResetChanges(thisElement,uniqueId){
	$("#tr"+uniqueId+" input").each(function(){
		$(this).val($(this).attr("hidden-value"));
	});
}
<?php } else { ?>
	function makeSaveChanges(thisElement,uniqueId){
		alert("You dont have access");
	}
<?php } ?>

function addRowInTbl(thisElement,uniqueId){
	$("tr#tr0").show();
	$("tr#tr0 input").show();
}
</script>
<div id="main">
<div class="Top-Strip">
     
	

<div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 float-left px-0">
			<h5 style="font-weight:bold;color:#<?php echo $_SESSION['tclr'] ?>;">Drugs Stock Availability</h5>
			</div>	

		<!--	<div class="custom-resposive" style="width:40%;">
												
											 
												<span style="padding-right:15px;">
												<select id="no_recs_per_page" name="no_recs_per_page" class="select2-me" style="width:165px;" >
										<option value="10">10</option>
										<option value="50" selected="selected">50</option>
										<option value="100">100</option>
										<option value="250">250</option>
										<option value="500">500</option>
												</select>
												</span>
												<span>
													<strong><span id="start">0</span>-<span id="end">0</span></strong> of <strong><span id="tot">0</span></strong>
												</span>
											</div>
											<div class="btn-group" style="width:5%; padding-top:5px;">
												<a href="#" class="btn" onclick="movePrevious()"><i class="fa fa-arrow-left"></i></a>
												<a href="#" class="btn" onclick="moveNext()"><i class="fa fa-arrow-right"></i></a></div>-->
<div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 float-left top-pad">
<select name="mphar" class="w-100 h-75" id="chphr" onchange="getListofDatas('search',this,'','','')">
																
	<?php
    $obj = new ManageUsers();
$sels = (!empty($_SESSION['ohc_loca'])) ? $_SESSION['ohc_loca'] : $_SESSION['currentlocation_id'];
$pharmsy = $obj->listDirectQuery(" select * from ohc_pharmay where location_id='".$sels."' ");

foreach($pharmsy as $pr) {
    $sl = "";
    if($_SESSION['phrmcy_id'] == $pr['id']) {
        $sl = "selected='selected'";
    }
    echo '<option value="'.$pr['id'].'" '.$sl.'>'.$pr['name'].'</option>';

}?></select>
<?php /* $obj = new ManageUsers();
    $pharmsy=$obj->listDirectQuery(" select * from ohc_pharmay where location_id='".$_SESSION['currentlocation_id']."' ");
foreach($pharmsy as $pr){
        if($_SESSION['phrmcy_id']==$pr['id']){
echo '<h5>'.$pr['name'].'</h5>';

}}*/

?></div>
	<input type="hidden" id="pageno" name="pageno" value="1" />
	
	<div class="col-xl-3 col-lg-3 d-none d-lg-block float-left top-pad"> </div>
<div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 float-left top-pad text-right">

<?php /*if(!empty($_SESSION['mainphr']) || empty($_SESSION['ohc_loca'])){?>
        <a href="add-stock-pharma.php" class="btn btn-sm btn-primary">Add Stock</a>
<?   }*/?>
				</div>										

									
				</div>								
										
							
									
<?php
echo "<pre>";
print_r($pharmsy);
echo "</pre>";
if(!empty($_SESSION['ohc_loca'])) {?>
	<div class="content-table float-left" style="border-bottom: 1px dashed #ccc;">
									
							
						<div class="col-xl-4 col-lg-4 col-md-4 float-left p-2">
<input type="text" id="search" class="icon_90" name="search" value="" placeholder="Drug Name" onKeyUp="getListofDatas('search',this,'','','')"  /></div>
						<div class="col-xl-4 col-lg-4 col-md-4 float-left p-2">					
<input type="text" id="manuf" class="icon_90" name="search" value="" placeholder="Manufacturer" onKeyUp="getListofDatas('search',this,'','','')" /></div>
						<div class="col-xl-4 col-lg-4 col-md-4 float-left p-2">					
	<select name="mphar" id="filt" style="width:80%;height:25px;padding-left:2%;" onchange="getListofDatas('search',this,'','','')" >
											<option value="0"> Available</option>
											<option value="3"> Stop Issuing</option>
												<option value="1"> Expired </option>
												<option value="2"> Sold </option>
											</select>
									</div>
	
									
</div>
							
<?php }?>
								<input type="hidden" id="pageno" name="pageno" value="1" />	
								
								<!--return values here -->
								<div id="listPayments" class="float-left content-table"></div>
							</div>
						</div>
					</div>
				</div>
			 </div>
<?php require_once('../close.php')?>