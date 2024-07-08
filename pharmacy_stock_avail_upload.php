<?php
include_once('header.php');
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
function getListofDatas(type,thisElement,pageNo,sortField,sortType){

	$(".modal").css("display","block");
	if($.trim(type)=="search"){
		var searchValue=$(thisElement).val();
	} else {
		var searchValue="";
	}
	if(pageNo==""){
		pageNo=1;
	}
<?php if(!empty($_REQUEST['phr'])) {?>
var phrs="<?php echo $_REQUEST['phr'];?>";
<?php } else {?>
var phrs="0";
<?php }?>
	var recsPerPage=$('#no_recs_per_page option:selected').val();
	var dataString ={pageNo:pageNo,recsPerPage:recsPerPage,searchValue:searchValue,sortField:sortField,sortType:sortType,uploadGenerateId:<?php echo $_GET['id']; ?>,phr:phrs};
	$.ajax({
			type: "POST",
			url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_avail.php",
			data: dataString,
			cache: false,
			success: function (html) {
				TT = html.split("|");
				$('#listPayments').html(TT[0]);
				$('#pageno').val(TT[1]);
				$('#start').html(TT[2]);
				$('#end').html(TT[3]);
				$('#tot').html(TT[4]);
				$(".modal").css("display","none");
			}
		});
}
function moveNext(){
	$(".modal").css("display","block");
	var searchValue=$("#search").val();
	var recsPerPage=$('#no_recs_per_page option:selected').val();
	var pageno = parseInt($('#pageno').val())+1;
	var dataString ={pageNo:pageno,recsPerPage:recsPerPage,searchValue:searchValue,uploadGenerateId:<?php echo $_GET['id']; ?>};
	$.ajax({
		type: "POST",
		url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_avail.php",
		data: dataString,
		cache: false,
		success: function (html) {
			TT = html.split("|");
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
	var dataString ={pageNo:pageno,recsPerPage:recsPerPage,action:'listGrid',searchValue:searchValue,uploadGenerateId:<?php echo $_GET['id']; ?>};
	$.ajax({
		type: "POST",
		url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_avail.php",
		data: dataString,
		cache: false,
		success: function (html){
			TT = html.split("|");
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

function pharmacyConfirmActive(uploadGenerateId){
	$(".modal").css("display","block");
	var action="confirmActive";
	$.ajax({
		url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_data.php",
		cache: false,
		data: 'action='+action+'&uploadGenerateId='+uploadGenerateId,
		type: 'post',
		success: function(data) {
			$('.modal').css('display', 'none');
			document.location.href="stock-available-pharma.php";
		},
		error: function(data) {
			$('.modal').css('display', 'none');
		}
	});
}

function pharmacyConfirmDiscard(uploadGenerateId){
	if(confirm("Are you sure want to discard this upload?")){
		$(".modal").css("display","block");
		var action="confirmDiscard";
		$.ajax({
			url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_data.php",
			cache: false,
			data: 'action='+action+'&uploadGenerateId='+uploadGenerateId,
			type: 'post',
			success: function(data) {
				$('.modal').css('display', 'none');
				document.location.href="stock-available-pharma.php";
			},
			error: function(data) {
				$('.modal').css('display', 'none');
			}
		});
	}
}

function addRowInTbl(thisElement,uniqueId){
	$("tr#tr0").show();
	$("tr#tr0 input").show();
}
</script>
<div id="main">
	
		
				<div class="content-table float-left" >
					<div class="errmsg pt-5" style="background-color:#f0f0f0";><?php if(isset($_SESSION["errmsg"][0])) {
					    echo implode("<br/>", $_SESSION["errmsg"]);
					} else {
					    echo $_SESSION["errmsg"];
					} unset($_SESSION["errmsg"]); ?>
			
				
					
	
						<ul class="tabs tabs-inline tabs-left" style="background:white;">
							<li class='write hidden-480'></li>
						</ul>	
						
								<div class="col-xl-12 col-lg-12 col-md-12 float-left px-0">
									<div class="float-right">
									
											<div class="btn-group">
												<input type="text" id="search" name="search" value="" placeholder="Search" onKeyUp="getListofDatas('search',this,'','','')" style="width: 135px;padding:9%;" />	
											</div>
											<div class="btn-group text hidden-768 pr-3">
									
												<select id="no_recs_per_page" name="no_recs_per_page" class="select2-me" style="width:165px;" >
													<option value="10">10</option>
													<option value="50">50</option>
													<option value="100">100</option>
													<option value="250">250</option>
													<option value="500">500</option>
												</select>
							
												<!--<span>
													<strong><span id="start">0</span>-<span id="end">0</span></strong> of <strong><span id="tot">0</span></strong>
												</span>-->
											</div>
											<div class="btn-group">
											<!--	<a href="#" class="btn" onclick="movePrevious()"><i class="icon-angle-left"></i></a>
												<a href="#" class="btn" onclick="moveNext()"><i class="icon-angle-right"></i></a>-->
												
												<?php
$locss = (!empty($_SESSION['ohc_loca'])) ? $_SESSION['ohc_loca'] : $sessionlocation_id;
$sql = "SELECT count(*) as count FROM `pharmacy_stock_detail` WHERE isactive=0 AND upload_generate_id='".$_REQUEST['id']."' AND r_pharmacy_id='".$locss."' ";
$getListStocksIdObj = new ManageUsers();
$getListStocksIds = $getListStocksIdObj->listDirectQuery($sql);
$getListStocksIdObj = null;
if($getListStocksIds[0]['count'] > 0) {
    ?>
												<a class="btn btn-sm btn-primary" style="background-color:#199BBF !important;color:#fff;" onclick="pharmacyConfirmActive(<?php echo $_REQUEST['id']; ?>)">Confirm Active</a>
												<a class="btn btn-sm btn-primary" style="margin-left: 9px;background-color:#199BBF !important;color:#fff;" onclick="pharmacyConfirmDiscard(<?php echo $_REQUEST['id']; ?>)">Confirm Discard</a>
												<?php } ?>
											</div>
											
										
									</div>
								</div>
								<input type="hidden" id="pageno" name="pageno" value="1" />	
								<!--return values here -->
								<div id="listPayments"></div>
						
					
					</div>
		
			 </div>
<?php //require_once('../close.php')?>