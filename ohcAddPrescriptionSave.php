<?php
include('session.php');

include_once('core/class.manageUsers.php');
include_once('core/class.manageMasters.php');
include_once('dateconvert.php');
$testdata = $_POST['testdata'];
$hddn = [];
foreach($_POST['hiddendrugname'] as $drug) {
    $parts = explode("--", $drug);
    // Remove empty elements
    $parts = array_filter($parts, function ($value) {
        return $value !== '';
    });
    // Re-index the array
    $parts = array_values($parts);
    // Combine elements into the desired format
    if (count($parts) >= 3) {
        $combined = "{$parts[0]} ({$parts[2]}) {$parts[1]}";
        $hddn[] = $combined;
    }
}

$_POST['hiddendrugname'] = $hddn;
// echo "<pre>";
// print_r($hddn);
// print_r($_POST);
// print_r($_POST['Outsidehiddendrugtype']);
// echo "</pre>";
// exit;

define("MAX_SIZE", "30000");
function getExtension1($str)
{
    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }
    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

$saveRdraft = $_POST['saveRdraft'];
if ($saveRdraft == "Draft" || $saveRdraft == "AgainDraftPresc" || $saveRdraft == "DraftPrescs") {
    $isdraft = "1";
} else {
    $isdraft = "0";
}

//$tbs_userid=$_SESSION['userid'];
$userid = $tbs_userid;
//current date
$datetime = date("Y-m-d H:i:s");

//
$err = "";
$errors = array();
$error = 0;

$id = $_POST['id'];
$id = addslashes($id);

$docname = $_POST['docname'];
$docname = addslashes($docname);

$presID = "";
$speclisatinname = "";
$count = 1;
if (isset($_POST['speclisatinname'])) {
    $countspecialization = sizeof($_POST['speclisatinname']);
    foreach ($_POST['speclisatinname'] as $usedforoptions) {
        $speclisatinname .= $usedforoptions;
        $total = $count++;
        if ($total <> $countspecialization) {
            $speclisatinname .= ", ";
        }
    }
}
$hosname = $_POST['hosname'];
$hosname = addslashes($hosname);

$username = $_POST['username'];
$username = addslashes($username);

