<?

ini_set("display_errors", "1");
include_once('header.php');
include_once('core/class.manageMessage.php');
include_once('core/class.manageUsers.php');
include_once('top-menu.php');
include_once('left-nav.php');
session_start();

$suser = $tbs_userid;

$tbs_role =    $_SESSION['tbs-role'];

if (isset($_GET['deid'])) {

    $suser = $_GET['deid'];
}

$obj = new ManageUsers();
$user = $obj->listDirectQuery("select * from master_user_details where id='$suser'");

$healthindex = new ManageUsers();
extract($_REQUEST);
if (isset($save)) {

    $list = $obj->listDirectQuery("UPDATE master_user_details SET heights=" . $height . " where id=" . $suser);
}
$heights = $obj->listDirectQuery("select heights from master_user_details  where id=" . $suser);

$userheight = $heights[0]['heights'];

$index = $healthindex->listDirectQuery("select  hr.obtain_index,d.type from hra_induresults hr left join doctype
 d on d.id=hr.tempid where hr.userid='" . $_SESSION['userid'] . "' and d.type='HRAG' order by cmp_date DESC limit 1");

$firsttodate = date('Y-m-d');

$previousyear = date("Y", strtotime("-1 year"));
$firstfromdate = $previousyear . "-01-01" .
    $whrfirstdtnew = "and cmp_date>='" . $firstfromdate . "' and cmp_date <= '" . $firsttodate . "'";

$hrainduresult = $healthindex->listDirectQuery("select AVG(hr.`personal_factor`) as personal_factor,AVG(hr.`family_factor`) as family_factor,AVG(hr.`diet_factor`) as diet_factor,AVG(hr.`social_factor`) as social_factor,AVG(hr.`physical_factor`) as physical_factor,AVG(hr.`stress_factor`) as stress_factor,AVG(hr.`diabetes_factor`) as diabetes_factor,AVG(hr.`cvd_factor`) as cvd_factor,AVG(hr.`ckd_factor`) as ckd_factor   from hra_induresults hr left join doctype d on d.id=hr.tempid where hr.userid='" . $_SESSION['userid'] . "'   and d.type='HRAG'");


$hindex = $healthindex->listDirectQuery("select  obtain_index,cmp_date from hra_induresults where userid='" . $_SESSION['userid'] . "' ");


foreach ($hindex as $hin) {
    $healthind[] = $hin['obtain_index'];
    $myvalue = $hin['cmp_date'];



    $gdate = new DateTime($myvalue);

    $hdates = $gdate->format('Y-m');
    $hpdate[] = "'" . date("M-Y", strtotime($hdates)) . "'";
}

$rdate = implode(",", $hpdate);

$parameters = $obj->listDirectQuery("select parameters from master_user_details where id='$suser'");


$parameter = explode(",", $parameters[0]['parameters']);


if (($pos = strpos($parameter[0], "-")) != TRUE) {

    $cholesterol = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $parameter[0] . "' and test_results!='' order by test_date asc");
    $dashboard_data1 = $obj->listDirectQuery("select test_name as title,unit from  master_test where id ='" . $parameters[0] . "'");

    if ($dashboard_data1[0]['title'] == 'Weight') {
        $titlecard1 = 'BMI';
    } else {
        $titlecard1 = $dashboard_data1[0]['title'];
        $unitcard1 = $dashboard_data1[0]['unit'];
    }
    foreach ($cholesterol as $chol) {
        //$testdate[]=$chol['test_date'];
        $testdate[] = "'" . date("M-Y", strtotime($chol['test_date'])) . "'";
        if ($parameter[0] != 284) {
            $result[] = $chol['test_results'];
        } else {
            $result[] = round($chol['test_results'] / ($userheight * $userheight) * 10000, 2);
        }
    }
} else {
    $doublevalue = explode("-", $parameter[0]);
    /*$dashboard_data1=$obj->listDirectQuery("select test_name as title from  master_test where id ='".$doublevalue[0]."'");*/
    $dashboard_data1 = $obj->listDirectQuery("select test_name as title from  master_test where id =91 ");
    $dashboard_data11 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $doublevalue[1] . "'");
    if ($dashboard_data1[0]['title'] == 'Blood Glucose') {
        $titlecard1 = 'Blood Glucose';
    } else {
        $titlecard1 = 'Blood Pressure';
    }
    $whr = "";
    if ($doublevalue[0] == 276) {
        $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('276','204') AND `test_results`!='' group by test_date order by test_date desc");
        foreach ($datecount as $count) {
            if (strpos($count['tdate'], ",") == TRUE) {
                $onedata = explode(",", $count['tdate']);
                $samedate[] = $onedata[0];
            }
        }
        $bpdate = array_unique($samedate);
        $bppdate = implode("','", $bpdate);
        $whr .= " and test_date in ('" . $bppdate . "')";

        $cholesterol = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($cholesterol as $chol) {
            //$testdate[]=$chol['test_date'];
            $testdate[] = "'" . date("M-Y", strtotime($chol['test_date'])) . "'";
            $result[] = $chol['test_results'];
        }
        $secondvalue = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($secondvalue as $sv) {
            //$testdate[]=$chol['test_date'];
            $second_testdate[] = "'" . date("M-Y", strtotime($sv['test_date'])) . "'";
            $second_result[] = $sv['test_results'];
        }
    }
    if ($doublevalue[0] == 228) {

        $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('228','91') and test_results!='' group by test_date order by test_date desc");
        foreach ($datecount as $count) {

            $onedata = explode(",", $count['tdate']);
            $samedate[] = $onedata[0];
        }
        $bpdate = array_unique($samedate);
        $bppdate = implode("','", $bpdate);
        $whr .= " and test_date in ('" . $bppdate . "')";


        //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[0]."' ".$whr." order by test_date asc limit 3";
        $cholesterol = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($cholesterol as $tri) {
            $tdate[] = $tri['test_date'];
            $testdaee[] = "'" . date("M-Y", strtotime($tri['test_date'])) . "'";
            $resuls[] = $tri['test_results'];
        }
        $bgf = array_combine($tdate, $resuls);
        //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[1]."' ".$whr." order by test_date desc limit 3";
        $secondvalue = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($secondvalue as $svv) {
            $ttdate[] = $svv['test_date'];
            $second_testdate[] = "'" . date("M-Y", strtotime($svv['test_date'])) . "'";
            $second_resul[] = $svv['test_results'];
        }

        $bgpp = array_combine($ttdate, $second_resul);
        $bgg = array_fill_keys($bpdate, 0);
        $result = array_merge($bgg, $bgf);
        ksort($result);
        $second_result = array_merge($bgg, $bgpp);
        ksort($second_result);


        $tesdatee = array_merge($ttdate, $tdate);
        $tstdatee = array_unique($tesdatee);
        asort($tstdatee);
        $tet = array_values($tstdatee);

        for ($i = 0; $i < count($tet); $i++) {
            $testdate[] = "'" . date("M-Y", strtotime($tet[$i])) . "'";
        }
    }
}

