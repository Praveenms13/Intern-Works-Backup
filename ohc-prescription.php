<?php
/*Ajax File Called
ajax/ajax_addohc-pres-temp.php( bringDrug)
ajax/ajax_addohc-pres-temp.php(bringmedcountp)
newdoctor.php(getDoctorNew)
ajax/ajax_hosp_doctor_search.php(getHospiDoctorNew)
ajax/ajax_CaseFile_Details.php(casefile)
ajax/ajax_drug.php(getTypeahead)
ajax/ajax_drug.php(substringGet)
user_conditions.php(nxtassdt)
add-prescription.php(ToPassIdGroup)

End*/
require_once "header.php";
require_once "top-menu.php";
include_once "core/class.manageUsers.php";
extract($_REQUEST);
?>

<style>
    .selecter .selecter-options {
        width: 100%;
    }

    .wrap {
        position: relative;
        width: 220px;

    }

    .pheader {
        position: relative;
        line-height: 1em;
    }

    .filterform {
        width: 220px;
        font-size: 12px;
        display: block;
    }
</style>

<div class="container-fluid" id="content">
    <script>
        function getdrugdetails(data) {
            var dataVal = $(data).val();
            var datas = dataVal.split("--");

            $(data).parent().parent().find('.mastrdrug').val(datas[1]);
            var $rw = $(data).closest("tr");
            var dt = datas[0];

            bringmedcountp(dt, $rw);
        }
        /*----------------------bringdrug functionality Starts here ----------------------------*/

        function bringDrug(searchElement, searchValue, e) {
            var hiddenValToPass = $(".hiddenValToPass").val();
            if (!hiddenValToPass || hiddenValToPass != searchValue) {
                var searchId = $(searchElement).parent().closest("tr").attr("id");
                e.stopPropagation();
                $.ajax({
                    url: "ajax/ajax_addohc-pres-temp.php",
                    cache: false,
                    data: 'searchValue=' + searchValue + '&searchId=' + searchId + '&action=bringDrug',
                    type: 'post',
                    success: function(data) {
                        if ($.trim(data).match(/notfound/g)) {
                            splittingDataDrug = $.trim(data).split("^^^");
                            $(searchElement).next().css("display", "block");
                            $(searchElement).next().html('Click here to add this drug');
                            $(searchElement).next().attr("onClick", "openDrugAdd('" + splittingDataDrug[2] + "','" + splittingDataDrug[1] + "',this)");
                        } else {
                            $(searchElement).next().css("display", "block");
                            $(searchElement).next().html(data);
                            $(searchElement).next().attr("onClick", "");
                        }

                        if (!hiddenValToPass) {
                            if ($("body").next().hasClass("hiddenValToPass")) {
                                $(".hiddenValToPass").val(searchValue);
                            } else {
                                $("body").after("<input type='hidden' class='hiddenValToPass' value='" + searchValue + "'/>");
                            }
                        } else {
                            $(".hiddenValToPass").val(searchValue);
                        }
                    }
                });
            }
        }

        /*----------------------bringdrug functionality ends here ----------------------------*/
        /*----------------------bringmedcountp functionality starts here ----------------------------*/
        function bringmedcountp(dt, $rw) {

            var id = dt;
            var action = "bringdrugcount";
            var dataString = {
                dvalue: id,
                action: action
            };
            $.ajax({
                type: "POST",
                url: "ajax/ajax_addohc-pres-temp.php",
                data: dataString,
                success: function(data) {

                    //  alert(data); 
                    values = data.split('--');
                    // alert(values[0]);
                    // alert(values[1]);
                    // $rw.find('.avalcount').html(values[0]);
                    $rw.find('.dtype').html(values[1] + '<input   type="hidden" id="hiddendrugtype" name="hiddendrugtype[]" value="' + values[1] + '" >');
                    //  $rw.find('.dintype').val(values[1]);


                }


            });
        }
        /*----------------------bringmedcountp functionality ends here ----------------------------*/
    </script>
    <?php
