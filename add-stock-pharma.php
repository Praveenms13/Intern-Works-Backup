<?php

/*
Ajax File Called
                ajax/ajax_pharmacy_stock_avail.php (getListofDatas)
                ajax/ajax_pharmacy_stock_avail.php (movenext)
                ajax/ajax_pharmacy_stock_avail.php (moveprevious)
                ajax/ajax_pharmacy_stock_avail.php (makesavechange)



Included Files
        header.php                         core/class.manageUsers.php                              close.php
        top-menu.php              left-nav.php
End */

?>


<?php

include_once('header.php');
include_once('top-menu.php');
include_once('left-nav.php');
include_once('core/class.manageUsers.php');
$id = $_GET['id'];
?>
<style>
    #container {
        display: flex;
        justify-content: space-between;
    }

    #details {
        margin: 0px;
        flex: 1;
    }

    #details_2 {
        margin: 0px;
        flex: 0 0 200px;
        /* Adjust width as needed */
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
</style>
<?php
if (is_numeric($id)) {
    $prescriptionDetailObj = new ManageUsers();
    $sql = "SELECT * FROM pharmacy_stock_detail WHERE id='" . $id . "'";
    $prescriptions = $prescriptionDetailObj->listDirectQuery($sql);

?>
    <br>
    <div id="main" style="padding-top: 30px !important;">
        <div class="Top-Strip ">
            <h5 style="font-weight:bold; color:#<?php echo $_SESSION['tclr'] ?>;">Edit Stock</h5>
        </div>

        <div class="box box-color">

            <div class="container">
                <form action="pharmacy_stock_avail_save_drug.php" method="POST">
                    <div class="page-header">
                    </div>
                    <?php
                    // echo "<pre>";
                    $array = $prescriptions[0];
                    foreach ($array as $key => $value) {
                        if (is_numeric($key)) {
                            unset($array[$key]);
                        }
                    }
                    if (!empty($array['drug_manifaturer_date'])) {
                        $array['drug_manifaturer_date'] = date("d/m/Y", strtotime($array['drug_manifaturer_date']));
                    }
                    if (!empty($array['drug_expiry_date'])) {
                        $array['drug_expiry_date'] = date("d/m/Y", strtotime($array['drug_expiry_date']));
                    }
                    // print_r($array);
                    // echo "</pre>";
                    ?>
                    <div id="details_3" class="fty d-flex flex-column align-items-end" style="float:right;">
                        <!-- <input type="hidden" name="location_id" id="location_id" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="ohc" id="ohc" value="ohc" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="corp_id" id="corp_id" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="drugtemplateid" id="drugtemplateid" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="drug_name" id="drug_name" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="drug_type" id="drug_type" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="inventory" id="drug_inventory" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="hsncode" id="hsncode" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="created_on" id="created_on" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="created_role" id="created_role" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="created_by" id="created_by" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="drug_strength" id="drug_strengthed" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="schedule" id="scheduled" value="" class="form-control" style="max-width: 200px; float: right;" required />
                        <input type="hidden" name="idno" id="idnno" value="" class="form-control" style="max-width: 200px; float: right;" required />
                        <input type="hidden" name="crd" id="crdd" value="" class="form-control" style="max-width: 200px; float: right;" required />
                        <input type="hidden" name="bill_status" id="billstatus" value="" class="form-control" style="max-width: 200px; float: right;" required />
                        <input type="hidden" name="drug_manifaturer" id="drug_manifaturer" value="" class="form-control" style="max-width: 200px; float: right;" />
                        <input type="hidden" name="drug_ingredient" id="drug_ing" value="" class="form-control" style="max-width: 200px; float: right;" /> -->
                        <div class="p-1">
                            <label for="quantity" style="display: block; text-align: right;">
                                Current Availability: <span id="currentAvailability"><?php echo $array['cur_availability']; ?></span>
                            </label>
                            &nbsp;&nbsp;&nbsp;
                            <input type="text" name="quantity" id="quantity" placeholder="Enter quantity" class="form-control" style="max-width: 150px; float: right;" required />
                            <div style="float: right; margin-top: 10px; display: flex; align-items: center;">
                                <label style="margin-right: 10px; display: flex; align-items: center;">
                                    <input type="radio" name="change" value="increase" style="margin-right: 5px;" /> +
                                </label>
                                <label style="display: flex; align-items: center;">
                                    <input type="radio" name="change" value="decrease" style="margin-right: 5px;" /> -
                                </label>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const quantityInput = document.getElementById('quantity');
                                const currentAvailability = parseInt(document.getElementById('currentAvailability').textContent);
                                const radioButtons = document.getElementsByName('change');

                                function validateQuantity() {
                                    const value = quantityInput.value;
                                    const enteredValue = parseInt(value, 10);
                                    const selectedOption = Array.from(radioButtons).find(radio => radio.checked);
                                    
                                    if (selectedOption && selectedOption.value === 'decrease') {
                                        if (enteredValue > currentAvailability) {
                                            alert('You cannot subtract more than the current quantity.');
                                            quantityInput.value = '';
                                            return false;
                                        }
                                    }
                                    return true;
                                }

                                quantityInput.addEventListener('input', function(event) {
                                    let value = event.target.value;
                                    
                                    if (!/^\d*$/.test(value)) {
                                        event.target.value = value.replace(/[^\d]/g, '');
                                        return;
                                    }

                                    validateQuantity();
                                });

                                radioButtons.forEach(radio => {
                                    radio.addEventListener('change', validateQuantity);
                                });
                            });
                        </script>
                        <div class="p-1">
                            <label for="drug_manifaturer_date" style="display: block; text-align: right;">Manufacturer Date</label>
                            <input type="text" name="drug_manifaturer_date" id="drug_manifaturer_date" value="<?php echo htmlspecialchars($array['drug_manifaturer_date']); ?>" placeholder="DD/MM/YYYY" class="form-control mask_date_new" style="max-width: 200px; float: right;" required />
                        </div>
                        <div class="p-1">
                            <label for="drug_expiry_date" style="display: block; text-align: right;">Expiry Date</label>
                            <input type="text" name="drug_expiry_date" id="drug_expiry_date" value="<?php echo htmlspecialchars($array['drug_expiry_date']); ?>" class="form-control mask_date_new" placeholder="DD/MM/YYYY" style="max-width: 200px; float: right;" required />
                        </div>
                        <div class="p-1">
                            <label for="drug_batch" style="display: block; text-align: right;">Batch</label>
                            <input type="text" name="drug_batch" id="drug_batch" value="<?php echo htmlspecialchars($array['drug_batch']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                        </div>
                        <input type="hidden" name="record_id" value="<?php echo $id ?>">
                        <br>
                        <div class="button-container">
                            <style>
                                .button-container {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    width: 150px;
                                    margin: 0 auto;
                                }
                            </style>
                            <button onclick="goBack()">Back To List</button>
                            <input type="submit" value="update" name="update">
                        </div>
                        <script>
                            function goBack() {
                                window.location.href = 'stock-available-pharma.php';
                            }
                        </script>
                    </div>
                    <div id="container" style="display: flex; justify-content: space-between;">
                        <div id="details" style="margin: 0px; flex: 1;">
                            <style>
                                .details_item {
                                    margin-bottom: 5px;
                                }
                            </style>
                            <form method="POST" action="your_action_page.php">
                                <p class="details_item"><strong>Drug Name:</strong>
                                    <span><?php echo htmlspecialchars($array['drug_name']); ?></span>
                                    <input type="hidden" name="drug_name" value="<?php echo htmlspecialchars($array['drug_name']); ?>">
                                </p>
                                <p class="details_item"><strong>Manufacturer:</strong>
                                    <span><?php echo htmlspecialchars($array['drug_manifaturer']); ?></span>
                                    <input type="hidden" name="drug_manifaturer" value="<?php echo htmlspecialchars($array['drug_manifaturer']); ?>">
                                </p>
                                <p class="details_item"><strong>HSN Code:</strong>
                                    <span><?php echo htmlspecialchars($array['hsncode']); ?></span>
                                    <input type="hidden" name="hsncode" value="<?php echo htmlspecialchars($array['hsncode']); ?>">
                                </p>
                                <p class="details_item"><strong>Restock:</strong>
                                    <span><?php echo htmlspecialchars($array['inventory']); ?></span>
                                    <input type="hidden" name="inventory" value="<?php echo htmlspecialchars($array['inventory']); ?>">
                                </p>
                                <p class="details_item"><strong>ID No:</strong>
                                    <span><?php echo htmlspecialchars($array['idno']); ?></span>
                                    <input type="hidden" name="idno" value="<?php echo htmlspecialchars($array['idno']); ?>">
                                </p>
                                <p class="details_item"><strong>Bill Status:</strong>
                                    <span><?php echo htmlspecialchars($array['bill_status']); ?></span>
                                    <input type="hidden" name="bill_status" value="<?php echo htmlspecialchars($array['bill_status']); ?>">
                                </p>
                                <p class="details_item"><strong>Schedule:</strong>
                                    <span><?php echo htmlspecialchars($array['schedule']); ?></span>
                                    <input type="hidden" name="schedule" value="<?php echo htmlspecialchars($array['schedule']); ?>">
                                </p>
                                <p class="details_item"><strong>Drug Ingredient:</strong>
                                    <span><?php echo htmlspecialchars($array['drug_ingredient']); ?></span>
                                    <input type="hidden" name="drug_ingredient" value="<?php echo htmlspecialchars($array['drug_ingredient']); ?>">
                                </p>
                                <p class="details_item"><strong>CRD:</strong>
                                    <span><?php echo htmlspecialchars($array['crd']); ?></span>
                                    <input type="hidden" name="crd" value="<?php echo htmlspecialchars($array['crd']); ?>">
                                </p>
                                <!-- <input type="submit" value="Submit"> -->
                            </form>
                        </div>

                        <div id="details_2" style="margin: 0px; flex: 0 0 200px; display: flex; flex-direction: column; align-items: flex-start;">
                            <div class="p-1">
                                <label for="mrp" style="display: block; text-align: right;">MRP</label>
                                <input type="text" name="amount_per_strip" id="amount_per_strip" value="<?php echo htmlspecialchars($array['amount_per_strip']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div>
                            <div class="p-1">
                                <label for="mrp_per_unit" style="display: block; text-align: right;">MRP Per Unit</label>
                                <input type="text" name="amount_per_tab" id="amounttab" value="<?php echo htmlspecialchars($array['amount_per_tab']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div>
                            <div class="p-1">
                                <label for="package_unit" style="display: block; text-align: right;">Package Unit</label>
                                <input type="text" name="tablet_qty_strip" id="tablet" value="<?php echo htmlspecialchars($array['tablet_qty_strip']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div>
                            <div class="p-1">
                                <label for="unit" style="display: block; text-align: right;">Unit</label>
                                <input type="text" name="unit_per_tab" id="unit" value="<?php echo htmlspecialchars($array['unit_per_tab']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div>
                            <div class="p-1">
                                <label for="sgst" style="display: block; text-align: right;">SGST</label>
                                <input type="text" name="sgst" id="ssgst" value="<?php echo htmlspecialchars($array['sgst']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div>
                            <div class="p-1">
                                <label for="cgst" style="display: block; text-align: right;">CGST</label>
                                <input type="text" name="cgst" id="ccgst" value="<?php echo htmlspecialchars($array['cgst']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div>
                            <div class="p-1">
                                <label for="igst" style="display: block; text-align: right;">IGST</label>
                                <input type="text" name="igst" id="iigst" value="<?php echo htmlspecialchars($array['igst']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div>
                            <!-- <div class="p-1">
                                <label for="discount" style="display: block; text-align: right;">Discount</label>
                                <input type="text" name="discount_settings" id="discount" value="<?php echo htmlspecialchars($array['discount_settings']); ?>" class="form-control" style="max-width: 200px; float: right;" required />
                            </div> -->

                            <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-3 p-2">
                                Discount<br>
                                <div style="display: flex; align-items: center;">
                                    <input type="checkbox" id="discount_settings_checkbox" name="discount_settings_checkbox" value="1" <?php echo ($array['discount_settings'] == "1") ? "checked='checked'" : ""; ?> onchange="toggleDiscountInput()">
                                    <input type="text" name="discount" id="discount" value="<?php echo $array['discount']; ?>" class="input-large" style="margin-left: 10px;" oninput="toggleCheckbox()">
                                    <input type="hidden" id="discount_settings" name="discount_settings" value="<?php echo $array['discount_settings'] == '1' ? '1' : '0'; ?>">
                                </div>
                                <p class="hint-1">Click checkbox to apply overall discount.</p>
                            </div>

                            <script>
                                function toggleDiscountInput() {
                                    var checkbox = document.getElementById('discount_settings_checkbox');
                                    var discountInput = document.getElementById('discount');
                                    var hiddenInput = document.getElementById('discount_settings');

                                    if (checkbox.checked) {
                                        discountInput.disabled = true;
                                        discountInput.value = "";
                                        hiddenInput.value = "1";
                                    } else {
                                        discountInput.disabled = false;
                                        hiddenInput.value = "0";
                                    }
                                }

                                function toggleCheckbox() {
                                    var checkbox = document.getElementById('discount_settings_checkbox');
                                    var discountInput = document.getElementById('discount');
                                    var hiddenInput = document.getElementById('discount_settings');

                                    if (discountInput.value !== "") {
                                        checkbox.checked = false;
                                        hiddenInput.value = "0";
                                    }
                                    checkbox.disabled = discountInput.value !== "";
                                }

                                window.onload = function() {
                                    var checkbox = document.getElementById('discount_settings_checkbox');
                                    var discountInput = document.getElementById('discount');
                                    if (checkbox.checked) {
                                        discountInput.disabled = true;
                                    }
                                };
                            </script>

                            <!-- <p><strong>Discount Settings:</strong> <input id="discount_settings" value="<?php echo htmlspecialchars($array['discount_settings']); ?>" /></p> -->
                        </div>


                    </div>
                    <div id="no_records" style="display: none;">
                        <h4>No records found.</h4>
                    </div>
                </form>


            </div>
        </div>
    <?php
} else {
    $phar_id = $_GET['pharma_id'];
    $prescriptionDetailObj = new ManageUsers();
    $drugList = $prescriptionDetailObj->listDirectQuery("SELECT drug_name,drug_type, drug_strength FROM drug_template");
    $optionsHtml = '';
    foreach ($drugList as $drug) {
        $optionsHtml .= '<option value="' . htmlspecialchars($drug['drug_name']) . '">' . htmlspecialchars($drug['drug_name']) . '</option>';
    }
    if (!empty($_SESSION['ohc_loca'])) {
        $gst = $prescriptionDetailObj->listDirectQuery("SELECT sgst,cgst,igst FROM `master_corporate`  WHERE id='" . $_SESSION['ohc_loca'] . "'");
    } else {

        $gst = $prescriptionDetailObj->listDirectQuery("SELECT sgst,cgst,igst FROM `master_pharmacy`  WHERE
	id='" . $sessionlocation_id . "' ");
    }
    $prescriptionDetailObj = null;
    if ($prescriptions != 0) {
        $drug_name = $prescriptions[0]['drug_name'];
        $drug_ingredient = $prescriptions[0]['drug_ingredient'];
        $drug_manifaturer = $prescriptions[0]['drug_manifaturer'];
        $drug_manifaturer_date = explode("-", $prescriptions[0]['drug_manifaturer_date']);
        $drug_manifaturer_date = ($drug_manifaturer_date[2] <> "") ? ($drug_manifaturer_date[2] . "/" . $drug_manifaturer_date[1] . "/" . $drug_manifaturer_date[0]) : "";
        $drug_batch = $prescriptions[0]['drug_batch'];
        $drug_expiry_period = $prescriptions[0]['drug_expiry_period'];
        $drug_expiry_date = explode("-", $prescriptions[0]['drug_expiry_date']);
        $drug_expiry_date = ($drug_expiry_date[2] <> "") ? ($drug_expiry_date[2] . "/" . $drug_expiry_date[1] . "/" . $drug_expiry_date[0]) : "";
        $drug_type = $prescriptions[0]['drug_type'];
        $drug_strength = $prescriptions[0]['drug_strength'];
        $inventory = $prescriptions[0]['inventory'];
        $amount_per_tab = $prescriptions[0]['amount_per_tab'];
        $amount_per_strip = $prescriptions[0]['amount_per_strip'];
        $tablet_qty_strip = $prescriptions[0]['tablet_qty_strip'];
        $unit_per_tab = $prescriptions[0]['unit_per_tab'];
        $quantity = $prescriptions[0]['quantity'];
        $discount = $prescriptions[0]['discount'];
        $isactive = $prescriptions[0]['isactive'];
        $drugtype = $prescriptions[0]['drug_type'];
        $schedule = $prescriptions[0]['schedule'];

        $discount_settings = $prescriptions[0]['discount_settings'];
    }
    if (!empty($_REQUEST['id'])) {
        $sgst = $prescriptions['sgst'];
        $cgst = $prescriptions['cgst'];
        $igst = $prescriptions['igst'];
    } else {
        echo $sgst = $gst[0]['sgst'];
        $cgst = $gst[0]['cgst'];
        $igst = $gst[0]['igst'];
    }
    ?>

        <div id="main" style="padding-top: 30px !important;">
            <div class="Top-Strip ">
                <h5 style="font-weight:bold; color:#<?php echo $_SESSION['tclr'] ?>;">Add Stock Availability</h5>
            </div>



            <div class="box box-color">

                <div class="container">
                    <form action="pharmacy_stock_avail_save_drug.php" method="POST">
                        <div class="page-header">
                            <b>Select from Drug Template</b><br>
                                <select title="Drug Type" name="drug-name" id="drug-name" class="select2-me" required onchange="fetchData()" style="width:50%; text-align:center; font-size:20px !important; height: 50px !important;">
                                    <option value="">Search</option>
                                    <?php
                                    foreach ($drugList as $inj) {
                                        $sel = '';
                                        echo '<option value="' . $inj['drug_name'] . '" ' . $sel . '>' . $inj['drug_name'] . ' (' . $inj['drug_strength'] .') ' . $inj['drug_type'] .'</option>';
                                    }
                                    ?>
                                </select>
                        </div>
                        <div id="container" style="width: 66%; float:left; justify-content: space-between;">
                            <div id="details" style="margin: 0px; flex: 0 0 50%; display: none; width: 50%;">
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Manufaturer</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="additional_details"></span></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>HSN Code</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="hsncodee"></span></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Re-stock Count</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="emp_inventory"></span></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>ID no</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="idno"></span></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Bill Status</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="bill_status"></span></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Schedule</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="schedule"></span></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Drug Ingredient</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="drug_ingredient"></span></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>CRD</strong></div>
                                    <div style="width: 70%; float:left;">: <span id="crd"></span></div>
                                </div>
                            </div>
                            <div id="details_2" style="margin: 0px; flex: 0 0 50%; display: flex; flex-direction: column; align-items: flex-start; display: none;">
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>MRP</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="amount_per_strip" id="amount_per_strip" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Packed Unit</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="tablet_qty_strip" id="tablet" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div>
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>MRP Per Unit</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="amount_per_tab" id="amounttab" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div> 
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Unit to Issue</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="unit_per_tab" id="unit" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div>  
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>SGST</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="sgst" id="ssgst" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div> 
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>CGST</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="cgst" id="ccgst" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div> 
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>IGST</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="igst" id="iigst" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div>  
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Discount</strong></div>
                                    <div style="width: 70%; float:left;">
                                        <input type="checkbox" id="discount_settings_checkbox" name="discount_settings_checkbox" value="1" <?php echo ($discount_settings == "1") ? "checked='checked'" : ""; ?> onchange="toggleDiscountInput()">
                                        <input type="text" name="discount" id="discount" value="<?php echo $discount; ?>" style="margin-left: 5px; max-width: 80px;" oninput="toggleCheckbox()">
                                        <input type="hidden" id="discount_settings" name="discount_settings" value="<?php echo $discount_settings == '1' ? '1' : '0'; ?>">
                                        <p class="hint-1">Click checkbox to apply overall discount.</p>
                                    </div>
                                </div>  

                                <script>
                                    function toggleDiscountInput() {
                                        var checkbox = document.getElementById('discount_settings_checkbox');
                                        var discountInput = document.getElementById('discount');
                                        var hiddenInput = document.getElementById('discount_settings');

                                        if (checkbox.checked) {
                                            discountInput.disabled = true;
                                            discountInput.value = "";
                                            hiddenInput.value = "1";
                                        } else {
                                            discountInput.disabled = false;
                                            hiddenInput.value = "0";
                                        }
                                    }

                                    function toggleCheckbox() {
                                        var checkbox = document.getElementById('discount_settings_checkbox');
                                        var discountInput = document.getElementById('discount');
                                        var hiddenInput = document.getElementById('discount_settings');

                                        if (discountInput.value !== "") {
                                            checkbox.checked = false;
                                            checkbox.disabled = true;
                                            hiddenInput.value = "0";
                                        } else {
                                            checkbox.disabled = false;
                                        }
                                    }

                                    window.onload = function() {
                                        toggleDiscountInput();
                                    };
                                </script>

                                <input type="hidden" name="phar_ids" id="phar_ids" value="<?php echo $phar_id; ?>" class="form-control" style="max-width: 200px" />
                                <!-- <p><strong>Discount Settings:</strong> <input id="discount_settings" value="" /></p> -->

                            </div>

                        </div>
                        
                        <div class="" style="float:right; width:34%;">
                            <div style="display: none;" id="details_3" style="float:right;">
                                <input type="hidden" name="location_id" id="location_id" />
                                <input type="hidden" name="ohc" id="ohc" value="ohc" />
                                <input type="hidden" name="corp_id" id="corp_id" />
                                <input type="hidden" name="drugtemplateid" id="drugtemplateid" />
                                <input type="hidden" name="drug_name" id="drug_name" />
                                <input type="hidden" name="drug_type" id="drug_type" />
                                <input type="hidden" name="inventory" id="drug_inventory" />
                                <input type="hidden" name="hsncode" id="hsncode" />
                                <input type="hidden" name="created_on" id="created_on" />
                                <input type="hidden" name="created_role" id="created_role" />
                                <input type="hidden" name="created_by" id="created_by" />
                                <input type="hidden" name="drug_strength" id="drug_strengthed" />
                                <input type="hidden" name="schedule" id="scheduled" />
                                <input type="hidden" name="idno" id="idnno" />
                                <input type="hidden" name="crd" id="crdd" />
                                <input type="hidden" name="bill_status" id="billstatus" />
                                <input type="hidden" name="drug_manifaturer" id="drug_manifaturer" />
                                <input type="hidden" name="drug_ingredient" id="drug_ing" />
                                
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Quantity</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="quantity" id="quantity" value="" class="form-control" style="max-width: 100px;" required /></div>
                                </div> 
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Manufacturer Date</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="drug_manifaturer_date" id="drug_manifaturer_date" value="" placeholder="DD/MM/YYYY" class="form-control mask_date_new" style="max-width: 150px;" required /></div>
                                </div> 
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Expiry Date</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="drug_expiry_date" id="drug_expiry_date" value="" class="form-control mask_date_new" placeholder="DD/MM/YYYY" style="max-width: 150px;" required /></div>
                                </div> 
                                <div style="width: 100%; float:left;">
                                    <div style="width: 30%; float:left;"><strong>Batch</strong></div>
                                    <div style="width: 70%; float:left;"><input type="text" name="drug_batch" id="drug_batch" value="" class="form-control" style="max-width: 150px;" required /></div>
                                </div>  
                                <input type="submit" value="Submit" name="submit">
                            </div>
                        </div>

                        <div id="no_records" style="display: none;">
                            <h4>No records found.</h4>
                        </div>
                    </form>


                </div>
            </div>
            <!--				<div id="existUploadXls"></div>-->

            <script>
                function fetchData() {
                    var drug = $('#drug-name').val();

                    if (drug !== '') {
                        $.ajax({
                            type: "POST",
                            url: 'ajax/ajax_pharmacy_data.php',
                            data: {
                                drug_nameed: drug
                            },
                            success: function(data) {
                                console.log(data);
                                if (data.length > 0) {
                                    console.log(data);
                                    var data = JSON.parse(data);
                                    $("#details").show();
                                    $("#details_2").show();
                                    $("#details_3").show();
                                    $("#emp_inventory").text(data.inventory);
                                    $("#ohc").val(data.ohc);
                                    $("#corp_id").val(data.corp_id);
                                    $("#location_id").val(data.location_id);
                                    $("#emp_inventory").text(data.inventory);
                                    $("#drugtemplateid").val(data.id);
                                    $("#drug_inventory").val(data.inventory);
                                    $("#hsncode").val(data.hsncode);
                                    $("#hsncodee").text(data.hsncode);
                                    $("#drug_name").val(data.drug_name);
                                    $("#drug_type").val(data.drug_type);
                                    $("#created_on").val(data.created_on);
                                    $("#created_role").val(data.created_role);
                                    $("#created_by").val(data.created_by);
                                    $("#drug_manifaturer").val(data.drug_manufacturer);
                                    $("#mani").text(data.drug_manufacturer);
                                    $("#amountper_strip").text(data.amount_per_strip);
                                    $("#amount_per_strip").val(data.amount_per_strip);
                                    $("#unit_per_tab").text(data.unit_per_tab);
                                    $("#unit").val(data.unit_per_tab);
                                    $("#tablet_qty_strip").text(data.tablet_qty_strip);
                                    $("#tablet").val(data.tablet_qty_strip);
                                    $("#amount_per_tab").text(data.amount_per_tab);
                                    $("#amounttab").val(data.amount_per_tab);
                                    $("#discount_settings").text(data.discount_settings);
                                    $("#discount").val(data.discount);
                                    $("#sgst").text(data.sgst);
                                    $("#ssgst").val(data.sgst);
                                    $("#idnno").val(data.idno);
                                    $("#idno").text(data.idno);

                                    $("#cgst").text(data.cgst);
                                    $("#ccgst").val(data.cgst);
                                    $("#igst").text(data.igst);
                                    $("#iigst").val(data.igst);
                                    $("#bill_status").text(data.bill_status);
                                    $("#billstatus").val(data.bill_status);
                                    $("#schedule").text(data.schedule);
                                    $("#scheduled").val(data.schedule);
                                    $("#crd").text(data.crd);
                                    $("#crdd").val(data.crd);
                                    $("#drug_strength").text(data.drug_strength);
                                    $("#drug_strengthed").val(data.drug_strength);
                                    $("#additional_details").text(data.drug_manufacturer);
                                    $("#amountper_strip").val(data.amount_per_strip);
                                    $("#unit_per_tab").val(data.unit_per_tab);
                                    $("#tablet_qty_strip").val(data.tablet_qty_strip);
                                    $("#amount_per_tab").val(data.amount_per_tab);
                                    $("#discount_settings").val(data.discount_settings);
                                    if (data.discount_settings == 1) {
                                        document.getElementById("discount_settings_checkbox").checked = true;
                                    }
                                    $("#sgst").val(data.sgst);
                                    $("#cgst").val(data.cgst);
                                    $("#igst").val(data.igst);
                                    $("#details").show();
                                    $("#details_2").show();
                                    $("#details_3").show();
                                    $.ajax({
                                        url: 'ajax/ajax_pharmacy_data.php',
                                        type: 'POST',
                                        data: {
                                            drug_ingredient_id: parsedData.drug_ingredient
                                        },
                                        success: function(response) {
                                            var ingredientData;
                                            try {
                                                ingredientData = JSON.parse(response);
                                            } catch (e) {
                                                console.error('Failed to parse ingredient data');
                                                return;
                                            }
                                            $("#drug_ingredient").text(ingredientData.name);
                                            $("#drug_ing").val(ingredientData.name);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Stock request failed:', error);
                                        }
                                    });
                                } else {
                                    alert('No Data Found');
                                    html += '<tr>';
                                    html += '<td colspan="7">No data found</td>';
                                    html += '</tr>';
                                }
                                $('#dataTable tbody').html(html);
                            },
                            error: function() {
                                alert('Error: ' + 'An error occurred while processing your request. Please try again.');
                            }
                        });
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    var ftyElement = document.querySelector('.fty');
                    if (ftyElement) {
                        ftyElement.style.display = 'none';
                    }
                });
            </script>

        <?php } ?>

        <?php require_once('../close.php') ?>