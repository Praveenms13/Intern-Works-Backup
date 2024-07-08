<?php
include('../session.php');
include_once('../core/class.manageSettings.php');
include_once('../core/class.manageUsers.php');
include_once('../core/class.common.php');
$action = $_POST['action'];

if(isset($_POST) && !empty($_POST)) {

    $columnDbNameArrayLists1[] = "drugmorning";
    $columnDisplayNameArrayLists1[] = "Morning (AM)";
    $columnDisplayIconArrayLists1[] = "<img src='".$sitepath."../img/Morning.png'>";
    $columnInputNameArrayLists1[] = "morning";
    $columnShortNameArrayLists1[] = "m0";



    $columnDbNameArrayLists1[] = "drugafternoon";
    $columnDisplayNameArrayLists1[] = "Noon";
    $columnDisplayIconArrayLists1[] = "<img src='".$sitepath."../img/Noon.png'>";
    $columnInputNameArrayLists1[] = "afternoon";
    $columnShortNameArrayLists1[] = "a0";



    $columnDbNameArrayLists1[] = "drugevening";
    $columnDisplayNameArrayLists1[] = "Evening";
    $columnDisplayIconArrayLists1[] = "<img src='".$sitepath."../img/Evening.png'>";
    $columnInputNameArrayLists1[] = "evening";
    $columnShortNameArrayLists1[] = "e0";

    $columnDbNameArrayLists1[] = "drugnight";
    $columnDisplayNameArrayLists1[] = "Night (PM)";
    $columnDisplayIconArrayLists1[] = "<img src='".$sitepath."../img/Night.png'>";
    $columnInputNameArrayLists1[] = "night";
    $columnShortNameArrayLists1[] = "n0";


    ?>
<style>.cboxContentCls{background:none !important; border:none !important;}
.outside { color:#<?php echo $_SESSION['tclr'] ?> !important; }

.box.box-bordered.box-color .box-content { border-color:#fff !important;}
.dropdown-menu.dropdown-primary, #navigation .dropdown-menu, .box.box-bordered.box-color .box-title, .box.box-bordered.box-color .box-content { border-color:#fff !important;}
</style>

<?php
        $doc = new ManageUsers();
    $conqry = "SELECT id,condition_name from prescription_condition";
    $cond = $doc->listDirectQuery($conqry);
    foreach($cond as $res) {
        $cids[] = $res['id'];
        $conval[] = $res['condition_name'];
    }
    if($tbs_role == 2) {
        $doc = new ManageUsers();
        $doc_id = $_POST['usercolumn'];
        $sql1 = "select DISTINCT(doctor_id) from prescription where user_id='$doc_id'";
        $use = $doc->listDirectQuery($sql1);
        foreach($use as $key) {
            $user_array .= $key['doctor_id'].", ";
        }
        $user_array = chop($user_array, ", ");
    }
    ?>
		<div class="content-table float-left" id="content-infinite">
<?php
$getcode = ",IF(p.created_role=1,mud.user_id,IF(p.created_role=2,md.user_id, 'hospital')) as user_code,p.from_date as from_date";

    @$id = $_POST['id'];
    $thisid = $_POST['thisid'];
    $action = $_POST['action'];
    @$getId = $_POST['getId'];
    $tablename = "prescription";
    $prescriptionDetail = new ManageUsers();
    if($getId != 0) {
        $whr = " and p.id=".$getId;
    } elseif(@$action == "listprescription" || @$action == "listOtherPrescription") {
        $whr = " and 1=1";
        $expl = explode("-", $_POST['doctorcolumn']);




        @$doctorvalue = $expl[0];
        @$uservalue = $_POST['usercolumn'];
        @$fdatevalue = DateTime::createFromFormat('d/m/Y', $_POST['fdatecolumn'])->format('Y-m-d');
        @$tdatevalue = DateTime::createFromFormat('d/m/Y', $_POST['tdatecolumn'])->format('Y-m-d');
        if($tbs_role == 1 && !empty($_POST['doctorcolumn'])) {
            if(!empty($expl[1])) {
                $whr .= " and p.ohc='1'";
            } else {
                $whr .= " and p.ohc='0'";
            }
        }
        if($doctorvalue != "" && $action != "listOtherPrescription") {
            $doctorcolumn = "doctor_id";

            $whr .= " and p.".$doctorcolumn."=".$doctorvalue;
        }
        if($uservalue != "") {
            $whr .= " and mud.first_name like '".$uservalue."%'";
        }
        if($fdatevalue != "" && $tdatevalue != "") {
            $datecolumn = "from_date";

            $whr .= " and (p.".$datecolumn." between '".$fdatevalue."' and '".$tdatevalue."')";
        }


    } else {
        $whr = " and 1=1";
    }
    if($tbs_role == "3") {
        $mdid = array();
        $Doctorallist = new ManageUsers();
        $Doctorallists = $Doctorallist->listDirectQuery("SELECT md.id as mdid FROM doctorconsultation dc left outer join master_hcsp mh on mh.id=dc.hcsp_id left outer join master_doctor md on md.id=dc.user_id WHERE dc.hcsp_id='$sessionlocation_id' GROUP BY dc.user_id,md.id");
        if($Doctorallists != 0) {
            foreach($Doctorallists as $DoctorallistsL) {
                array_push($mdid, $DoctorallistsL['mdid']);
            }
        }
        if(count($mdid) == "0") {
            array_push($mdid, "-1");
            $mdid = implode(', ', $mdid);
        } else {
            $mdid = implode(', ', $mdid);
        }
        $whr .= " AND p.doctor_id IN($mdid) AND p.master_hcsp_user_id='$sessionlocation_id'";
    }

    $offset = is_numeric($_POST['offset']) ? $_POST['offset'] : 0;
    $postnumbers = is_numeric($_POST['number']) ? $_POST['number'] : 3;

    $prescriptionCount = $prescriptionDetail->listDirectQuery("select count(*) as prescount from prescription p left outer join master_doctor md on md.id=p.doctor_id left outer join master_user_details mud on mud.id=p.user_id where p.status='1' and p.isdraft='0' and p.role_id='".$tbs_role."' " .$whr. " order by p.id desc");

    if(is_array($prescriptionCount)) {
        foreach($prescriptionCount as $prescriptionCounts) {
            $prescriptionClist = $prescriptionCounts['prescount'];
            if($postnumbers > $prescriptionClist) {
                ?><style type="text/css">.loading-bar{display:none;}</style><?php
            } else {
            }
        }
    }



    if($tbs_role == 2 || $tbs_role == 3) {
        if($_POST['action'] == "listOtherPrescription") {
            $sql = "SELECT p.*".$getcode.",mp.pharmacy_name,ml.lab_name,md.first_name as doctor_name,mcm.med_condition,md.last_name as doctor_last_name,md.qualification,md.specialization,mud.first_name  as username,mud.last_name  as lastname,mud.gender,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, CONCAT(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' month'), CONCAT(TIMESTAMPDIFF(year,mud.dob,NOW()), ' yrs')) as age,LEFT(monthname(p.from_date),3) as monthname,LEFT(DAYNAME(p.from_date),3) as dayname,DAYOFMONTH(p.from_date)as dayofmonth
								FROM prescription p
								LEFT OUTER JOIN master_doctor md on md.id=p.doctor_id
								LEFT OUTER JOIN master_user_details mud on mud.id=p.user_id
								LEFT OUTER JOIN med_condition_map mcm on mcm.id=p.conditionname
								LEFT OUTER JOIN `master_pharmacy` mp on mp.id=p.fav_pharmacy
								LEFT OUTER JOIN `master_lab` ml on ml.id=p.fav_lab
								WHERE p.status='1'
										AND p.isdraft='0'
										AND p.doctor_id!='$tbs_userid'
										AND EXISTS (
											SELECT GROUP_CONCAT(DISTINCT allt.userdetails)
												FROM
													((
														SELECT user_id as userdetails,doctor_id as doctorid
														FROM prescription
														WHERE doctor_id='$tbs_userid'
													) 
													UNION ALL 
													(
														SELECT accountof_user_id as userdetails,accessto_user_id as doctorid
														FROM userdoctoraccessrights
														WHERE accessto_user_id='$tbs_userid'
																AND 
															pres='1'
													)
													UNION ALL 
													(
														SELECT user_id as userdetails,added_by as doctorid
														FROM case_file
														WHERE added_by='$tbs_userid'
															AND
															added_role='$tbs_role'
													)
													UNION ALL
													(
														SELECT id as userdetails,insertedby as doctorid
														FROM master_user_details
														WHERE insertedby='$tbs_userid' 
															AND insertedrole='$tbs_role'
													)) allt
													WHERE allt.doctorid='$tbs_userid' AND  p.user_id=allt.userdetails
													GROUP BY allt.userdetails
										) " .$whr. "
								GROUP BY p.id
								ORDER BY p.from_date DESC,p.id DESC
								LIMIT ".$postnumbers." OFFSET ".$offset;
        } else {
            if($tbs_role == 2) {
                $doctorvalue = $tbs_userid;
            } else {
                $doctorvalue = $doctorvalue;
            }
            //if($tbs_role==2){$whr.=" AND p.doctor_id='$doctorvalue'";} else {$whr.=" AND p.master_hcsp_user_id='$sessionlocation_id'";}
            $sql = "select p.*".$getcode.",mp.pharmacy_name,ml.lab_name,mp_area.doctype as pharmacy_area,ml_area.doctype as lab_area,md.first_name as doctor_name,mcm.med_condition,md.last_name as doctor_last_name,md.qualification,md.specialization,mud.first_name  as username,mud.last_name  as lastname,mud.gender,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, CONCAT(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' month'), CONCAT(TIMESTAMPDIFF(year,mud.dob,NOW()), ' yrs')) as age,LEFT(monthname(p.from_date),3) as monthname,LEFT(DAYNAME(p.from_date),3) as dayname,DAYOFMONTH(p.from_date)as dayofmonth from prescription p left outer join master_doctor md on md.id=p.doctor_id left outer join master_user_details mud on mud.id=p.user_id 
		left outer join med_condition_map mcm on mcm.id=p.conditionname 
		LEFT OUTER JOIN `master_pharmacy` mp on mp.id=p.fav_pharmacy
		LEFT OUTER JOIN `doctype` mp_pincode on mp_pincode.id=mp.pincode
		LEFT OUTER JOIN `doctype` mp_area on mp_area.id=mp_pincode.parent_id
		LEFT OUTER JOIN `master_lab` ml on ml.id=p.fav_lab
		LEFT OUTER JOIN `doctype` ml_pincode on ml_pincode.id=ml.pincode
		LEFT OUTER JOIN `doctype` ml_area on ml_area.id=ml_pincode.parent_id
		where p.status='1' and p.isdraft='0' " .$whr. " GROUP BY p.id ORDER BY p.from_date desc,p.id desc LIMIT ".$postnumbers." OFFSET ".$offset;

        }
    }
    if($tbs_role == 1) {

        $sql = "select p.*".$getcode.",mp.pharmacy_name,ml.lab_name,mp_area.doctype as pharmacy_area,ml_area.doctype as lab_area,md.first_name as doctor_name,mcm.med_condition,md.last_name as doctor_last_name,md.qualification,md.specialization,mud.first_name  as username,mud.last_name  as lastname,mud.gender,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, CONCAT(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' month'), CONCAT(TIMESTAMPDIFF(year,mud.dob,NOW()), ' month')) as age,LEFT(monthname(p.from_date),3) as monthname,LEFT(DAYNAME(p.from_date),3) as dayname,DAYOFMONTH(p.from_date)as dayofmonth from prescription p left outer join master_doctor md on md.id=p.doctor_id left outer join master_user_details mud on mud.id=p.user_id 
left outer join med_condition_map mcm on mcm.id=p.conditionname
LEFT OUTER JOIN `master_pharmacy` mp on mp.id=p.fav_pharmacy
LEFT OUTER JOIN `doctype` mp_pincode on mp_pincode.id=mp.pincode
LEFT OUTER JOIN `doctype` mp_area on mp_area.id=mp_pincode.parent_id
LEFT OUTER JOIN `master_lab` ml on ml.id=p.fav_lab
LEFT OUTER JOIN `doctype` ml_pincode on ml_pincode.id=ml.pincode
LEFT OUTER JOIN `doctype` ml_area on ml_area.id=ml_pincode.parent_id
where p.status='1' and p.isdraft='0'  " .$whr. " group by p.id order by p.from_date desc,p.id desc LIMIT ".$postnumbers." OFFSET ".$offset;

    }
    if($tbs_role == 4 && (!empty($_SESSION['ohc_doc']) || !empty($_REQUEST['oc']))) {


        $docid = $prescriptionDetail->listDirectQuery("SELECT GROUP_CONCAT( acc.user_id ) as doid FROM `ohc_rights` acc JOIN master_corporate_user mcu ON mcu.id = acc.`user_id` JOIN master_corporate mc ON mc.id = acc.`corp_id` where acc.corp_id =".$_SESSION['ohc_loca']." ");
        $whr .= " AND p.doctor_id in(".$docid[0]['doid'].")";

        $sql = "select p.*".$getcode.",mp.name,ml.lab_name,ml_area.doctype as lab_area,md.first_name as doctor_name,mcm.med_condition,md.last_name as doctor_last_name,oh.qualification,oh.specialization,mud.first_name  as username,mud.last_name  as lastname,mud.gender,if(TIMESTAMPDIFF(year,mud.dob,NOW())=0, CONCAT(TIMESTAMPDIFF(MONTH,mud.dob,now()), ' month'), CONCAT(TIMESTAMPDIFF(year,mud.dob,NOW()), ' yrs')) as age,LEFT(monthname(p.from_date),3) as monthname,LEFT(DAYNAME(p.from_date),3) as dayname,DAYOFMONTH(p.from_date)as dayofmonth,YEAR(p.from_date)as currentyear from prescription p left outer join master_corporate_user md on md.id=p.doctor_id left outer join ohc_rights oh on oh.user_id=md.id left outer join master_user_details mud on mud.id=p.user_id 
		left outer join med_condition_map mcm on mcm.id=p.conditionname 
		LEFT OUTER JOIN `ohc_pharmay` mp on mp.id=p.fav_pharmacy
		LEFT OUTER JOIN `master_lab` ml on ml.id=p.fav_lab
		LEFT OUTER JOIN `doctype` ml_pincode on ml_pincode.id=ml.pincode
		LEFT OUTER JOIN `doctype` ml_area on ml_area.id=ml_pincode.parent_id
		where p.status='1' and p.isdraft='0' and p.ohc='1' " .$whr. " GROUP BY p.id ORDER BY p.from_date desc,p.id desc LIMIT ".$postnumbers." OFFSET ".$offset;
        //echo $sql;
    }
    $prescription = $prescriptionDetail->listDirectQuery($sql);
    if($prescription != 0) {
        ?>
							<script>removeloadingvalue2();</script>
							
							<?php
        foreach($prescription as $listprescription) {
            $id = $listprescription['id'];
            $prescriptionMainid = $listprescription['id'];
            /*if(!empty($listprescription['ohc'])){
             $drprescription=$prescriptionDetail->listDirectQuery(" select first_name,last_name from master_corporate_user where id='".$listprescription['doctor_id']."'");

                                        $doctor_name=ucfirst($drprescription[0]['first_name']);
                                        $doctor_last_name=ucfirst($drprescription[0]['last_name']);
            }else{	*/
            $doctor_name = ucfirst($listprescription['doctor_name']);
            $doctor_last_name = ucfirst($listprescription['doctor_last_name']);
            //}
            $user_id = $listprescription['user_id'];
            $doctor_id = $listprescription['doctor_id'];
            $from_date = $listprescription['from_date'];
            $created_by = $listprescription['created_by'];
            $created_role = $listprescription['created_role'];
            $med_condition = $listprescription['med_condition'];
            $is_conformance = $listprescription['is_conformance'];
            $condname = $listprescription['conditionname'];

            if($created_role == "2") {
                $GetCreatedByName = new ManageUsers();
                $GetCreatedByNames = $GetCreatedByName->listDirectQuery("SELECT * from master_doctor where id=".$created_by);
                if($GetCreatedByNames) {
                    foreach($GetCreatedByNames as $GetCreatedByNamesL) {
                        $user_code = $GetCreatedByNamesL['user_id'];
                    }
                }
                $GetCreatedByName = null;
            } elseif($created_role == "3") {
                $GetCreatedByNameq = new ManageUsers();
                $GetCreatedByNameqs = $GetCreatedByNameq->listDirectQuery("SELECT * from master_hcsp_user where id=".$created_by);
                if($GetCreatedByNameqs) {
                    foreach($GetCreatedByNameqs as $GetCreatedByNameqsL) {
                        $user_code = $GetCreatedByNameqsL['user_id'];
                    }
                }
                $GetCreatedByName = null;
            } else {
                $GetCreatedByName = new ManageUsers();
                $GetCreatedByNames = $GetCreatedByName->listDirectQuery("SELECT * from master_corporate_user where id=".$created_by);
                if($GetCreatedByNames) {
                    foreach($GetCreatedByNames as $GetCreatedByNamesL) {
                        $user_code = $GetCreatedByNamesL['user_id'];
                    }
                }
                $GetCreatedByName = null;
            }

            /*if($listprescription['ohc']=="1"){
                            $GetCreatedByName = new ManageUsers();
                                                $GetCreatedByNames=$GetCreatedByName->listDirectQuery("SELECT * from master_user_details where id='".$listprescription['doctor_id']."'");
                                                if($GetCreatedByNames){
                                                    foreach($GetCreatedByNames as $GetCreatedByNamesL){
                                                        $user_code=$GetCreatedByNamesL['user_id'];
                                                    }
                                                }
                                                $GetCreatedByName=null;
            }*/


            $from_dates = $listprescription['from_date'];
            $master_specialization_id = $listprescription['master_specialization_id'];
            $master_hcsp_user_id = $listprescription['master_hcsp_user_id'];
            $doctornotes = $listprescription['doctornotes'];
            $usernotes = $listprescription['usernotes'];
            $attachment_id = $listprescription['attachment_id'];

            $username = ucfirst($listprescription['username']);
            $lastname = ucfirst($listprescription['lastname']);
            $gender = $listprescription['gender'];
            $usernotes = $listprescription['usernotes'];
            $sharewithdoctor = $listprescription['sharewithdoctor'];
            $age = $listprescription['age'];
            $isdraft = $listprescription['isdraft'];
            $test_modified = $listprescription['test_modified'];
            $monthname = $listprescription['monthname'];
            $dayname = $listprescription['dayname'];
            $currentyear = $listprescription['currentyear'];
            $dayofmonth = $listprescription['dayofmonth'];
            $fav_pharmacy = $listprescription['fav_pharmacy'];
            $pharmacy_name = $listprescription['pharmacy_name'];
            $fav_lab = $listprescription['fav_lab'];
            $lab_name = $listprescription['lab_name'];
            $pharmacy_area = $listprescription['pharmacy_area'];
            $lab_area = $listprescription['lab_area'];


            $from_dates1 = explode("-", $from_dates);
            $from_datep = $from_dates1[2].$from_dates1[1].$from_dates1[0];

            //get qualification
            $qualification = new common();
            $qualification = $qualification->getQualification($listprescription['qualification']);

            //get specialization
            $specialization = new common();
            $specialization = $specialization->getSpecialization($listprescription['specialization']);

            if($toplabel != $username) {


                ?>

<div style="width:100%; height: auto; float:left; background-color:#<?php echo $_SESSION['tclr'] ?>;  padding: 0.5%; color:#fff; font-size:14px;">
   <div style="width:50%; float:left; font-size:16px;"><?php echo ''.$prefix." ".$username." ".$lastname; ?></div>
   <div style="width:25%; float:left;"><?php echo ucfirst($gender)." ". $age; ?></div>
   <div style="width:25%; float:left; text-align:right;"><!--<i class="glyphicon-user"></i> View Profile--></div>
</div>

<?php $toplabel = $username;
            }?>
                        <div class="box box-bordered box-color view-prescription" style="clear:both; width:100%; padding-bottom: 5px; border-bottom: 0px #ccc solid; margin-bottom: 10px;">
						<?php if($action == "listOtherPrescription") {?>
	  <div class="box-title" style="background-color:#999999 !Important; font-size:14px; color:#fff; padding: 5px 1%; width: 100%; margin-top: 0 !important;">
                                    	<?php echo "Dr.".$doctor_name." ".$doctor_last_name."".", ".$qualification.""; ?> - 
					<?php echo $specialization; ?>
										
                                    </div>		
						<?php } $bckstrp = ($action == "listOtherPrescription") ? "#dfdfdf" : "#999999";
            $bckstrpclr = ($action == "listOtherPrescription") ? "#000" : "#fff";?>									
  <div class="box-title" style="background-color: <?php echo $bckstrp;?> !Important; font-size:14px; color:<?php echo $bckstrpclr;?>; margin: 0 !important; width:100%; padding: 0 1%;"> 
    <div class="col-xl-4 col-lg-4 col-md-4 float-left px-0"> <?php echo "Dr.".$doctor_name." ".$doctor_last_name.""."";?></div>
    <div class="col-xl-4 col-lg-4 col-md-4 float-left px-0">
      <div class="title-manual">
        IDs: <?php echo $user_code."/".$from_datep."/000".$prescriptionMainid?>
            <?php //echo $dayname.' , '.$dayofmonth.' '.$monthname;?>
      </div>
    </div>


  <!--  <div class="col-xl-1 col-lg-1 col-md-1 float-left px-0">
     <?php if(!isset($_POST['popUp'])) { ?>
      <div class="title-manual capitalize">
           <a <?php if($action == "listOtherPrescription") {
               echo " style='color:#AAAAAA'";
           } else { ?> onclick="PrescriptionAction('<?php echo $prescriptionMainid; ?>', 'cancel')" <?php } ?> > 
                       <i class="fa fa-trash-o" title="Cancel Prescription" <?php if($action == "listOtherPrescription") {
                           echo " style='color:#AAAAAA'";
                       } ?> alt="Cancel Prescription"></i></a>
     </div>
<?php } ?>
   </div>-->


    <div class="col-xl-2 col-lg-2 col-md-2 float-left px-0">
      <div class="title-manual">
           <?php echo $dayname.', '.$dayofmonth.' '.$monthname.' '.$currentyear; ?>
      </div>
    </div>

    <div class="col-xl-2 col-lg-2 col-md-2 float-left px-0">
<!--
    <?php if($is_conformance == '1') {
        $confirmance = '<i class="fa fa-thumbs-o-up" aria-hidden="true"></i> ';
    } else {
        $confirmance = '<i class="fa fa-thumbs-o-down" aria-hidden="true"></i> ';
    }echo $confirmance; ?>

<?php if(isset($_POST['popUp'])) { ?>
										<i class="glyphicon-circle_plus" title="Select Prescription" alt="Select Prescription" style="font-size:25px !important;float:right;" onClick="popUpCopy(this,'<?php echo $prescriptionMainid; ?>')"></i>
									<?php } else {?>

-->
<div style="width:25%;float:left;">
     <a <?php if($action == "listOtherPrescription") {
         echo " style='color:#AAAAAA'";
     } else { ?> onClick="sendMailPrecription('<?php echo $listprescription['id'];?>')"
		 <?php } ?>><i class="fa fa-envelope-o" title="Send Mail Prescription" <?php if($action == "listOtherPrescription") {
		     echo " style='color:#AAAAAA'";
		 } ?> alt="Send Mail Prescription"></i></a></div>
<!--
     <a onclick="PrescriptionAction('<?php echo $prescriptionMainid; ?>', 'copy')"><i class="fa fa-files-o" title="Copy Prescription" alt="Copy Prescription"></i></a>

     <?php if($prescriptionMainid != "") {
         $attachments = "onclick='viewattachmentsdb(".$prescriptionMainid.", 1)'";
     } else {
         $attachments = "onclick=noattachment()";
     }?> <a <?php echo @$attachments; ?>><i class="fa fa-paperclip" title="View Attachment" alt="View Attachment"></i></a>
--><div style="width:25%;float:left;">
     <a onclick="printdiv('<?php echo $user_id; ?>', '<?php echo $id; ?>')"><i class="fa fa-print" title="Print Prescription" alt="Print Prescription"></i></a></div>
    <?php
       $obj = new ManageUsers();
									    $prs = $obj->listDirectQuery("select id from prescription where prescription_id='"."/".$from_datep."/000".$prescriptionMainid."' ");
									    $prs_issue = $obj->listDirectQuery("select count(id) as issue_count from pharmacy_sold_stock_detail where r_prescription_id='".$prs[0]['id']."' ");
									    if($prs_issue[0]['issue_count'] == '0') {
									        ?> 
    <div style="width:25%;float:left;">
       
     <a onclick="CancelPrescription('<?php echo $prescriptionMainid; ?>', 'cancel')"><i class="fa fa-minus-circle" title="Delete Prescription" alt="Delete Prescription" style="color:red;"></i></a>
     </div>
     <?php } ?>
     <div style="width:25%;float:left;">
     <?php

									                                              //echo "select prescription_type from prescription_detail where prescription_id =".$listprescription['id']."";
									                                               $outsideprint = $obj->listDirectQuery("select prescription_type from prescription_detail where prescription_id =".$listprescription['id']."");
									    foreach($outsideprint as $outprint) {
									        if($outprint['prescription_type'] == 2) {
									            ?>
     
     <a onclick="printdivout('<?php echo $user_id; ?>', '<?php echo $id; ?>')"><i class="fa fa-external-link" title="Print Outside Prescription" alt="Print Prescription"></i></a>
									<?php break;
									        }
									    } ?> 
							&nbsp;</div>		
									
									<?php } ?>
							  </div>
							</div>        

							<div class="box-content nopadding">
								<div class="tab-content padding0 tab-content-inline tab-content-bottom">
									<div class="tab-pane active" id="user">
                                    <div class="prescription-table">
										<?php
									                                        $tableHeading = '<div class="prescription-tr-table" style="width:100%; padding: 5px 0; float:left; border-bottom: 1px #ad235e solid;color:#'.$_SESSION['tclr'].'">
                                            <div class="prescription-th-table" style="width:3%;">&nbsp;</div>
                                        	<div class="prescription-th-table" style="'.((count($columnDisplayNameArrayLists) == "3") ? "width:34%;" : "width:22%;").'">Drug Name</div>
                                        	<div class="prescription-th-table" style="width: 4%; text-align:center;">Days</div>
<div class="prescription-th-table" style="width: 24%; padding-right:3%">';
            $tablename = "prescription_detail";
            $prescriptionDetailObj = new ManageUsers();

            $prescriptiondetail = $prescriptionDetailObj->listDirectQuery("select p.*,md1.drug_name as substitude_drugname,ds.doctype as drugtypes,dss.doctype as drugintakeconditions from prescription_detail p 
											 left outer join master_drugs md on md.id=p.drugs_id 
											 left outer join master_drugs md1 on md1.id=p.substitude_drug 
											 left outer join doctype_static ds on ds.id=p.drugtype
											 left outer join doctype_static dss on dss.id=p.drugintakecondition where prescription_id =".$listprescription['id']."");
            $prescriptionDetailObj = null;

            $count = 0;
            foreach($columnDbNameArrayLists1 as $columnDisplayNameArrayList) {
                $chks = $prescriptiondetail[0][$columnDisplayNameArrayList];
                if(empty($_SESSION['ohc_loca'])) {
                    if($chks != "") {
                        $columnInputNameArrayList = $columnInputNameArrayLists1[$count];
                        $columnShortNameArrayList = $columnShortNameArrayLists1[$count];
                        $columnDisplayIconArrayList = $columnDisplayIconArrayLists1[$count];
                        $tableHeading .= '<div class="prescription-th-table" style="'.((count($columnDisplayNameArrayLists) == "3") ? "width: 12.5%;" : "width: 12.5%;").'; text-align:center !important;" title="'.$columnDisplayNameArrayList.'">'.$columnDisplayIconArrayList.'</div>';
                    }
                } else {
                    $columnInputNameArrayList = $columnInputNameArrayLists1[$count];
                    $columnShortNameArrayList = $columnShortNameArrayLists1[$count];
                    $columnDisplayIconArrayList = $columnDisplayIconArrayLists1[$count];
                    $tableHeading .= '<div class="prescription-th-table" style="'.((count($columnDisplayNameArrayLists) == "3") ? "width: 12.5%;" : "width: 12.5%;").'; text-align:center !important;" title="'.$columnDisplayNameArrayList.'">'.$columnDisplayIconArrayList.'</div>';
                }
                $count = $count + 1;
            }
            $tableHeading .= '</div><div class="prescription-th-table" style="width: 9%;" title="Unit of Measurement">UOM</div>
											<div class="prescription-th-table" style="'.((count($columnDisplayNameArrayLists) == "3") ? "width:10%;" : "width:8%; ").'">AF/BF</div>
											<div class="prescription-th-table" style="width: 27%;">Remarks</div>
                                        </div>';
            echo $tableHeading;
            ?>
										<div style="clear:both; width:100%; border-bottom: 1px #999 solid !important; float:left; padding-bottom: 5px;">
											<?php

                  if($prescriptiondetail != 0) {
                      $countpres = 0;
                      echo "<pre>";
                      print_r($prescriptiondetail);
                      echo "</pre>";
                      foreach($prescriptiondetail as $listprescriptiondetail) {
                          $countpres = $countpres + 1;
                          $bg = ($countpres % 2) ? "odd-row" : "even-row";
                          ?>
													<div class="prescription-tr-table <?php echo $bg; ?>" style="width: 100%; float: left; padding: 3px 0; border-bottom: 1px #ccc dashed;">
														<div class="prescription-td-table" style="width:3%; text-align:right;"><input type="hidden" name="getrows1" id="getrows" value="1" />
														  <input type="hidden" name="rowid[]" id="rowid" value="<?php echo $listprescriptiondetail['id'];?>" /><?php echo $countpres; ?> &nbsp;
														</div>
														<div class="prescription-td-table" style="width:22%;">
															<?php echo($listprescriptiondetail['drugs_name'] == "" ? "N/A" : $listprescriptiondetail['drugs_name']); ?>&nbsp; <?php echo ($listprescriptiondetail['prescription_type'] == "2") ? "<i class='fa fa-share-square-o outside'  aria-hidden='true'></i>" : '' ;?>
															<?php echo ($listprescriptiondetail['substitude_drugname'] != "") ? ('<i> (sub: '.$listprescriptiondetail['substitude_drugname'].')</i>') : "";  ?>
														</div>
														<div class="prescription-td-table" style="width: 4%; text-align:center;"><?php echo $listprescriptiondetail['prescribed_for_days'];?></div>
<div class="prescription-th-table" style="width: 24%; color:#<?php echo $_SESSION['tclr'] ?>; padding-right:3%">
<?php

$count = 0;
                          foreach($columnDbNameArrayLists1 as $columnDisplayNameArrayList) {
                              $chks = $listprescriptiondetail[$columnDisplayNameArrayList];
                              if(empty($_SESSION['ohc_loca'])) {
                                  if($chks != "") {
                                      $columnInputNameArrayList = $columnInputNameArrayLists1[$count];
                                      $columnShortNameArrayList = $columnShortNameArrayLists1[$count];
                                      $columnDisplayIconArrayList = $columnDisplayIconArrayLists1[$count];
                                      echo '<div class="prescription-td-table" style="'.((count($columnDisplayNameArrayLists) == "3") ? "width: 12.5%;" : "width: 12.5%; ").'; text-align:center !important;" title="'.$columnDisplayNameArrayList.'">'.(($chks == "") ? "0" : $chks).'</div>';
                                  }
                              } else {
                                  $columnInputNameArrayList = $columnInputNameArrayLists1[$count];
                                  $columnShortNameArrayList = $columnShortNameArrayLists1[$count];
                                  $columnDisplayIconArrayList = $columnDisplayIconArrayLists1[$count];
                                  echo '<div class="prescription-td-table" style="'.((count($columnDisplayNameArrayLists) == "3") ? "width: 12.5%;" : "width: 12.5%; ").'; text-align:center !important;" title="'.$columnDisplayNameArrayList.'">'.(($chks == "") ? "0" : $chks).'</div>';

                              }
                              $count = $count + 1;
                          }


                          ?>
													  
														</div><div class="prescription-td-table" style="width: 9%;" title="Unit of Measurement"><?php echo $listprescriptiondetail['drugtype'];?></div>
														<div class="prescription-td-table" style="width:8%;"><?php echo ($listprescriptiondetail['drugintakeconditions'] == "" ? "N/A" : $listprescriptiondetail['drugintakeconditions'])?></div>
														<div class="prescription-td-table" style="width:27%;"><?php echo($listprescriptiondetail['remarks'] == "" ? "N/A" : $listprescriptiondetail['remarks']);?></div>																			</div>
          <?php
                      }
                  }
            ?>
		</div></div></div> 


		<div style="width:53%; padding: 0 1%; float:left;">
		<!--CONDITION SECTION-->
		<?php if($condname <> "") {
		    $serky = array_search($condname, $cids); ?>
		<div style="width:100%; padding: 5px 0;">
 		    <b style="color:#<?php echo $_SESSION['tclr'] ?>;">Condition</b>:<br> 
		                <?php
                if($serky !== false) {
                    echo $conval[$serky];
                }
		    // echo $med_condition;?></div>
				<?php } ?>


		<!--DOCTOR/PATIENT NOTES-->
                      <?php if(($usernotes != "" || !empty($usernotes)) && (($tbs_role == "1" && $created_role == "2" && $sharewithdoctor == "1") || ($tbs_role == "2" && $created_role == "1" && $sharewithdoctor == "1") || ($tbs_role == "1" && $created_role == "1") || ($tbs_role == "2" && $created_role == "2") || ($tbs_role == "3" && $created_role == "3"))) { ?>

                      <div style="width:100%; padding: 5px 0;">
					<b style="color:#<?php echo $_SESSION['tclr'] ?>;"><?php if($created_role == "1") {
					    echo "Patient Notes";
					} else {
					    echo "Doctor Notes";
					}?>:</b><br>
                       <?php echo stripslashes($usernotes); ?></div>
                            <?php } else { ?>	
                                              <?php } ?>
                                              <?php if($sharewithdoctor == "1") {?> 
                                              <?php } ?>

		<!--FAVOURITE PHARMACY-->									
		 <?php if(empty($_SESSION['ohc_loca'])) {
		     if($fav_pharmacy != "" && $fav_pharmacy != "0") {?>
                 <div style="width:100%; padding: 5px 0;"><b style="color:#<?php echo $_SESSION['tclr'] ?>;">Pharmacy:</b> <?php echo $pharmacy_name.", ".$pharmacy_area; ?></div>
		 <?php }
		     }?>


               </div>


		<div style="width:43%; padding: 0 1%; float:left;">
             		<!--TEST SECTION-->   
<?php
                                    $TestCheckDetails = new ManageUsers();
            $TestChecks = $TestCheckDetails->listDirectQuery("SELECT GROUP_CONCAT(' ',test_name) as test_name FROM (SELECT  if(st.subgroup IS NULL,mt.test_name,st.subgroup) as test_name FROM prescription p 
									left outer join master_doctor md on md.id=p.doctor_id
									left outer join master_user_details mud on mud.id=p.user_id,prescribed_tests pt
									left outer join master_test mt on mt.id=pt.test_id
									left outer join subgroup_test st on st.id=pt.subgroup
									where pt.prescription_id=p.id and pt.prescription_id='".$listprescription['id']."'
									group by if(st.subgroup IS NULL,pt.test_id,st.subgroup) ORDER BY mt.test_name ASC) as testconcatinated");
            $TestCheckDetails = null;
            $testname = "";
            $testname = $TestChecks[0]['test_name'];
            if($testname != "") {
                ?>
                                    <div style="width:100%; padding: 5px 0;">
                                    <?php  //if($tbs_role!="3") {?>
                                    <b style="color:#<?php echo $_SESSION['tclr'] ?>;">Test Results:</b>
                                    <a href="prescription-test.php?presid=<?php echo $listprescription['id'];?>"><i class="fa fa-street-view" aria-hidden="true" style="color:#<?php echo $_SESSION['tclr'] ?>;; margin:0 !important; text-align:center;"></i>
                                    <?php //}?> 
                                    <b style="color: #000;"><?php echo $testname; ?></b></a>
								   </div>
								   <?php } ?>

		<!--NOTES TO PATIENT-->		
 	               <?php if($doctornotes != "" || !empty($doctornotes)) {?>
                       <div style="width:100%; padding: 5px 0;">
                         <b style="color:#<?php echo $_SESSION['tclr'] ?>;">Notes to the patient:</b><br>
                           <?php echo stripslashes($doctornotes); ?></div>
                           <?php } ?>


		<!--FAVOURITE LAB-->
		<?php if($fav_lab != "" && $fav_lab != "0") {?>
                <div style="width:100%; padding: 5px 0;"><b style="color:#<?php echo $_SESSION['tclr'] ?>;">Lab:</b> <?php echo $lab_name.", ".$lab_area; ?></div>
                <?php } ?>
										</div>
										
								</div>
                               </div>
							  </div>
								 
	 <?php
        }
    } else {
        if(@$action == "listprescription" || @$action == "listOtherPrescription") {
            ?>
			<script>
			//removeloadingvalue1();
			</script>
			<?php
            //echo "<div class='fushebg padding5 colorwhite'><img src='img/noresult.png' style='width: 30px; height: 30px;' />Sorry No results Found</div>";
            echo '<div class="textcenter">No records to display!</div>';
        }
    }



    ?>
	 
	 
	</div>
</div>

<?php }
if($action == "sendMailPrecription") {

    include_once('../sendmail/conform_registration.php');
    $pid = $_POST['pid'];
    //User Detail
    $activateTheDoctorSel = new ManageUsers();
    $uid = $activateTheDoctorSel->listDirectQuery("select user_id from prescription where id=".$pid);
    $activateTheDoctorDetail = $activateTheDoctorSel->listDirectQuery("select * from master_user_details where id=".$uid[0]['user_id']);
    $toUserPrintMail = $activateTheDoctorDetail[0]["email"];
    $nameUserPrintMail = $activateTheDoctorDetail[0]["first_name"]." ".$activateTheDoctorDetail[0]["last_name"];

    $nameHospPrintMail = $activateTheDoctorSel ->listDirectQuery("select * from master_corporate where id=".$_SESSION['ohc_loca']);

    $toDoctorPrintMails = $activateTheDoctorSel ->listDirectQuery("select * from master_corporate_user where id=".$_SESSION['userid']);


    $toDoctorPrintMail = $toDoctorPrintMails[0]["email"];
    $nameDoctorPrintMail = $toDoctorPrintMails[0]["first_name"]." ".$activateTheDoctorDetail[0]["last_name"];

    $toDoctorPrintMail = $toDoctorPrintMails[0]['email'];
    $pdetails = $activateTheDoctorSel ->listDirectQuery("select * from prescription_detail where prescription_id=".$pid);
    $mailcontent = '<table width="650" cellspacing="0" cellpadding="0" border="0" align="center" style="border:solid 1px #dbdbd9;font-family:georgia;">
					<tbody>
					<tr>
						<td height="35" bgcolor="#199bbf" style="padding:5px;">
							<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
								<tbody>
									<tr><td style="color:#FFFFFF; font-size:15px;">Dear '.$nameUserPrintMail.',<br/>Dr. '.$nameDoctorPrintMail.' has prescribed the below medicine(s) as per prescription ID:'.$pid.'</td></tr> 
								</tbody>
							</table>
						</td>
					</tr>';

    $qCount = 0;
    $mailcontent .= '<tr><td>';
    $drugcontent = '<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
						<tr><th align="left">Drug Name</th><th>Days</th><th>Morning</th><th>Noon</th><th>Evening</th><th>Night</th><th style="width:150px;">Remarks</th></tr>';
    foreach($pdetails as $val) {
        $remarks[$qCount] = addslashes($remarks[$qCount]);
        if($val <> "") {
            $rowval = $rowid[$qCount];
            $drugcontent .= '<tr><td>'.$val['drugs_name'].'</td><td align="center">'.$val['prescribed_for_days'].'</td><td align="center">'.$val['drugmorning'].'</td><td align="center">'.$val['drugafternoon'].'</td><td align="center">'.$val['drugevening'].'</td><td align="center">'.$val['drugnight'].'</td><td style="text-align:center;">'.$val['remarks'].'</td></tr> ';
        }

    }
    $drugcontent .= '</table>';
    $mailcontent .= $drugcontent;
    $mailcontent .= '</tr></td>';
    $mailcontent .= '<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center">
								<tbody>
										<tr><td colspan="2">
											<div>
											</b>	'.$nameHospPrintMail[0]['corporate_name'].' - '.$nameHospPrintMail[0]['displayname'].' </b>
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

    $mailsend = sendmail('myHealthvalet - Prescription from Dr '.$nameDoctorPrintMail, $toUserPrintMail, $mailcontent, $toDoctorPrintMail);
    echo "Prescription has been sent to the patient's registered email address.";


}?>	