include_once "core/class.manageSettings.php";
include_once "core/class.manageUsers.php";
include_once "core/class.manageAppointment.php";
include_once "core/class.manageUnregDoctors.php";
include_once "core/class.manageMasters.php";
include_once "core/class.manageMessage.php";
include_once "left-nav.php";
if ($tbs_role == "4" && !empty($_SESSION["ohc_doc"])) {
    @$id = $_GET["id"];
    $tablename = "prescription";
    $prescriptionDetail = new ManageUsers();
    $prescription = $prescriptionDetail->listShowDetails($tablename, ["id" => $id, ]);
    if ($prescription != 0) {
        foreach ($prescription as $listprescription) {
            $id = $listprescription["id"];
            $user_id = $listprescription["user_id"];
            $doctor_id = $listprescription["doctor_id"];
            $from_date = $listprescription["from_date"];
            $master_specialization_id = $listprescription["master_specialization_id"];
            $master_hcsp_user_id = $listprescription["master_hcsp_user_id"];
            $doctornotes = $listprescription["doctornotes"];
            $usernotes = $listprescription["usernotes"];
            $test_id = $listprescription["test_id"];
        }
    } else {
        $id = 0;
    }
    ?>
        <script type="text/javascript">
            // function addRow1(tableID) {

            //     $('.typeahead-custom').css("display", "none");
            //     var LengthofRow = (document.getElementById("dataTable1").rows.length);

            //     var table = document.getElementById(tableID);
            //     var rowCount = table.rows.length;
            //     var row = table.insertRow(rowCount);
            //     var colCount = table.rows[0].cells.length;

            //     document.getElementById('getrows').value = table.rows.length;
            //     for (var i = 0; i < colCount; i++) {
            //         var newcell = row.insertCell(i);
            //         newcell.innerHTML = table.rows[0].cells[i].innerHTML;
            //         var ManualRow = $("#dataTable1").find("tr").eq(LengthofRow).attr("id", LengthofRow);
            //         switch (newcell.childNodes[0].type) {
            //             case "text":
            //                 newcell.childNodes[0].value = "";
            //                 break;
            //             case "checkbox":
            //                 newcell.childNodes[0].checked = false;
            //                 break;
            //             case "select-one":
            //                 newcell.childNodes[0].selectedIndex = 0;
            //                 break;
            //         }

            //         var $row = $(this).closest("tr");
            //         $("#dataTable1").find('#' + LengthofRow).find('.addjs').attr('onclick', 'deleteTableRow(this, "dataTable1")');
            //         $("#dataTable1").find('#' + LengthofRow).find('.addjs').attr('class', 'cross-del');
            //         $("#dataTable1").find('td:nth-child(2)').css({
            //             'width': '10%',
            //             'padding': '5px 0',
            //             'text-align': 'center'
            //         });
            //         // $("#dataTable1").find('td:nth-child(2)').attr('class', 'avalcount');
            //         $("#dataTable1").find('td:nth-child(5)').attr('class', 'dtype');
            //         $("#dataTable1").find('#' + LengthofRow).find('td:nth-child(5)').html("");

            //         $("#dataTable1").find('#' + LengthofRow).find('td:nth-child(2)').html("--");
            //         $("#dataTable1").find('td:nth-child(3)').css({
            //             'width': '5%',
            //             'padding': '5px 0'
            //         });
            //         $("#dataTable1").find('#' + LengthofRow).find('.firstRowOnly').removeClass('cross-del');
            //         $("#dataTable1").find('#' + LengthofRow).find('.firstRowOnly').removeAttr('onclick');


            //         if ($(".early_morning").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.early_morning').attr('id', 'em' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.early_morning').val('');
            //         }
            //         if ($(".morning").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.morning').attr('id', 'm' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.morning').val('');
            //         }
            //         if ($(".late_morning").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.late_morning').attr('id', 'lm' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.late_morning').val('');
            //         }
            //         if ($(".afternoon").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.afternoon').attr('id', 'a' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.afternoon').val('');
            //         }
            //         if ($(".late_afternoon").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.late_afternoon').attr('id', 'la' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.late_afternoon').val('');
            //         }
            //         if ($(".evening").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.evening').attr('id', 'e' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.evening').val('');
            //         }
            //         if ($(".night").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.night').attr('id', 'n' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.night').val('');
            //         }
            //         if ($(".late_night").length > 0) {
            //             $("#dataTable1").find('#' + LengthofRow).find('.late_night').attr('id', 'ln' + LengthofRow);
            //             $("#dataTable1").find('#' + LengthofRow).find('.late_night').val('');
            //         }

            //         $("#dataTable1").find('#' + LengthofRow).find('.mastrdrug').val('');
            //         $("#dataTable1").find('#' + LengthofRow).find('.hiddendrugname').attr('value', '');
            //     }

            // }




            // Add this at the end of your addRow1 function


            function addRow2(tableID) {


                var LengthofRow = (document.getElementById("dataTable2").rows.length);

                var table = document.getElementById(tableID);

                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                var colCount = table.rows[0].cells.length;

                document.getElementById('getrows2').value = table.rows.length;
                for (var i = 0; i < colCount; i++) {
                    var newcell = row.insertCell(i);
                    newcell.innerHTML = table.rows[0].cells[i].innerHTML;
                    var ManualRow = $("#dataTable2").find("tr").eq(LengthofRow).attr("id", LengthofRow);
                    switch (newcell.childNodes[0].type) {
                        case "text":
                            newcell.childNodes[0].value = "";
                            break;
                        case "checkbox":
                            newcell.childNodes[0].checked = false;
                            break;
                        case "select-one":
                            newcell.childNodes[0].selectedIndex = 0;
                            break;
                    }

                    var $row = $(this).closest("tr");
                    $("#dataTable2").find('#' + LengthofRow).find('.addjs').attr('onclick', 'deleteTableRow(this, "dataTable2")');
                    $("#dataTable2").find('#' + LengthofRow).find('.addjs').attr('class', 'cross-del');





                    $("#dataTable2").find('td:nth-child(2)').css({
                        'width': '5%',
                        'padding': '5px 2px'
                    });
                    $("#dataTable2").find('#' + LengthofRow).find('.firstRowOnly').removeClass('cross-del');
                    $("#dataTable2").find('#' + LengthofRow).find('.firstRowOnly').removeAttr('onclick');


                    if ($(".early_morning").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.early_morning').attr('id', 'em' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.early_morning').val('');
                    }
                    if ($(".morning").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.morning').attr('id', 'm' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.morning').val('');
                    }
                    if ($(".late_morning").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.late_morning').attr('id', 'lm' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.late_morning').val('');
                    }
                    if ($(".afternoon").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.afternoon').attr('id', 'a' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.afternoon').val('');
                    }
                    if ($(".late_afternoon").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.late_afternoon').attr('id', 'la' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.late_afternoon').val('');
                    }
                    if ($(".evening").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.evening').attr('id', 'e' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.evening').val('');
                    }
                    if ($(".night").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.night').attr('id', 'n' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.night').val('');
                    }
                    if ($(".late_night").length > 0) {
                        $("#dataTable2").find('#' + LengthofRow).find('.late_night').attr('id', 'ln' + LengthofRow);
                        $("#dataTable2").find('#' + LengthofRow).find('.late_night').val('');
                    }

                    $("#dataTable2").find('#' + LengthofRow).find('.mastrdrug').val('');
                    $("#dataTable2").find('#' + LengthofRow).find('.tbldrugtype').val('');
                    $("#dataTable2").find('#' + LengthofRow).find('.hiddendrugname').attr('value', '');
                }

            }
            $(document).ready(function() {
                <?php if (isset($_REQUEST["doid"]) && !empty($_REQUEST["doid"])) { ?>
                    getDoctorNew(<?php echo $_REQUEST["doid"]; ?>);
                <?php
                } ?>
            });

            function attachmultipresc() {
                $("#lighbox-background").addClass("attachmentbodybg");
                //$("body").addClass("attachmentbodybg");
                //$("body > :not(.openitAttached)").addClass("attachmentdivbg");
                $(".openitAttached").before("<div id='cboxOverlay' style='cursor: pointer; opacity: 1;'></div>");
                $(".openitAttached").css("top", "10%");
            }

            function wholedatatopaste() {
                if ($.trim($('.attachedDoc').html()) == "") {
                    setTimeout(function() {
                        $('.loaderoff').html('<img src="img/user-loader.gif" />&nbsp;<i>Please wait to upload</i>');
                    }, 10);
                    setTimeout(function() {
                        $('.loaderoff').html('<i>Sorry, Required file to upload</i>');
                    }, 10);
                } else {
                    $('.loaderoff').html('<img src="img/user-loader.gif" />&nbsp;<i>Please wait to upload</i>');
                }
                $('.loaderoff').show();
                $('.loaderoff').hide();
                $('.openitAttached').css("top", "-440%");
                $("body > :not(.openitAttached)").removeClass("attachmentdivbg");
                $("body").removeClass("attachmentbodybg");
                $(".openitAttached").prev('#cboxOverlay').remove();
            }

            function wholedatatopastecancel() {
                $("body > :not(.openitAttached)").removeClass("attachmentdivbg");
                $("body").removeClass("attachmentbodybg");
                $('.openitAttached').css("top", "-440%");
                $(".multifile").each(function() {
                    $(this).val('');
                    $('.attachedDoc').html('');
                });

                $('#dataTable10 tr#0').nextAll().each(function() {
                    $(this).remove();
                });
                $(".openitAttached").prev('#cboxOverlay').remove();
            }



            function ToPassId(ToPassValue) {
                $.fn.colorbox.close();
                document.getElementById('docname').innerHTML = ToPassValue;
                var docnameSelected = document.getElementById('docname').options[document.getElementById('docname').selectedIndex].text;
                var docPasses = $("#docname").prev().attr("id");
                $("#" + docPasses + " span").html(docnameSelected);
            }

            function ToPassIdDrug(closetr, adddrug, liadddrg) {
                $.fn.colorbox.close();
                $("tr#" + closetr + " .drugname").html(adddrug);
                $("tr#" + closetr + " .hiddendrugname").val(liadddrg);
                $("tr#" + closetr + " .drugname .hiddendrugname").parent().parent().parent().nextAll().each(function() {
                    var closesEach = $(this).attr("id");
                    $("tr#" + closesEach + " .drugname").html(adddrug);
                });
                $("#dataTable1").find('#' + LengthofRow).find('.drugname').html(html);
            }

            function ToPassIdCondition(adddrug, liadddrg) {
                $.fn.colorbox.close();
                $(".conditionname").html(adddrug);
                $(".hiddenconditionname").val(liadddrg);
            }

            function ToPassSpec(ToPassSpec) {
                $.fn.colorbox.close();
                document.getElementById('speclisatinname').innerHTML = ToPassSpec;
                var Allispec = [];
                var gtels = document.getElementById('speclisatinname');
                for (var ispec = 0; ispec < gtels.options.length; ispec++) {
                    if (gtels.options[ispec].selected == true) {
                        var ispecs = gtels.options[ispec].text;
                        Allispec.push('<li class="select2-search-choice"><div>' + ispecs + '</div><a class="select2-search-choice-close" tabindex="-1" onclick="return false;" href="#"></a></li>');
                    }
                }
                $(".specialization .select2-choices").html(Allispec);
            }

            function ToPassHosp(ToPassHosp) {
                $.fn.colorbox.close();
                document.getElementById('hosname').innerHTML = ToPassHosp;
                var docnameSelectedH = document.getElementById('hosname').options[document.getElementById('hosname').selectedIndex].text;
                var hosPasses = $("#hosname").prev().attr("id");
                $("#" + hosPasses + " span").html(docnameSelected);
            }

            function ToPassTest(ToPassTest) {
                $.fn.colorbox.close();
                document.getElementById('testid').value = ToPassTest;
            }

            function CheckNumber(ee, evt, evalu) {
                //var eereplaced=ee.replace("a", "");
                var replaceda = ee.search("a") >= 0;
                var replacedm = ee.search("m") >= 0;
                var replacedn = ee.search("n") >= 0;
                if (replaceda) {
                    var eereplaced = ee.replace("a", "");
                    var firstele = document.getElementById("m" + eereplaced).value;
                    var secele = document.getElementById("n" + eereplaced).value;
                } else if (replacedm) {
                    var eereplaced = ee.replace("m", "");
                    var firstele = document.getElementById("a" + eereplaced).value;
                    var secele = document.getElementById("n" + eereplaced).value;
                } else if (replacedn) {
                    var eereplaced = ee.replace("n", "");
                    var firstele = document.getElementById("a" + eereplaced).value;
                    var secele = document.getElementById("m" + eereplaced).value;
                }
                if (evalu == 0 && firstele == 0 && secele == 0) {
                    alert('<?php echo isset($msgInvQua) ? $msgInvQua : $sessionMsg; ?>');
                    document.getElementById("m" + eereplaced).value = null;
                    document.getElementById("a" + eereplaced).value = null;
                    document.getElementById("n" + eereplaced).value = null;
                }
            }

            function addBgClass(id) {
                $(".colorboxCls").addClass(id);
                $(".colorboxCls").css("background-color", "#fff");
            }

            function removeBgClass(id) {
                $(".colorboxCls").removeClass(id);
                $(".colorboxCls").css("background-color", "");
            }


            function templateselect(seltem) {
                if (seltem == "template") {
                    //$("#newtemplate").removeAttr("disabled");
                    var thisid = "";
                } else if (seltem == "") {
                    //$("#newtemplate").attr("disabled", "disabled");
                    var thisid = "";
                } else {
                    //$("#newtemplate").attr("disabled", "disabled");
                    var thisid = "retrivetemplate";
                }
                var action = "temp";
                var dataString = {
                    id: seltem,
                    thisid: thisid,
                    action: action
                };
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_addohc-pres-temp.php",
                    data: dataString,
                    success: function(html) {
                        var htmlsplit = html.split("*-*splitvalue*-*")
                        $("#dataTable1").html(htmlsplit[0]);
                        $(".templatenotes").html(htmlsplit[1]);

                    }
                });
            }
            /*----------------------getdoctornew functionality Starts here ----------------------------*/

            function getDoctorNew(sel) {
                if (sel == "doctor") {
                    $.fn.colorbox({
                        href: "newdoctor.php",
                        iframe: true,
                        escKey: true,
                        open: true,
                        close: true,
                        innerWidth: 900,
                        innerHeight: 461,
                        onOpen: function() {
                            removeBgClass("bborder");
                        }
                    });
                } else {
                    ajLoaderOn();
                    var thsis = "getselect";
                    var dataString = {
                        id: sel,
                        thsis: thsis
                    };
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajax_unreg_doctor_search.php",
                        data: dataString,
                        success: function(html) {
                            ajLoaderOff();
                            var htmls = html.split("---");
                            document.getElementById('speclisatinname').innerHTML = htmls[1];
                            var Allispec = [];
                            var gtels = document.getElementById('speclisatinname');
                            for (var ispec = 0; ispec < gtels.options.length; ispec++) {
                                if (gtels.options[ispec].selected == true) {
                                    var ispecs = gtels.options[ispec].text;
                                    Allispec.push('<li class="select2-search-choice"><div>' + ispecs + '</div><a class="select2-search-choice-close" tabindex="-1" onclick="return false;" href="#"></a></li>');
                                }
                            }

                            $(".specialization .select2-choices").html(Allispec);
                            $(".hosname .select2-choice span").html("");
                            document.getElementById('hosname').innerHTML = htmls[2];

                            document.getElementById('case_id').innerHTML = htmls[3];
                            $("#case_id").prev().find(".select2-choice span").html("CASE ID");
                            $("select#case_id").val("");

                            document.getElementById('existtemplate').innerHTML = htmls[4];
                            $("#existtemplate").prev().find(".select2-choice span").html("Select Template");
                            $("select#existtemplate").val("");

                            if (sel != "") {
                                document.getElementById('username').innerHTML = htmls[5];
                            }
                            $(".username").prev().find(".select2-choice span").html("Select Patient");
                            $("#username").val("");
                            $('.timdur1').html(htmls[6]);
                            $('.timdur2').html(htmls[7]);
                        }

                    });
                }
            }
            /*----------------------getdoctornew functionality ends here ----------------------------*/
            /*----------------------gethospidoctornew functionality starts here ----------------------------*/
            function getHospiDoctorNew(sel) {
                if (sel != "") {
                    var thsis = "getselect";
                    var dataString = {
                        id: sel,
                        thsis: thsis
                    };
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajax_hosp_doctor_search.php",
                        data: dataString,
                        success: function(html) {
                            var htmls = html.split("---");
                            document.getElementById('speclisatinname').innerHTML = htmls[0];
                            var Allispec = [];
                            var gtels = document.getElementById('speclisatinname');
                            for (var ispec = 0; ispec < gtels.options.length; ispec++) {
                                if (gtels.options[ispec].selected == true) {
                                    var ispecs = gtels.options[ispec].text;
                                    Allispec.push('<li class="select2-search-choice"><div>' + ispecs + '</div><a class="select2-search-choice-close" tabindex="-1" onclick="return false;" href="#"></a></li>');
                                }
                            }
                            $(".specialization .select2-choices").html(Allispec);
                            document.getElementById('existtemplate').innerHTML = htmls[1];
                            document.getElementById('case_id').innerHTML = htmls[2];
                            document.getElementById('username').innerHTML = htmls[3];
                            $("#username").val("");
                        }
                    });
                } else {
                    document.getElementById('speclisatinname').innerHTML = "";
                    document.getElementById('existtemplate').innerHTML = "";
                    document.getElementById('username').innerHTML = "";
                    document.getElementById('case_id').innerHTML = "";
                    $(".specialization .select2-choices").html("");
                }
                $("#existtemplate").prev().find(".select2-choice span").html("Select Template");
                $("#case_id").prev().find(".select2-choice span").html("CASE ID");
                $(".username").prev().find(".select2-choice span").html("Select Patient");
                $("select#existtemplate").val("");
                $("select#case_id").val("");
                $("select#username").val("");
                $(".dnotes").val("");
                $(".mnotes").val("");
                $(".hiddenconditionname").val("");
                $(".mastrcondition").val("");
            }

            /*----------------------gethospidoctornew functionality ends here ----------------------------*/

            function deleteTableRow(e, tableName) {
                var table = document.getElementById(tableName);
                var rowCount = table.rows.length;
                if (rowCount == "1") {
                    alert("Cannot delete all the rows.");
                    return true;
                } else {
                    $(e).parents('tr').find("td").fadeOut('', function() {
                        var table = document.getElementById(tableName);
                        $(this).parent().remove();
                    });
                    $(e).parents('tr').nextAll().each(function() {
                        var nextAllID = $(this).attr("id") - 1;
                        $(this).attr("id", nextAllID);
                    });
                    document.getElementById('getrows').value = table.rows.length - 1;
                }
                return false;
            }

            function patientdetails(rowId) {
                $('.modal').css('display', 'block');
                if (rowId == "patient") {
                    $('.modal').css('display', 'none');
                    addnewpatient();
                } else {
                    var fav_pharmacy_old_id = $("#fav_pharmacy option:selected").val();
                    var fav_pharmacy_old_name = $("#fav_pharmacy option:selected").text();
                    var fav_lab_old_id = $("#fav_lab option:selected").val();
                    var fav_lab_old_name = $("#fav_lab option:selected").text();
                    var dataString = 'id=' + rowId;
                    $.ajax({
                        type: "POST",
                        url: 'ajax_patentdetails.php',
                        data: dataString,
                        success: function(html) {
                            $('.modal').css('display', 'none');
                            var htmls = html.split("---");
                            document.getElementById('timeline-post2').style.display = "none";
                            document.getElementById('timeline-post').innerHTML = htmls[0];
                            $("#case_id").prev().find(".select2-choice span").html("CASE ID");
                            document.getElementById('case_id').innerHTML = htmls[1];
                            $("#fav_pharmacy").html(htmls[2]);
                            $("#fav_lab").html(htmls[3]);
                            if (fav_pharmacy_old_id > 0) {
                                $("#fav_pharmacy option:selected").val("");
                                $("select#fav_pharmacy").append("<option value='" + fav_pharmacy_old_id + "' selected=selected>" + fav_pharmacy_old_name + "</option>");
                            } else {
                                $("#fav_pharmacy option:selected").val("");
                                $("#fav_pharmacy").prev().find(".select2-choice span").html("Select Your Favorite");
                            }
                            if (fav_lab_old_id > 0) {
                                $("#fav_lab option:selected").val("");
                                $("select#fav_lab").append("<option value='" + fav_lab_old_id + "' selected=selected>" + fav_lab_old_name + "</option>");
                            } else {
                                $("#fav_lab option:selected").val("");
                                $("#fav_lab").prev().find(".select2-choice span").html("Select Your Favorite");
                            }

                        },
                        error: function(html) {
                            $('.modal').css('display', 'none');
                        }
                    });
                }
            }
            /*----------------------casefile functionality Starts here ----------------------------*/
            function casefile(casefilevalue) {
                var action = 'addprescriptionpage';
                var dataString = 'id=' + casefilevalue + '&action=' + action;

                if (casefilevalue == "") {
                    $('.jspatientb').html(""); //Remove text box
                    $('.jspatientc').css("display", "block");
                    $('select.username').attr("name", "username");
                } else {
                    $.ajax({
                        type: "POST",
                        url: 'ajax/ajax_CaseFile_Details.php',
                        data: dataString,
                        success: function(html) {
                            var htmls = html.split("---");
                            $('.username .select2-choice span').html(htmls[0]);
                            var html1val = $.trim(htmls[1]);
                            if ($.trim(htmls[0]) != "" && casefilevalue != "") {
                                $('div.jspatient').children().css("display", "none");
                                //	$('div#timeline-post').html('<label for="questions" class="control-label"><i class="icon-user"></i> Gender: <span style="text-transform:capitalize;">'+htmls[7]+'</span></label><div class="controls"><label for="questions" class="control-label"><i class="icon-calendar"></i> Age: <span style="text-transform:capitalize;">'+$.trim(htmls[8])+'</span></label>');
                                var nms = htmls[7] + ' / ' + htmls[8];
                                $('#timeline-post').html(nms);

                                $('div.jspatient').html('<div class="jspatientb"><input type="text" name="usernamea" id="usernamea" class="usernameremove" readonly value="' + $.trim(htmls[0]) + '" style="display:block" /><input type="hidden" name="username" id="username" class="usernameremove" value="' + $.trim(htmls[1]) + '"  /></div>');
                                $('div.jspatient').find('.jspatientb').css("display", "block");
                                $('.jspatientc').css("display", "none");
                                $('select.username').attr("name", "usernameold");
                                $('select.username').val(htmls[0]);
                            } else {
                                $('select.username').val('');
                                $('div.jspatient').children().css("display", "block");
                                $('div.jspatientc').find('select.username').attr("name", "username");
                                $('.jspatientb').find('.usernameremove').remove();
                                $('div#timeline-post').html('<label for="questions" class="control-label"><i class="icon-user"></i> Gender: <span style="text-transform:capitalize;"></span></label><div class="controls"><label for="questions" class="control-label"><i class="icon-calendar"></i> Age: <span style="text-transform:capitalize;"></span></label>');
                            }
                            $('.dnotes').val(htmls[2]);
                            $('.mnotes').val(htmls[3]);


                            $('.mastrcondition').val(htmls[4]);

                            $('.hiddenconditionname').val(htmls[5]);

                            if (htmls[6] == 1) {
                                $('.isshowdoctor').prop("checked", true);
                            } else {
                                $('.isshowdoctor').prop("checked", false);
                            }
                        }
                    });
                }
            }
            /*----------------------casefile functionality ends here ----------------------------*/
            /*----------------------get Typeahead functionality starts here ----------------------------*/
            function getTypeahead(thisid) {
                TRofRow = $(thisid).closest("tr").attr("id");
                var dataString = 'id=1';
                $.ajax({
                    type: "POST",
                    url: 'ajax/ajax_drug.php',
                    data: dataString,
                    success: function(html) {
                        $("#dataTable1").find('#' + TRofRow).find('.drugname').html(html);
                    }
                });
            }
            /*----------------------get Typeahead functionality ends here ----------------------------*/

            /*---------------------substringGet functionality starts here ----------------------------*/
            function substringGet(drugid, whichpart) {
                if ($.trim(whichpart) == "drugname") {
                    var userid = $('select#username').val();
                    var dataString = 'id=scrAddPrescription&alergic=' + drugid + '&userid=' + userid;
                    $.ajax({
                        type: "POST",
                        url: 'ajax/ajax_drug.php',
                        data: dataString,
                        success: function(html) {
                            if ($.trim(html) == "empty") {} else {
                                alert("This is allergic drug for this patient");
                            }
                        }
                    });

                }

            }

            /*---------------------substringGet functionality ends here ----------------------------*/
            function draftrsave(GetValueOfIt) {
                $("#saveRdraft").val($(GetValueOfIt).attr("title"));
            }

            function showTest() {
                $("#testDescriptions").show();
                $("#drugDescriptions").hide();
            }

            function showDrugDescription() {

                $("#drugDescriptions").show();

                $("#testDescriptions").hide();

            }

            function Totransferattachment(attachedfilesare) {

                $('.attachmentfiletoget').html(attachedfilesare);

                $.fn.colorbox.close();

            }

            function addRow10(tableID) {
                var LengthofRow = (document.getElementById("dataTable10").rows.length);
                var table = document.getElementById(tableID);
                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                var colCount = table.rows[0].cells.length;
                document.getElementById('getrows10').value = table.rows.length;
                for (var i = 0; i < colCount; i++) {
                    var newcell = row.insertCell(i);
                    newcell.innerHTML = table.rows[0].cells[i].innerHTML;
                    var ManualRow = $("#dataTable10").find("tr").eq(LengthofRow).attr("id", LengthofRow);
                    switch (newcell.childNodes[0].type) {
                        case "text":
                            newcell.childNodes[0].value = "";
                            break;
                        case "checkbox":
                            newcell.childNodes[0].checked = false;
                            break;
                        case "select-one":
                            newcell.childNodes[0].selectedIndex = 0;
                            break;
                    }
                    $("#dataTable10").find('#' + LengthofRow).find('.addjs').attr('onclick', 'deleteUploadedImage(this,' + LengthofRow + ',"dataTable10","delete")');

                    $("#dataTable10").find('#' + LengthofRow).find('.addjs').attr('class', 'cross-del');
                    $("#dataTable10").find('#' + LengthofRow).find('.firstRowOnly').removeClass('cross-del');
                    $("#dataTable10").find('#' + LengthofRow).find('.firstRowOnly').removeAttr('onclick');

                    $("#dataTable10").find('#' + LengthofRow).find('.multifile').val("");

                    $("#dataTable10").find('#' + LengthofRow).find('.multifile').attr('onchange', 'fileuploadeddoc(this.value, ' + LengthofRow + ')');
                    $("#dataTable10").find('#' + LengthofRow).find('.multifile').attr('id', 'multifile' + LengthofRow);
                    $("#dataTable10").find('#' + LengthofRow).find('.uploadFileId').attr('id', 'uploadId' + LengthofRow);
                    $("#dataTable10").find('#' + LengthofRow).find('.multiupload').attr('data-upload-id', LengthofRow);

                    $("#dataTable10").find('#' + LengthofRow).find('.attachedDoc').html("");

                }

            }





            function fileuploadeddoc(thisfilename, idval) {

                $("#dataTable10").find('#' + idval).find('.attachedDoc').html("<img src='img/attachment.png' style='width:20px;height:20px;' />" + thisfilename)

                //$("#dataTable10").find('#'+idval).find('.multifile').attr("value",thisfilename);

            }

            function add_cust_test() {
                $('#custom_tests').show();
            }

            function hideAddCustTest() {
                $('#custom_tests').hide();
            }


            /*----------------------nxtassdt functionality Starts here ----------------------------*/

            function nxtassdt() {
                //id="+elementValue+"&action="+elementAction+"&na="+na
                //id="+ids
                var ids = $('#username').val();
                $.fn.colorbox({
                    href: "user_conditions.php?id=" + ids,
                    iframe: true,
                    escKey: true,
                    open: true,
                    close: true,
                    innerWidth: 500,
                    innerHeight: 500
                });
            }
            /*----------------------nxtassdt functionality ends here ----------------------------*/
            /*----------------------ToPassIdGroup functionality starts here ----------------------------*/
            function ToPassIdGroup(a, b, c) {
                $.fn.colorbox.close();
                window.location.href = "add-prescription.php?id=0";
            }
            /*----------------------ToPassIdGroup functionality ends here ----------------------------*/
        </script>


        <?php if (isset($_GET["suser"])) {
            $suser = $_GET["suser"];
        } else {
            $suser = "";
        } ?>
        <div id="main" class="PrescriptionPage">



            <?php if ($tbs_role == "4") {
                $usertbl_name = "master_corporate_user";
            } ?>



            <form action="ohcAddPrescriptionSave.php" name="f1" method="post" class="form-horizontal" enctype="multipart/form-data" onSubmit="return validatePrescription()" style="width:99.5%;">

                <div style="position:fixed;width:450px; height:400px;top:-440%;left:30%;text-align:center;z-index:99999;" class="openitAttached">
                    <div class="row-fluid">
                        <div class="box box-color box-bordered" style="padding:0px 10px;">
                            <div class="box-title">
                                <h3><i class="glyphicon-paperclip" style="font-size:10px"></i> Attach</h3>
                                <div style="float: right; margin-right: 45px;"><a href="javascript:void('0')" class="icon icon-popupcls" onClick="wholedatatopaste()">&nbsp;</a></div>
                            </div>
                            <div class="box-content" style="height:150px;overflow-y:scroll;">
                                <div>
                                    <div class="control-group wholedatatopaste">
                                        <div style="min-height:150px;">
                                            <ul id="fileListPrescriptionAttachmentId"></ul>
                                            <div class="uploadDataPrescriptionAttachmentId"></div>
                                            <div id="container">
                                                <a id="browsePrescriptionAttachmentId">Browse...</a>
                                                <a id="uploadPrescriptionAttachmentId" href="javascript:;">Start Upload</a>
                                            </div>
                                            <pre id="consolePrescriptionAttachmentId"></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="Top-Strip">
                        <div class="col-xl-4 col-lg-4 col-md-4 float-left px-0">
                            <h5 style="font-weight:bold;color:#<?php echo $_SESSION["tclr"]; ?>;">Add Prescription</h5>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 float-left" style="padding: 5px;"><b><?php echo $UserDetailfirst_name . " " . $UserDetaillast_name . "  " . $DoctorQualification; ?></b></div>

                    </div>




                    <div class="content-table float-left text-right pb-2 mb-2" style="color:#333; font-size:12px; border-bottom: 1px #ccc dashed;">

                        <img src='https://login.myhealthvalet.in/img/Morning.png' align='absmiddle'> Morning &nbsp; &nbsp;
                        <img src='https://login.myhealthvalet.in/img/Noon.png' align='absmiddle'> Noon &nbsp; &nbsp;
                        <img src='https://login.myhealthvalet.in/img/Evening.png' align='absmiddle'> Evening &nbsp; &nbsp;
                        <img src='https://login.myhealthvalet.in/img/Night.png' align='absmiddle'> Night &nbsp; &nbsp;

                    </div>

                    <div class="box box-bordered box-color" style="margin-top: 10px;">
                        <div id="drugDescriptions"><!--open div for the drugDescriptions-->

                            <?php if ($tbs_role == "4") { ?>

                                <input type="hidden" name="docname" value="<?php echo $tbs_userid; ?>" />
                                <input type="hidden" name="ohcs" value='1' />
                                <input type="hidden" name="fav_pharmacy" value="<?php echo $_SESSION["ohc_loca"]; ?>" />
                                <input type="hidden" name="corp_id" value='<?php echo $_SESSION["ohc_loca"]; ?>' />
                                <div class="content-table float-left pt-0">
                                    <input type="hidden" name="case_id" value="<?php echo $_GET["case"]; ?>" />

                                    <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2">

                                        <select class="select2-me icon_90 " style="height:25px;" name="existtemplate" id="existtemplate" onChange="templateselect(this.value);">

                                            <option value="" selected="selected">Select Template</option>

                                            <option value="template">Add New</option>

                                            <?php
                $GtTemplate = new ManageUsers();
                                echo "SELECT * from template where user_id='" . $tbs_userid . "'";
                                $GtTemplates = $GtTemplate->listDirectQuery("SELECT * from template where user_id='" . $tbs_userid . "' and ohc='1'");
                                if ($GtTemplates != 0) {
                                    foreach ($GtTemplates as $GtTemplatesL) {
                                        echo '<option value="' . $GtTemplatesL["id"] . '">' . $GtTemplatesL["template_name"] . "</option>";
                                    }
                                }
                                ?>

                                        </select>

                                    </div>




                                    <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2">

                                        <input type="text" class="input-large icon_90" name="newtemplate" id="newtemplate" placeholder="New Template Name">

                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2">
                                        <?php if (isset($_GET["case"])) {
                                            $corporatecomponent = new ManageMessage();
                                            // echo "SELECT * from corporate_component where corp_id='".$_SESSION['loc_id']."'";
                                            $corporatecomponentdetails = $corporatecomponent->listDirectQuery("SELECT * from corporate_component where corp_id='" . $_SESSION["loc_id"] . "'");
                                            $corporatecomponent = null;
                                            $obj = new ManageUsers();
                                            $UserDeta = $obj->listDirectQuery("SELECT date(cf.cr_date) as created_case,date(cf.act_date) as act_case,concat(mud.first_name, '  ',mud.last_name) as fullname,cf.* from ohc cf left outer join master_user_details mud on mud.id=cf.user_id where cf.id=" . $_GET["case"] . "");
                                            $obj = null;
                                            if ($corporatecomponentdetails[0]["report_time"] == "1,2") {
                                                $created_case = date("d/m/Y", strtotime($UserDeta[0]["created_case"]));
                                            } elseif ($corporatecomponentdetails[0]["report_time"] == "1") {
                                                $created_case = date("d/m/Y", strtotime($UserDeta[0]["created_case"]));
                                            } elseif ($corporatecomponentdetails[0]["report_time"] == "2") {
                                                $created_case = date("d/m/Y", strtotime($UserDeta[0]["act_case"]));
                                            }
                                        } ?>
                                        <input type="text" value="<?php echo date("d/m/Y"); ?>" id="fromdate" name="fromdate" onchange="set_date_time()" style="width: 90px;height:25px!important;padding:0px 3%; margin:0 3px; border: 1px #ccc solid !important;" readonly />
                                        <!--<input type="text" name="fromdate" id="fromdate" readonly="readonly" class="datepick input-mini datepickohc" style="width:90px;"  value="<?php echo isset($_GET["case"]) ? $created_case : date("d/m/Y"); ?>">-->
                                    </div>
                                </div>
                                <div class=" float-left pt-0" style="border-top:1px solid #999; border-bottom:1px solid #999; width:100%; background-color: #eee;">

                                    <div class="col-xl-1 col-lg-1 col-md-1 float-left p-2">
                                        <label for="questions" class="control-label titclr" style="font-size:14px;font-weight:bold;color:#<?php echo $_SESSION["tclr"]; ?>;"> User Details</label>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-3 float-left p-2" style="border-right:1px dashed #999;">
                                        <div class="jspatient">
                                            <?php if (isset($_GET["case"]) || isset($_GET["suser"])) { ?>
                                                <div class="jspatientb">
                                                    <?php
                                            if (isset($_GET["case"])) {
                                                @$textuser_id = @$caseuser_id;
                                            } else {
                                                @$textuser_id = @$suserid;
                                            }
                                                $case_id = $_GET["case"];
                                                if (isset($_GET["case"])) {
                                                    if (empty($_SESSION["ohc_doc"])) {
                                                        $sql = "SELECT mu.first_name,mu.last_name,mu.id,if(TIMESTAMPDIFF(year,mu.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mu.dob,now()), ' month'), TIMESTAMPDIFF(year,mu.dob,NOW())) as age,gender from case_file c
								LEFT OUTER JOIN master_user_details mu ON mu.id=c.user_id 
								WHERE c.id='$case_id'";
                                                    } else {
                                                        $sql = "SELECT mu.first_name,mu.last_name,mu.id,if(TIMESTAMPDIFF(year,mu.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mu.dob,now()), ' month'), TIMESTAMPDIFF(year,mu.dob,NOW())) as age,gender from ohc c
								LEFT OUTER JOIN master_user_details mu ON mu.id=c.user_id 
								WHERE c.id='$case_id'";
                                                    }
                                                }
                                                if (isset($_GET["suser"])) {
                                                    $sql = "SELECT mu.first_name,mu.last_name,mu.id,if(TIMESTAMPDIFF(year,mu.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mu.dob,now()), ' month'), TIMESTAMPDIFF(year,mu.dob,NOW())) as age,gender 
								FROM master_user_details mu WHERE mu.id='$suserid'";
                                                }
                                                $user = new ManageUsers();
                                                $user1 = $user->listDirectQuery($sql);
                                                //print_r($user1);
                                                if ($user1 != 0) {
                                                    foreach ($user1 as $key) {
                                                        $name = $key["first_name"] . " " . $key["last_name"];
                                                        $textuser_id = $key["id"];
                                                        $gender = $key["gender"];
                                                        $age = $key["age"];
                                                    }
                                                }
                                                $fullname1 = $name;
                                                echo '<input type="text" name="usernamea" id="usernamea" class="usernameremove" readonly value="' . @$fullname1 . '" style="width: 95%; padding: 12px 10px;" /><input type="hidden" name="username" id="username" class="usernameremove" value="' . $textuser_id . '" /></div>
							<b style="text-transform: capitalize; color:#<? echo $_SESSION[tclr] ?>; float:right; margin-top: 10px;">' . $gender . "/" . $age . " </b>";
                                                ?>


                                                </div>
                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-4 float-left p-1" style="border-right:1px dashed #999;">
                                            <div class="col-xl-12 col-lg-12 col-md-12 float-left p-0">
                                                <div class="col-xl-5 col-lg-5 col-md-5 float-left p-0" style="text-align:right">Medical Conditions: &nbsp;</div>
                                                <div class="col-xl-7 col-lg-7 col-md-7 float-left p-0"><b>
                                                        <?php
                                                            $med = new ManageMessage();
                                                $med_con = $med->listDirectQuery("select * from med_condition_map where user_id='" . $textuser_id . "'");
                                                $medical_condition = explode(",", $med_con[0]["is_active"]);
                                                foreach ($medical_condition as $med_val) {
                                                    if (!empty($med_val)) {
                                                        $medi = $med->listDirectQuery(" select condition_name  from prescription_condition where id=" . $med_val . " ");
                                                    }
                                                    $med_arr[] = $medi[0]["condition_name"];
                                                }
                                                $med_arrArray = array_filter($med_arr);
                                                if (count($med_arrArray) === 0) {
                                                    echo "None";
                                                } else {
                                                    echo implode(",", $med_arr);
                                                }
                                                ?>
                                                    </b></div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12 float-left p-0">
                                                <div class="col-xl-5 col-lg-5 col-md-5 float-left p-0" style="text-align:right">Allergic Ingredient: &nbsp;</div>
                                                <div class="col-xl-7 col-lg-7 col-md-7 float-left p-0"><b>
                                                        <?php
                                                            $aller = new ManageMessage();
                                                $allergy = $aller->listDirectQuery("select * from allergic_drugs_map where user_id='" . $textuser_id . "'");
                                                $allergy_details = explode(",", $allergy[0]["drug_id"]);
                                                foreach ($allergy_details as $allergy) {
                                                    if (!empty($allergy)) {
                                                        $all = $aller->listDirectQuery(" select id,name  from ingredients where id=" . $allergy . " ");
                                                    }
                                                    $aller_arr[] = $all[0]["name"];
                                                }
                                                $aller_arrArray = array_filter($aller_arr);
                                                if (count($aller_arrArray) === 0) {
                                                    echo "None";
                                                } else {
                                                    echo implode(",", $aller_arr);
                                                }
                                                ?>
                                                    </b></div>
                                            </div>
                                        <?php
                                            } ?>

                                        <?php if ($UserDeta[0]["related"] == "1" || $UserDeta[0]["related"] == "3") { ?>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 float-left p-1" style="border-left:1px dashed #999;">
                                            <div class="col-xl-12 col-lg-12 col-md-12 float-left p-0">
                                                <div class="col-xl-5 col-lg-5 col-md-5 float-left p-0" style="text-align:right">Nature of Injury: &nbsp;</div>
                                                <div class="col-xl-7 col-lg-7 col-md-7 float-left p-0"><b><?php
                                                $inju = new ManageMessage();
                                            $injus = $inju->listDirectQuery(" select *  from injury where status='2'");
                                            foreach ($injus as $ins) {
                                                $sel = "";
                                                if ($UserDeta[0]["nature"] == $ins["id"]) {
                                                    echo $ins["name"];
                                                }
                                            }
                                            ?></b></div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12 float-left p-0">
                                                <div class="col-xl-5 col-lg-5 col-md-5 float-left p-0" style="text-align:right">Mechanism of Injury: &nbsp;</div>
                                                <div class="col-xl-7 col-lg-7 col-md-7 float-left p-0"><b><?php
                                                        $mecha = new ManageMessage();
                                            $mechanism = $mecha->listDirectQuery(" select *  from injury where status='7'");
                                            foreach ($mechanism as $mech) {
                                                $sel = "";
                                                if ($UserDeta[0]["mechanism"] == $mech["id"]) {
                                                    echo $mech["name"];
                                                }
                                            }
                                            ?></b></div>
                                            </div>
                                        </div>
                                    <?php
                                        } ?>

                                    <?php if ($UserDeta[0]["related"] == "2" || $UserDeta[0]["related"] == "4" || $UserDeta[0]["related"] == "5") { ?>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 float-left p-1" style="border-left:1px dashed #999;">
                                        <div class="col-xl-12 col-lg-12 col-md-12 float-left p-0">
                                            <div class="col-xl-5 col-lg-5 col-md-5 float-left p-0" style="text-align:right">Symptoms: &nbsp;</div>
                                            <div class="col-xl-7 col-lg-7 col-md-7 float-left p-0"><b>
                                                    <?php
                                            $inju = new ManageMessage();
                                        $consels = $inju->listDirectQuery("select * from ohc_condition where rids='" . $_REQUEST["case"] . "' and type='2'");
                                        $symptoms = explode(",", $consels[0]["symptoms"]);
                                        foreach ($symptoms as $ins) {
                                            if (!empty($ins)) {
                                                $injus = $inju->listDirectQuery(" select name  from injury where id=" . $ins . " ");
                                            }
                                            $arr[] = $injus[0]["name"];
                                        }
                                        echo implode(",", $arr);
                                        ?>
                                                </b></div>
                                        </div>
                                        <div class="col-xl-12 col-lg-12 col-md-12 float-left p-0">
                                            <div class="col-xl-5 col-lg-5 col-md-5 float-left p-0" style="text-align:right">Medical System: &nbsp;</div>
                                            <div class="col-xl-7 col-lg-7 col-md-7 float-left p-0"><b>
                                                    <?php $condit = explode(",", $consels[0]["conditions"]); ?>


                                                    <?php
                                                    foreach ($condit as $ins) {
                                                        if (!empty($ins)) {
                                                            $condition = $inju->listDirectQuery(" select name  from injury where id=" . $ins . " ");
                                                        }
                                                        $arr2[] = $condition[0]["name"];
                                                    }
                                        echo implode(",", $arr2);
                                        ?>
                                                </b></div>
                                        </div>

                                        <div class="col-xl-12 col-lg-12 col-md-12 float-left p-0">
                                            <div class="col-xl-5 col-lg-5 col-md-5 float-left p-0" style="text-align:right">Diagnosis: &nbsp;</div>
                                            <div class="col-xl-7 col-lg-7 col-md-7 float-left p-0"><b>
                                                    <?php
                                                    $user = new ManageUsers();
                                        $dia = $user->listDirectQuery("select name from injury where id in ($diag)");
                                        foreach ($dia as $d) {
                                            echo $d["name"] . ", ";
                                        }
                                        ?>
                                                </b></div>
                                        </div>
                                    </div>
                                <?php
                                    } ?>

                                <div class="jspatientc" style="<?php if (isset($_GET["case"]) || @$suserid != "") { ?>display:none;<?php
                                } ?>">
                                    <select name="<?php if (isset($_GET["case"])) {
                                        echo "usernameold";
                                    } else {
                                        echo "username";
                                    } ?>" id="user_id" title="Associates" class="uservaluefull nrm a1 select2-me  floatleft  texttransformcap" style="width: 90% !important;" onchange="userdata(this.value)">
                                        <?php
                                    $AppointmentDetail = new ManageUsers();
                                $Appointment = $AppointmentDetail->listDirectQuery("SELECT mud.id, mud.first_name, mud.last_name, cum.emp_id,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, concat(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' month'), TIMESTAMPDIFF(year,mud.dob,NOW())) as age,gender FROM corporate_user_mapping cum LEFT OUTER JOIN master_user_details mud ON mud.id = cum.r_user_id WHERE cum.location='" . $_SESSION["ohc_loca"] . "' AND cum.isactive='1' AND cum.emp_type not in(1,6)	GROUP BY mud.id	ORDER BY mud.first_name");
                                $AppointmentDetail = null;
                                if ($Appointment != 0) {
                                    foreach ($Appointment as $listAppointments) {
                                        $sel = "";
                                        $Appids = $listAppointments["id"];
                                        $first_name = $listAppointments["first_name"];
                                        $last_name = $listAppointments["last_name"];
                                        $gender = $listAppointments["gender"];
                                        $age = $listAppointments["age"];
                                        $empid = $listAppointments["emp_id"];
                                        if ($edits[0]["user_id"] == $Appids) {
                                            $sel = "selected='selected'";
                                        }
                                        echo '<option value="' . $Appids . '" ' . $sel . ">" . ucfirst($first_name) . "  " . ucfirst($last_name) . " - " . $empid . " </option>";
                                    }
                                }
                                ?>
                                    </select>

                                    <div id="get_age_gender"><b style="text-transform: capitalize; color:#<?php echo $_SESSION[tclr]; ?>; float:right; margin-top: 10px; padding-right: 25px;"><?php echo $age . "/" . $gender; ?></b></div>
                                </div>

                                </div>
                                <!--	 <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2">
						<label for="questions" class="control-label"> <span style="text-transform:capitalize;"><?php echo @$gendercase; ?></span></label>
						<div class="controls">
							 <label for="questions" class="control-label"><span style="text-transform:capitalize;"><?php echo @$agecase; ?></span></label>
						</div>  
					 <div id="timeline-post2" style="float:left;">
					 </div>
				</div> 