if ($parameter[1] != '') {
    if (($pos = strpos($parameter[1], "-")) != TRUE) {
        //echo "select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='".$parameter[1]."' and test_results!=''  order by test_date asc";
        // echo  "select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='".$parameter[1]."' and test_results!=''and test_date>='".$firstfromdate."' and test_date <= '".$firsttodate."'  order by test_date asc";
        //	$triglyeroid = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $parameter[1] . "' and test_results!='' and test_date>='" . $firstfromdate . "' and test_date <= '" . $firsttodate . "'  order by test_date asc");
        $dashboard_data2 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $parameter[1] . "'");

        if ($dashboard_data2[0]['title'] == 'Weight') {
            $titlecard2 = 'BMI';
        } else {
            $titlecard2 = $dashboard_data2[0]['title'];
        }
        foreach ($triglyeroid as $tri) {
            $testdatee[] = "'" . date("M-Y", strtotime($tri['test_date'])) . "'";
            if ($parameter[1] != 284) {
                $results[] = $tri['test_results'];
            } else {
                $results[] = round($tri['test_results'] / ($userheight * $userheight) * 10000, 2);
            }
        }
    } else {
        $doublevalue = explode("-", $parameter[1]);
        $dashboard_data2 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $doublevalue[0] . "'");
        $dashboard_data22 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $doublevalue[1] . "'");
        if ($dashboard_data2[0]['title'] == 'Glucose - Post Prandial') {
            $titlecard2 = 'Blood Glucose';
        } else {
            $titlecard2 = 'Blood Pressure';
        }
        $whr = "";

        if ($doublevalue[0] == 276) {

            $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('276','204') and test_results!='' group by test_date order by test_date desc");
            foreach ($datecount as $count) {
                if (strpos($count['tdate'], ",") == TRUE) {
                    $onedata = explode(",", $count['tdate']);
                    $samedate[] = $onedata[0];
                }
            }
            $bpdate = array_unique($samedate);
            $bppdate = implode("','", $bpdate);
            $whr .= " and test_date in ('" . $bppdate . "')";

            //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[0]."' ".$whr." and test_results!='' and test_date>='".$firstfromdate."' and test_date <= '".$firsttodate."' order by test_date asc limit 3";
            $triglyeroid = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' and test_date>='" . $firstfromdate . "' and test_date <= '" . $firsttodate . "' order by test_date asc limit 3");
            foreach ($triglyeroid as $tri) {
                $testdatee[] = "'" . date("M-Y", strtotime($tri['test_date'])) . "'";
                $results[] = $tri['test_results'];
            }

            $secondvalue2 = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
            foreach ($secondvalue2 as $svv) {
                //$testdate[]=$chol['test_date'];
                $second_testdate2[] = "'" . date("M-Y", strtotime($svv['test_date'])) . "'";
                $second_result2[] = $svv['test_results'];
            }
        }

        if ($doublevalue[0] == 228) {
            //echo "SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('228','91') AND `test_results` IS NOT NULL group by test_date order by test_date desc";
            $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('228','91') and test_results!='' group by test_date order by test_date desc");
            foreach ($datecount as $count) {
                if (strpos($count['tdate'], ",") == TRUE) {
                    $onedata = explode(",", $count['tdate']);
                    $samedate[] = $onedata[0];
                }
            }
            $bpdate = array_unique($samedate);
            $bppdate = implode("','", $bpdate);
            $whr .= " and test_date in ('" . $bppdate . "')";


            //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[0]."' ".$whr." order by test_date asc limit 3";
            $triglyeroid = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' and test_results!='' order by test_date asc limit 3");
            foreach ($triglyeroid as $tri) {
                $tdate[] = $tri['test_date'];
                $testdaee[] = "'" . date("M-Y", strtotime($tri['test_date'])) . "'";
                $resuls[] = $tri['test_results'];
            }
            $bgf = array_combine($tdate, $resuls);

            $secondvalue2 = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' and test_results!='' order by test_date asc limit 3");
            foreach ($secondvalue2 as $svv) {
                $ttdate[] = $svv['test_date'];
                $second_testdate2[] = "'" . date("M-Y", strtotime($svv['test_date'])) . "'";
                $second_resul2[] = $svv['test_results'];
            }

            $bgpp = array_combine($ttdate, $second_resul2);
            $bgg = array_fill_keys($bpdate, 0);
            $results = array_merge($bgg, $bgf);
            ksort($results);
            $second_result2 = array_merge($bgg, $bgpp);
            ksort($second_result2);

            $tesdatee = array_merge($ttdate, $tdate);
            $tstdatee = array_unique($tesdatee);
            asort($tstdatee);
            $tet = array_values($tstdatee);

            for ($i = 0; $i < count($tet); $i++) {
                $testdatee[] = "'" . date("M-Y", strtotime($tet[$i])) . "'";
            }
        }
        // var_dump($dashboard_data1);
        // exit();

        //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[0]."' ".$whr." order by test_date asc limit 3";


    }
}
if (($pos = strpos($parameter[2], "-")) != TRUE) {
    $random = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $parameter[2] . "' and test_results!='' order by test_date asc");
    $dashboard_data3 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $parameter[2] . "'");

    if ($dashboard_data3[0]['title'] == 'Weight') {
        $titlecard3 = 'BMI';
    } else {
        $titlecard3 = $dashboard_data3[0]['title'];
    }
    foreach ($random as $ran) {
        $testdateee[] = "'" . date("M-Y", strtotime($ran['test_date'])) . "'";
        if ($parameter[2] != 284) {
            $resultss[] = $ran['test_results'];
        } else {
            $resultss[] = round($ran['test_results'] / ($userheight * $userheight) * 10000, 2);
        }
    }
} else {
    $doublevalue = explode("-", $parameter[2]);
    $dashboard_data3 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $doublevalue[0] . "'");
    $dashboard_data33 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $doublevalue[1] . "'");
    if ($dashboard_data3[0]['title'] == 'Glucose - Post Prandial') {
        $titlecard3 = 'Blood Glucose';
    } else {
        $titlecard3 = 'Blood Pressure';
    }
    $whr = "";
    if ($doublevalue[0] == 276) {
        //echo "SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('276','204') AND `test_results` IS NOT NULL group by test_date order by test_date desc";
        $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('276','204') and test_results!='' group by test_date order by test_date desc");
        foreach ($datecount as $count) {
            if (strpos($count['tdate'], ",") == TRUE) {
                $onedata = explode(",", $count['tdate']);
                $samedate[] = $onedata[0];
            }
        }
        $bpdate = array_unique($samedate);
        $bppdate = implode("','", $bpdate);
        $whr .= " and test_date in ('" . $bppdate . "')";


        $random = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($random as $ran) {
            $testdateee[] = "'" . date("M-Y", strtotime($ran['test_date'])) . "'";
            $resultss[] = $ran['test_results'];
        }
        $secondvalue3 = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($secondvalue3 as $svvv) {
            //$testdate[]=$chol['test_date'];
            $second_testdate3[] = "'" . date("M-Y", strtotime($svvv['test_date'])) . "'";
            $second_result3[] = $svvv['test_results'];
        }
    }



    if ($doublevalue[0] == 228) {

        $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('228','91') and test_results!='' group by test_date order by test_date desc");
        foreach ($datecount as $count) {
            if (strpos($count['tdate'], ",") == TRUE) {
                $onedata = explode(",", $count['tdate']);
                $samedate[] = $onedata[0];
            }
        }
        $bpdate = array_unique($samedate);
        $bppdate = implode("','", $bpdate);
        $whr .= " and test_date in ('" . $bppdate . "')";


        //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[0]."' ".$whr." order by test_date asc limit 3";
        $random = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($random as $tri) {
            $tdate[] = $tri['test_date'];
            $testdaee[] = "'" . date("M-Y", strtotime($tri['test_date'])) . "'";
            $resuls[] = $tri['test_results'];
        }
        $bgf = array_combine($tdate, $resuls);

        $secondvalue3 = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($secondvalue3 as $svv) {
            $ttdate[] = $svv['test_date'];
            $second_testdate2[] = "'" . date("M-Y", strtotime($svv['test_date'])) . "'";
            $second_resul2[] = $svv['test_results'];
        }

        $bgpp = array_combine($ttdate, $second_resul2);
        $bgg = array_fill_keys($bpdate, 0);
        $resultss = array_merge($bgg, $bgf);
        ksort($resultss);
        $second_result3 = array_merge($bgg, $bgpp);
        ksort($second_result3);
        $tesdatee = array_merge($ttdate, $tdate);
        $tstdatee = array_unique($tesdatee);
        asort($tstdatee);
        $tet = array_values($tstdatee);

        for ($i = 0; $i < count($tet); $i++) {
            $testdateee[] = "'" . date("M-Y", strtotime($tet[$i])) . "'";
        }
    }



    //echo "select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='".$doublevalue[0]."' ".$whr." order by test_date desc limit 3";



}

