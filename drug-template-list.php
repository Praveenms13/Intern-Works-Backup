<?php include "../session.php";
include_once "../core/class.manageSettings.php";
include_once "../core/class.manageMessage.php";
$today = date(Y - m - d);
extract($_REQUEST);
$dataobj = new ManageMessage();
$sus = $_POST["sus"];
$user_id = $_SESSION["userid"];
$parameter = $_POST["parameter"];
// echo "Parameter: " . $parameter . "<br>";
// print_r($_POST);
$fromdate = date("Y-m-d", strtotime(str_replace("/", "-", $_POST["fromdate"])));
$todate = date("Y-m-d", strtotime(str_replace("/", "-", $_POST["todate"])));
$whr = "";
if ($todate != "1970-01-01") {
    $whr .= " and test_date<='$todate'";
}
if ($fromdate != "1970-01-01") {
    $whr .= " and test_date>='$fromdate'";
}
$heights = $dataobj->listDirectQuery("select heights from master_user_details  where id=" . $user_id);
$userheight = $heights[0]["heights"];
// 91, 228, 95
$real_parameters = $parameter;
$parameter = explode(",", $parameter);
// print_r($parameter);
// echo "<br>Array Count: " . count($parameter);
if (in_array(91, $parameter) and in_array(95, $parameter) and in_array(228, $parameter)) {
    if (isset($sus)) {
        $sql = $dataobj->listDirectQuery(
            "SELECT pt1.test_id, pt1.test_results, pt1.test_date 
             FROM prescribed_tests pt1
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[0] . " 
                   AND user_id = '" . $sus . "'
             ) pt2 ON pt1.test_date = pt2.test_date
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[1] . " 
                   AND user_id = '" . $sus . "'
             ) pt3 ON pt1.test_date = pt3.test_date
             WHERE pt1.test_id IN (" . $parameter[0] . ", " . $parameter[1] . ")
               AND pt1.user_id = '" . $sus . "'
               AND pt1.test_results IS NOT NULL
               AND pt1.test_results <> ''"
        );
        $sql_95 = $dataobj->listDirectQuery("SELECT test_id, test_results, test_date
                FROM prescribed_tests
                WHERE test_id = '$parameter[2]'
                AND user_id = '$sus'
                AND test_results IS NOT NULL
                AND test_results <> ''
                AND test_date IN (
                    SELECT test_date
                    FROM prescribed_tests
                    WHERE test_id = '$parameter[2]' AND user_id = '$sus'
                )");
    } else {
        $sql = $dataobj->listDirectQuery(
            "SELECT pt1.test_id, pt1.test_results, pt1.test_date 
             FROM prescribed_tests pt1
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[0] . " 
                   AND user_id = '" . $user_id . "'
             ) pt2 ON pt1.test_date = pt2.test_date
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[1] . " 
                   AND user_id = '" . $user_id . "'
             ) pt3 ON pt1.test_date = pt3.test_date
             WHERE pt1.test_id IN (" . $parameter[0] . ", " . $parameter[1] . ")
               AND pt1.user_id = '" . $user_id . "'
               AND pt1.test_results IS NOT NULL
               AND pt1.test_results <> ''"
        );
                $sql_95 = $dataobj->listDirectQuery("SELECT test_id, test_results, test_date
                FROM prescribed_tests
                WHERE test_id = '$parameter[2]'
                AND user_id = '$user_id'
                AND test_results IS NOT NULL
                AND test_results <> ''
                AND test_date IN (
                    SELECT test_date
                    FROM prescribed_tests
                    WHERE test_id = '$parameter[2]' AND user_id = '$user_id'
                )");
    }
    //print_r($sql);



    
    $dashboard_data1 = $dataobj->listDirectQuery("select test_name as title,unit from  master_test where id ='$real_parameters'");
    if ($dashboard_data1[0]["title"] == "Weight") {
        $titlecard1 = "BMI";
    } else {
        $titlecard1 = $dashboard_data1[0]["title"];
        $unitcard1 = $dashboard_data1[0]["unit"];
    }
    $results_91 = [];
    $result_95 = [];
    $results_228 = [];
    $testdate = [];
    $testdate_2 = [];
    foreach ($sql as $row) {
        $testdate[] = "'" . date("M-Y", strtotime($row["test_date"])) . "'";
    }
    foreach ($sql_95 as $row) {
        $testdate_2[] = "'" . date("M-Y", strtotime($row["test_date"])) . "'";
    }
    // print_r($testdate);
    // print_r($testdate_2);
    foreach ($sql as $row) {
        if ($row["test_id"] == 91) {
            $results_91[] = $row["test_results"];
        } elseif ($row["test_id"] == 228) {
            $results_228[] = $row["test_results"];
        }
    }
    foreach ($sql_95 as $row) {
        if ($row["test_id"] == 95) {
            $results_95[] = $row["test_results"];
        }
    }
} elseif ($parameter[0] == 276 and $parameter[1] == 204) {
    if (isset($sus)) {
        $sql = $dataobj->listDirectQuery(
            "SELECT pt1.test_id, pt1.test_results, pt1.test_date 
             FROM prescribed_tests pt1
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[0] . " 
                   AND user_id = '" . $sus . "'
             ) pt2 ON pt1.test_date = pt2.test_date
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[1] . " 
                   AND user_id = '" . $sus . "'
             ) pt3 ON pt1.test_date = pt3.test_date
             WHERE pt1.test_id IN (" . $parameter[0] . ", " . $parameter[1] . ")
               AND pt1.user_id = '" . $sus . "'
               AND pt1.test_results IS NOT NULL
               AND pt1.test_results <> ''"
        );
    } else {
        $sql = $dataobj->listDirectQuery(
            "SELECT pt1.test_id, pt1.test_results, pt1.test_date 
             FROM prescribed_tests pt1
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[0] . " 
                   AND user_id = '" . $user_id . "'
             ) pt2 ON pt1.test_date = pt2.test_date
             JOIN (
                 SELECT test_date 
                 FROM prescribed_tests 
                 WHERE test_id = " . $parameter[1] . " 
                   AND user_id = '" . $user_id . "'
             ) pt3 ON pt1.test_date = pt3.test_date
             WHERE pt1.test_id IN (" . $parameter[0] . ", " . $parameter[1] . ")
               AND pt1.user_id = '" . $user_id . "'
               AND pt1.test_results IS NOT NULL
               AND pt1.test_results <> ''"
        );
    }
    
    $dates = [];
    foreach ($sql as $item) {
        $date = $item["test_date"];
        $test_id = $item["test_id"];
        if (!isset($dates[$date])) {
            $dates[$date] = [];
        }
        $dates[$date][] = $test_id;
    }
    $filtered = [];
    foreach ($sql as $item) {
        $date = $item["test_date"];
        $test_id = $item["test_id"];
        if (isset($dates[$date]) && count(array_unique($dates[$date])) > 1) {
            $filtered[] = $item;
        }
    }
    $sql = $filtered;
    $dashboard_data1 = $dataobj->listDirectQuery("SELECT test_name AS title, unit 
FROM master_test
WHERE id IN (" . $real_parameters . ");
");
    if ($dashboard_data1[0]["title"] == "Weight") {
        $titlecard1 = "BMI";
    } else {
        $titlecard1 = $dashboard_data1[0]["title"];
        $unitcard1 = $dashboard_data1[0]["unit"];
    }
    if (count($parameter) == 1) {
        $results_1 = [];
        foreach ($sql as $row) {
            $results_1[] = $row["test_results"];
        }
    }
    foreach ($sql as $chol) {
        $testdate[] = "'" . date("M-Y", strtotime($chol["test_date"])) . "'";
        $result[] = round($chol["test_results"]);
    }
    $results_1 = [];
    $results_2 = [];
    $PressureDates = [];
    foreach ($sql as $row) {
        if ($row["test_id"] == 204) {
            $results_1[] = $row["test_results"];
        } elseif ($row["test_id"] == 276) {
            $results_2[] = $row["test_results"];
        }
    }
    $result_276 = [];
    $results_204 = [];
    foreach ($sql as $row) {
        if ($row["test_id"] == 276) {
            $results_276[] = $row["test_results"];
        } elseif ($row["test_id"] == 204) {
            $results_204[] = $row["test_results"];
        }
    }
    foreach ($sql as $row) {
        $PressureDates[] = $row["test_date"];
    }
} else {
    if (isset($sus)) {
        $sql = "SELECT test_results, test_date, test_id 
                FROM prescribed_tests  
                WHERE user_id='$sus' 
                  AND test_id IN (" . $real_parameters . ") 
                  AND test_results != '' " . $whr . " 
                ORDER BY test_date ASC";
    } else {
        $sql = "SELECT test_results, test_date, test_id 
                FROM prescribed_tests  
                WHERE user_id='$user_id' 
                  AND test_id IN (" . $real_parameters . ") 
                  AND test_results != '' " . $whr . " 
                ORDER BY test_date ASC";
    }


    // echo "SQL: " . $sql;
    $sql = $dataobj->listDirectQuery($sql);
    if (count($parameter) > 1) {
        // echo "<br>Sql: " . $sql;
        // echo "<br>All Test Ids: " . $real_parameters;
        // echo "<pre>";
        // print_r($sql);
        // echo "</pre>";
        $array = $sql;
        $test_ids = array($real_parameters);
        $test_ids = explode(',', $test_ids[0]);
        // echo "<br>Test Ids: ";
        // print_r($test_ids);
        $dates = [];
        foreach ($array as $item) {
            if (in_array($item['test_id'], $test_ids)) {
                $dates[$item['test_id']][] = $item['test_date'];
            }
        }
        $common_dates = call_user_func_array('array_intersect', array_values($dates));
        $result = array_filter($array, function ($item) use ($common_dates) {
            return in_array($item['test_date'], $common_dates);
        });
        // echo "Display<hr>";
        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        $sql = $result;
    }


    $resultsByTestId = [];
    foreach ($sql as $row) {
        $test_id = $row["test_id"];
        $test_date = $row["test_date"];
        $test_result = $row["test_results"];
        if (!isset($resultsByTestId[$test_id])) {
            $resultsByTestId[$test_id] = [];
        }
        if (!isset($resultsByTestId[$test_id][$test_date])) {
            $resultsByTestId[$test_id][$test_date] = [];
        }
        $resultsByTestId[$test_id][$test_date][] = $test_result;
    }
    $testdate = [];
    foreach ($sql as $row) {
        $testdate[] = "'" . date("M-Y", strtotime($row["test_date"])) . "'";
    }
    if (count($parameter) > 1) {
        $dashboard_data1 = [];
        for ($i = 0;$i < count($parameter);$i++) {
            $dashboard_data1[] = $dataobj->listDirectQuery("SELECT test_name AS title, unit, id
            FROM master_test 
            WHERE id = '$parameter[$i]'");
        }
    } else {
        $dashboard_data1 = $dataobj->listDirectQuery("SELECT test_name AS title, unit
                                                  FROM master_test 
                                                  WHERE id = '$real_parameters'");
    }
    if ($dashboard_data1[0]["title"] == "Weight") {
        $titlecard1 = "BMI";
    } else {
        $titlecard1 = $dashboard_data1[0]["title"];
        $unitcard1 = $dashboard_data1[0]["unit"];
    }
    $finalResults = [];
    $i = 0;
    foreach ($resultsByTestId as $test_id => $dates) {
        $i++;
        foreach ($dates as $date => $results) {
            $finalResults[$test_id]["dates"][] = $date;
            $finalResults[$test_id]["results"][] = $results;
        }
    }
}
if ($real_parameters == 276) {
    $sqlCustom = "SELECT group_concat(test_id),group_concat(test_date) as tdate  FROM `prescribed_tests` WHERE user_id='$suser' AND `test_id` IN ('276','204') and test_results!='' group by test_date order by test_date desc";
    $datecount = $dataobj->listDirectQuery($sqlCustom);
    foreach ($datecount as $count) {
        if (strpos($count["tdate"], ",") == true) {
            $onedata = explode(",", $count["tdate"]);
            $samedate[] = $onedata[0];
        }
    }
    $bpdate = array_unique($samedate);
    $bppdate = implode("','", $bpdate);
    $whr .= " and test_date in ('" . $bppdate . "')";
    //echo "select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='".$doublevalue[0]."' ".$whr." and test_results!='' and test_date>='".$firstfromdate."' and test_date <= '".$firsttodate."' order by test_date asc limit 3";
    $triglyeroid = $dataobj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[0] . "' " . $whr . " and test_results!='' and test_date>='" . $firstfromdate . "' and test_date <= '" . $firsttodate . "' order by test_date asc limit 3");
    foreach ($triglyeroid as $tri) {
        $testdatee[] = "'" . date("M-Y", strtotime($tri["test_date"])) . "'";
        $results[] = $tri["test_results"];
    }
    $secondvalue2 = $dataobj->listDirectQuery("select test_results,test_date from prescribed_tests  where user_id='$suser' and test_id ='" . $doublevalue[1] . "' " . $whr . " and test_results!='' order by test_date asc limit 3");
    foreach ($secondvalue2 as $svv) {
        //$testdate[]=$chol['test_date'];
        $second_testdate2[] = "'" . date("M-Y", strtotime($svv["test_date"])) . "'";
        $second_result2[] = $svv["test_results"];
    }
}
// if ($sql == 0) {
//     $int_rand = rand();
//     echo "<br><br><h3>No Data available !! <h3>";
//     exit();
// }
?>
<div style="width:100%; display: flex; flex-wrap: wrap; justify-content: center;">
    <!-- First graph and table -->
    <?php if (in_array(91, $parameter) and in_array(95, $parameter) and in_array(228, $parameter)) { ?>
        <div style="width:45%; margin: 2%; padding: 1%;">
            <div id="ajcon1" style="height: 350px; margin: 0 auto;">
            </div>
            <div style="width:100%; padding: 0 1%; float: left;">
                <table cellpadding="5" cellspacing="5" width="100%" align="center" border="1" class="table tableborder ajaxemp table-striped">
                    <tr>
                        <td colspan=3 align=center><b> Blood Glucose</b></td>
                    </tr>
                    <tr class="bold" style="color:#fff; background-color:#<?php echo $_SESSION["tclr"]; ?>;">
                        <th width=100 style="text-align:center;">Date</th>
                        <th style="text-align:center;">Fasting</th>
                        <th style="text-align:center;">PP</th>
                    </tr>
                    <?php
    $grouped = [91 => [], 228 => [], ];
        foreach ($sql as $item) {
            if ($item["test_id"] == 91) {
                $grouped[91][] = $item;
            } elseif ($item["test_id"] == 228) {
                $grouped[228][] = $item;
            }
        }
        $resultsByDate = [];
        foreach ($grouped[91] as $item) {
            $resultsByDate[$item["test_date"]]["fasting"] = $item["test_results"];
        }
        foreach ($grouped[228] as $item) {
            $resultsByDate[$item["test_date"]]["pp"] = $item["test_results"];
        }
        //print_r($resultsByDate);
        foreach ($resultsByDate as $date => $results) {
            $formattedDate = date("d-m-Y", strtotime($date));
            $fastingResult = isset($results["fasting"]) ? $results["fasting"] : "-";
            $ppResult = isset($results["pp"]) ? $results["pp"] : "-";
            ?>
                        <tr>
                            <td>
                                <center><?php echo $formattedDate; ?></center>
                            </td>
                            <td>
                                <center><?php echo $fastingResult; ?></center>
                            </td>
                            <td>
                                <center><?php echo $ppResult; ?></center>
                            </td>
                        </tr>
                    <?php
        }
        ?>
                </table>

            </div>
        </div>
        <div style="width:45%; margin: 2%; padding: 1%;">
            <div id="ajcon2" style="height: 350px; margin: 0 auto;">
            </div>
            <div style="width:100%; padding: 0 1%; float: left;">
                <table cellpadding="5" cellspacing="5" width="100%" align="center" border="1" class="table tableborder ajaxemp table-striped">
                    <tr>
                        <td colspan=2 align=center><b>HbA1C</b></td>
                    </tr>
                    <tr class="bold" style="color:#fff; background-color:#<?php echo $_SESSION["tclr"]; ?>;">
                        <th width=100 style="text-align:center;">Date</th>
                        <th style="text-align:center;">Value</th>
                    </tr>
                    <?php foreach ($sql_95 as $chart_details) {
                        $testdats = date("d-m-Y", strtotime($chart_details["test_date"])); ?>
                        <tr>
                            <td>
                                <center><?php echo $testdats; ?></center>
                            </td>
                            <td>
                                <center><?php echo $chart_details["test_results"]; ?></center>
                            </td>
                        </tr>
                    <?php
                    } ?>
                </table>
            </div>
        </div>
    <?php
    } elseif ($parameter[0] == 276 and $parameter[1] == 204) { ?>
        <div class="container" style="display: flex;">
            <div id="ajcon1" class="left" style="height: 350px; margin: 0 auto; width: 55%;">
            </div>
            <div class="right" style="width: 40%; padding: 1%;">
                <table cellpadding="5" cellspacing="5" width="100%" align="center" border="1" class="table tableborder ajaxemp table-striped">
                    <tr>
                        <td colspan=3 align=center><b><?php echo $titlecard1; ?>Blood Glucose</b></td>
                    </tr>
                    <tr class="bold" style="color:#fff; background-color:#<?php echo $_SESSION["tclr"]; ?>;">
                        <th width=100 style="text-align:center;">Date</th>
                        <th style="text-align:center;"><?php echo $dashboard_data1[1]["title"]; ?></th>
                        <th style="text-align:center;"><?php echo $dashboard_data1[0]["title"]; ?></th>
                    </tr>
                    <?php
        $grouped = [276 => [], 204 => [], ];
        foreach ($sql as $item) {
            if ($item["test_id"] == 276) {
                $grouped[276][] = $item;
            } elseif ($item["test_id"] == 204) {
                $grouped[204][] = $item;
            }
        }
        $resultsByDate = [];
        foreach ($grouped[276] as $item) {
            $resultsByDate[$item["test_date"]]["fasting"] = $item["test_results"];
        }
        foreach ($grouped[204] as $item) {
            $resultsByDate[$item["test_date"]]["pp"] = $item["test_results"];
        }
        foreach ($resultsByDate as $date => $results) {
            $formattedDate = date("d-m-Y", strtotime($date));
            $fastingResult = isset($results["fasting"]) ? $results["fasting"] : "-";
            $ppResult = isset($results["pp"]) ? $results["pp"] : "-";
            ?>
                        <tr>
                            <td>
                                <center><?php echo $formattedDate; ?></center>
                            </td>
                            <td>
                                <center><?php echo $fastingResult; ?></center>
                            </td>
                            <td>
                                <center><?php echo $ppResult; ?></center>
                            </td>
                        </tr>
                    <?php
        }
        ?>
                </table>
            </div>
        </div>

    <?php
    } elseif (count($parameter) == 1) { ?>
        <div class="container" style="display: flex;">
            <div id="ajcon1" class="left" style="height: 350px; margin: 0 auto; width: 55%; float:left;">
            </div>
            <div class="right" style="width: 40%; padding: 1%;">
                <table cellpadding="5" cellspacing="5" width="100%" align="center" border="1" class="table tableborder ajaxemp table-striped">
                    <tr>
                        <td colspan=2 align=center><b><?php echo $titlecard1; ?>(<?php echo $unitcard1; ?>)</b></td>
                    </tr>
                    <tr class="bold" style="color:#fff; background-color:#<?php echo $_SESSION["tclr"]; ?>;">
                        <th width=100 style="text-align:center;">Date</th>
                        <th style="text-align:center;"><?php echo $dashboard_data1[0]["title"]; ?></th>
                    </tr>
                    <?php
        function date_compare($a, $b)
        {
            $t1 = strtotime($a["test_date"]);
            $t2 = strtotime($b["test_date"]);
            return $t1 - $t2;
        }
        usort($sql, "date_compare");
        $allResults = [];
        foreach ($sql as $chart_details) {
            $testdats = date("d-m-Y", strtotime($chart_details["test_date"]));
            $allResults[] = $chart_details["test_results"];
            ?>
                        <tr>
                            <td>
                                <center><?php echo $testdats; ?></center>
                            </td>
                            <td>
                                <center><?php echo $chart_details["test_results"]; ?></center>
                            </td>
                        </tr>
                    <?php
        }
        ?>
                </table>
            </div>
        </div>

    <?php
    } elseif (count($parameter) == 2) { ?>
        <div style="width:45%; margin: 2%; padding: 1%;">
            <div id="ajcon1" style="height: 350px; margin: 0 auto; width:55%; float:left;">
            </div>
            <div style="width:40%; padding: 0 1%; float: left;">
                <table cellpadding="5" cellspacing="5" width="100%" align="center" border="1" class="table tableborder ajaxemp table-striped">
                    <tr>
                        <td colspan=2 align=center><b><?php echo $titlecard1; ?>(<?php echo $unitcard1; ?>)</b></td>
                    </tr>
                    <tr class="bold" style="color:#fff; background-color:#<?php echo $_SESSION["tclr"]; ?>;">
                        <th width=100 style="text-align:center;">Date</th>
                        <th style="text-align:center;">Fasting</th>
                    </tr>
                    <?php foreach ($sql as $chart_details) {
                        $testdats = date("d-m-Y", strtotime($chart_details["test_date"])); ?>
                        <tr>
                            <td>
                                <center><?php echo $testdats; ?></center>
                            </td>
                            <td>
                                <center><?php echo $chart_details["test_results"]; ?></center>
                            </td>
                        </tr>
                    <?php
                    } ?>
                </table>
            </div>
        </div>
    <?php
    } else {
        $formattedResults = "";
        $idToTitle = [];
        $title = [];
        $data = [];
        foreach ($dashboard_data1 as $dataItem) {
            $idToTitle[$dataItem[0]["id"]] = $dataItem[0]["title"];
        }
        foreach ($dashboard_data1 as $dataItem) {
            $title[] = $dataItem[0]["title"];
        }
        $grouped_data = [];
        // Extract and group data by date
        foreach ($finalResults as $id => $result) {
            $currentTitle = isset($idToTitle[$id]) ? $idToTitle[$id] : "";
            if ($currentTitle) {
                foreach ($result["dates"] as $index => $date) {
                    if (!isset($grouped_data[$date])) {
                        $grouped_data[$date] = array_fill(0, count($title), "");
                    }
                    $grouped_data[$date][array_search($currentTitle, $title) ] = $result["results"][$index][0];
                }
            }
        }
        ?>
        <div class="container" style="display: flex;">
            <div id="ajcon1" class="left" style=" height: 350px; margin: 0 auto; width: 55%;"></div>
            <div class="right" style="width: 40%; padding: 1%;">
                <table cellpadding="5" cellspacing="5" width="100%" align="center" border="1" class="table tableborder ajaxemp table-striped">
                    <tr>
                        <td align="center" colspan="<?php echo count($title) + 1; ?>">
                            <b><?php echo $_POST["test_name"]; ?></b>
                        </td>
                    </tr>
                    <tr class="bold" style="color:#fff; background-color:#<?php echo $_SESSION["tclr"]; ?>;">
                        <th width=100 style="text-align:center;">Date</th>
                        <?php foreach ($title as $columnTitle) { ?>
                            <th style="text-align:center;"><?php echo $columnTitle; ?></th>
                        <?php
                        } ?>
                    </tr>
                    <?php foreach ($grouped_data as $date => $results) { ?>
                        <tr>
                            <td style="text-align:center;"><?php echo date("d-m-Y", strtotime($date)); ?></td>
                            <?php foreach ($results as $result) { ?>
                                <td style="text-align:center;"><?php echo $result; ?></td>
                            <?php
                            } ?>
                        </tr>
                    <?php
                    } ?>
                </table>
            </div>
        </div>



    <?php
    } ?>
</div>

<?php
    //echo "select all_ques as qid from hra_coporate_element where  corp_id= '".$_SESSION['ucorp_id']."' and all_ques!='' ";
    $sel = $dataobj->listDirectQuery("select all_ques as qid from hra_coporate_element where  corp_id= '" . $_SESSION["ucorp_id"] . "' and all_ques!='' ");
$qst = $sel[0]["qid"];
if ($qst != "") {
    $qars = explode(",", $qst);
} else {
    $qars = "";
}
$ind = $dataobj->listDirectQuery("select * from hra_induresults where r_corporate_id='" . $_SESSION["ucorp_id"] . "'");
if ($qst != "") {
    $charttitle = $dataobj->listDirectQuery("select dashtext,id from  hra_question where id in($qst)");
}
$w = 1;
foreach ($qars as $qr) {
    $high = $dataobj->listDirectQuery("select high_data from hra_templates where qus_id='$qr' ");
    $hg = $high[0]["high_data"];
    //var_dump($_SESSION);
    //echo "SELECT cmp_date as end_date  FROM  `hra_induresults` hra left join `doctype` d  on hra.tempid=d.id where d.type='HRAG' and hra.location='".$_SESSION['ucorp_id']."'  and hra.userid='".$_SESSION['userid']."' and hra.cmp_date<='".$today."' GROUP BY YEAR(cmp_date)";
    //exit;
    //$sltm=$dataobj->listDirectQuery("SELECT cmp_date as end_date  FROM  `hra_induresults` where r_corporate_id='".$_SESSION['parent_id']."'  and userid in ($r_usermid) and cmp_date<='".$todateformat."' GROUP BY YEAR(cmp_date)" );
    $sltm = $dataobj->listDirectQuery("SELECT cmp_date as end_date  FROM  `hra_induresults` hra left join `doctype` d  on hra.tempid=d.id where d.type='HRAG' and hra.location='" . $_SESSION["ucorp_id"] . "'  and hra.userid='" . $_SESSION["userid"] . "' and hra.cmp_date<='" . $today . "' GROUP BY YEAR(cmp_date)");
    foreach ($sltm as $mnyr) {
        $ {
            "yrsar" . $w
        }
        [] = $mnyr["end_date"];
        //if(!empty($r_usermid)) {
        //	echo "select count(id) as xcnt ,fromdate from corporate_user_mapping where r_corporate_id='".$_SESSION['ucorp_id']."' and  isactive='1' and r_user_id='".$_SESSION['userid']."' and fromdate <= '".$mnyr['end_date']."' ";
        //	exit;
        //echo "select count(id) as xcnt ,fromdate from corporate_user_mapping where r_corporate_id='".$_SESSION['ucorp_id']."' and  isactive='1' and r_user_id='".$_SESSION['userid']."' and fromdate <= '".$mnyr['end_date']."' ";
        //echo  "</br>";
        $excnt = $dataobj->listDirectQuery("select count(id) as xcnt ,fromdate from corporate_user_mapping where r_corporate_id='" . $_SESSION["ucorp_id"] . "' and  isactive='1' and r_user_id='" . $_SESSION["userid"] . "' and fromdate <= '" . $mnyr["end_date"] . "' ");
        //}
        $ {
            "xcnts" . $w
        }
        [] = $excnt[0]["xcnt"];
    }
    //if(!empty($r_usermid)) {
    if ($qr != 0) {
        //${"chartvalqry".$w}=$dataobj->listDirectQuery("select count(a.`id`) as val, b.end_date as month1,hra.cmp_date as month from `hra_results` a LEFT JOIN corp_template_assign b on b.id=a.ctmp_id left join hra_induresults hra on hra.tempid=a.tempid
        //where a.qus_id='$qr' and a.pnts in ($hg)  and a.userid in ($r_usermid) and hra.cmp_date<='".$todateformat."' GROUP BY YEAR(cmp_date)");
        //echo "select count(a.`id`) as val, b.end_date as month1,hra.cmp_date as month from `hra_results` a LEFT JOIN corp_template_assign b on b.id=a.ctmp_id  left join hra_induresults hra on hra.userid=a.userid   left join `doctype` d on  hra.tempid=d.id   where  d.type='HRAG' and a.qus_id='$qr' and a.pnts in ($hg)  and a.userid='".$_SESSION['userid']."' and hra.cmp_date<='".$today."' and hra.location='".$_SESSION['ucorp_id']."' GROUP BY YEAR(hra.cmp_date) ";
        // exit;
        $ {
            "chartvalqry" . $w
        } = $dataobj->listDirectQuery("select count(a.`id`) as val, b.end_date as month1,hra.cmp_date as month from `hra_results` a LEFT JOIN corp_template_assign b on b.id=a.ctmp_id  left join hra_induresults hra on hra.userid=a.userid   left join `doctype` d on  hra.tempid=d.id   where  d.type='HRAG' and a.qus_id='$qr' and a.pnts in ($hg)  and a.userid='" . $_SESSION["userid"] . "' and hra.cmp_date<='" . $today . "' and hra.location='" . $_SESSION["ucorp_id"] . "' GROUP BY YEAR(hra.cmp_date) ");
        //echo "select count(a.`id`) as val, b.end_date as month1,hra.cmp_date as month from `hra_results` a LEFT JOIN corp_template_assign b on b.id=a.ctmp_id  left join hra_induresults hra on hra.userid=a.userid   left join `doctype` d on  hra.tempid=d.id   where  d.type='HRAG' and a.qus_id='$qr' and a.pnts in ($hg)  and a.userid in ($r_usermid) and hra.cmp_date<='".$todateformat."' and hra.r_corporate_id='".$_SESSION['parent_id']."' GROUP BY YEAR(hra.cmp_date) ";

    }
    $ky = 0; //foreach(${"chartvalqry".$w} as $vall){
    foreach ($ {
        "yrsar" . $w
    } as $yr) {
        $explodeyear = explode("-", $ {
            "chartvalqry" . $w
        }
        [$ky]["month"]) [0];
        $explodehrayear = explode("-", $yr) [0];
        if ($explodehrayear == $explodeyear) {
            //if($yr==${"chartvalqry".$w}[$ky]['month']){
            $ {
                "chartval" . $w
            }
            [] = $ {
                "chartvalqry" . $w
            }
            [$ky]["val"];
            //${"chartmonth".$w}[]="'".date("M-Y",strtotime(${"chartvalqry".$w}[$ky]['month']))."'";
            $ {
                "prc" . $w
            }
            [] = round(($ {
                "chartvalqry" . $w
            }
            [$ky]["val"] / $ {
                "xcnts" . $w
            }
            [$ky]) * 100, 2);
            foreach ($date_array as $end_datewise_single) {
                $end_month = explode("/", $end_datewise_single);
                $end_month_filter = $end_month[1] . "-" . $end_month[0];
                $monthdate = date("M-y", strtotime($end_month_filter));
                $ {
                    "chartmonth" . $w
                }
                [] = '"' . $monthdate . '"';
            }
        } else {
            $ {
                "chartval" . $w
            }
            [] = 0;
            $ {
                "prc" . $w
            }
            [] = 0;
            //${"chartmonth".$w}[]="'".date("M-Y",strtotime($yr))."'";
            foreach ($date_array as $end_datewise_single) {
                $end_month = explode("/", $end_datewise_single);
                $end_month_filter = $end_month[1] . "-" . $end_month[0];
                $monthdate = date("M-y", strtotime($end_month_filter));
                $ {
                    "chartmonth" . $w
                }
                [] = '"' . $monthdate . '"';
            }
        }
        $ky++;
    }
    $w++;
}
$dataobj = new ManageMessage();
$result = "SELECT * FROM `master_test` WHERE id IN ($real_parameters)";
$result = $dataobj->listDirectQuery($result);
$gender = $dataobj->listDirectQuery("SELECT gender FROM master_user_details WHERE id='" . $_SESSION["userid"] . "'");
$gender = $gender[0]["gender"];
?>

<style>
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    padding: 20px;
}