-->
                        </div>
                        <div id="get_med_condition_div">

                        </div>
                    </div>
                <?php
                            } ?>

                <div class="box box-bordered box-color" style="float:left; margin-top:10px;">

                    <div class=" nopadding">
                        <div class="tab-pane active content-table py-0 float-left" id="user">
                            <div class="prescription-tr-table" style="width:100%; padding: 5px 0; float:left; border-bottom: 1px #333 solid; font-weight:bold;color:#<?php echo $_SESSION["tclr"]; ?>;">
                                <div class="prescription-th-table" style="width: 21%; float:left;">Drug Name</div>
                                <div class="prescription-th-table" style="width: 11%; text-align:center; float:left;">Available</div>
                                <div class="prescription-th-table" style="width: 6%;  text-align:center; float:left;">Days</div>
                                <div class="prescription-th-table" style="width: 20%; text-align:center; float:left;">
                                    <?php
                            $columnDisplayIconArrayLis[] = "<img src='" . $sitepath . "img/Morning.png'>";
    $columnDisplayIconArrayLis[] = "<img src='" . $sitepath . "img/Noon.png'>";
    $columnDisplayIconArrayLis[] = "<img src='" . $sitepath . "img/Evening.png'>";
    $columnDisplayIconArrayLis[] = "<img src='" . $sitepath . "img/Night.png'>";
    $count = 0;
    foreach ($columnDisplayNameArrayLists as $columnDisplayNameArrayList) {
        $columnInputNameArrayList = $columnInputNameArrayLists[$count];
        $columnShortNameArrayList = $columnShortNameArrayLists[$count];
        $columnDisplayIconArrayList = $columnDisplayIconArrayLis[$count];
        echo ' <div style="float:left;margin-right:.8%;text-align:center;width:30px;margin:0 5px;
				" >' . $columnDisplayIconArrayList . "</div>";
        $count = $count + 1;
    }
    ?>
                                </div>

                                <div class="prescription-th-table" style="width: 13%; float:left;" title="Unit of Measurement">UOM</div>
                                <div class="prescription-th-table" style="width: 14%; float:left;">AF/BF</div>
                                <div class="prescription-th-table" style="width: 15%; float:left;">Remarks</div>
                            </div>

                            <div class="prescription-tr-table" style="width:100%; padding: 5px 0; border-bottom: 1px #333 solid;float:left;">
                                <?php
        // Assuming this PHP code is placed within the same file where HTML is embedded, adjust as necessary
        $DrugTemplates = new ManageUsers();
    $locationID = $_SESSION["loc_id"];
    // $sql = "SELECT psd.drug_name, psd.drug_type, psd.drug_strength, SUM(psd.cur_availability) AS total_availability
    // FROM ohc_pharmay op
    // JOIN pharmacy_stock_detail psd ON op.id = psd.r_pharmacy_id
    // WHERE op.location_id = " . $locationID . "  AND psd.cur_availability > 0
    // GROUP BY psd.drug_name, psd.drug_type, psd.drug_strength";
    $sql = "SELECT psd.drug_name, psd.drug_type, psd.drug_strength, psd.drug_template_id, SUM(psd.cur_availability) AS total_availability FROM ohc_pharmay op JOIN pharmacy_stock_detail psd ON op.id = psd.phar_ids WHERE op.location_id = " . $locationID . " AND psd.cur_availability > 0 GROUP BY psd.drug_name, psd.drug_type, psd.drug_strength, psd.drug_template_id";
    // echo $sql;
    $result = $DrugTemplates->listDirectQuery($sql);
    // print_r($result);
    $consolidatedDrugs = [];
    foreach ($result as $drug) {
        if ($drug["drug_template_id"]) {
            $drugName = $drug["drug_name"];
            $drugType = $drug["drug_type"];
            $drugStrength = intval($drug["drug_strength"]);
            $drugId = $drug["id"];
            $totalAvailability = $drug["total_availability"];
            $drugTemplateId = $drug["drug_template_id"];
            if (!isset($consolidatedDrugs[$drugName])) {
                $consolidatedDrugs[$drugName] = ["id" => $drugId, "type" => $drugType, "totalStrength" => 0, "totalAvailability" => 0, "count" => 0, "drug_template_id" => $drugTemplateId, ];
            }
            $consolidatedDrugs[$drugName]["totalStrength"] += $drugStrength;
            $consolidatedDrugs[$drugName]["totalAvailability"] += $totalAvailability;
            $consolidatedDrugs[$drugName]["count"]++;
        }
    }
    ksort($consolidatedDrugs);
    ?>