if (($pos = strpos($parameter[3], "-")) != TRUE) {

    $systolic = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $parameter[3] . "' and test_results!='' order by test_date asc");
    $dashboard_data4 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $parameter[3] . "'");

    if ($dashboard_data4[0]['title'] == 'Weight') {
        $titlecard4 = 'BMI';
    } else {
        $titlecard4 = $dashboard_data4[0]['title'];
    }
    foreach ($systolic as $pul) {
        $testdateeee[] = "'" . date("M-Y", strtotime($pul['test_date'])) . "'";
        if ($parameter[3] != 284) {
            $resultsss[] = $pul['test_results'];
        } else {

            $resultsss[] = round($pul['test_results'] / ($userheight * $userheight) * 10000, 2);
        }
    }
} else {
    $doublevalue = explode("-", $parameter[3]);
    $dashboard_data4 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $doublevalue[0] . "'");
    $dashboard_data44 = $obj->listDirectQuery("select test_name as title from  master_test where id ='" . $doublevalue[1] . "'");
    if ($dashboard_data4[0]['title'] == 'Glucose - Post Prandial') {
        $titlecard4 = 'Blood Glucose';
    } else {
        $titlecard4 = 'Blood Pressure';
    }
    $whr = "";
    if ($doublevalue[0] == 276) {
        $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('276','204') and test_results!='' group by test_date order by test_date desc");
        foreach ($datecount as $count) {
            if (strpos($count['tdate'], ",") == TRUE) {
                $onedata = explode(",", $count['tdate']);
                $samedate[] = $onedata[0];
            }
        }
        $bpdate = array_unique($samedate);
        $bppdate = implode("','", $bpdate);
        //$whr.=" and test_date in ('".$bppdate."')";



        $systolic = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($systolic as $pul) {
            $testdateeee[] = "'" . date("M-Y", strtotime($pul['test_date'])) . "'";
            $resultsss[] = $pul['test_results'];
        }
        $secondvalue4 = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser'and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($secondvalue4 as $sv4) {
            //$testdate[]=$chol['test_date'];
            $second_testdate4[] = "'" . date("M-Y", strtotime($sv4['test_date'])) . "'";
            $second_result4[] = $sv4['test_results'];
        }
    }

    if ($doublevalue[0] == 228) {

        $datecount = $obj->listDirectQuery("SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('228','91') and test_results!='' group by test_date order by test_date desc");
        foreach ($datecount as $count) {

            $onedata = explode(",", $count['tdate']);
            $samedate[] = $onedata[0];
        }
        $bpdate = array_unique($samedate);
        $bppdate = implode("','", $bpdate);
        $whr .= " and test_date in ('" . $bppdate . "')";


        //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[0]."' ".$whr." order by test_date asc limit 3";
        $systolic = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($systolic as $tri) {
            $tdate[] = $tri['test_date'];
            $testdaee[] = "'" . date("M-Y", strtotime($tri['test_date'])) . "'";
            $resuls[] = $tri['test_results'];
        }
        $bgf = array_combine($tdate, $resuls);

        $secondvalue4 = $obj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
        foreach ($secondvalue4 as $svv) {
            $ttdate[] = $svv['test_date'];
            $second_testdate2[] = "'" . date("M-Y", strtotime($svv['test_date'])) . "'";
            $second_result4[] = $svv['test_results'];
        }

        $bgpp = array_combine($ttdate, $second_result4);
        $bgg = array_fill_keys($bpdate, 0);
        $resultsss = array_merge($bgg, $bgf);
        ksort($resultsss);
        $second_result4 = array_merge($bgg, $bgpp);

        ksort($second_result4);
        $tesdatee = array_merge($ttdate, $tdate);
        $tstdatee = array_unique($tesdatee);
        asort($tstdatee);
        $tet = array_values($tstdatee);

        for ($i = 0; $i < count($tet); $i++) {
            $testdateeee[] = "'" . date("M-Y", strtotime($tet[$i])) . "'";
        }
    }
}