.chart {
    flex: 1 1 100%;
    max-width: 600px;
}

.test-results {
    flex-wrap: wrap;
    gap: 20px;
}

.result-table {
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.result-table th, .result-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

.result-table thead th {
    background-color: #666;
    color: white;
}

.result-table .header-row th {
    background-color: #f2f2f2;
    color: #333;
}
</style>
    <!--<div id="ajcon1" class="chart" style="height: 350px; margin: 0 auto; width:55%;"></div>-->
    <div class="test-results">
        <?php foreach ($result as $test) {
            $m_min = explode("~~~", $test["m_min"]);
            $m_max = explode("~~~", $test["m_max"]);
            $f_min = explode("~~~", $test["f_min"]);
            $f_max = explode("~~~", $test["f_max"]);
            $conditions = explode("~~~", $test["cond"]);
            echo "<table class='result-table' width=28% style='float:right; margin: 0 2%;'>";
            echo "<thead>";
            echo "<tr><th colspan='2'>" . $test["test_name"] . " (" . ucfirst($test['unit']) . ")</th></tr>";
            echo "<tr class='header-row'>";
            echo "<th>Condition</th>";
            echo "<th>Value</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            for ($i = 0;$i < count($conditions);$i++) {
                echo "<tr>";
                if ($conditions[$i] == "") {
                    $conditions[$i] = "Normal Range";
                }
                echo "<td>" .$conditions[$i]. "</td>";
                if ($gender == "male") {
                    $min_value = $m_min[$i] === "" ? "0" : $m_min[$i];
                    $max_value = $m_max[$i] === "" ? "And Above" : $m_max[$i];
                } else {
                    $min_value = $f_min[$i] === "" ? "0" : $f_min[$i];
                    $max_value = $f_max[$i] === "" ? "And Above" : $f_max[$i];
                }
                echo "<td>" . $min_value . " - " . $max_value . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } ?>
    </div>
</div>
</div>

<?php if (in_array(91, $parameter) and in_array(95, $parameter) and in_array(228, $parameter)) { ?>
    <script>
        var chart = Highcharts.chart('ajcon1', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<?php echo "Blood Glucose"; ?>'
            },

            xAxis: {
                type: 'datetime',
                categories: [
                    <?php echo implode(",", array_unique($testdate)); ?>
                ],
                plotBands: [{

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
                    name: '<?php echo $dashboard_data1[0]["title"]; ?>',

                    data: [<?php echo implode(",", $results_91); ?>],
                    showInLegend: false

                },
                {
                    name: '<?php echo "Glucose - PP"; ?>',


                    data: [<?php echo implode(",", $results_228); ?>],

                    showInLegend: false

                }
            ]
        }, function(chart) { // on complete
            <?php if (!isset($results_91) && !isset($results_228) && $dashboard_data1[0]["title"] != "") { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <?php
            } ?>
        });
    </script>
    <script>
        var chart = Highcharts.chart('ajcon2', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<?php echo "Glycosylated Haemoglobin (Hb A1C)"; ?>'
            },

            xAxis: {
                type: 'datetime',
                categories: [
                    <?php echo implode(",", array_unique($testdate_2)); ?>
                ],
                plotBands: [{

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
                name: '<?php echo $dashboard_data1[0]["title"]; ?>',

                data: [<?php echo implode(",", $results_95); ?>],
                showInLegend: false

            }]
        }, function(chart) { // on complete
            <?php if (!isset($results_95) && $dashboard_data1[0]["title"] != "") { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <?php
            } ?>
        });
    </script>