try {
    $obj = new ManageUsers();
    $caseId = intval($_GET['case']);
    $query = "SELECT date(cf.cr_date) as created_case, concat(mud.first_name, ' ', mud.last_name) as fullname, cf.* 
              FROM ohc cf 
              LEFT OUTER JOIN master_user_details mud ON mud.id = cf.user_id 
              WHERE cf.id = $caseId";
    $UserDeta = $obj->listDirectQuery($query);
    $obj = null;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
$created_case = date("d/m/Y", strtotime($UserDeta[0]['created_case']));

$fromdate = $_POST['fromdate'];
$fromdate = convertDateFormat($fromdate, '/');

$dnotes = $_POST['dnotes'];
$dnotes = addslashes($dnotes);

$mnotes = $_POST['mnotes'];
$mnotes = addslashes($mnotes);

$drugname = $_POST['drugname'];
$outsideDrugname = $_POST['outsidedrugname'];

$duration = $_POST['duration'];
$outsideduration = $_POST['outsideduration'];
$concat = $_POST['concat'];

$early_morning = $_POST['early_morning'];
$morning = $_POST['morning'];
$Outsidemorning = $_POST['Outsidemorning'];
$late_morning = $_POST['late_morning'];
$afternoon = $_POST['afternoon'];
$late_afternoon = $_POST['late_afternoon'];
$evening = $_POST['evening'];
$night = $_POST['night'];
$late_night = $_POST['late_night'];
$drugid = $_POST['drugtype'];

$Outsideafternoon = $_POST['Outsideafternoon'];
$Outsideevening = $_POST['Outsideevening'];
$Outsidenight = $_POST['Outsidenight'];

$drugtype = $_POST['hiddendrugtype'];
$Outsidehiddendrugtype = $_POST['Outsidehiddendrugtype'];
$hiddendrugname = $_POST['hiddendrugname'];
$drugintakecondition = $_POST['drugintakecondition'];

$remarks = $_POST['remarks'];

$rowid = $_POST['rowid'];

$testid = $_POST['testid'];

$case_id = $_POST['case_id'];
$textcond = $_POST['textcond'];
$conditionname = $_POST['conditionname'];
$hiddenconditionname = $_POST['hiddenconditionname'];
//$hiddenconditionname = addslashes($hiddenconditionname);
$newtemplate = $_POST['newtemplate'];
$existtemplate = $_POST['existtemplate'];
//$showcond=(!empty($_POST['showcond']))?'1':'2';
$showcond = $_POST['showcond'];
$role_id = $tbs_role;
$created_by = $tbs_userid;
$testids = $_POST['testids'];
$ohcs = $_POST['ohcs'];
$corp_id = $_POST['corp_id'];

$conditionnameexist = new ManageUsers();
$conditionnameexists = $conditionnameexist->listDirectQuery("SELECT count(*) as counthidden from `med_condition_map` where med_condition='" . $hiddenconditionname . "'");
if ($conditionnameexists) {
    foreach ($conditionnameexists as $conditionnameexistsL) {
        $counthidden = $conditionnameexistsL['counthidden'];
    }
}

//!empty($hiddenconditionname) && $counthidden=="0"

if (!empty($conditionname)) {
    $CurrentAppointMnt = new ManageMasters();
    //$addInsertedID=$CurrentAppointMnt->addCondition($hiddenconditionname,$username,$tbs_role,$created_by);
    //$addInsertedID=$CurrentAppointMnt->addCondition($conditionname,$username,$tbs_role,$created_by,$concat,$showcond);

    $today = date('Y-m-d h:i:s');
    foreach ($conditionname as $key => $cndsname) {
        $conca = $concat[$key];

        $shcnt = (!empty($showcond[$key])) ? '1' : '2';

        $conditionnameexistss = $conditionnameexist->listDirectQuery("SELECT count(*) as counthidden from `med_condition_map` where med_condition='$cndsname' and user_id='$username' and (is_active='1' or is_active='2') ");

        if ($conditionnameexistss[0]['counthidden'] == "0") {

            $conditionnameexists = $CurrentAppointMnt->listDirectQuery("INSERT into med_condition_map(med_condition,user_id,is_active,created_role,created_id,created_on,cat_cond) VALUES('$cndsname','$username','$shcnt','$tbs_role','$created_by','$today','$conca')");
        }
    }
    //	$conditionname=$addInsertedID;
    $CurrentAppointMnt = null;
}


$fav_pharmacy = $_POST['fav_pharmacy'];
$fav_lab = $_POST['fav_lab'];
$favoritesCheckObj = new ManageUsers();
$favoritesCheck = $favoritesCheckObj->listDirectQuery("SELECT count(*) as countfavorites from `favorites` WHERE reference_id='" . $fav_pharmacy . "' AND user_id='" . $tbs_userid . "' AND role_id='" . $tbs_role . "' AND status=1");
$favoritesCheckObj = null;
if ($favoritesCheck[0]['countfavorites'] == 0 && $fav_pharmacy != "") {
    $favoritesAddCheckObj = new ManageUsers();
    $favoritesAdd = $favoritesAddCheckObj->AddDirectQuery("INSERT INTO `favorites`(reference_id,user_id,role_id,status,created_on,created_role,created_by)	VALUES('" . $fav_pharmacy . "','" . $tbs_userid . "','" . $tbs_role . "','1','" . $datetime . "','" . $tbs_role . "','" . $tbs_userid . "')");
    $favoritesAddCheckObj = null;
} elseif ($favoritesCheck[0]['countfavorites'] > 0 && $fav_pharmacy != "") {
    $favoritesUpdateCheckObj = new ManageUsers();
    $favoritesUpdate = $favoritesUpdateCheckObj->listDirectQuery("UPDATE `favorites` SET modified_on='" . $datetime . "',modified_role='" . $tbs_role . "',modified_by='" . $tbs_userid . "' WHERE reference_id='" . $fav_pharmacy . "' AND user_id='" . $tbs_userid . "' AND role_id='" . $tbs_role . "' AND status=1");
    $favoritesUpdateCheckObj = null;
}

$favoritesCheckObj = new ManageUsers();
$favoritesCheck = $favoritesCheckObj->listDirectQuery("SELECT count(*) as countfavorites from `favorites` WHERE reference_id='" . $fav_lab . "' AND user_id='" . $tbs_userid . "' AND role_id='" . $tbs_role . "' AND status=2");
$favoritesCheckObj = null;
if ($favoritesCheck[0]['countfavorites'] == 0 && $fav_lab != "") {
    $favoritesAddCheckObj = new ManageUsers();
    $fav_labAdd = $favoritesAddCheckObj->AddDirectQuery("INSERT INTO `favorites`(reference_id,user_id,role_id,status,created_on,created_role,created_by)	VALUES('" . $fav_lab . "','" . $tbs_userid . "','" . $tbs_role . "','2','" . $datetime . "','" . $tbs_role . "','" . $tbs_userid . "')");
    $favoritesAddCheckObj = null;
} elseif ($favoritesCheck[0]['countfavorites'] > 0 && $fav_lab != "") {
    $favoritesUpdateCheckObj = new ManageUsers();
    $favoritesUpdate = $favoritesUpdateCheckObj->listDirectQuery("UPDATE `favorites` SET modified_on='" . $datetime . "',modified_role='" . $tbs_role . "',modified_by='" . $tbs_userid . "' WHERE reference_id='" . $fav_lab . "' AND user_id='" . $tbs_userid . "' AND role_id='" . $tbs_role . "' AND status=2");
    $favoritesUpdateCheckObj = null;
}

if (isset($_POST['isconformo'])) {
    if ($saveRdraft == "DraftPresc" || $saveRdraft == "AgainDraftPresc" || $saveRdraft == "DraftPrescs") {
        $isconformo = 0;
    } else {
        $isconformo = 1;
    }
} else {
    $isconformo = 0;
}

if (isset($_POST['isshowdoctor'])) {
    $showdoctor = 1;
} else {
    $showdoctor = 0;
}

$sendMailPresToPatient = $_POST['sendMailPresToPatient'];

$status = 1;
$isprescribed = 1;
$presDesc = $_POST['presDesc'];
$prescriptionId = $_POST['prescriptionId'];
$dnotes = addslashes($_POST['dnotes']);
$mnotes = addslashes($_POST['mnotes']);

if ($saveRdraft == "TemplateOnly" || $saveRdraft == "TemplateSave") {
    if ($newtemplate == "") {
        $templateid = $existtemplate;
        $SearchTemplates = new ManageUsers();

        $InsertTemplated = $SearchTemplates->listDirectQuery("SELECT * from template_detail where template_id=" . $templateid);
        $SearchTemplates = null;
        if ($InsertTemplated != 0) {
            $tempdrugs = array();
            foreach ($InsertTemplated as $InsertTemplatedLis) {
                $tempdrug = $InsertTemplatedLis['drugs_name'];
                array_push($tempdrugs, $InsertTemplatedLis['drugs_name']);
            }
            $q = 0;
            foreach ($drugname as $drugvalue) {
                $remarks[$q] = addslashes($remarks[$q]);
                if ($drugvalue != "") {
                    if (!in_array($drugvalue, $tempdrugs)) {
                        $InsertTemplates1 = new ManageUsers();

                        $drugsname_temp = $InsertTemplates1->listDirectQuery("select drug_name from pharmacy_stock_detail where id=" . $drugname[$q]);
                        $dtemp = $drugsname_temp[0]['drug_name'];

                        $InsertTemplate = $InsertTemplates1->listDirectQuery("INSERT into template_detail(template_id,drugs_name,prescribed_for_days,drugearlymorning,drugmorning,druglatemorning,drugafternoon,druglateafternoon,drugevening,drugnight,druglatenight,drugtype,drugintakecondition,remarks) values('$templateid',   '$dtemp','$duration[$q]','$early_morning[$q]','$morning[$q]','$late_morning[$q]','$afternoon[$q]','$late_afternoon[$q]','$evening[$q]', '$night[$q]','$late_night[$q]','$drugtype[$q]','$drugintakecondition[$q]','$remarks[$q]')");
                        $InsertTemplates1 = null;
                    } else {
                        $InsertTemplates1 = new ManageUsers();

                        $dtemp = $drugname[$q];
                        // echo "delete DELETE FROM template_detail WHERE condition;

                        $InsertTemplate = $InsertTemplates1->listDirectQuery("UPDATE template_detail SET prescribed_for_days='$duration[$q]',drugearlymorning='$early_morning[$q]',drugmorning='$morning[$q]',druglatemorning='$late_morning[$q]',drugafternoon='$afternoon[$q]',druglateafternoon='$late_afternoon[$q]',drugevening='$evening[$q]',drugnight='$night[$q]',druglatenight='$late_night[$q]',drugtype='$drugtype[$q]',drugintakecondition='$drugintakecondition[$q]',remarks='$remarks[$q]' WHERE drugs_name='$dtemp' AND template_id='$templateid'");
                        $InsertTemplates1 = null;
                    }
                }
                $q = $q + 1;
            }


            foreach ($tempdrugs as $tempdrugsvalue) {
                if (!in_array($tempdrugsvalue, $drugname)) {
                    $InsertTemplates1 = new ManageUsers();
                    $InsertTemplate = $InsertTemplates1->listDirectQuery("DELETE FROM template_detail where template_id=" . $templateid . " and drug_id=" . $tempdrugsvalue);
                    $InsertTemplates1 = null;
                }
            }
        }
    } else {
        $InsertTemplates = new ManageUsers();

        $InsertTemplated = $InsertTemplates->AddDirectQuery("INSERT into template(user_id,template_name,created_on,ohc) values('$docname', '$newtemplate', '$datetime','$ohcs')");
        $q = 0;
        foreach ($drugname as $drugvalue) {
            $remarks[$q] = addslashes($remarks[$q]);
            $InsertTemplates1 = new ManageUsers();

            $drugsname_temp = $InsertTemplates1->listDirectQuery("select drug_name from pharmacy_stock_detail where id =" . $drugname[$q]);
            $dtemp = $drugsname_temp[0]['drug_name'];

            $InsertTemplate = $InsertTemplates1->listDirectQuery("INSERT into template_detail(template_id,drugs_name,prescribed_for_days,drugearlymorning,drugmorning,druglatemorning,drugafternoon,druglateafternoon,drugevening,drugnight,druglatenight,drugtype,drugintakecondition,remarks) values('$InsertTemplated', '$dtemp', '$duration[$q]','$early_morning[$q]','$morning[$q]','$late_morning[$q]','$afternoon[$q]','$late_afternoon[$q]','$evening[$q]', '$night[$q]','$late_night[$q]','$drugtype[$q]','$drugintakecondition[$q]','$remarks[$q]')");
            $InsertTemplates1 = null;
            $q = $q + 1;
            $InsertTemplates1 = "";
        }
        $templateid = $InsertTemplated;
    }
    $selectTemplatesNotesObj = new ManageUsers();
    $selectTemplatesNotes = $selectTemplatesNotesObj->listDirectQuery("SELECT count(*) as counts FROM template_detail_notes WHERE template_id='$templateid'");
    $selectTemplatesNotesObj = null;
    if ($selectTemplatesNotes[0]["counts"] > 0) {

        $updateTemplatesNotesObj = new ManageUsers();
        $updateTemplatesNotes = $updateTemplatesNotesObj->listDirectQuery("UPDATE template_detail_notes SET patientnotes='$dnotes',doctornotes='$mnotes' WHERE template_id='$templateid'");
        $updateTemplatesNotesObj = null;
    } else {

        $InsertTemplatesNotesObj = new ManageUsers();
        $InsertTemplatesNotes = $InsertTemplatesNotesObj->listDirectQuery("INSERT into template_detail_notes(template_id,patientnotes,doctornotes) values('$templateid', '$dnotes', '$mnotes')");
        $InsertTemplatesNotesObj = null;
    }
}


if ($saveRdraft != "TemplateOnly" && $username != "") {
    if ($saveRdraft == "DraftPresc" || $saveRdraft == "DraftPrescs" || $saveRdraft == "CopyPresc" || $saveRdraft == "AgainDraftPresc" || $saveRdraft == "AgainDraftSavePresc") {
        $UpdatePrescription = new ManageUsers();
        $UpdatePresID = $UpdatePrescription->listDirectQuery("UPDATE prescription SET doctor_id='$docname',master_specialization_id='$speclisatinname',master_hcsp_user_id='$hosname',user_id='$username',doctornotes='$dnotes',usernotes='$mnotes',sharewithdoctor='$showdoctor',isdraft='$isdraft',modifiedon='$datetime',is_conformance='$isconformo',from_date='$fromdate',conditionname='$conditionname',fav_pharmacy='$fav_pharmacy',fav_lab='$fav_lab',condition_cat='$concat',corp_id='$corp_id',ohc='$ohcs' where id='$prescriptionId'");
        $q = 0;
        if (!empty($drugname)) {

            foreach ($drugname as $val) {

                $Prescription = new ManageUsers();

                $remarks[$q] = addslashes($remarks[$q]);

                if ($presDesc[$q] == "") {

                    $prescription_detailid = $Prescription->addohPrescriptionDetails($prescriptionId, $drugname[$q], $duration[$q], $early_morning[$q], $morning[$q], $late_morning[$q], $afternoon[$q], $late_afternoon[$q], $evening[$q], $night[$q], $late_night[$q], $drugtype[$q], $drugintakecondition[$q], $remarks[$q]);
                    echo $prescription_detailid;
                } else {
                    $tablename = "prescription_detail";
                    $PrescriptionDe = new ManageUsers();
                    $PrescriptionDeta = $PrescriptionDe->listDirectQuery("UPDATE prescription_detail SET  prescription_id='$prescriptionId',drugs_name='$drugname[$q]',prescribed_for_days='$duration[$q]',drugearlymorning='$early_morning[$q]',drugmorning='$morning[$q]',druglatemorning='$late_morning[$q]',drugafternoon='$afternoon[$q]',druglateafternoon='$late_afternoon[$q]',drugevening='$evening[$q]',drugnight='$night[$q]',druglatenight='$late_night[$q]',drugtype='$drugtype[$q]',drugintakecondition='$drugintakecondition[$q]',remarks='$remarks[$q]' where id='$presDesc[$q]'");
                    $prescription_detailid = $presDesc[$q];
                    $PrescriptionDe = null;
                }
                if ($isconformo == 1 && $saveRdraft == "AgainDraftSavePresc") {
                    for ($i = 1; $i <= $duration[$q]; $i++) {
                        $dates = new ManageMasters();
                        $j = $i - 1;
                        $nextdate = $dates->nextdate($fromdate, $j);
                        foreach ($nextdate as $newnextdate) {
                            $Prescriptionsv = new ManageUsers();
                            $zerovalue = '';
                            $datetime = date("Y-m-d H:i:s");
                            if ($drugtype[$q] == "177") {
                            } else {
                                $remarks[$q] = addslashes($remarks[$q]);
                                $prescriptionDetailId = $prescription_detailid;
                                $confirmance = $Prescriptionsv->addConfirmanceDetail($prescriptionDetailId, $prescriptionId, $drugname[$q], $zerovalue, $zerovalue, $zerovalue, $drugtype[$q], $drugintakecondition[$q], $remarks[$q], $newnextdate['CheckDate'], $datetime, $tbs_userid, $tbs_role);
                            }
                        }
                    }
                }
                $q = $q + 1;
            }
        }


        if (sizeof($testids) > 0) {
            //Array seperation (Important Notice:*Dont Delete Comma)
            $testIdDepartments = "," . (implode(",", $testids));
            $testFilter = preg_replace("/(,s1g[0-9]+)|(,s2g[0-9]+)/", '', $testIdDepartments);
            $testFilter = substr($testFilter, 1, strlen($testFilter));
            $testLists = explode(",", $testFilter);

            $subGroupFilter = preg_replace("/(,[0-9]+)|(,s2g[0-9]+)/", '', $testIdDepartments);
            $subGroupFilter = substr($subGroupFilter, 1, strlen($subGroupFilter));
            $subGroupFilter = str_replace("s1g", "", $subGroupFilter);
            $subGroupLists = explode(",", $subGroupFilter);

            $subSubGroupFilter = preg_replace("/(,[0-9]+)|(,s1g[0-9]+)/", '', $testIdDepartments);
            $subSubGroupFilter = substr($subSubGroupFilter, 1, strlen($subSubGroupFilter));
            $subSubGroupFilter = str_replace("s2g", "", $subSubGroupFilter);
            $subSubGroupLists = explode(",", $subSubGroupFilter);


            $PrescriptionTest = new ManageUsers();
            $sql = "DELETE FROM `prescribed_tests` WHERE prescription_id='$prescriptionId' AND ((ifnull(subgroup,0)=0 AND ifnull(subsubgroup,0)=0 AND test_id NOT IN (" . ($testFilter == '' ? 0 : $testFilter) . ")) OR (ifnull(subgroup,0)>0 AND ifnull(subsubgroup,0)=0 AND subgroup NOT IN (" . ($subGroupFilter == '' ? 0 : $subGroupFilter) . ")) OR (ifnull(subgroup,0)=0 AND ifnull(subsubgroup,0)>0 AND subsubgroup NOT IN (" . ($subSubGroupFilter == '' ? 0 : $subSubGroupFilter) . ")))";
            $deleteTestIdQuery = $PrescriptionTest->listDirectQuery($sql);
            $PrescriptionTest = null;

            $PrescriptionTestsMax = new ManageUsers();
            $MaxTestCodes = $PrescriptionTestsMax->listDirectQuery("SELECT ifnull(max(test_code),0) as maxid FROM `prescribed_tests`");
            $PrescriptionTestsMax = null;
            if ($MaxTestCodes != 0) {
                foreach ($MaxTestCodes as $MaxTestCodesL) {
                    $maxid = $MaxTestCodesL['maxid'] + 1;
                }
            }

            //Add Tests
            if (count($testLists) > 0) {

                $prescriptionTestObj = new ManageUsers();
                $addlabreq = $prescriptionTestObj->AddDirectQuery("INSERT INTO lab_request_status (`r_lab_id`,`r_test_code`, `user_id`, `hp_id`, `generate_test_request_id`, `visit_status`, `status`, `status_date`, `created_on`, `created_role`, `created_by`) values ('$fav_lab','$maxid','$username','$subgroup','$generateTestRequestId','1','1',now(),'$datetime','$tbs_role','$tbs_userid')");
                $prescriptionTestObj = null;
                foreach ($testLists as $testList) {
                    $PrescriptionTestObj = new ManageUsers();
                    $insertExceptOldDatas = $PrescriptionTestObj->listDirectQuery("SELECT COUNT(id) as count FROM `prescribed_tests` WHERE prescription_id='$prescriptionId' and test_id='" . $testList . "'");
                    $PrescriptionTestObj = null;
                    if ($insertExceptOldDatas[0]['count'] == 0 && $testList <> "") {
                        $prescriptionTestObj = new ManageUsers();
                        $addPrescriptionTestObj = $prescriptionTestObj->AddDirectQuery("INSERT INTO `prescribed_tests`(prescription_id,test_code,test_date,test_id,subgroup,subsubgroup,user_id,doctor_id,fav_lab,lab_id,created_on,created_by,created_role,test_type,textcond) VALUES('$prescriptionId','$maxid','$fromdate','$testList','0','0','$username','$docname','$fav_lab','$fav_lab','$datetime','$tbs_userid','$tbs_role','0','$textcond')");
                        $prescriptionTestObj = null;
                    }
                }
            }

            //Add Sub Group
            if (count($subGroupLists) > 0) {

                foreach ($subGroupLists as $subGroupList) {
                    $PrescriptionTestObj = new ManageUsers();
                    $insertExceptOldDatas = $PrescriptionTestObj->listDirectQuery("SELECT COUNT(id) as count FROM `prescribed_tests` WHERE prescription_id='$prescriptionId' and subgroup='" . $subGroupList . "'");
                    $PrescriptionTestObj = null;
                    if ($insertExceptOldDatas[0]['count'] == 0) {
                        $prescriptionTestObj = new ManageUsers();
                        $addPrescriptionTestObj = $prescriptionTestObj->AddDirectQuery("INSERT INTO `prescribed_tests`(prescription_id,test_code,test_date,test_id,subgroup,subsubgroup,user_id,doctor_id,fav_lab,lab_id,created_on,created_by,created_role,test_type)
						SELECT '" . $prescriptionId . "','" . $maxid . "','" . $fromdate . "',mt.id,mt.subgroup,ifnull(mt.subsubgroup,0),'" . $username . "','" . $docname . "','" . $fav_lab . "','" . $fav_lab . "','" . $datetime . "','" . $tbs_userid . "','" . $tbs_role . "','0'
						FROM `master_test` mt
						WHERE mt.subgroup='" . $subGroupList . "'");
                        $prescriptionTestObj = null;
                    }
                }
            }

            //Add Sub Sub Group
            if (count($subGroupLists) > 0) {

                foreach ($subSubGroupLists as $subSubGroupList) {
                    $PrescriptionTestObj = new ManageUsers();
                    $insertExceptOldDatas = $PrescriptionTestObj->listDirectQuery("SELECT COUNT(id) as count FROM `prescribed_tests` WHERE prescription_id='$prescriptionId' and subsubgroup='" . $subSubGroupList . "'");
                    $PrescriptionTestObj = null;
                    if ($insertExceptOldDatas[0]['count'] == 0) {
                        $prescriptionTestObj = new ManageUsers();
                        $addPrescriptionTestObj = $prescriptionTestObj->AddDirectQuery("INSERT INTO `prescribed_tests`(prescription_id,test_code,test_date,test_id,subgroup,subsubgroup,user_id,doctor_id,fav_lab,lab_id,created_on,created_by,created_role,test_type)
						SELECT '" . $prescriptionId . "','" . $maxid . "','" . $fromdate . "',mt.id,0,mt.subsubgroup,'" . $username . "','" . $docname . "','" . $fav_lab . "','" . $fav_lab . "','" . $datetime . "','" . $tbs_userid . "','" . $tbs_role . "','0'
						FROM `master_test` mt
						WHERE mt.subsubgroup='" . $subSubGroupList . "'");
                        $prescriptionTestObj = null;
                    }
                }
            }
        }
    } else {

        $q = 0;
        $PrescriptionTests = new ManageUsers();
        $Prescription = new ManageUsers();
        $created_on = date("Y-m-d H:i:s");
        for ($n = 0; $q < count($hiddendrugname); $n++) {
            if (!empty($hiddendrugname[$n])) {
                $SqlPresID = $Prescription->addPrescriptionMain($username, $docname, $fromdate, $speclisatinname, $hosname, $dnotes, $mnotes, $isconformo, $showdoctor, $status, $isprescribed, $case_id, $isdraft, $conditionname, $templateid, $role_id, $created_on, $created_by, $tbs_role, $concat, $ohcs, $corp_id);

                $presID = $SqlPresID;
                $fromdateCombined = explode("-", $fromdate);
                $fromdateCombined = $fromdateCombined[2] . $fromdateCombined[1] . $fromdateCombined[0];


                $prescriptionIdCombined = $tbs_login . "/" . $fromdateCombined . "/000" . $presID;
                $dateTime = date("Y-m-d H:i:s");
                $addPrescriptionIdObj = new ManageUsers();
                $addPrescriptionId = $addPrescriptionIdObj->listDirectQuery("UPDATE prescription SET prescription_id='$prescriptionIdCombined',fav_pharmacy='$fav_pharmacy',fav_lab='$fav_lab' where id='$presID'");
                $addPrescriptionIdObj = null;
                break;
            }
        }

        if (sizeof($testids) > 0) {
            //Array seperation (Important Notice:*Dont Delete Comma)
            $testIdDepartments = "," . (implode(",", $testids));
            $testFilter = preg_replace("/(,s1g[0-9]+)|(,s2g[0-9]+)/", '', $testIdDepartments);
            $testFilter = substr($testFilter, 1, strlen($testFilter));
            $testLists = explode(",", $testFilter);

            $subGroupFilter = preg_replace("/(,[0-9]+)|(,s2g[0-9]+)/", '', $testIdDepartments);
            $subGroupFilter = substr($subGroupFilter, 1, strlen($subGroupFilter));
            $subGroupFilter = str_replace("s1g", "", $subGroupFilter);
            $subGroupLists = explode(",", $subGroupFilter);

            $subSubGroupFilter = preg_replace("/(,[0-9]+)|(,s1g[0-9]+)/", '', $testIdDepartments);
            $subSubGroupFilter = substr($subSubGroupFilter, 1, strlen($subSubGroupFilter));
            $subSubGroupFilter = str_replace("s2g", "", $subSubGroupFilter);
            $subSubGroupLists = explode(",", $subSubGroupFilter);


            $PrescriptionTest = new ManageUsers();
            $sql = "DELETE FROM `prescribed_tests` WHERE prescription_id='$SqlPresID' AND ((ifnull(subgroup,0)=0 AND ifnull(subsubgroup,0)=0 AND test_id NOT IN (" . ($testFilter == '' ? 0 : $testFilter) . ")) OR (ifnull(subgroup,0)>0 AND ifnull(subsubgroup,0)=0 AND subgroup NOT IN (" . ($subGroupFilter == '' ? 0 : $subGroupFilter) . ")) OR (ifnull(subgroup,0)=0 AND ifnull(subsubgroup,0)>0 AND subsubgroup NOT IN (" . ($subSubGroupFilter == '' ? 0 : $subSubGroupFilter) . ")))";
            $deleteTestIdQuery = $PrescriptionTest->listDirectQuery($sql);
            $PrescriptionTest = null;

            $PrescriptionTestsMax = new ManageUsers();
            $MaxTestCodes = $PrescriptionTestsMax->listDirectQuery("SELECT ifnull(max(test_code),0) as maxid FROM `prescribed_tests`");
            $PrescriptionTestsMax = null;
            if ($MaxTestCodes != 0) {
                foreach ($MaxTestCodes as $MaxTestCodesL) {
                    $maxid = $MaxTestCodesL['maxid'] + 1;
                }
            }

            //Add Tests
            if (count($testLists) > 0) {
                $prescriptionTestObj = new ManageUsers();
                $addlabreq = $prescriptionTestObj->AddDirectQuery("INSERT INTO lab_request_status (`r_lab_id`,`r_test_code`, `user_id`, `hp_id`, `generate_test_request_id`, `visit_status`, `status`, `status_date`, `created_on`, `created_role`, `created_by`) values ('$fav_lab','$maxid','$username','$subgroup','$generateTestRequestId','1','1',now(),'$datetime','$tbs_role','$tbs_userid')");
                $prescriptionTestObj = null;
                foreach ($testLists as $testList) {
                    $PrescriptionTestObj = new ManageUsers();
                    $insertExceptOldDatas = $PrescriptionTestObj->listDirectQuery("SELECT COUNT(id) as count FROM `prescribed_tests` WHERE prescription_id='$SqlPresID' and test_id='" . $testList . "'");
                    $PrescriptionTestObj = null;
                    if ($insertExceptOldDatas[0]['count'] == 0 && $testList <> "") {
                        $prescriptionTestObj = new ManageUsers();
                        $addPrescriptionTestObj = $prescriptionTestObj->AddDirectQuery("INSERT INTO `prescribed_tests`(prescription_id,test_code,test_date,test_id,subgroup,subsubgroup,user_id,doctor_id,fav_lab,lab_id,created_on,created_by,created_role,test_type,textcond) VALUES('$SqlPresID','$maxid','$fromdate','$testList','0','0','$username','$docname','$fav_lab','$fav_lab','$datetime','$tbs_userid','$tbs_role','0','$textcond')");
                        $prescriptionTestObj = null;
                    }
                }
            }

            //Add Sub Group
            if (count($subGroupLists) > 0) {
                foreach ($subGroupLists as $subGroupList) {
                    $PrescriptionTestObj = new ManageUsers();
                    $insertExceptOldDatas = $PrescriptionTestObj->listDirectQuery("SELECT COUNT(id) as count FROM `prescribed_tests` WHERE prescription_id='$SqlPresID' and subgroup='" . $subGroupList . "'");
                    $PrescriptionTestObj = null;
                    if ($insertExceptOldDatas[0]['count'] == 0) {
                        $prescriptionTestObj = new ManageUsers();
                        $addPrescriptionTestObj = $prescriptionTestObj->AddDirectQuery("INSERT INTO `prescribed_tests`(prescription_id,test_code,test_date,test_id,subgroup,subsubgroup,user_id,doctor_id,fav_lab,lab_id,created_on,created_by,created_role,test_type)
						SELECT '" . $SqlPresID . "','" . $maxid . "','" . $fromdate . "',mt.id,mt.subgroup,ifnull(mt.subsubgroup,0),'" . $username . "','" . $docname . "','" . $fav_lab . "','" . $fav_lab . "','" . $datetime . "','" . $tbs_userid . "','" . $tbs_role . "','0'
						FROM `master_test` mt
						WHERE mt.subgroup='" . $subGroupList . "'");
                        $prescriptionTestObj = null;
                    }
                }
            }

            //Add Sub Sub Group
            if (count($subGroupLists) > 0) {
                foreach ($subSubGroupLists as $subSubGroupList) {
                    $PrescriptionTestObj = new ManageUsers();
                    $insertExceptOldDatas = $PrescriptionTestObj->listDirectQuery("SELECT COUNT(id) as count FROM `prescribed_tests` WHERE prescription_id='$SqlPresID' and subsubgroup='" . $subSubGroupList . "'");
                    $PrescriptionTestObj = null;
                    if ($insertExceptOldDatas[0]['count'] == 0) {
                        $prescriptionTestObj = new ManageUsers();
                        $addPrescriptionTestObj = $prescriptionTestObj->AddDirectQuery("INSERT INTO `prescribed_tests`(prescription_id,test_code,test_date,test_id,subgroup,subsubgroup,user_id,doctor_id,fav_lab,lab_id,created_on,created_by,created_role,test_type)
						SELECT '" . $SqlPresID . "','" . $maxid . "','" . $fromdate . "',mt.id,0,mt.subsubgroup,'" . $username . "','" . $docname . "','" . $fav_lab . "','" . $fav_lab . "','" . $datetime . "','" . $tbs_userid . "','" . $tbs_role . "','0'
						FROM `master_test` mt
						WHERE mt.subsubgroup='" . $subSubGroupList . "'");
                        $prescriptionTestObj = null;
                    }
                }
            }
        }


        // Upload Image - Attachment
        $uploadFileIds = $_POST['uploadFileId'];
        $x = 0;
        foreach ($uploadFileIds as $uploadFileId) {
            $x = $x + 1;
            if ($uploadFileId <> "") {
                $uploadFileName = explode("___", $uploadFileId);
                $image_name = $SqlPresID . '00' . $x . $uploadFileName[1];
                $copied = copy($uploadFileId, 'img/prescription_attachment/' . $image_name);
                if ($copied) {
                    unlink($uploadFileId);
                    $attachedpresc = new ManageUsers();
                    $attachedprescs = $attachedpresc->listDirectQuery("INSERT INTO attachment_prescription(prescription_id,image_name,doctype) values('$SqlPresID', '$image_name', '1')");
                    $attachedpresc = "";
                }
            }
        }

        $tablename = "prescription";

        $Prescriptions = new ManageUsers();
        foreach ($drugname as $val) {
            $remarks[$q] = addslashes($remarks[$q]);
            if (!empty($hiddendrugname[$q])) {

                $rowval = $rowid[$q];
                if ($rowval == 0) {

                    if ($val <> "") {

                        $drugnames = $Prescriptions->listDirectQuery("SELECT `drug_name` FROM  `pharmacy_stock_detail` WHERE  `id`='" . $drugname[$q] . "'  or `drug_name`='" . $drugname[$q] . "'");
                        $drugnamevalue = $drugnames[0]['drug_name'];
                        $drugtypevalue = $drugtype[$q];
                        $type = 1;
                    } else {
                        $m = 0;
                        $drugnamevalue = $hiddendrugname[$q];
                        //echo "SELECT `doctype` FROM  `doctype_static` WHERE  `id`='".$drugid[$m]."'";
                        $drugtypes = $Prescriptions->listDirectQuery("SELECT `doctype` FROM  `doctype_static` WHERE  `id`='" . $drugid[$m] . "'  ");
                        //echo $drugtypes[0]['doctype'];
                        $drugtypevalue = $drugtypes[0]['doctype'];
                        $type = 1;
                        $m++;
                    }

                    $prescriptionDetailId = $Prescriptions->addohPrescriptionDetails($SqlPresID, $drugnamevalue, $duration[$q], $early_morning[$q], $morning[$q], $late_morning[$q], $afternoon[$q], $late_afternoon[$q], $evening[$q], $night[$q], $late_night[$q], $_POST['hiddendrugtype'][$q], $drugintakecondition[$q], $remarks[$q], $type, 88);
                    unset($drugnamevalue);
                    unset($drugtypevalue);


                    //Notification
                    if ($isconformo == 1 && $saveRdraft != "Draft") {
                        for ($i = 1; $i <= $duration[$q]; $i++) {
                            $dates = new ManageMasters();
                            $j = $i - 1;
                            $nextdate = $dates->nextdate($fromdate, $j);
                            foreach ($nextdate as $newnextdate) {
                                $Prescription = new ManageUsers();
                                $zerovalue = '';
                                $datetime = date("Y-m-d H:i:s");
                                $confirmance = $Prescription->addConfirmanceDetail($prescriptionDetailId, $SqlPresID, $drugname[$q], $zerovalue, $zerovalue, $zerovalue, $drugtype[$q], $drugintakecondition[$q], $remarks[$q], $newnextdate['CheckDate'], $datetime, $tbs_userid, $tbs_role);
                            }
                        }
                    }
                } else {
                    $tablename = "prescription_detail";
                    $param = array('drugs_name' => $drugname[$q], 'prescribed_for_days' => $duration[$q], 'drugearlymorning' => $early_morning[$q], 'drugmorning' => $morning[$q], 'druglatemorning' => $late_morning[$q], 'drugafternoon' => $afternoon[$q], 'druglateafternoon' => $late_afternoon[$q], 'drugevening' => $evening[$q], 'drugnight' => $night[$q], 'druglatenight' => $late_night[$q], 'drugtype' => $drugtype[$q], 'drugintakecondition' => $drugintakecondition[$q], 'remarks' => $remarks[$q]);
                    $resultPrescription = $Prescription->editDetails($tablename, $rowval, $param);
                }
            }
            $q = $q + 1;
        }

        $sql = "SELECT id, doctype FROM doctype_static WHERE 1=1 AND doctypename_static_id='3'";
        $result = new ManageUsers();
        $result = $result->listDirectQuery($sql);
        $mapping = array();
        foreach ($result as $row) {
            $mapping[$row['id']] = $row['doctype'];
        }
       
        if ($_POST['outsidedrugname'][0] != null){
            echo "<pre>";
            print_r($_POST['Outsidehiddendrugtype']);
            echo "</pre>";
            echo "Now null, drug present";
            $replacedValues = array();
            foreach ($_POST['Outsidehiddendrugtype'] as $value) {
                if (isset($mapping[$value])) {
                    $replacedValues[] = $mapping[$value];
                } else {
                    $replacedValues[] = $value;
                }
            }
            echo "<pre>";
            print_r($replacedValues);
            echo "</pre>";
            $_POST['Outsidehiddendrugtype'] = $replacedValues;
            $q = 0;
            foreach ($outsideDrugname as $val) {
                $prescriptionDetailId = $Prescriptions->addohPrescriptionDetails($SqlPresID, $val, $outsideduration[$q], $early_morning[$q], $Outsidemorning[$q], $late_morning[$q], $Outsideafternoon[$q], $late_afternoon[$q], $Outsideevening[$q], $Outsidenight[$q], $late_night[$q], $_POST['Outsidehiddendrugtype'][$q], $_POST['Outsidedrugintakecondition'][$q], $remarks[$q], 2);
                $q = $q + 1;
            }
        } 
    }
    //Send Mail the Prescription
    if ($sendMailPresToPatient == "1" && $isdraft == "0") {
        include_once('sendmail/conform_registration.php');

        //User Detail
        $activateTheDoctorSel = new ManageUsers();
        $activateTheDoctorDetail = $activateTheDoctorSel->getUserBasicDetail($username, "1");
        $activateTheDoctorSel = null;
        $toUserPrintMail = $activateTheDoctorDetail[0]["email"];
        $nameUserPrintMail = $activateTheDoctorDetail[0]["first_name"] . " " . $activateTheDoctorDetail[0]["last_name"];


        //Doctor Detail
        $activateTheDoctorSel = new ManageUsers();
        $activateTheDoctorDetail = $activateTheDoctorSel->listDirectQuery("select * from master_corporate_user where id=" . $_SESSION['userid']);
        $activateTheDoctorSel = null;
        $toDoctorPrintMail = $activateTheDoctorDetail[0]["email"];
        $nameDoctorPrintMail = $activateTheDoctorDetail[0]["first_name"] . " " . $activateTheDoctorDetail[0]["last_name"];

        //Hosp Detail
        $activateTheHospSel = new ManageUsers();

        $nameHospPrintMail = $activateTheHospSel->listDirectQuery("select * from master_corporate where id=" . $_SESSION['ohc_loca']);

        $mailcontent = '<table width="650" cellspacing="0" cellpadding="0" border="0" align="center" style="border:solid 1px #dbdbd9;font-family:georgia;">
					<tbody>
					<tr>
						<td height="35" bgcolor="#199bbf" style="padding:5px;">
							<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
								<tbody>
									<tr><td style="color:#FFFFFF; font-size:15px;">Dear ' . $nameUserPrintMail . ',<br/>Dr. ' . $nameDoctorPrintMail . ' has prescribed the below medicine(s) as per prescription ID:' . $prescriptionIdCombined . '</td></tr> 
								</tbody>
							</table>
						</td>
					</tr>';
        $hiddendrugnamePrint = $_POST['hiddendrugname'];
        $qCount = 0;
        $mailcontent .= '<tr><td>';
        $drugcontent = '<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
						<tr><th align="left">Drug Name</th><th>Days</th><th>Morning</th><th>Noon</th><th>Evening</th><th>Night</th><th style="width:150px;">Remarks</th></tr>';
        foreach ($drugname as $val) {
            $remarks[$qCount] = addslashes($remarks[$qCount]);
            if ($val <> "") {
                $rowval = $rowid[$qCount];
                $drugcontent .= '<tr><td>' . $hiddendrugnamePrint[$qCount] . '</td><td align="center">' . $duration[$qCount] . '</td><td align="center">' . $morning[$qCount] . '</td><td align="center">' . $afternoon[$qCount] . '</td><td align="center">' . $evening[$qCount] . '</td><td align="center">' . $night[$qCount] . '</td><td style="text-align:center;">' . $remarks[$qCount] . '</td></tr> ';
            }
            $qCount = $qCount + 1;
        }
        $drugcontent .= '</table>';
        $mailcontent .= $drugcontent;
        $mailcontent .= '</tr></td>';
        $mailcontent .= '<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
								<tbody>
										<tr><td colspan="2">
											<div><b>
												' . $nameHospPrintMail[0]['corporate_name'] . ' - ' . $nameHospPrintMail[0]['displayname'] . '</b>
											</div>
											<div>
												<br/><b>Regards, <br>The myHealthvalet team</b><br/>
												<div style="font-size:12px;">* Note: This is for your reference only and may not be considered as the original prescription. Kindly obtain the original prescription from your doctor.<br/>
												
												</div>
												
											</div>
										</td></tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td height="35" bgcolor="#199bbf" style="padding:5px;">
							<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
								<tbody>
									<tr><td style="color:#FFFFFF; font-size:15px;">Visit myhealthvalet.in for more details or look up the blog  <a href="http://www.hygeiaes.com/blog/" target="_blank"><u>http://www.hygeiaes.com/blog/</u></a> </td></tr>
								</tbody>
							</table>
						</td>
					</tr>		
			</table>';

        $mailsend = sendmail('myHealthvalet - Prescription from Dr ' . $nameDoctorPrintMail, $toUserPrintMail, $mailcontent, $toDoctorPrintMail);
    }
}
if (isset($_POST['id']) && ($_POST['id'] != '0')) {
    $SqlPresID = $_POST['id'];
} else {
    $SqlPresID = $presID;
}
$count1 = 0;
if (isset($customTest)) {
    foreach ($customTest as $key) {
        if ($key == "") {
            $count1++;
        }
    }
}

// Add other test
$insertedTest = $tbs_userid . "/" . $tbs_role;
$customTest = $_POST['custom_test'];

if ($customTest <> "") {
    $addOtherTestObj = new ManageMasters();
    $addOtherTests = $addOtherTestObj->addOtherTest($insertedTest, $customTest);
    $addOtherTestObj = null;
    $customTest = $addOtherTests;
}

if (isset($customTest) && $count1 != '5') {

    if ($maxid == "") {
        $maxid = 0;
    }
    if (intVal($maxid) > 0) {
        $maxid1 = $maxid;
    } else {
        $PrescriptionTestsMax = new ManageUsers();
        $MaxTestCodes = $PrescriptionTestsMax->listDirectQuery("select ifnull(max(test_code),0) as maxid from prescribed_tests");
        if ($MaxTestCodes) {
            foreach ($MaxTestCodes as $MaxTestCodesL) {
                $maxid1 = $MaxTestCodesL['maxid'] + 1;
            }
        }
    }
    $PrescriptionTest = new ManageUsers();


    $custom_test = $customTest;
    foreach ($custom_test as $PresTestVal) {

        $checkTestId = $PrescriptionTest->listDirectQuery("select * from prescribed_tests where prescription_id='$SqlPresID' and test_id='$PresTestVal'");

        if ($checkTestId == 0) {
            $dateonly = date("Y-m-d");
            if ($PresTestVal != "") {
                $resultPrescriptionTests = $PrescriptionTest->AddDirectQuery("INSERT INTO prescribed_tests(prescription_id,test_code,test_date,test_id,user_id,doctor_id,fav_lab,lab_id,created_on,created_by,created_role,test_type) values ('$SqlPresID','$maxid1','$fromdate', '$PresTestVal', '$username', '$docname', '$fav_lab', '$fav_lab', '$datetime', '$tbs_userid', '$tbs_role','0')");
            }
        } else {
            $deleteTestId = $PrescriptionTest->listDirectQuery("delete  from prescribed_tests where prescription_id='$SqlPresID' and test_id='$PresTestVal'");
        }
    }
}



//Add Points
if ($tbs_role == "1") {
    include_once("get_points.php");
    $points = $prescriptionU;
    $types = 1;
    $description = "Prescription";
    $point_userid = $tbs_userid;
    include_once("points_save.php");
}

if ($tbs_role == "2") {
    include_once("get_points.php");
    $points = $prescriptionU;
    $types = 1;
    $description = "Prescription";
    $point_userid = $tbs_userid;
    include_once("points_save.php");
}

if ($tbs_role == "3") {
    include_once("get_points.php");
    $points = $prescriptionU;
    $types = 1;
    $description = "Prescription";
    $point_userid = $sessionlocation_id;
    include_once("points_save.php");
}
//To add notification in settingsworkur.php
if (isset($_SESSION['morningtime'])) {
    unset($_SESSION['morningtime']);
}
if ($_POST['popUp'] == "1") { ?>
    <script>
        parent.popUpCopy("<?php echo urlencode($drugcontent); ?>", "<?php echo $SqlPresID; ?>", "<?php echo $_POST['countIndex']; ?>", "0");
    </script>
    <?php } else {
        if ($ohcs == '1' && !empty($case_id)) { ?>
        <script>
            document.location.href = "add-registry.php?id=<?php echo $case_id; ?>";
        </script>
    <?php    } elseif ($saveRdraft == "Save" || $saveRdraft == "AgainDraftSavePresc" || $saveRdraft == "TemplateSave") { ?>
        <script>
            var username = '<?php echo $username; ?>';
            document.location.href = "view-ohc-prescription.php?suser=" + username;
        </script>
    <?php } elseif ($saveRdraft == "Draft" || $saveRdraft == "AgainDraftPresc" || $saveRdraft == "DraftPrescs") { ?>
        <script>
            document.location.href = "DraftPrescription.php";
        </script>
    <?php } elseif ($saveRdraft == "DraftPresc") { ?>
        <script>
            document.location.href = "view-ohc-prescription.php";
        </script>
    <?php } elseif ($saveRdraft == "DraftPrescs") { ?>
        <script>
            var id = '<?php echo $prescriptionId; ?>';
            document.location.href = "DraftPrescription.php?presid=" + id;
        </script>
    <?php } elseif ($saveRdraft == "CopyPresc") { ?>
        <script>
            var id = '<?php echo $prescriptionId; ?>';
            document.location.href = "view-ohc-prescription.php?id=" + id;
        </script>
    <?php } elseif ($saveRdraft == "TemplateOnly") { ?>
        <script>
            document.location.href = "scrAddPrescription.php?id=0";
        </script>
    <?php } elseif ($saveRdraft == "addtest") { ?>
        <script>
            var username = '<?php echo $username; ?>';
            var id = '<?php echo $prescriptionId; ?>';
            document.location.href = "prescription-test.php?suser=<?php echo $username; ?>&presid=<?php echo $SqlPresID; ?>&add";
        </script>
    <?php } else { ?>
        <script>
            var id = '<?php echo $SqlPresID; ?>';
            document.location.href = "scrAddPrescription.php?err=ESD&id=" + id;
        </script>
<?php }
    } ?>