?>
<!-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/modules/treemap.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script> -->

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/modules/treemap.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script>
    $(document).ready(function() {
        $('.slide_con').css('display', 'none');
        search();
    });


    function healthcondition(pid) {

        $.fn.colorbox({
            href: "userpopup.php?pid=" + pid,
            iframe: true,
            open: true,
            innerWidth: 500,
            innerHeight: 500,
            onOpen: function() {
                $('#cboxContent').removeClass("cboxContentCls");
            }
        });
    }

    $(function() {

        var chart = Highcharts.chart('con1', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<? echo $titlecard1; ?>'
            },

            xAxis: {
                type: 'datetime',
                categories: [
                    <?
                    echo implode(",", $testdate);
                    ?>
                ],
                plotBands: [{ // visualize the weekend
                    from: 3.5,
                    to: 4.5,
                    color: 'rgba(216, 200, 180, .6)'
                }]
            },
            yAxis: {
                /* max: 100,
        min: 0,
		 tickInterval: 25,*/
                title: {
                    text: ' '
                }


            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            credits: {
                enabled: true
            },
            plotOptions: {
                spline: {
                    fillOpacity: 0.8
                }
            },
            exporting: {
                buttons: {
                    contextButton: {
                        enabled: false
                    }
                }
            },
            series: [{
                    name: '<? echo $dashboard_data1[0]['title']; ?>',

                    data: [<?
                            echo implode(",", $result);
                            ?>],
                    showInLegend: false

                }
                /*,
                    
                    {
                		 name: '<? echo $dashboard_data11[0]['title']; ?>',
                		  
                        
                        data: [  <?
                                    echo implode(",", $second_result);
                                    ?>],
                		   
                showInLegend:false 

                    }*/
            ]
        }, function(chart) { // on complete
            <? if (!isset($second_result) && !isset($result) && $dashboard_data1[0]['title'] != '') { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <? } ?>
        });


        /*  var myChart = Highcharts.chart('con1', {
              chart: {
                  type: 'spline'
              },
              title: {
                  text: 'Blood Sugar'
              },
              xAxis: {
                  categories: [<?
                                // echo implode(",",$testdate);		   
                                ?>]
              },
              yAxis: {
                  title: {
                      text: ''
                  }
              },
              series: [{
                  name: 'Blood Sugar',
                  data: [<?
                            // echo implode(",",$result);		   
                            ?>]
              }]
          }); */
    });
    $(function() {
        var chart = Highcharts.chart('con2', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<? echo $titlecard2; ?>'
            },

            xAxis: {
                categories: [
                    <?
                    echo implode(",", $testdatee);
                    ?>
                ],
                plotBands: [{ // visualize the weekend
                    from: 3.5,
                    to: 4.5,
                    color: 'rgba(216, 200, 180, .6)'
                }]
            },
            yAxis: {
                /* max: 100,
        min: 0,
		 tickInterval: 25,*/
                title: {
                    text: ' '
                }


            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                spline: {
                    fillOpacity: 1.0
                }
            },
            exporting: {
                buttons: {
                    contextButton: {
                        enabled: false
                    }
                }
            },
            series: [{
                    name: '<? echo $dashboard_data2[0]['title']; ?>',

                    data: [<?
                            echo implode(",", $results);
                            ?>],
                    showInLegend: false

                },

                {
                    name: '<? echo $dashboard_data22[0]['title']; ?>',
                    data: [<?
                            echo implode(",", $second_result2);
                            ?>],

                    showInLegend: false

                }
            ]

        }, function(chart) { // on complete
            <? if (!isset($second_result2) && !isset($results) && $dashboard_data2[0]['title'] != '') { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <? } ?>
        });
    });
    $(function() {
        Highcharts.chart('con3', {
            colors: ['rgb(3, 93, 150, .8)', 'rgba(217, 218, 150, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<? echo $titlecard3; ?>'
            },

            xAxis: {
                categories: [
                    <?
                    echo implode(",", $testdateee);
                    ?>
                ],
                plotBands: [{ // visualize the weekend
                    from: 3.5,
                    to: 4.5,
                    color: 'rgba(216, 200, 180, .6)'
                }]
            },
            yAxis: {
                /* max: 100,
        min: 0,
		 tickInterval: 25,*/
                title: {
                    text: ' '
                }


            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                spline: {
                    fillOpacity: 0.8
                }
            },
            exporting: {
                buttons: {
                    contextButton: {
                        enabled: false
                    }
                }
            },
            series: [{
                    name: '<? echo $dashboard_data3[0]['title']; ?>',

                    data: [<?
                            echo implode(",", $resultss);
                            ?>],
                    showInLegend: false

                },

                {
                    name: '<? echo $dashboard_data33[0]['title']; ?>',


                    data: [<?
                            echo implode(",", $second_result3);
                            ?>],

                    showInLegend: false

                }
            ]
        }, function(chart) { // on complete
            <? if (!isset($second_result3) && !isset($resultss) && $dashboard_data3[0]['title'] != '') { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <? } ?>
        });
    });
    $(function() {
        var chart = Highcharts.chart('con4', {
            colors: ['rgb(241, 211, 182, .8)', 'rgba(155, 205, 195, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<? echo $titlecard4; ?>'
            },

            xAxis: {
                categories: [
                    <?
                    echo implode(",", $testdateeee);
                    ?>
                ],

                plotBands: [{ // visualize the weekend
                    from: 3.5,
                    to: 4.5,
                    color: 'rgba(216, 200, 180, .6)'
                }]
            },
            yAxis: {
                /* max: 100,
        min: 0,
		 tickInterval: 25,*/
                title: {
                    text: ' '
                }


            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                spline: {
                    fillOpacity: 0.8
                }
            },
            exporting: {
                buttons: {
                    contextButton: {
                        enabled: false
                    }
                }
            },
            series: [{
                    name: '<? echo $dashboard_data4[0]['title']; ?>',
                    color: '#82e7b1',

                    data: [<?
                            echo implode(",", $resultsss);
                            ?>],

                    showInLegend: false

                },

                {
                    name: '<? echo $dashboard_data44[0]['title']; ?>',


                    data: [<?
                            echo implode(",", $second_result4);
                            ?>],

                    showInLegend: false

                }
            ]
        }, function(chart) { // on complete
            <? if (!isset($second_result4) && !isset($resultsss) && $dashboard_data4[0]['title'] != '') { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <? } ?>
        });
    });