<?php
} elseif ($parameter[0] == 276 and $parameter[1] == 204) { 
    ?>

    <script>
        var chart = Highcharts.chart('ajcon1', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<?php echo "Blood Pressure"; ?>'
            },

            xAxis: {
                type: 'datetime',
                categories: [
                    <?php echo implode(",", array_unique($testdate)); ?>
                ],
                plotBands: [{

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
            series: [
                {
                    name: '<?php echo $dashboard_data1[1]["title"]; ?>',


                    data: [<?php echo implode(",", $results_2); ?>],

                    showInLegend: false

                },
                {
                    name: '<?php echo $dashboard_data1[0]["title"]; ?>',

                    data: [<?php echo implode(",", $results_1); ?>],
                    showInLegend: false

                }
            ]
        }, function(chart) { // on complete
            <?php if ($results_1 == null && $results_2 == null && $dashboard_data1[0]["title"] != "") { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <?php
            } ?>
        });
    </script>
<?php
} elseif (count($parameter) == 1) {
    // print_r($testdate);
    // $testdate = array_map(function ($date) {
    //     return trim($date, "'");
    // }, $testdate);
    // function compareDates($a, $b)
    // {
    //     $dateA = DateTime::createFromFormat('M-Y', $a);
    //     $dateB = DateTime::createFromFormat('M-Y', $b);
    //     if ($dateA == $dateB) {
    //         return 0;
    //     }
    //     return ($dateA < $dateB) ? -1 : 1;
    // }
    // usort($testdate, 'compareDates');
    // $testdate = array_map(function ($item) {
    //     return "'" . $item . "'";
    // }, $testdate);
    // print_r($testdate);

    ?>
    <script>
        var chart = Highcharts.chart('ajcon1', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<?php echo $_POST["test_name"]; ?>'
            },

            xAxis: {
                type: 'datetime',
                categories: [
                    <?php echo implode(",", $testdate); ?>
                ],
                plotBands: [{

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
                name: '<?php echo $dashboard_data1[0]["title"]; ?>',


                data: [<?php echo implode(",", $allResults); ?>],

                showInLegend: false

            }]
        }, function(chart) { // on complete
            <?php if ($allResults == null && $dashboard_data1[0]["title"] != "") { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <?php
            } ?>
        });
    </script>
<?php
} elseif (count($parameter) == 2) { ?>

    <script>
        var chart = Highcharts.chart('ajcon1', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<?php echo "Blood Glucose"; ?>'
            },

            xAxis: {
                type: 'datetime',
                categories: [
                    <?php echo implode(",", array_unique($testdate)); ?>
                ],
                plotBands: [{

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
                    name: '<?php echo $dashboard_data1[0]["title"]; ?>',

                    data: [<?php echo implode(",", $results_1); ?>],
                    showInLegend: false

                },
                {
                    name: '<?php echo $dashboard_data11[0]["title"]; ?>',


                    data: [<?php echo implode(",", $results_2); ?>],

                    showInLegend: false

                }
            ]
        }, function(chart) { // on complete
            <?php if (!isset($results_1) && !isset($results_2) && $dashboard_data1[0]["title"] != "") { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <?php
            } ?>
        });
    </script>


