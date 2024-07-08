<?php
include_once('header.php');
include_once('top-menu.php');
include_once('left-nav.php');
include_once('core/class.manageUsers.php');
$recsPerPage = 10;
?>
<div class="container-fluid" id="content">
    <script type="text/javascript">
        $(document).ready(function() {
            getListofDatas('', '', '', '', '');
            $("#no_recs_per_page").change(function() {
                getListofDatas('', '', '', '', '');
            });

        });

        function getListofDatas(type, thisElement, pageNo, sortField, sortType) {
            $(".modal").css("display", "block");
            /*if($.trim(type)=="search"){
            	var searchValue=$(thisElement).val();
            } else {
            	var searchValue="";
            }
            if(pageNo==""){
            	pageNo=1;
            }*/
            var manufa = $("#manuf").val();
            var searchValue = $("#search").val();
            var recsPerPage = $('#no_recs_per_page option:selected').val();
            var dataString = {
                pageNo: pageNo,
                recsPerPage: recsPerPage,
                searchValue: searchValue,
                sortField: sortField,
                sortType: sortType,
                manufa: manufa,
            };
            $.ajax({
                type: "POST",
                url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_move.php",
                data: dataString,
                cache: false,
                success: function(html) {
                    TT = html.split("|*|*|*|*|");
                    $('#listPayments').html(TT[0]);
                    $('#pageno').val(TT[1]);
                    $('#start').html(TT[2]);
                    $('#end').html(TT[3]);
                    $('#tot').html(TT[4]);
                    $(".modal").css("display", "none");
                }
            });
        }

        function moveNext() {
            $(".modal").css("display", "block");
            var searchValue = $("#search").val();
            var recsPerPage = $('#no_recs_per_page option:selected').val();
            var pageno = parseInt($('#pageno').val()) + 1;
            var dataString = {
                pageNo: pageno,
                recsPerPage: recsPerPage,
                searchValue: searchValue
            };
            $.ajax({
                type: "POST",
                url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_move.php",
                data: dataString,
                cache: false,
                success: function(html) {
                    TT = html.split("|*|*|*|*|");
                    $('#listPayments').html(TT[0]);
                    $('#pageno').val(TT[1]);
                    $('#start').html(TT[2]);
                    $('#end').html(TT[3]);
                    $('#tot').html(TT[4]);
                    $(".modal").css("display", "none");
                }
            });
        }

        function movePrevious() {
            $(".modal").css("display", "block");
            var searchValue = $("#search").val();
            var recsPerPage = $('#no_recs_per_page option:selected').val();
            var pageno = parseInt($('#pageno').val()) - 1;
            var dataString = {
                pageNo: pageno,
                recsPerPage: recsPerPage,
                action: 'listGrid',
                searchValue: searchValue
            };
            $.ajax({
                type: "POST",
                url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_move.php",
                data: dataString,
                cache: false,
                success: function(html) {
                    TT = html.split("|*|*|*|*|");
                    $('#listPayments').html(TT[0]);
                    $('#pageno').val(TT[1]);
                    $('#start').html(TT[2]);
                    $('#end').html(TT[3]);
                    $('#tot').html(TT[4]);
                    $(".modal").css("display", "none");
                }
            });
        }

        function makeChanges(thisElement, uniqueId) {
            var doctype = $('#doctype' + uniqueId).val();
            var dataString = {
                doctype: doctype,
                action: 'editGrid'
            };
            $(".inputData").hide();
            $(".labelData").show();
            $("#tr" + uniqueId + " .inputData").show();
            $("#tr" + uniqueId + " .labelData").hide();
        }

        <?php if ($_SESSION["maintenance_edit"] == 1) { ?>

            function makeSaveChanges(thisElement, uniqueId) {
                var doctype = $('#doctype' + uniqueId).val();
                var recsPerPage = $('#no_recs_per_page option:selected').val();
                if (doctype == "") {
                    alert("Required Data");
                } else {
                    var dataString = {
                        doctype: doctype,
                        recsPerPage: recsPerPage,
                        id: uniqueId,
                        action: 'editGrid'
                    };
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $sitepath; ?>ajax/ajax_pharmacy_stock_move.php",
                        data: dataString,
                        cache: false,
                        success: function(html) {
                            if (uniqueId > 0) {
                                $("#tr" + uniqueId + " label").each(function() {
                                    $(this).html($(this).prev().val());
                                });
                                $(".inputData").hide();
                                $(".labelData").show();
                            } else {
                                $("tr#tr0").hide();
                                getListofDatas('', '', $.trim(html), 'id', 'asc');
                            }
                        }
                    });
                }
            }

            function makeResetChanges(thisElement, uniqueId) {
                $("#tr" + uniqueId + " input").each(function() {
                    $(this).val($(this).attr("hidden-value"));
                });
            }
        <?php } else { ?>

            function makeSaveChanges(thisElement, uniqueId) {
                alert("You dont have access");
            }
        <?php } ?>

        function addRowInTbl(thisElement, uniqueId) {
            $("tr#tr0").show();
            $("tr#tr0 input").show();
        }

        function pharmacyUploadXls() {

            var phr = $('#chphr').val();

            var upfl = $('#upfile').val();
            if (phr == 0 && upfl == "") {
                alert("Please Select Pharmacy");
                alert("Please Select File");
            } else if (phr == 0) {
                alert("Please Select Pharmacy");
            } else if (upfl == "") {
                alert("Please Select File");
            } else {
                $(".modal").css("display", "block");
                var file_data = $('#upfile').prop('files')[0];
                if (file_data.name.toLowerCase().lastIndexOf(".csv") == -1) {
                    $(".modal").css("display", "none");
                    alert("Required .csv Format File");
                } else {
                    $('#upfile').css("disabled", "disabled");
                    var form_data = new FormData();
                    if (form_data) {
                        form_data.append("file", file_data);
                        form_data.append("action", "ohcuploaded");
                        form_data.append("phrss", phr);
                    }
                    $.ajax({
                        url: 'ajax/ajax_pharmacy_data.php',
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function(data) {

                            document.location.href = "pharmacy_stock_avail_upload.php?id=" + $.trim(data) + "&phr=" + phr;
                            $('.modal').css('display', 'none');
                            //alert(data);
                            //console.log(data);
                        },
                        error: function(data) {
                            alert("Sorry, Please try again");
                            $('.modal').css('display', 'none');
                        }
                    });
                }
            }
        }
    </script>
    <div id="main">
        <div class="Top-Strip">

            <h5 style="font-weight:bold;color:#<? echo $_SESSION['tclr'] ?>;">Drugs Move</h5>
        </div>




        <input type="hidden" id="pageno" name="pageno" value="1" />
        <div class="searchbar pb-0 border-0">
            <div class="col-xl-5 col-lg-5 col-md-4 float-left">
                <div class="w-50 my-3 float-left">
                    <input type="text" id="search" name="search" value="" placeholder="Drug Name" onKeyUp="getListofDatas('search',this,'','','')" />
                </div>
                <div class="w-50 my-3 float-left">
                    <input type="text" id="manuf" name="search" value="" placeholder="Manufacturer" onKeyUp="getListofDatas('search',this,'','','')" />
                </div>
            </div>
            <div class="col-xl-7 col-lg-7 col-md-8 float-right bulks">
                <div class="col-xl-2 col-lg-2 col-md-2 px-0 float-left text-center mx-1"><b>Bulk Move</b></div>
                <div class="col-xl-3 col-lg-3 col-md-3 float-left text-left h-100 px-0 mx-1">

                    <?
                    $obj = new ManageUsers();
                    $sels = (!empty($_SESSION['ohc_loca'])) ? $_SESSION['ohc_loca'] : $_SESSION['currentlocation_id'];
                    $pharmsy = $obj->listDirectQuery(" select * from ohc_pharmay where location_id='" . $sels . "' "); ?>
                    <select name="mphar" class="w-100 h-100" id="chphr">
                        <option class="alert" value="0"> Select Pharmacy</option>
                        <?
                        foreach ($pharmsy as $pr) {
                            if ($_SESSION['phrmcy_id'] != $pr['id']) {
                                echo '<option value="' . $pr['id'] . '">' . $pr['name'] . '</option>';
                            }
                        } ?>
                    </select>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 px-0 float-left ml-2">
                    <input type="file" name="files" id="upfile" />
                </div>
                <div class="col-xl-2 col-lg-2 col-md-2 px-0 float-left mx-1">
                    <input type="button" name="save" value="Upload" class="btn btn-sm btn-primary py-0" onclick="pharmacyUploadXls()" />
                </div>
            </div>


        </div>
        <!--return values here -->
        <div id="listPayments" class="content-table"></div>
    </div>
</div>
</div>
</div>
</div>
<?php require_once('../close.php') ?>