</script>
<?php require_once('top-menu.php') ?>

<style>
    .boxshadow {
        box-shadow: 0px 0px 10px #aaa;
        background-color: white;
    }
</style>

<div class="container-fluid" id="content">

    <?php
    require_once('left-nav.php');
    include_once($rootSub . 'core/class.manageUsers.php');
    $dataobj = new ManageUsers();

    ?>

    <div id="main" style="">
        <div class="Top-Strip">
            <div class="col-xl-8 col-lg-8 col-md-8 float-left">
                <h5 style="font-weight:bold;color:#<? echo $_SESSION['tclr'] ?>;">Health Trend</h5>
            </div>

        </div>

        <div class="col-xl-12 col-lg-12 col-md-12  p-2 float-left">






            <!-- Health Parameters-->
            <div class="" style="width:100%; height: 350px;">

                <div class="col-xl-2 col-lg-2 col-md-2 float-left p-2">
                    <i class="icon_10 fa fa-calendar icon_size" aria-hidden="true"></i>
                    <input placeholder="From Date" title="Entry Date" type="text" id="fromdate" class="datepick" onchange="search()" style="padding: 3px 5px; margin-left:5px; height: 26px !important; width:120px;" />
                </div>

                <div class="col-xl-2 col-lg-2 col-md-2 float-left p-2">
                    <i class="icon_10 fa fa-calendar icon_size" aria-hidden="true"></i>
                    <input placeholder="To Date" title="Entry Date" type="text" id="todate" class="datepick" onchange="search()" style="padding: 3px 5px; height: 26px !important; margin-left:5px; width:120px;" />
                </div>
                <?php
                $userparameters = explode(",", $parameters[0]['parameters']);
                $userparamarray = array();
                foreach ($userparameters as $singleparameter) {
                    if (strpos($singleparameter, "-") !== false) {
                        $singleparameter = str_replace("-", "", $singleparameter);
                        array_push($userparamarray, $singleparameter);
                    } else {
                        array_push($userparamarray, $singleparameter);
                    }
                }
                $userparamarray = implode(",", $userparamarray);
                ?>
                <div class="col-xl-4 col-lg-4 col-md-4 float-left p-2">
                    <i class="icon_10 fa fa-flask icon_size" aria-hidden="true"></i>
                    <select title="test" name="test" id="test" onchange="search()" class='floatleft icon_90 select2-me input-large depvalue'>
                        <?
                        $liststest = $obj->listDirectQuery("select id,test_name from master_test where id ='" . $parameter[0] . "'");
                        foreach ($liststest as $singletest) {
                        ?>
                            <!--<option value="<? php/* echo $singletest['id'];*/ ?>" ><?php /*echo $singletest['test_name'];*/ ?></option>-->
                        <?php     }
                        ?>
                        <?
                        $liststest = $obj->listDirectQuery("select id,test_name from master_test");
                        /*echo '<option value="" selected>'."Select Parameter".'</option>"';*/
                        echo '<option value="91,228,95" selected>' . "Blood Glucose" . '</option>"';
                        echo '<option value="229">' . "Blood Glucose - Random" . '</option>"';
                        echo '<option value="276,204">' . "Blood Pressure" . '</option>"';
                        echo '<option value="96">' . "Blood Urea" . '</option>"';
                        echo '<option value="97">' . "BUN" . '</option>"';
                        echo '<option value="137">' . "Cortisol" . '</option>"';
                        echo '<option value="246">' . "Creatinine" . '</option>"';
                        echo '<option value="55,241,243,257">' . "Differential Count" . '</option>"';
                        echo '<option value="58-152">' . "ESR" . '</option>"';
                        echo '<option value="133">' . "Estradiol" . '</option>"';
                        echo '<option value="52">' . "Haemoglobin" . '</option>"';
                        echo '<option value="138">' . "LH" . '</option>"';
                        echo '<option value="100,101,102,104,103">' . "Lipid Profile" . '</option>"';
                        echo '<option value="105,110,50">' . "Liver Function Test (LFT) - Bilirubin" . '</option>"';
                        echo '<option value="106,111,107">' . "Liver Function Test (LFT) - Protein" . '</option>"';
                        echo '<option value="56">' . "Platelet Count" . '</option>"';
                        echo '<option value="132">' . "Progesterone" . '</option>"';
                        echo '<option value="51">' . "RBC" . '</option>"';
                        echo '<option value="139">' . "Testosterone" . '</option>"';
                        echo '<option value="128,135,136">' . "Thyroid Profile" . '</option>"';
                        echo '<option value="101">' . "Triglyceride" . '</option>"';
                        echo '<option value="99">' . "Uric Acid" . '</option>"';
                        echo '<option value="54" >' . "WBC" . '</option>"';

                        //echo '<option value="58">'."ESR (30min)".'</option>"';
                        //echo '<option value="152">'."ESR (60min)".'</option>"'; 
                        // echo '<option value="276">'."Blood Pressure - Systolic".'</option>"'; 
                        // echo '<option value="204">'."Blood Pressure - Diastolic".'</option>"';
                        // echo '<option value="91">'."Blood Glucose - Fasting".'</option>"';
                        // echo '<option value="228">'."Blood Glucose - Post Prandial".'</option>"';
                        // echo '<option value="229">'."Blood Glucose - Random".'</option>"';
                        // echo '<option value="95">'."HbA1C".'</option>"';
                        // echo '<option value="50">'."Bilirubin - Indirect".'</option>"';
                        // echo '<option value="105">'."Total Bilirubin".'</option>"';
                        // echo '<option value="110">'."Bilirubin - Direct".'</option>"';

                        ?>
                    </select>
                </div>
                <? if ($tbs_role == "4" && !empty($_SESSION['ohc_doc']) || $tbs_role == "1"  || $tbs_role == "2"  || $tbs_role == "3") {
                    //$dis=($tbs_role=="1")?'disabled="disabled"':"";
                    //if( $tbs_role=="1"  ) {	
                ?>
                    <div class=" col-xl-4 col-lg-4 col-md-4 float-left p-2">

                        <select style="width:95% !important;" name="search" id="susers" class='select2-me input-large' onChange="search()" <? echo $dis; ?>>
                            <option value=""> Select other users</option>
                            <?php
                            if (isset($_GET['presid'])) {
                                $presid = $_GET['presid'];
                                $PrescriptionChk = new ManageMessage();
                                $PrescriptionChks = $PrescriptionChk->listDirectQuery("SELECT user_id from prescription where id='$presid'");
                                if ($PrescriptionChks != 0) {
                                    foreach ($PrescriptionChks as $PrescriptionChksL) {
                                        $presuser_id = $PrescriptionChksL['user_id'];
                                    }
                                }
                            }

                            $AppointmentDetail = new ManageMessage();
                            if ($tbs_role == "1") {
                                $Appointment = $AppointmentDetail->listDirectQuery("SELECT allt.* FROM (SELECT id,first_name,last_name,user_id,mob_num from master_user_details where id=" . $suser . " union (SELECT id,first_name,last_name,user_id,mob_num from master_user_details where dependent_of=" . $suser . " order by first_name) union (SELECT mud.id,mud.first_name,mud.last_name,user_id,mob_num from userdependanceaccessrights u left outer join  master_user_details mud on mud.id=u.accountof_user_id where accessto_user_id=" . $suser . " and contmon_view=1 order by first_name)) allt 
															ORDER BY allt.first_name ASC");
                            } else if ($tbs_role == "2") {
                                $Appointment = $AppointmentDetail->listDirectQuery("select allt.userdetails,mud.* from((select user_id as userdetails,doctor_id as doctorid from prescription where doctor_id='$suser') UNION ALL (select accountof_user_id as userdetails,accessto_user_id as doctorid from userdoctoraccessrights where accessto_user_id='$suser' and contmon='1') UNION ALL (select user_id as userdetails,added_by as doctorid from case_file where added_by='$suser' and added_role='$tbs_role') UNION ALL (select id as userdetails,insertedby as doctorid from master_user_details where insertedby='$suser' and insertedrole='$tbs_role') UNION ALL (select user_id as userdetails,doctor_id as doctorid from prescribed_tests where doctor_id='$suser'  GROUP BY user_id,doctor_id)) allt left outer join master_user_details mud on mud.id=allt.userdetails where mud.id IS NOT NULL AND allt.doctorid='$suser' group by allt.userdetails ORDER BY mud.first_name");
                            } else if ($tbs_role == "3") {
                                $mdid = array();
                                $Doctorallist = new ManageUsers();
                                $Doctorallists = $Doctorallist->listDirectQuery("SELECT md.id as mdid FROM doctorconsultation dc left outer join master_hcsp mh on mh.id=dc.hcsp_id left outer join master_doctor md on md.id=dc.user_id WHERE dc.hcsp_id='$sessionlocation_id' AND dc.isapproved=1 GROUP BY dc.user_id,md.id ORDER BY md.first_name");
                                $Doctorallist = null;
                                if ($Doctorallists != 0) {
                                    foreach ($Doctorallists as $DoctorallistsL) {
                                        array_push($mdid, $DoctorallistsL['mdid']);
                                    }
                                }
                                if (count($mdid) == "0") {
                                    array_push($mdid, "-1");
                                    $mdid = implode(', ', $mdid);
                                } else {
                                    $mdid = implode(', ', $mdid);
                                }

                                $Appointment = $AppointmentDetail->listDirectQuery("select allt.userdetails,mud.* from ((select user_id as userdetails,doctor_id as doctorid from prescription where doctor_id IN($mdid) AND master_hcsp_user_id='$sessionlocation_id') UNION ALL (select user_id as userdetails,doctor_id as doctorid from case_file where doctor_id IN($mdid) AND hosp_id='$sessionlocation_id') UNION ALL (select id as userdetails,insertedby as doctorid from master_user_details where insertedby='$sessionlocation_id' and insertedrole='3') UNION ALL (select user_id as userdetails,doctor_id as doctorid from prescribed_tests where doctor_id IN($mdid) AND hosp_id='$sessionlocation_id'  GROUP BY user_id,doctor_id)) allt left  outer join master_user_details mud on mud.id=allt.userdetails where mud.id IS NOT NULL
															GROUP BY allt.userdetails
															ORDER BY mud.first_name");
                            } else if ($tbs_role == "4") {
                                $sl = "SELECT mud.id, mud.first_name, mud.last_name, cum.emp_id FROM corporate_user_mapping cum LEFT OUTER JOIN master_user_details mud ON mud.id = cum.r_user_id WHERE cum.location='" . $_SESSION['ohc_loca'] . "' AND cum.isactive='1' AND (cum.emp_type='2'  or cum.emp_type='1')	GROUP BY mud.id	ORDER BY mud.first_name";
                                $Appointment = $AppointmentDetail->listDirectQuery($sl);

                                /*	$Appointment=$AppointmentDetail->listDirectQuery("SELECT mud.*
															FROM corporate_user_mapping cum
															LEFT OUTER JOIN master_user_details mud on mud.id=cum.r_user_id
															LEFT OUTER JOIN doctype pincode ON pincode.id=mud.pincode
															LEFT OUTER JOIN doctype area ON area.id=pincode.parent_id
															LEFT OUTER JOIN doctype city ON city.id=area.parent_id
															LEFT OUTER JOIN doctype state ON state.id=city.parent_id
															LEFT OUTER JOIN doctype country ON country.id=state.parent_id
															WHERE cum.r_corporate_id='$sessionlocation_id' AND cum.isactive='1' GROUP BY mud.user_id ORDER BY mud.first_name ASC");*/
                            }
                            $AppointmentDetail = null;
                            if ($Appointment != 0) {

                                $t = 1;
                                foreach ($Appointment as $listAppointments) {
                                    $Appids = $listAppointments['id'];
                                    $first_name = $listAppointments['first_name'];
                                    $last_name = $listAppointments['last_name'];
                                    $user_id = $listAppointments['user_id'];
                                    $mob_num = $listAppointments['mob_num'];

                                    if (isset($listAppointments['emp_id'])) {
                                        $emp_id = $listAppointments['emp_id'];
                                    }
                                    if ($listAppointments['id'] == $suser && isset($suser)) {
                                        $sel = "selected='selected'";
                                        $seluser = $listAppointments['id'];
                                    } else if ($listAppointments['id'] == $presuser_id && isset($_GET['presid'])) {
                                        $sel = "selected='selected'";
                                        $seluser = $listAppointments['id'];
                                    } else {
                                        $sel = "";
                                        if ($t == '1') {
                                            $seluser = $listAppointments['id'];
                                        }
                                    }
                                    if ($tbs_role != '4') {
                                        echo '<option value="' . $Appids . '" ' . $sel . '>' . ucfirst($first_name) . '  ' . ucfirst($last_name) . ' - ' . $user_id . ' / ' . $mob_num . '</option>';
                                    } else {
                                        echo '<option value="' . $Appids . '" ' . $sel . '>' . ucfirst($first_name) . '  ' . ucfirst($last_name) . ' / ' . $emp_id . '</option>';
                                    }

                                    $t++;
                                }
                            }

                            ?>
                        </select>
                        <?php //} 
                        ?>
                    </div>

                    <?php /*if($tbs_role=="4"){ ?>
									<div class="floatright" style="margin-right:20px;">
										<select name="doctorhospvalue" id="doctorhospvalue" class='doctorhospvalue select2-me input-medium texttransformcap' onChange="searchfortest('hosp_id',this.value, '', '', '', '')">
											   <?php
													$prescriptionHospFiltersObj = new ManageMessage();
													$prescriptionHospFilters=$prescriptionHospFiltersObj->listDirectQuery("SELECT mh.id AS hcspid, mh.`hcsp_name` AS hospitalname, area.doctype AS areaName FROM  `master_hcsp` mh join corporate_empanelment ce on mh.id=ce.link_id LEFT OUTER JOIN  `doctype` pincode ON pincode.id = mh.pincode LEFT OUTER JOIN  `doctype` area ON area.id = pincode.parent_id	 where ce.type='2' and ce.corp_id='$tbs_parentid'");
													$prescriptionHospFiltersObj = null;
													if($prescriptionHospFilters!=0){
														foreach($prescriptionHospFilters as $prescriptionHospFilter){ 
															$sel="";
															if($hcsp==$prescriptionHospFilter['id']){
																$sel=" selected='selected'";
															}
															echo '<option value="'.$prescriptionHospFilter['id'].'" '.$sel.'>'.$prescriptionHospFilter['hospitalname'].', '.$prescriptionHospFilter['areaName'].'</option>';	
														}
													}
												?>
										</select>
									</div>
								<?php }*/
                    if (!isset($_GET['case'])) {
                    } else if ($tbs_role == "2" || $tbs_role == "4") {
                    } else { ?>
                        <div class="floatright" style="margin-right:20px;">
                            <select name="doctorhospvalue" id="doctorhospvalue" class='doctorhospvalue select2-me input-medium texttransformcap' onChange="searchfortest('doctor_id',this.value, '', '', '', '')">
                                <?php
                                if ($tbs_role == "3") {
                                    $GtDoctors = new ManageMessage();
                                    $GtDoctorsL = $GtDoctors->listDirectQuery("SELECT md.* FROM doctorconsultation dc LEFT OUTER JOIN master_hcsp mh ON mh.id = dc.hcsp_id LEFT OUTER JOIN master_doctor md ON md.id = dc.user_id WHERE dc.hcsp_id='$sessionlocation_id' AND dc.isapproved=1 GROUP BY dc.user_id ORDER BY md.first_name");
                                    if ($GtDoctorsL != 0) {
                                        foreach ($GtDoctorsL as $GtDoctorsLs) {
                                            $gtfirst_name = ucfirst($GtDoctorsLs['first_name']);
                                            if (strstr($gtfirst_name, 'Dr')) {
                                                $concat = "";
                                            } else {
                                                $concat = "Dr ";
                                            }
                                            echo '<option value="' . $GtDoctorsLs['id'] . '">' . $concat . ' ' . ucfirst($GtDoctorsLs['first_name']) . ' ' . ucfirst($GtDoctorsLs['last_name']) . '</option>';
                                        }
                                    }
                                } else {
                                    echo '<option value="">All</option>';
                                    $GtDoctors = new ManageMessage();
                                    $GtDoctorsL = $GtDoctors->listDirectQuery("SELECT md.first_name,md.last_name,md.id FROM master_doctor md left outer join prescription p on p.doctor_id=md.id where ifnull(isactive,0)=1 and p.user_id IN($groupdependants) union (SELECT first_name,last_name,id FROM master_doctor where created_by IN($groupdependants) and doctortype_doctype_static_id=5) union (SELECT md.first_name,md.last_name,md.id FROM master_doctor md left outer join hospitalization_details hd on hd.doctor_id=md.id where ifnull(isactive,0)=1 and hd.user_id IN($groupdependants))");
                                    if ($GtDoctorsL != 0) {
                                        foreach ($GtDoctorsL as $GtDoctorsLs) {
                                            $gtfirst_name = ucfirst($GtDoctorsLs['first_name']);
                                            if (strstr($gtfirst_name, 'Dr')) {
                                                $concat = "";
                                            } else {
                                                $concat = "Dr ";
                                            }
                                            echo '<option value="' . $GtDoctorsLs['id'] . '">' . $concat . ' ' . ucfirst($GtDoctorsLs['first_name']) . ' ' . ucfirst($GtDoctorsLs['last_name']) . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    <?php } ?>

                <? } ?>
                <?php

                if ($parameters[0]['parameters'] == '') { ?>

                    <div class="w-100 p-2 float-left font-weight-bold" style="margin-top: 20%;">
                        <p class="w-100" style="font-size:16px; text-align:center;">Click <a href="userdashboard-data.php?userid=<? echo $suser; ?>">Change Parameters</a> to select the health parameters to be displayed here</p>
                    </div>

                <?php } else { ?>

                    <div style="width:100%; margin :1% 0 !important; float:left;" class="slide_con">
                        <div style="width:66%;padding:0 1%;float:left;">
                            <div id="con1" style="min-width: 250px; height: 300px; margin: 0 auto; ">
                            </div>
                        </div>
                        <div style="width:33%; padding:0 1%;float:left;">
                            <table cellpadding="5" cellspacing="5" width="90%" align="center" border="1" class="table tableborder ajaxemp table-striped">
                                <tr>
                                    <td colspan=2 align=center><b><?php echo $titlecard1; ?> (<?php echo $unitcard1; ?>)</b></td>
                                </tr>
                                <tr class="bold" style="color:#fff;background-color:#<? echo $_SESSION['tclr'] ?>;">
                                    <th width=50% style="text-align:center;">Date</th>
                                    <th width=50% style="text-align:center;">Value</th>
                                </tr>
                                <?php
                                foreach ($cholesterol as $chart_details) {
                                    $testdate = date('d-m-Y', strtotime($chart_details['test_date']));
                                ?>
                                    <tr>
                                        <td>
                                            <center><?php echo $testdate; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $chart_details['test_results']; ?></center>
                                        </td>
                                    </tr>
                                <?php } // }
                                ?>
                            </table>
                        </div>
                    </div>
                    <!-- <div style="width:49%; margin :0 .5% !important; float:left;">
                        <div style="width:98%;padding:0 1%;float:left;">-
                            <div id="con2" style="min-width: 250px; height: 190px; margin: 0 auto; ">
                            </div>
                        </div>
                    </div>


                    <div style="width:49%; margin :0 .5% !important; float:left;">
                        <div style="width:98%;padding:0 1%;float:left;">
                            <div id="con3" style="min-width: 250px; height: 190px; margin: 0 auto; ">
                            </div>
                        </div>
                    </div>


                    <div style="width:49%; margin :0 .5% !important; float:left;">
                        <div style="width:98%;padding:0 1%;float:left;">
                            <div id="con4" style="min-width: 250px; height: 190px; margin: 0 auto; ">
                            </div>
                        </div>
                    </div> -->
                    <div style="width:100%; margin :0 .5% !important; float:left; display:none;" class="ajax_con"></div>
                <?php } ?>
            </div>

            <?
            $medcond = $obj->listDirectQuery("select is_active from  med_condition_map where user_id='$suser'");

            ?>

            <script>
                function search() {
                    var fromdate = $("#fromdate").val();
                    var todate = $("#todate").val();
                    var parameter = $("#test").val();
                    var test_name = $("#test option:selected").text();
                    var nam = $('#nam').val();
                    <? if ($_SESSION['tbs-role'] == "3") { ?>
                        var huser = $('#huser').val();
                    <? } else { ?>
                        var huser = "0";
                    <? } ?>

                    <? if ($_SESSION['tbs-role'] == "1") { ?>
                        var sus = $('#susers').val();
                        if (sus == null) {
                            var sus = <? echo $suser ?>;
                        }
                    <? } else { ?>
                        var sus = "0";
                    <? } ?>

                    var action = "empttype";
                    var dataString = {
                        action: action,
                        fromdate: fromdate,
                        todate: todate,
                        parameter: parameter,
                        test_name: test_name,
                        huser: huser,
                        sus: sus
                    };

                    $.ajax({
                        type: "POST",
                        url: "ajax/ajax_user_report.php",
                        data: dataString,
                        success: function(data) {
                            console.log(data);
                            $('.slide_con').css('display', 'none');
                            $('.ajax_con').css('display', 'block');
                            $(".ajax_con").html(data);
                        },
                        error: function(html) {
                            $('.ajax_con').css('display', 'none');
                        }
                    });
                }
            </script>



        </div>
    </div>