<?php
} else {
    $formattedResults = "";
    $idToTitle = [];
    foreach ($dashboard_data1 as $data) {
        $idToTitle[$data[0]["id"]] = $data[0]["title"];
    }
    foreach ($finalResults as $id => $result) {
        $data = [];
        foreach ($result["results"] as $res) {
            $data[] = $res[0];
        }
        $title = isset($idToTitle[$id]) ? $idToTitle[$id] : "";
        $formattedResults .= "
    {
        name: '$title',
        data: [" . implode(",", $data) . "],
        showInLegend: false
    },";
    }
    $formattedResults = rtrim($formattedResults, ",");
    ?>
    <script>
        var chart = Highcharts.chart('ajcon1', {
            colors: ['rgba(102, 204, 0, .8)', 'rgba(255, 224, 131, .8)'],
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<?php echo $_POST["test_name"]; ?>'
            },

            xAxis: {
                type: 'datetime',
                categories: [
                    <?php echo implode(",", array_unique($testdate)); ?>
                ],
                plotBands: [{

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
            series: [<?php echo $formattedResults; ?>]
        }, function(chart) { // on complete
            <?php if ($$resultsByDate == null) { ?>
                chart.renderer.text('No Data Available', 120, 95)
                    .css({
                        color: '#4572A7',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    })
                    .add();
            <?php
            } ?>
        });
    </script>
<?php
}