<table id="dataTable1" width="100%" cellspacing="2px">
    <input type="hidden" name="getrows1" id="getrows" value="1" />
    <tr id="0">
        <td data-content="Drug Name" style="padding: 5px 0; width: 22%;">
            <input type="hidden" name="rowid[]" id="rowid" value="0" />
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            <style>
                .select2-container .select2-selection--single {
                    height: 25px;
                }
                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    line-height: 25px;
                }
                .select2-container--default .select2-selection--single .select2-selection__arrow {
                    height: 25px;
                }
            </style>

            <div class="drugname" title="drugname">
                <input type="hidden" id="drugname" name="drugname[]" class="mastrdrug" />
                <select class="hiddendrugname" style="height:25px;width:85%;" name="hiddendrugname[]" id="hiddendrugname" onfocus="changeDefaultText();">
                    <option value="" selected="selected">Search</option>
                    <?php foreach ($consolidatedDrugs as $drugName => $drugInfo) {
                        $totalStrength = $drugInfo["totalStrength"];
                        $totalAvailability = $drugInfo["totalAvailability"];
                        $drugTemplateId = $drugInfo["drug_template_id"];
                        echo '<option value="' . $drugName . '--' . $drugInfo["id"] . '--' . $drugInfo['type'] .'--' . $totalStrength . '--' . $totalAvailability . '--' . $drugTemplateId . '">' . $drugName . " - " . $drugInfo["type"] . " - " . $totalStrength . "</option>";
                    } ?>
                </select>
            </div>
        </td>
        <td style="padding: 5px 0; width:10%; text-align:center;"><span class="strengthDisplay"></span></td>
        <td data-content="Days" style="padding: 4px 0; width:5%;">
            <input type="text" maxlength="3" name="duration[]" id="duration" class="duration tblduration input-minix" placeholder="Days" onKeyPress="return ValidNumber(event)" value="" style="width:65px;" />
        </td>
        <td class="timdur2" style="width:20%; padding: 3px 1px;">
            <div data-content="Morning (AM)"><input type="text" maxlength="2" name="morning[]" id="m0" class="morning input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;"></div>
            <div data-content="Noon"><input type="text" maxlength="2" name="afternoon[]" id="a0" class="afternoon input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;"></div>
            <div data-content="Evening"><input type="text" maxlength="2" name="evening[]" id="e0" class="evening input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;"></div>
            <div data-content="Night (PM)"><input type="text" maxlength="2" name="night[]" id="n0" class="night input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;"></div>
        </td>
        <td data-content="Drug Type" style="padding: 5px 0; width:12%;" class="dtype"></td>
        <td data-content="AF/BF" style="padding: 5px 0; width:14%;">
            <select name="drugintakecondition[]" id="drugintakecondition" class="select drugintakecondition py-2">
                <?php
                    $drugintakecondition = "10";
    $drugintakeconditionObj = new ManageUsers();
    $drugintakeconditionLists = $drugintakeconditionObj->listDirectQuery("SELECT doctype,id FROM doctype_static WHERE doctypename_static_id='4' ORDER BY `order_list` ASC");
    $drugintakeconditionObj = null;
    if ($drugintakeconditionLists != 0) {
        foreach ($drugintakeconditionLists as $drugintakeconditionList) {
            $sel = $drugintakeconditionList["id"] == $drugintakecondition ? "selected='selected'" : "";
            echo '<option value="' . $drugintakeconditionList["id"] . '" ' . $sel . ">" . $drugintakeconditionList["doctype"] . "</option>";
        }
    }
    ?>
            </select>
        </td>
        <td data-content="Remarks" style="padding: 5px 0; width: 12%;">
            <input type="text" name="remarks[]" id="remarks" placeholder="Remarks" class="remarks input-medium" style="width:90%;height:28px!important;" />
        </td>
        <td class="add">
            <div style="position:absolute; margin-top:-8px; cursor:pointer;" class="margin-t-8 cross addjs" id="cl" onClick="addRow1('dataTable1')"><i class="glyphicon glyphicon-circle_plus"></i></div>
        </td>
    </tr>
