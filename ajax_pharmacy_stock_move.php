<?php
/*
Description: Maintenance Screen
Created By:
Created Date:
Modified By:Punitha Subramani
Modified Date: 16-Jan-2015
*/
include_once("../session.php");
require_once("../core/class.manageUsers.php");
require_once("../core/class.getDetails.php");

function limit_char($tchar, $tlimit)
{
    if (strlen($tchar) <= $tlimit) {
        echo $tchar;
    } else {
        $ychar = substr($tchar, 0, $tlimit) . '...';
        echo $ychar;
    }
}
if (isset($_POST) && !empty($_POST)) {
    $action = $_POST['action'];
    $datetime = date("Y-m-d H:i:s");
    $datetimeDisplay = date("d M Y");

    if ($action == "editGrid") { //To edit the grid
        $doctype = $_POST['doctype'];
        $id = $_POST['id'];
        if ($id == 0) {
            $sql = "INSERT INTO `pharmacy_stock_detail` (r_pharmacy_id,drug_name,drug_ingredient,drug_manifaturer,drug_batch,drug_manifaturer_date,drug_expiry_period,drug_expiry_date,drug_type,drug_strength,inventory,amount_per_tab,quantity,isactive,created_type,created_on,created_role,created_by) VALUES('" . $sessionlocation_id . "','" . $drug_name . "','" . $drug_ingredient . "','" . $drug_manifaturer . "','" . $drug_batch . "','" . $drug_manifaturer_date . "','" . $drug_expiry_period . "','" . $drug_expiry_date . "','" . $drug_type . "','" . $drug_strength . "','" . $inventory . "','" . $amount_per_tab . "','" . $quantity . "','" . $isactive . "','1','" . $datetime . "','" . $tbs_role . "','" . $tbs_userid . "')";
        } else {
            $sql = "UPDATE `pharmacy_stock_detail` SET drug_name='" . $drug_name . "',drug_ingredient='" . $drug_ingredient . "',drug_manifaturer='" . $drug_manifaturer . "',drug_batch='" . $drug_batch . "',drug_manifaturer_date='" . $drug_manifaturer_date . "',drug_expiry_period='" . $drug_expiry_period . "',drug_expiry_date='" . $drug_expiry_date . "',drug_type='" . $drug_type . "',drug_strength='" . $drug_strength . "',inventory='" . $inventory . "',amount_per_tab='" . $amount_per_tab . "',quantity='" . $quantity . "',isactive='" . $isactive . "',modified_type='1',modified_on='" . $datetime . "',modified_role='" . $tbs_role . "',modified_by='" . $tbs_userid . "' WHERE id='" . $id . "' AND r_pharmacy_id='" . $sessionlocation_id . "'";
        }
        $updateSqlData = new ManageUsers();
        $querySqlData = $updateSqlData->listDirectQuery($sql);
        $updateSqlData = null;

        if ($id == 0) {
            $sqlCount = "SELECT count(id) as did FROM `pharmacy_stock_detail` WHERE isactive=1 AND r_pharmacy_id='" . $sessionlocation_id . "'" . $whr;
            $details = new ManageUsers();
            $detailsObj = $details->listDirectQuery($sqlCount);
            $details = null;
            $count = $detailsObj[0]['did'];
            echo $x =  ceil(intVal($count) / intVal($_POST['recsPerPage']));
        }
    } else { //To display the grid
        $pageNo = $_POST['pageNo'];
        $recsPerPage = $_POST['recsPerPage'];
        $searchValue = $_POST['searchValue'];
        $manufa = $_POST['manufa'];
        $whereClass = "";

        if ($pageNo == "" || $pageNo == 0) {
            $pageNo = 1;
        }
        if ($recsPerPage == "" || $recsPerPage == 0) {
            $recsPerPage = ($defaultPageDisplay == "") ? 10 : $defaultPageDisplay;
        }
        if (!isset($pageNo)) {
            $pageNo = 1;
        }
        $startLimitSql = ($pageNo - 1) * $recsPerPage;
        $startLimit = ($pageNo - 1) * $recsPerPage + 1;


        /*if($searchValue!=""){
			$whr=" AND (psd.drug_name LIKE '".$searchValue."%' || psd.drug_ingredient LIKE '%".$searchValue."%' || psd.drug_manifaturer LIKE '".$searchValue."%' || psd.drug_type LIKE '".$searchValue."%')";
		}*/
        if ($searchValue != "") {
            $whr .= " AND psd.drug_name LIKE '" . $searchValue . "%'";
        }
        if ($manufa != "") {
            $whr .= " AND psd.drug_manifaturer LIKE '" . $manufa . "%'";
        }
        $referLink = explode("?", basename($_SERVER["HTTP_REFERER"]));
        $pageNameHalf = $referLink[0];
        if (basename($_SERVER["HTTP_REFERER"]) == "pharmacy_stock_avail_upload.php" || $pageNameHalf == "pharmacy_stock_avail_upload.php") {
            if (isset($_POST['uploadGenerateId'])) {
                $whr .= " AND psd.upload_generate_id=" . $_POST['uploadGenerateId'];
            }
            $whr .= " AND psd.isactive=0";
        } else {
            $whr .= " AND psd.isactive=1";
        }
        if (empty($_SESSION['ohc_loca'])) {
            $whr .= " AND psd.r_pharmacy_id='" . $sessionlocation_id . "'";
        } else {
            $whr .= " AND psd.r_pharmacy_id='" . $_SESSION['ohc_loca'] . "' AND psd.ohc='1' and psd.phar_ids='" . $_SESSION['phrmcy_id'] . "' AND psd.drug_expiry_date > '$tday' ";
            $hav = " HAVING  balance_quantity > '0' ";
        }

        $sortField = ($_POST['sortField'] == "") ? "psd.drug_name" : $_POST['sortField'];
        $sortType = ($_POST['sortType'] == "") ? "ASC" : $_POST['sortType'];
        $sql = "SELECT * FROM (
                    SELECT psd.*, mp.reminder_issue,
                           (psd.quantity - IFNULL(SUM(pssd.qty), 0)) AS balance_quantity,
                           DATE_FORMAT(psd.created_on, '%d %b %Y') AS added,
                           DATE_FORMAT(psd.modified_on, '%d %b %Y') AS modified,
                           IFNULL(mp.reminder_expiry, 0) AS reminder_expiry,
                           (SELECT COUNT(*) FROM `pharmacy_stock_detail` psd
                            LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
                            LEFT OUTER JOIN `master_pharmacy` mp ON mp.id = pssd.r_pharmacy_id
                            WHERE 1=1 " . $whr . ") AS total_count
                    FROM `pharmacy_stock_detail` psd
                    LEFT OUTER JOIN `pharmacy_sold_stock_detail` pssd ON pssd.r_stock_id = psd.id
                    LEFT OUTER JOIN `master_pharmacy` mp ON mp.id = pssd.r_pharmacy_id
                    WHERE 1=1 " . $whr . " 
                    GROUP BY psd.id " . $hav . " 
                    ORDER BY " . $sortField . " " . $sortType . "
                ) AS mainQuery";

        $getSqlData = new ManageUsers();
        $listingMaintenaces = $getSqlData->listDirectQuery($sql);
        $getSqlData = null;
        $count = $listingMaintenaces[(count($listingMaintenaces) - 1)]["total_count"];
        $endLimit = ($count <= (($startLimit - 1) + $recsPerPage)) ? $count : (($startLimit - 1) + $recsPerPage);
?>
        <div style="width:100%; float: left;">
            <!--<table class="table table-striped table-nomargin table-mail">-->
            <table cellpadding="5" cellspacing="5" style="width:100%;" border="1" align="center" class="table table-striped tableborder">
                <thead>
                    <tr class="bold" style="color:#fff;background-color:#<? echo $_SESSION['tclr'] ?>;">
                        <th>#</th>
                        <th style="width:17%; text-align:left;">
                            Drug Name (Strength)<br> Type
                        </th>
                        <th style="width:28%; text-align:left;">
                            Manufacturer<br>
                            Batch Number / Quantity
                        </th>
                        <th style="width:14%; text-align:left;">
                            Mfg Date<br>Expiry Date
                        </th>
                        <th>
                            <div style="width:110px;float:left;"> No of Tablets</div>

                            <div style="float:left;"> Sub Pharmacy List</div>


                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $countQual = 0;
                    $tday = date("Y-m-d");
                    $getSqlData = new ManageUsers();
                    $sels = (!empty($_SESSION['ohc_loca'])) ? $_SESSION['ohc_loca'] : $_SESSION['currentlocation_id'];
                    //echo "select reminder_issue from master_pharmacy where id='".$sels."' ";
                    $stopissues = $getSqlData->listDirectQuery("select reminder_issue from master_corporate where id='" . $sels . "' ");
                    $pharmsy = $getSqlData->listDirectQuery(" select * from ohc_pharmay where location_id='" . $sels . "' ");
                    $getSqlData = null;
                    $stp = "+" . $stopissues[0]['reminder_issue'] . "days";
                    $stopiss = date("Y-m-d", strtotime($tday . " " . $stp));
                    if ($listingMaintenaces != "" && count($listingMaintenaces) > 1) {
                        foreach ($listingMaintenaces as $listingMaintenace) {  // echo "<pre>"; echo "Count: " . count($listingMaintenaces); echo "<br><br><br><hr></pre>";
                            // echo "<br>";
                            // print_r($listingMaintenace);
                            // echo "<hr>";
                            $id = $listingMaintenace['id'];
                            $obj = new ManageUsers();
                            $sql = "SELECT * FROM `pharmacy_stock_detail` WHERE id='" . $id . "'";
                            $result = $obj->listDirectQuery($sql); // echo "<pre>"; print_r($result); echo "</pre>"; exit;
                            if ($result[0]['cur_availability'] > 0) {
                                if ($listingMaintenace["total_count"] > 0) {
                                    if ($listingMaintenace["drug_expiry_date"] > $stopiss) {
                                        $countQual = $countQual + 1;

                                        $drugName = $listingMaintenace['drug_name'];
                                        $balance_quantity = $listingMaintenace['balance_quantity'];
                                        $id = $listingMaintenace['id'];
                                        $added = $listingMaintenace['added'];
                                        $modified = $listingMaintenace['modified'];
                                        $drug_expiry_date = $listingMaintenace['drug_expiry_date'];
                                        $reminder_expiry = $listingMaintenace['reminder_expiry'];
                                        $drug_type = $listingMaintenace['drug_type'];
                                        $isactive = $listingMaintenace['isactive'];
                                        $listingMaintenace['drug_expiry_date'] . " - " . $reminder_expiry;
                                        if ($reminder_expiry == "") {
                                            $expiryDateCheck = date("d-m-Y", strtotime($listingMaintenace['drug_expiry_date']));
                                        } else {
                                            $expiryDateCheck = date("d-m-Y", strtotime("-" . $reminder_expiry, strtotime($listingMaintenace['drug_expiry_date'])));
                                        }
                                        $today = date("d-m-Y");

                                        if (strtolower($drug_type) == "capsule" || strtolower($drug_type) == "tablet") {
                                            $drug_type = "<img src='" . $sitepath . "/img/pharma_ico/pharma_ico7.png' style='width:18px;' />" . $drug_type;
                                        } else if (strtolower($drug_type) == "syrup") {
                                            $drug_type = "<img src='" . $sitepath . "/img/pharma_ico/pharma_ico13.png' style='width:18px;' />" . $drug_type;
                                        } else if (strtolower($drug_type) == "injection") {
                                            $drug_type = "<img src='" . $sitepath . "/img/pharma_ico/pharma_ico5.png' style='width:18px;' />" . $drug_type;
                                        } else {
                                            $drug_type = "<img src='" . $sitepath . "/img/pharma_ico/pharma_ico15.png' style='width:18px;' />" . $drug_type;
                                        }

                                        $style = "";
                                        //echo $today."<".$expiryDateCheck."&&".$expiryDateCheck."<".$stopiss."</br>";
                                        if (strtotime($today) >= strtotime($expiryDateCheck) || $isactive == "0") {
                                            $style = "display:none";
                                        } elseif (strtotime($tday) < strtotime($listingMaintenace['drug_expiry_date']) && strtotime($stopiss) > strtotime($listingMaintenace['drug_expiry_date'])) {
                                            $style = "display:none;";
                                        }
                                        if (isset($_POST['uploadGenerateId'])) {
                                            $style = "";
                                        }

                                        echo '<tr id="tr' . $id . '" style="' . $style . '">

								<td valign="top">' . $countQual . '. </td>
								<td valign="top"><b class="uppercase">' . $listingMaintenace['drug_name'] . '</b><br>' . $drug_type . '</td>
								<td valign="top">' . $listingMaintenace['drug_manifaturer'] . '<br>' . $listingMaintenace['drug_batch'] . ' / ' . $listingMaintenace['cur_availability'] . '</td>
								<td valign="top">' . $listingMaintenace['drug_manifaturer_date'] . '<br>' . $listingMaintenace['drug_expiry_date'] . '</td>

								 
								
								<td align="center" valign="top" >
<form method="post" action="pharmacy_stock_move_save.php">

<input type="hidden" name="ids" value="' . $id . '"/>
<div style="width:110px;float:left;"><input type="number" style="width:50px;" name="mqty"
 max="' . $listingMaintenace['balance_quantity'] . '" required min="1" /></div><div style="float:left;"><select required name="mphar" ><option value=""> Select Pharmacy</option>';

                                        foreach ($pharmsy as $pr) {
                                            if ($_SESSION['phrmcy_id'] != $pr['id']) {
                                                echo '<option value="' . $pr['id'] . '">' . $pr['name'] . '</option>';
                                            }
                                        }
                                        echo '</select></div> <input type="submit" name="move" value="Move" class="btn btn-sm float-right m-1 btn-primary"/></form></td>
							</tr><tr><td style="border-bottom: 1px #ccc Dashed;"></td><td colspan="6" style="border-bottom: 1px #ccc Dashed;"><i>';
                                        limit_char($listingMaintenace['drug_ingredient'], 57);
                                        echo '</i></td></tr>';
                                    }
                                }
                            }
                        }
                        /*
					<td align="left"><p>MRP: '.$listingMaintenace['amount_per_strip'].'</p><p>CPU: '.$listingMaintenace['amount_per_tab'].'</p><p>Discount: '.$listingMaintenace['discount'].'%</p></td>
					
								<td><label><p>';
	 if(!empty($_SESSION['mainphr']) || empty($_SESSION['ohc_loca'])){							
 echo '<a href="add-stock-pharma.php?id='.$id.'">Edit</a> /'; 
	 }
echo 'History</p><p>Added '.$added.'</p><p>Modified '.$modified.'</p> </label></td>
					*/
                    ?>
                </tbody>
            </table>
        </div>|*|*|*|*|<?php echo $pageNo; ?>|*|*|*|*|<?php echo $startLimit; ?>|*|*|*|*|<?php echo $endLimit; ?>|*|*|*|*|<?php echo $count;
                                                                                                                        } else {
                                                                                                                            echo "<table class=\"table table-striped table-nomargin table-mail\" ><tr class=\"test\"><td class=\"table-fixed-medium\">No Data to Display here.</td></tr></table>";
                                                                                                                        }
                                                                                                                    }
                                                                                                                            ?>
    <?php } ?>