</table>

<style>
    #dataTable1 td {
        vertical-align: middle;
    }
    .strengthDisplay {
        display: inline-block;
        width: 100%;
        text-align: center;
    }
</style>
<script>
function changeDefaultText() {
    var selectElement = document.getElementById('hiddendrugname');
    var firstOption = selectElement.options[0];
    if (firstOption.value === "") {
        firstOption.text = "Search";
    }
}

function displayTotalAvailability(selectElement) {
    var selectedOption = selectElement.options[selectElement.selectedIndex].value;
    var row = selectElement.closest('tr');
    if (selectedOption) {
        var parts = selectedOption.split('--');
        var totalAvailability = parts[4];
        row.querySelector('.strengthDisplay').innerText = totalAvailability;
    } else {
        row.querySelector('.strengthDisplay').innerText = '';
    }
}

function checkDuplicateDrug(selectElement) {
    var selectedDrug = selectElement.value;
    var allDrugSelects = document.querySelectorAll('select[name^="hiddendrugname"]');
    var count = 0;

    allDrugSelects.forEach(function(select) {
        if (select.value === selectedDrug && select.value !== "") {
            count++;
        }
    });

    if (count > 1) {
        alert("This drug has already been selected. Please choose a different drug.");
        // Clear the selection and remove the row
        $(selectElement).val(null).trigger('change');
        deleteRow(selectElement);
        return false;
    }
    return true;
}

$(document).ready(function() {
    $('select[name^="hiddendrugname"]').select2({
        placeholder: "Search",
        allowClear: true,
        width: 'resolve'
    });

    $(document).on('change', 'select[name^="hiddendrugname"]', function() {
        if (checkDuplicateDrug(this)) {
            getdrugdetails(this);
            drugname_validation();
            get_allergic_ingredient_alert(this);
            displayTotalAvailability(this);
        }
    });
});

function drugname_validation() {
    var drugnames = $("select[name^='hiddendrugname']")
        .map(function() {
            return $(this).val();
        }).get();

    var action = "validate_drugname";
    var dataString = {
        drugname: drugnames,
        action: action
    };

    $.ajax({
        type: "POST",
        url: "ajax/ajax_addohc-pres-temp.php",
        data: dataString,
        success: function(data) {
            data = $.trim(data);
            var datas = data.split("@@");
            var data1 = datas[0];
            var data2 = datas[1];

            if (data2 == 'Drugname already exist') {
                alert("You have already selected this drug. Please select a different drug.");
                $("select[name^='hiddendrugname']").eq(data1).val("").trigger('change');
            }
        }
    });
}

function addRow1(tableID) {
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    row.id = rowCount;
    
    // Clone the first row
    var firstRow = table.rows[0];
    row.innerHTML = firstRow.innerHTML;
    
    // Update IDs and clear values
    var inputs = row.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        var oldId = input.id;
        var newId = oldId + '_' + rowCount;
        input.id = newId;
        input.value = '';
        
        if (input.name.includes('[]')) {
            input.name = input.name.replace('[]', '[' + rowCount + ']');
        }
    });
    
    // Handle the drug select element specifically
    var newSelect = row.querySelector('select[name^="hiddendrugname"]');
    if (newSelect) {
        // Remove any existing Select2 containers
        var oldContainer = row.querySelector('.select2-container');
        if (oldContainer) oldContainer.remove();
        
        // Add options to the new select element
        var options = firstRow.querySelector('select[name^="hiddendrugname"]').innerHTML;
        newSelect.innerHTML = options;  // Remove the previous options including 'Search'
        
        // Initialize Select2 on the new select element
        $(newSelect).select2({
            placeholder: "Search",
            allowClear: true,
            width: 'resolve'
        });
        
        // Add event listener
        newSelect.addEventListener('change', function() {
            if (checkDuplicateDrug(this)) {
                getdrugdetails(this);
                drugname_validation();
                get_allergic_ingredient_alert(this);
                displayTotalAvailability(this);
            }
        });
    }
    
    // Clear strength display
    var strengthDisplay = row.querySelector('.strengthDisplay');
    if (strengthDisplay) strengthDisplay.textContent = '';
    
    // Update the last cell to show delete icon instead of add
    var lastCell = row.cells[row.cells.length - 1];
    lastCell.innerHTML = '<div style="position:absolute; margin-top:-8px; cursor:pointer;" class="margin-t-8 cross removejs" id="cl" onClick="deleteRow(this)"><i class="glyphicon glyphicon-circle_minus"></i></div>';
    
    // Update the hidden input for row count
    document.getElementById('getrows').value = table.rows.length;

    // Call drugname_validation after adding a new row
    drugname_validation();
}

function deleteRow(btn) {
    var row = btn.closest('tr');
    row.parentNode.removeChild(row);
    
    // Update the hidden input for row count
    var table = document.getElementById('dataTable1');
    document.getElementById('getrows').value = table.rows.length;
}

$(document).ready(function() {
    $('#hiddendrugname').select2({
        placeholder: "Search",
        allowClear: true,
        width: 'resolve'
    });

    $(document).on('change', 'select[name^="hiddendrugname"]', function() {
        if (checkDuplicateDrug(this)) {
            getdrugdetails(this);
            drugname_validation();
            get_allergic_ingredient_alert(this);
            displayTotalAvailability(this);
        }
    });
});
</script>



                            </div>

                          <!-- Other Prescription -->
                          <div style="width:100%; padding: 5px 0; float:left; border-bottom: 1px #333 solid; font-weight:bold;color:#<?php echo $_SESSION['tclr'] ?>;">
                                Outside Prescription
                            </div>
                            <div class="prescription-tr-table" style="width:100%; padding: 5px 0; border-bottom: 1px #333 solid;float:left;">
                                <table id="dataTable2" width="100%" cellspacing: 2px;>
                                    <input type="hidden" name="getrows1" id="getrows2" value="1" />
                                    <tr id="0">
                                        <td data-content="Drug Name" style="width: 32%; ">
                                            <input type="hidden" name="rowid[]" id="rowid2" value="0" />
                                            <div class="drugname " title="drugname">
                                                <input type="hidden" id="drugname" name="drugname[]" class="mastrdrug" />

                                                <?php

                                                echo '<input type="text"  name="outsidedrugname[]" onkeypress="return samblockSpecialChar(event)" placeholder="Outside Drug Name"   class="input-large outside-drug-name"  style="width:95%;"/>';
    ?>
                                            </div>
                                        </td>
                                        <!--td style="width:10%; text-align:center;" class="avalcount">--</td-->


                                        <td data-content="Days" style="width:5%;"><input type="text" maxlength="3" name="outsideduration[]" id="duration" class="duration tblduration input-minix" placeholder="Days" onKeyPress="return ValidNumber(event)" value="" style="width:65px;" /></td>
                                        <td class="timdur2" style="width:20%; padding: 3px 1px;">
                                            <?php

    ?>

                                            <div data-content="Morning (AM)"><input type="text" maxlength="2" name="Outsidemorning[]" id="m0" class="morning input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;
				"></div>
                                            <div data-content="Noon"><input type="text" maxlength="2" name="Outsideafternoon[]" id="a0" class="afternoon input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;
				"></div>
                                            <div data-content="Evening"><input type="text" maxlength="2" name="Outsideevening[]" id="e0" class="evening input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;
				"></div>
                                            <div data-content="Night (PM)"><input type="text" maxlength="2" name="Outsidenight[]" id="n0" class="night input-minix" placeholder="0" onkeyup="" onkeypress="return ValidNumber(event)" value="" style="float:left;text-align:center;width:33px;margin-right:8px;padding:0px;
				">
                                        </td>

                                        <td data-content="Drug Type" style="width:12%;">

                                            <!-- <input type="hidden" id="hiddendrugtype" name="Outsidehiddendrugtype[]" class="mastrdrug" /> -->

                                            <select name="Outsidehiddendrugtype[]" id="drugtype" class="select tbldrugtype " style="width:autp;">



                                                <?php
        $tablename = 'doctype_static';
    $valuecol = 'id';
    $displaycol = 'doctype';
    $where = 'doctypename_static_id="3"';
    $ischecked = '1';
    $selectedvalue = '184';
    $showDropDowndrugtype = new ManageSettings();
    echo $showDropDowndrugtype->createdropdownvalues($tablename, $valuecol, $displaycol, $ischecked, $selectedvalue, $where);
    $showDropDowndrugtype = null;
    ?>

                                            </select>
                                        </td>


                                        <td data-content="AF/BF" style="width:14%;"><select name="Outsidedrugintakecondition[]" id="drugintakecondition" class="select drugintakecondition py-2" style="width:auto;">

                                                <?php
    $drugintakecondition = "10";
    $drugintakeconditionObj = new ManageUsers();
    $drugintakeconditionLists = $drugintakeconditionObj->listDirectQuery("SELECT doctype,id FROM doctype_static WHERE doctypename_static_id='4' ORDER BY `order_list` ASC");
    $drugintakeconditionObj = null;
    if ($drugintakeconditionLists != 0) {
        foreach ($drugintakeconditionLists as $drugintakeconditionList) {
            $sel = ($drugintakeconditionList['id'] == $drugintakecondition) ? "selected='selected'" : '';
            echo '<option value="' . $drugintakeconditionList['id'] . '" ' . $sel . '>' . $drugintakeconditionList['doctype'] . '</option>';
        }
    }
    ?>

                                            </select></td>


                                        <td data-content="Remarks" style="width: 12%;"><input type="text" name="remarks[]" id="remarks" placeholder="Remarks" class="remarks input-medium" style="<?php if (count($columnDisplayNameArrayLists) == "3") {
                                            echo "width:90%;height:28px!important;";
                                        } else {
                                            echo "width:90%;height:28px!important";
                                        } ?>" /></td>

                                        <td class="add">
                                            <div style="position:absolute; margin-top:-8px; cursor:pointer;" class="margin-t-8 cross addjs" id="cl" onClick="addRow2('dataTable2')"><i class="glyphicon-circle_plus"></i></div>
                                        </td>

                                    </tr>

                                </table>
                            </div>

                            <!--End Other Prescription -->



                            <div class="col-xl-6 col-lg-6 col-md-6 float-left p-2">Select Pharmacy
                                <select class="select2-me" style="height:25px; width: 50%; float:left;" name="fav_pharmacy" id="fav_pharmacy">
                                    <?php
          $Gtpharmacy = $GtTemplate->listDirectQuery("SELECT * from  `ohc_pharmay` where corp_id=" . $_SESSION["parent_id"] . " and location_id=" . $_SESSION["ohc_loca"] . " and mainpharmacy!='1'");
    if ($Gtpharmacy != 0) {
        foreach ($Gtpharmacy as $Gtpharmacys) {
            echo '<option value="' . $Gtpharmacys["id"] . '">' . $Gtpharmacys["name"] . "</option>";
        }
    }
    ?>

                                </select>

                            </div>


                        </div>



                        <style>
                            optgroup:before {
                                content: attr(label);
                                display: block;
                            }

                            .selecter .selecter-selected {
                                height: 15%;
                            }
                        </style>


                        <?php if ($tbs_role != "4") { ?>
                            <div class="content-table float-left">

                                <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2">

                                    <select name="fav_pharmacy" id="fav_pharmacy" class="select2-me input-large floatleft" style="">
                                        <option value="">Select your favorite Pharmacy<?php if ($tbs_role == "1" || $tbs_role == "2") { ?>
                                            <optgroup label="Favorite - <?php if ($tbs_role == "2") { ?>Doctor<?php
                                            } else { ?>User<?php
                                            } ?>">
                                                <?php
                                            $listFavObj = new ManageUsers();
                                            $listFavs = $listFavObj->listDirectQuery("SELECT mp.pharmacy_name,mp.id,area.doctype as areaName FROM `favorites` f
										LEFT OUTER JOIN `master_pharmacy` mp ON mp.id=f.reference_id AND f.status=1
										LEFT OUTER JOIN `doctype` pincode ON pincode.id=mp.pincode
										LEFT OUTER JOIN `doctype` area ON area.id=pincode.parent_id 
										WHERE f.user_id='" . $tbs_userid . "' AND f.role_id='" . $tbs_role . "' GROUP BY mp.id ORDER BY f.modified_on DESC,f.created_on ASC");
                                            $listFavObj = null;
                                            $countRow = 0;
                                            $sel = "";
                                            foreach ($listFavs as $listFav) {
                                                $id = $listFav["id"];
                                                $name = $listFav["pharmacy_name"];
                                                if ($id != "") {
                                                    $countRow = $countRow + 1;
                                                    $sel = $countRow == 1 ? "" : "";
                                                    echo '<option value="' . $listFav["id"] . '" ' . $sel . ">" . $listFav["pharmacy_name"] . ", " . $listFav["areaName"] . " DELETE</option>";
                                                }
                                            }
                                            ?>
                                            </optgroup>
                                        <?php
                                        } ?>
                                        <optgroup label="Favorite - Add Other Pharmacy">
                                            <?php
                                        $listFavObj = new ManageUsers();
                            $listFavs = $listFavObj->listDirectQuery("SELECT mp.pharmacy_name,mp.id,area.doctype as areaName 
										FROM `master_pharmacy` mp 
										LEFT OUTER JOIN `doctype` pincode ON pincode.id=mp.pincode
										LEFT OUTER JOIN `doctype` area ON area.id=pincode.parent_id 										
										WHERE mp.id NOT IN (SELECT f.reference_id FROM `favorites` f WHERE f.user_id='" . $tbs_userid . "' AND f.role_id='" . $tbs_role . "' AND f.status=1) GROUP BY mp.id ORDER BY mp.pharmacy_name ASC");
                            $listFavObj = null;
                            foreach ($listFavs as $listFav) {
                                $id = $listFav["id"];
                                $name = $listFav["pharmacy_name"];
                                if ($id != "") {
                                    echo '<option value="' . $listFav["id"] . '">' . $listFav["pharmacy_name"] . ", " . $listFav["areaName"] . "</option>";
                                }
                            }
                            ?>
                                        </optgroup>
                                    </select>

                                </div>
                            </div>
                        <?php
                        } ?>
                        <div class="row-fluid">
                            <div id="custom_tests" style="display:none;padding-left:6px;" class="span12">
                                <p><b>Add other Test <i class="glyphicon-circle_minus" onclick="hideAddCustTest()" style="cursor:pointer;"></i></b></p>
                                <input type="text" name="custom_test[]" id="custom_test1" class="cust_test" placeholder="New test" />
                                <input type="text" name="custom_test[]" id="custom_test2" class="cust_test" placeholder="New test" />
                                <input type="text" name="custom_test[]" id="custom_test3" class="cust_test" placeholder="New test" />
                                <input type="text" name="custom_test[]" id="custom_test4" class="cust_test" placeholder="New test" />
                                <input type="text" name="custom_test[]" id="custom_test5" class="cust_test" placeholder="New test" />
                            </div>
                        </div>
                        <script>
                            $(document).ready(function() {
                                $("#fav_pharmacy").select2({
                                    tags: [{
                                            id: "1",
                                            text: "Nalam Pharmacy, Madras University",
                                            qt: 1
                                        },
                                        {
                                            id: "2",
                                            text: "Nalam Pharmacy, Hindi Prachar Sabha",
                                            qt: 2
                                        }
                                    ],
                                    formatResult: function(result) {
                                        if (result.qt === undefined) {
                                            return result.text;
                                        }
                                        return result.text + "<span class='used-number' onClick='aaaaa()'>" + result.qt + "</span>";
                                    }
                                });
                            });
                        </script>
                        <?php if ($tbs_role != "4") { ?>

                            <div class="content-table float-left" style="background: #eee;   border-bottom: 1px dashed #999;   border-top: 1px dashed #999;  margin:10px 0;">
                                <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2"> <label class="titclr" for="test" style="font-size:16px;font-weight:bold;"> Condition:</label></div>
                                <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2">
                                    <div class="conditionname " title="conditionname">
                                        <input type="hidden" id="conditionname" name="conditionname[]" class="mastrcondition conditionname" value="<?php echo $casemed_id; ?>" />
                                        <input type="text" id="hiddenconditionname" name="hiddenconditionname[]" placeholder="Condition" autocomplete="off" data-items="5" class="hiddenconditionname input-large" onKeyUp="return bringCondition(this,this.value,event)" value="<?php echo isset($casemed_condition) ? $casemed_condition : ""; ?>" />

                                        <ul class="typeahead-custom dropdown-menu dropdown-menu-custom" style="position:absolute;display:none;margin-top:27px;"></ul>
                                    </div>
                                    <div class="content-table float-left"> <input type="checkbox" name="showcond[]" value='1' /> Publish Condition </div>
                                </div>

                                <input type="hidden" name="testid" id="testid" class='input-large' value="<?php echo $test_id; ?>" />

                                <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2 catcond"> &nbsp </div>
                                <div class="col-xl-3 col-lg-3 col-md-4 float-left p-2"> <i class="glyphicon-circle_plus" onclick="addnewcond()"></i></div>
                            </div>
                        <?php
                        } ?>

                        <div class="col-xl-12 col-lg-12 col-md-12 p-2 float-left">
                            <div class="col-xl-6 col-lg-6 col-md-6 float-left">
                                <textarea class='dnotes input-large' rows="2" name="dnotes" id="dnotes" style="width:95%; height:70px;" placeholder="Notes to the patient"><?php if (isset($_GET["case"])) {
                                    echo @$casepatientnotes;
                                } else {
                                    echo @$doctornotes;
                                } ?></textarea>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 float-left">

                                <textarea class='mnotes input-large' rows="2" name="mnotes" id="mnotes" style="width:95%; height:70px;" placeholder="<?php if (isset($_GET["case"])) {
                                    echo "Doctor Notes";
                                } else {
                                    echo "Doctor Notes";
                                } ?>"><?php if (isset($_GET["case"])) {
                                    echo @$casedoctornotes;
                                } else {
                                    echo @$usernotes;
                                } ?></textarea>

                                <label style="padding-left:6px; float:left;">
                                    <input type="checkbox" id="isshowdoctor" name="isshowdoctor" class="margin0 isshowdoctor" <?php if (@$casesharewithpatients == "1") {
                                        echo " checked='checked' value='1' ";
                                    } else {
                                        echo 'value=""';
                                    } ?> />

                                    <span style="margin-left:5px;">Share with <?php if ($tbs_role == "1") {
                                        echo "Doctor";
                                    } else {
                                        echo "Patient";
                                    } ?></span></label>

                                <label style="padding-left:30px; float:left"><input type="checkbox" id="sendMailPresToPatient" name="sendMailPresToPatient" class="margin0 sendMailPresToPatient" value="1" checked="checked" /> <span> Send Mail To Patient</span></label>
                            </div>
                        </div>



                        <div class="form-actions margin5" style="float:left; width:100%;">

                            <div class="col-xl-6 col-lg-6 col-md-6 float-left">
                                <?php if (isset($_GET["popUp"])) { ?><input type="hidden" name="popUp" id="popUp" value="1" /><input type="hidden" name="countIndex" id="countIndex" value="<?php echo $_GET["countIndex"]; ?>" /><?php
                                } ?>
                                <input type="hidden" name="role_id" id="role_id" value="<?php echo $tbs_role; ?>" />
                                <input type="hidden" name="created_by" id="created_by" value="<?php echo $tbs_userid; ?>" />
                                <?php if ($tbs_role == "1") { ?>
                                    <input type="submit" class="btn btn-sm btn-primary m-1" value="Draft" title="Draft" onClick="draftrsave(this)" />
                                <?php
                                } ?>
                                <?php if ($tbs_role == "2") { ?>
                                    <input type="submit" class="btn btn-sm btn-primary m-1" value="Make Template & Save" title="TemplateSave" onClick="draftrsave(this)" />
                                    <input type="submit" class="btn btn-sm btn-primary m-1" value="Edit/Save Template" title="TemplateOnly" onClick="draftrsave(this)" />
                                <?php
                                } ?>

                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 float-left">
                                <input type="hidden" name="saveRdraft" id="saveRdraft" />
                                <?php if (!isset($_REQUEST["case"])) { ?>
                                    <input type="submit" class='btn btn-sm btn-primary m-1' value="Add Test" title="addtest" onClick="draftrsave(this)" />
                                <?php
                                } ?>
                                <input type="submit" class='btn btn-sm btn-primary m-1' id="btnSubmit" value="Save Prescription" title="Save" onClick="draftrsave(this)" />

                                <input type="submit" class="btn btn-sm btn-primary m-1" value="Make Template & Save" title="TemplateSave" onClick="draftrsave(this)" />
                            </div>

                        </div>
                    </div>
                </div>
                </div><!--close div for the druDescriptions-->


                <textarea id="testidsnew" rows="10" cols="20" style="display:none"><?php echo @$err; ?></textarea>

        </div>
</div>

</form>


</div>
<script type="text/javascript">
    function validatePrescription() {

        var frm = this.document.f1;

        var completeDate = frm.fromdate.value;
        var tdate = completeDate.substring(0, 2);
        var tmonth = completeDate.substring(3, 5);
        var tyear = completeDate.substring(6, 10);
        var fromdate = new Date(tyear, tmonth - 1, tdate);
        var todayDate = new Date();
        if (fromdate >= todayDate) {
            alert("Date sholud not be future date");
            return false;
        }


        if (frm.saveRdraft.value == "TemplateOnly") {

            if (frm.existtemplate.value == "") {
                if (confirm("Are you sure want to save template only?")) {
                    alert("Select New Template");
                    frm.existtemplate.focus();
                    return false;
                }
            }
            if (frm.existtemplate.value == "template" && frm.newtemplate.value == "") {

                alert("Required Template Name");

                frm.newtemplate.focus();

                return false;

            }

            var totalcount = $('#getrows').val();


            for (var itotcount = 0; itotcount <= totalcount; itotcount++) {
                var drugQty = $('tr#' + itotcount + " .mastrdrug").val();
                if (drugQty == "") {
                    alert("Drug Name Required");

                    $('tr#' + itotcount + " .hiddendrugname").focus();

                    return false;

                }

            }

        }

        // console.log("Value: " + frm.saveRdraft.value);
        if (frm.saveRdraft.value == "Save" || frm.saveRdraft.value == "TemplateSave" || frm.saveRdraft.value == "addtest") {
            if (frm.saveRdraft.value == "TemplateSave") {
                if (frm.existtemplate.value == "") {
                    alert("Select Template");
                    frm.existtemplate.focus();
                    return false;
                }
                if (frm.existtemplate.value == "template" && frm.newtemplate.value == "") {
                    alert("Required Template Name");
                    frm.newtemplate.focus();
                    return false;
                }
            }
            if (frm.docname.value == "" || frm.docname.value == "doctor") {

                alert("Select Doctor");

                frm.docname.focus();

                return false;

            }

            if (frm.usernamea) {
                var userValidation = frm.usernamea.value;
            } else {
                var userValidation = frm.username.value;
            }

            if (userValidation == "" || userValidation == "patient") {

                alert("Please select Patient");

                frm.username.focus();

                return false;

            }

            var totalcount = $('#getrows').val();

            for (var itotcount = 0; itotcount < totalcount; itotcount++) {
                // var earlyMrngQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .early_morning").length > 0) {
                //     var earlyMrngQty = $("#dataTable1").find('tr#' + itotcount + " .early_morning").val();
                // }
                // var mrngQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .morning").length > 0) {
                //     var mrngQty = $("#dataTable1").find('tr#' + itotcount + " .morning").val();
                // }
                // var lateMrngQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .late_morning").length > 0) {
                //     var lateMrngQty = $("#dataTable1").find('tr#' + itotcount + " .late_morning").val();
                // }
                // var aftQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .afternoon").length > 0) {
                //     var aftQty = $("#dataTable1").find('tr#' + itotcount + " .afternoon").val();
                // }
                // var lateAftQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .late_afternoon").length > 0) {
                //     var lateAftQty = $("#dataTable1").find('tr#' + itotcount + " .late_afternoon").val();
                // }
                // var eveQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .evening").length > 0) {
                //     var eveQty = $("#dataTable1").find('tr#' + itotcount + " .evening").val();
                // }
                // var nytQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .night").length > 0) {
                //     var nytQty = $("#dataTable1").find('tr#' + itotcount + " .night").val();
                // }
                // var lateNytQty = 0;
                // if ($("#dataTable1").find('tr#' + itotcount + " .late_night").length > 0) {
                //     var lateNytQty = $("#dataTable1").find('tr#' + itotcount + " .late_night").val();
                // }
                // var drugQty = $("#dataTable1").find('tr#' + itotcount + " .mastrdrug").val();
                // var DuraQty = $("#dataTable1").find('tr#' + itotcount + " .tblduration").val();

                var row = $(rows[i]);

                var earlyMrngQty = row.find(".early_morning").val() || 0;
                var mrngQty = row.find(".morning").val() || 0;
                var lateMrngQty = row.find(".late_morning").val() || 0;
                var aftQty = row.find(".afternoon").val() || 0;
                var lateAftQty = row.find(".late_afternoon").val() || 0;
                var eveQty = row.find(".evening").val() || 0;
                var nytQty = row.find(".night").val() || 0;
                var lateNytQty = row.find(".late_night").val() || 0;

                var drugQty = row.find(".mastrdrug").val();
                var DuraQty = row.find(".tblduration").val();
                /* if(drugQty==""){
                	alert("Drug Name Required");
                	$('tr#'+itotcount+" .hiddendrugname").focus();
                	return false;
                 }*/
                // 

                if (drugQty != "") {
                    if ((mrngQty == "" || mrngQty == 0) && (earlyMrngQty == "" || earlyMrngQty == 0) && (lateMrngQty == "" || lateMrngQty == 0) && (aftQty == "" || aftQty == 0) && (lateAftQty == "" || lateAftQty == 0) && (eveQty == "" || eveQty == 0) && (nytQty == "" || nytQty == 0) && (lateNytQty == "" || lateNytQty == 0)) {
                        alert("Atleast one Duration should be filled 1");

                        if (($("#dataTable1").find('tr#' + itotcount + " .early_morning").length > 0)) {
                            $("#dataTable1").find('tr#' + itotcount + " .early_morning").focus();
                        } else {
                            $("#dataTable1").find('tr#' + itotcount + " .morning").focus();
                        }
                        return false;
                    }
                    var tbldrugtype = $("#dataTable1").find('tr#' + itotcount + " .drugintakecondition option:selected").val();
                    if (tbldrugtype == "236" || tbldrugtype == "237") { // If SOS/Stat no need to alert morning,noon and nyt					
                    } else {
                        if (DuraQty == "" || DuraQty == 0) {
                            alert("Days Required");
                            $("#dataTable1").find('tr#' + itotcount + " .tblduration").focus();
                            return false;
                        }
                        if ((earlyMrngQty == "" || earlyMrngQty == 0) && (mrngQty == "" || mrngQty == 0) && (lateMrngQty == "" || lateMrngQty == 0) && (aftQty == "" || aftQty == 0) && (lateAftQty == "" || lateAftQty == 0) && (eveQty == "" || eveQty == 0) && (nytQty == "" || nytQty == 0) && (lateNytQty == "" || lateNytQty == 0)) {
                            alert("Qty Required");
                            if (($("#dataTable1").find('tr#' + itotcount + " .early_morning").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .early_morning").focus();
                            } else {
                                $("#dataTable1").find('tr#' + itotcount + " .morning").focus();
                            }
                            return false;
                        } else {
                            if ((earlyMrngQty == "" || earlyMrngQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .early_morning").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .early_morning").val(0);
                            }
                            if ((mrngQty == "" || mrngQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .morning").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .morning").val(0);
                            }
                            if ((lateMrngQty == "" || lateMrngQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .late_morning").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .late_morning").val(0);
                            }
                            if ((aftQty == "" || aftQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .afternoon").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .afternoon").val(0);
                            }
                            if ((lateAftQty == "" || lateAftQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .late_afternoon").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .late_afternoon").val(0);
                            }
                            if ((eveQty == "" || eveQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .evening").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .evening").val(0);
                            }
                            if ((nytQty == "" || nytQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .night").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .night").val(0);
                            }
                            if ((lateNytQty == "" || lateNytQty == 0) && ($("#dataTable1").find('tr#' + itotcount + " .late_night").length > 0)) {
                                $("#dataTable1").find('tr#' + itotcount + " .late_night").val(0);
                            }
                        }
                    }
                }
            }



            var totalcount2 = $('#getrows2').val();

            for (var itotcount = 0; itotcount < totalcount2; itotcount++) {
                var earlyMrngQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .early_morning").length > 0) {
                    var earlyMrngQty = $("#dataTable2").find('tr#' + itotcount + " .early_morning").val();
                }
                var mrngQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .morning").length > 0) {
                    var mrngQty = $("#dataTable2").find('tr#' + itotcount + " .morning").val();
                }
                var lateMrngQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .late_morning").length > 0) {
                    var lateMrngQty = $("#dataTable2").find('tr#' + itotcount + " .late_morning").val();
                }
                var aftQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .afternoon").length > 0) {
                    var aftQty = $("#dataTable2").find('tr#' + itotcount + " .afternoon").val();
                }
                var lateAftQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .late_afternoon").length > 0) {
                    var lateAftQty = $("#dataTable2").find('tr#' + itotcount + " .late_afternoon").val();
                }
                var eveQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .evening").length > 0) {
                    var eveQty = $("#dataTable2").find('tr#' + itotcount + " .evening").val();
                }
                var nytQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .night").length > 0) {
                    var nytQty = $("#dataTable2").find('tr#' + itotcount + " .night").val();
                }
                var lateNytQty = 0;
                if ($("#dataTable2").find('tr#' + itotcount + " .late_night").length > 0) {
                    var lateNytQty = $("#dataTable2").find('tr#' + itotcount + " .late_night").val();
                }
                var drugQty = $("#dataTable2").find('tr#' + itotcount + " .outside-drug-name").val();
                var DuraQty = $("#dataTable2").find('tr#' + itotcount + " .tblduration").val();
                /* if(drugQty==""){
                	alert("Drug Name Required");
                	$('tr#'+itotcount+" .hiddendrugname").focus();
                	return false;
                 }*/
                // 

                if (drugQty != "") {
                    if ((mrngQty == "" || mrngQty == 0) && (earlyMrngQty == "" || earlyMrngQty == 0) && (lateMrngQty == "" || lateMrngQty == 0) && (aftQty == "" || aftQty == 0) && (lateAftQty == "" || lateAftQty == 0) && (eveQty == "" || eveQty == 0) && (nytQty == "" || nytQty == 0) && (lateNytQty == "" || lateNytQty == 0)) {
                        alert("Atleast one Duration should be filled 2");

                        if (($("#dataTable2").find('tr#' + itotcount + " .early_morning").length > 0)) {
                            $("#dataTable2").find('tr#' + itotcount + " .early_morning").focus();
                        } else {
                            $("#dataTable2").find('tr#' + itotcount + " .morning").focus();
                        }
                        return false;
                    }
                    var tbldrugtype = $("#dataTable2").find('tr#' + itotcount + " .drugintakecondition option:selected").val();
                    if (tbldrugtype == "236" || tbldrugtype == "237") { // If SOS/Stat no need to alert morning,noon and nyt					
                    } else {
                        if (DuraQty == "" || DuraQty == 0) {
                            alert("Days Required");
                            $("#dataTable2").find('tr#' + itotcount + " .tblduration").focus();
                            return false;
                        }
                        if ((earlyMrngQty == "" || earlyMrngQty == 0) && (mrngQty == "" || mrngQty == 0) && (lateMrngQty == "" || lateMrngQty == 0) && (aftQty == "" || aftQty == 0) && (lateAftQty == "" || lateAftQty == 0) && (eveQty == "" || eveQty == 0) && (nytQty == "" || nytQty == 0) && (lateNytQty == "" || lateNytQty == 0)) {
                            alert("Qty Required");
                            if (($("#dataTable2").find('tr#' + itotcount + " .early_morning").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .early_morning").focus();
                            } else {
                                $("#dataTable2").find('tr#' + itotcount + " .morning").focus();
                            }
                            return false;
                        } else {
                            if ((earlyMrngQty == "" || earlyMrngQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .early_morning").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .early_morning").val(0);
                            }
                            if ((mrngQty == "" || mrngQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .morning").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .morning").val(0);
                            }
                            if ((lateMrngQty == "" || lateMrngQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .late_morning").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .late_morning").val(0);
                            }
                            if ((aftQty == "" || aftQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .afternoon").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .afternoon").val(0);
                            }
                            if ((lateAftQty == "" || lateAftQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .late_afternoon").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .late_afternoon").val(0);
                            }
                            if ((eveQty == "" || eveQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .evening").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .evening").val(0);
                            }
                            if ((nytQty == "" || nytQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .night").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .night").val(0);
                            }
                            if ((lateNytQty == "" || lateNytQty == 0) && ($("#dataTable2").find('tr#' + itotcount + " .late_night").length > 0)) {
                                $("#dataTable2").find('tr#' + itotcount + " .late_night").val(0);
                            }
                        }
                    }
                }
            }


        }
        frm.submit();
        return true;
    }
</script>
<?php
} else {
    echo '<div style="text-align:center;">No access rights !</div>';
}
?>
<script src="<?php echo $sitepath; ?>js/plugins/plupload/plupload.full.js"></script>
<script type="text/javascript">
    var uploader = new plupload.Uploader({
        runtimes: 'html5',
        max_file_size: '3mb',
        chunk_size: '2mb',
        unique_names: true,
        browse_button: 'browsePrescriptionAttachmentId',
        url: '<?php echo $sitepath; ?>ajax/ajax_prescriptionattachment.php'
    });
    uploader.init();
    uploader.bind('FilesAdded', function(up, files) {
        var html = '';
        plupload.each(files, function(file) {
            html += "<li id='" + file.id + "'>" + file.name + " (" + plupload.formatSize(file.size) + ") <b></b></li>";
        });
        document.getElementById('fileListPrescriptionAttachmentId').innerHTML += html;
    });
    uploader.bind('UploadProgress', function(up, file) {
        document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
    });

    uploader.bind('FileUploaded', function(up, file, object) {
        var myData;
        try {
            myData = object.response;
        } catch (err) {
            myData = object.response;
        }
        $('#fileListPrescriptionAttachmentId').next().append(myData);
    });
    uploader.bind('Error', function(up, err) {
        document.getElementById('consolePrescriptionAttachmentId').innerHTML += "\nError #: " + err.message;
    });
    document.getElementById('uploadPrescriptionAttachmentId').onclick = function() {
        uploader.start();
    };

    $(function() {
        $(document).click(function() {
            $('.typeahead-custom').hide(); //hide the button

        });

    });
</script>
<script>
    // function drugname_validation() {


    //     var drugname = $("select[name='hiddendrugname[]']")
    //         .map(function() {
    //             return $(this).val();
    //         }).get();
    //     //alert(drugname); 
    //     var action = "validate_drugname";
    //     var dataString = {
    //         drugname: drugname,
    //         action: action
    //     };
    //     $.ajax({
    //         type: "POST",
    //         url: "ajax/ajax_addohc-pres-temp.php",
    //         data: dataString,
    //         success: function(data) {
    //             data = $.trim(data);
    //             var datas = data.split("@@");
    //             var data1 = datas[0];
    //             var data2 = datas[1];
    //             //alert(data1);
    //             if (data2 == 'Drugname already exist') {
    //                 alert("You have already selected this drug. Please select a different drug.");
    //                 document.getElementById("dataTable1").deleteRow(data1);

    //             }


    //         }


    //     });
    // }

    function get_med_condition() {
        var userdata = $("#user_id").val();

        var action = "get_med_condition";
        //alert(userdata);
        $.ajax({
            url: 'ajax/ajax_medical_condition.php',
            type: "POST",
            data: ({
                userdata: userdata,
                action: action
            }),
            success: function(data) {
                $('#get_med_condition_div').html(data);
            }
        });
    }

    function get_allergic_ingredient_alert(data) {
        var case_id = "<?php echo $_GET["case"]; ?>";
        if (case_id != '') {
            var userdata = $("#username").val();
        } else {
            var userdata = $("#user_id").val();
        }
        var dataVal = $(data).val();
        var datas = dataVal.split("--");
        var location = "<?php echo $_SESSION["loc_id"]; ?>";
        $(data).parent().parent().find('.mastrdrug').val(datas[1]);
        var $rw = $(data).closest("tr");
        var dt = datas[0];


        var action = "get_allergic_ingredient_alert";

        $.ajax({
            url: 'ajax/ajax_medical_condition.php',
            type: "POST",
            data: ({
                userdata: userdata,
                action: action,
                drug_name: dt,
                location: location
            }),
            success: function(con) {

                if (con.trim() != '') {
                    alert("The patient is allergic to " + con + ", kindly prescribe a different drug.");
                    $(data).val('');
                    // $rw.find('.avalcount').html('--');
                }
            }
        });
    }
    $(document).ready(function() {
        var case_id = "<?php echo $_GET["case"]; ?>";
        if (case_id == '') {
            get_med_condition();
        }
        $("#user_id").change(function() {

            var userdata = $("#user_id").val();
            get_med_condition();
            var action = "get_age_gender";
            $.ajax({
                url: 'ajax/ajax_medical_condition.php',
                type: "POST",
                data: ({
                    userdata: userdata,
                    action: action
                }),
                success: function(data) {
                    $('#get_age_gender').html(data);
                }
            });

        });
    });
</script>