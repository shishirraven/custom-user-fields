<?php
include("braveUser.php");

// =========================================================================
// Read fields from the OPTIONs and add it to Edit, Add and Profile fields. 
// =========================================================================

add_action('init', 'addFieldsToUser');

function addFieldsToUser()
{
    $user = new braveUser();
    $fields = get_option("braveWPUser");

    if ($fields != "") {

        $field_array = json_decode($fields, true);

        for ($i = 0; $i < count($field_array); $i++) {

            $fieldName = $field_array[$i]["fieldName"];
            $fieldLabel = $field_array[$i]["fieldLabel"];
            $fieldPlaceholder = $field_array[$i]["fieldPlaceholder"];
            $rangeMin = $field_array[$i]["rangeMin"];
            $rangeMax = $field_array[$i]["rangeMax"];
            $rangeDefault = $field_array[$i]["rangeDefault"];
            $rangeStep = $field_array[$i]["rangeStep"];
            $fieldType = $field_array[$i]["fieldType"];
            $addRegistrationPage = $field_array[$i]["addRegistrationPage"];
            $showOnUserListing = $field_array[$i]["userList"];
            $optionData = array();
            if (isset($field_array[$i]["optionData"])) {
                $optionData = $field_array[$i]["optionData"];
            }

            $field = new wpUserField();
            $field->setName($fieldName);
            $field->setLabel($fieldLabel);
            $field->setPlaceholder($fieldPlaceholder);
            $field->setRangeMin($rangeMin);
            $field->setRangeMax($rangeMax);
            $field->setValue($rangeDefault);
            $field->setStep($rangeStep);
            $field->setShowOnRegistration($addRegistrationPage);
            $field->setShowOnUserListing($showOnUserListing);
            $field->setInputType($fieldType);

            if (current_user_can('administrator')) {
                $field->setPostFixHTML(
                    "
                    <div id='action-btn-group'>
                        <button type='button' data='" . $fieldName . "' class='button deleteField'>
                            <span class='dashicons dashicons-trash'></span> Delete
                        </button>
                        
                        <button type='button' data='" . $fieldName . "' data-json='" . json_encode($field_array[$i]) . "' class='button editBraveField'>
                            <span class='dashicons dashicons-edit '></span> Edit
                        </button> 
                        
                        <button type='button' data='" . $fieldName . "' class='button upField'>
                            <span class='dashicons dashicons-arrow-up-alt2'></span> Up
                        </button>

                        <button type='button' data='" . $fieldName . "' class='button downField'>
                            <span class='dashicons dashicons-arrow-down-alt2'></span> Down
                        </button>
                    </div>
                    "
                );
            }

            // of not logged in user
            if (!is_user_logged_in()) {
                $field->setFieldWrapper("stacked");
            }


            $array_option = array();

            foreach ($optionData as $key => $value) {
                $optionItem = array();
                //   print_r($value);
                $optionItem[] = $value["value"];
                $optionItem[] = $value["label"];
                $array_option[] = $optionItem;
            }
            $field->setOptions($array_option);

            $user->addField($field);
        }
    }

    // Show FORM. 
    // AJAX TO _brave_add_field
    if (current_user_can('administrator')) {
        add_action('edit_user_profile', 'addDynamicFields');
        add_action('show_user_profile', 'addDynamicFields');
        add_action('user_new_form', 'addDynamicFields');
    }
    // add_action('register_form', 'addDynamicFields');
}

function addDynamicFields()
{
?>
    <div class="new-field" id="BrAddField">
        NEED A NEW USER FIELD ?
    </div>
    <style>
        .table-otions td {
            border: 1px solid #000;
        }

        .new-field {
            margin-top: 10px;
            margin-bottom: 10px;
            border: 2px dashed grey;
            padding: 5px;
            text-align: center;
            font-weight: 500;
            border-radius: 5px;
            cursor: pointer;
            max-width: 550px;
            color: gray;
        }

        #options-for-input,
        #edit-options-for-input,
        #value_for_range,
        #edit_value_for_range {
            border: 1px solid grey;
            padding: 10px;
            margin: 10px 5px;
        }

        select#fieldType option {
            text-transform: capitalize;
        }

        #new-option-form {
            display: flex;
            gap: 10px;
            align-items: center;

        }

        #closebrAddForm {

            float: right;
        }

        #editDialog {
            width: 600px;
            -webkit-box-shadow: 7px 7px 30px -7px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: 7px 7px 30px -7px rgba(0, 0, 0, 0.75);
            box-shadow: 7px 7px 30px -7px rgba(0, 0, 0, 0.75);
            border: 0;
            border-radius: 0px;
        }
    </style>

    <!-- ======================================= -->
    <!-- BEGIN: ADD FIELD FORM -->
    <!-- ======================================= -->
    <div id="brAddForm" style="display: none; border: 1px dashed gray; padding: 20px; ">
        <button type="button" class="button" id="closebrAddForm">Close X</button>

        <div style="display:flex; flex-direction: column; gap: 10px; margin-bottom:10px;">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="fieldType">Field Type (required)</label>
                        </th>
                        <td>
                            <select id="fieldType" name="fieldType" value="" class="regular-text">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="checkbox">checkbox</option>
                                <option value="radio">radio</option>
                                <option value="select">Select</option>
                                <option value="password">password</option>
                                <option value="number">number</option>
                                <option value="color">color</option>
                                <option value="date">date</option>
                                <option value="datetime-local">datetime-local</option>
                                <option value="month">month</option>
                                <option value="range">range</option>
                                <option value="search">search</option>
                                <option value="tel">tel</option>
                                <option value="time">time</option>
                                <option value="url">url</option>
                                <option value="week">week</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="fieldLabel">Field Label (required)</label>
                        </th>
                        <td>
                            <input type="text" id="fieldLabel" name="fieldLabel" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="fieldName">Field Name (required)</label>
                        </th>
                        <td>
                            <input type="text" id="fieldname" name="fieldName" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="fieldPlaceholder">Placeholder</label>
                        </th>
                        <td>
                            <input type="text" id="fieldPlaceholder" name="fieldPlaceholder" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Registration page
                        </th>
                        <td>
                            <input type="checkbox" onclick="checkRegistration()" name="addRegistrationPage" id="addRegistrationPage" value="">
                            <label for="addRegistrationPage">Add field on registration page?</label>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            User listing
                        </th>
                        <td>
                            <input type="checkbox" onclick="checkUserList()" name="userList" id="userList" value="">
                            <label for="userList">Show on user listing?</label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Field Options -->
        <div id="options-for-input" style="display:none;">
            <div>
                <h2>Field Options</h2>
            </div>
            <div class="padding: 10px">
                <table id="optionsTable" class="wp-list-table widefat fixed striped table-view-list posts" style="display:none;">
                    <thead>
                        <tr>
                            <th>Value</th>
                            <th>Label</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="existingOptions"></tbody>
                </table>
            </div>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label for="option_label">Option Label</label>
                        </th>
                        <td>
                            <input type="text" id="option_label" name="option_label" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="option_value">Option Value </label>
                        </th>
                        <td>
                            <input type="text" id="option_value" name="option_value" value="" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th><button id="add_option" type="button" class="button">Add Option</button></th>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Range Min & Max Options -->
        <div id="value_for_range" style="display:none;">
            <div style="margin-bottom: 3px;"><b>Range Min & Max Value</b></div>
            <div id="new-option-form">
                <label for="rangeMin">Min</label>
                <input type="number" id="rangeMin" name="rangeMin" />
                <br />
                <label for="rangeMax">Max</label>
                <input type="number" id="rangeMax" name="rangeMax" value="" />

                <label for="rangeDefault">Value</label>
                <input type="number" id="rangeDefault" name="rangeDefault" value="" />

                <label for="rangeStep">Step</label>
                <input type="number" id="rangeStep" name="rangeStep" value="" />
            </div>
        </div>

        <div id="sendAjaxRequestToAdd" class="button button-primary">Add Field</div>
    </div>
    <!-- ======================================= -->
    <!-- END: ADD FIELD FORM -->
    <!-- ======================================= -->


    <!-- ======================================= -->
    <!-- BEGIN: EDIT DIALOG BOX -->
    <!-- ======================================= -->
    <dialog id="editDialog">
        <div>
            <div style="display: block; border: 1px dashed gray; padding: 20px; margin-bottom: 5px;">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th>
                                <label for="editFieldType">Field Type (required)</label>
                            </th>
                            <td>
                                <select id="editFieldType" name="editFieldType" value="" class="regular-text">
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="checkbox">checkbox</option>
                                    <option value="radio">radio</option>
                                    <option value="select">Select</option>
                                    <option value="password">password</option>
                                    <option value="number">number</option>
                                    <option value="color">color</option>
                                    <option value="date">date</option>
                                    <option value="datetime-local">datetime-local</option>
                                    <option value="month">month</option>
                                    <option value="range">range</option>
                                    <option value="search">search</option>
                                    <option value="tel">tel</option>
                                    <option value="time">time</option>
                                    <option value="url">url</option>
                                    <option value="week">week</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="editFieldLabel">Field Label (required)</label>
                            </th>
                            <td>
                                <input type="text" id="editFieldLabel" name="editFieldLabel" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="editFieldName">Field Name (required)</label>
                            </th>
                            <td>
                                <input type="text" id="editFieldname" name="editFieldName" class="regular-text" />
                                <input type="hidden" id="editFieldnameOld" name="editFieldnameOld" class="regular-text" />
                            </td>
                        </tr>
                        <tr id="placeholderRow">
                            <th>
                                <label for="editFieldPlaceholder">Placeholder</label>
                            </th>
                            <td>
                                <input type="text" id="editFieldPlaceholder" name="editFieldPlaceholder" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Registration page
                            </th>
                            <td>
                                <input type="checkbox" onclick="editCheckRegistration()" name="editAddRegistrationPage" id="editAddRegistrationPage" value="">
                                <label for="editAddRegistrationPage">Add field on registration page?</label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                User listing
                            </th>
                            <td>
                                <input type="checkbox" onclick="editCheckUserList()" name="editUserList" id="editUserList" value="">
                                <label for="editUserList">Show on user listing?</label>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Field Options -->
                <div id="edit-options-for-input" style="display: none;">
                    <div>
                        <h2>Field Options</h2>
                    </div>
                    <div style="padding: 10px;">
                        <table id="editOptionsTable" class="wp-list-table widefat fixed striped table-view-list posts" style="display:table;">
                            <thead>
                                <tr>
                                    <th>Value</th>
                                    <th>Label</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="editExistingOptions"></tbody>
                        </table>
                    </div>
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th>
                                    <label for="edit_option_label">Option Label</label>
                                </th>
                                <td>
                                    <input type="text" id="edit_option_label" name="edit_option_label" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="edit_option_value">Option Value </label>
                                </th>
                                <td>
                                    <input type="text" id="edit_option_value" name="edit_option_value" value="" class="regular-text" />
                                </td>
                            </tr>
                            <tr>
                                <th><button id="edit_add_option" type="button" class="button">Add Option</button></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Range Min & Max Options -->
                <div id="edit_value_for_range" style="display:none;">
                    <div style="margin-bottom: 10px;">
                        <h2 style="margin: 0px">Range Min &amp; Max Value</h2>
                    </div>
                    <div id="edit-new-option-form">

                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th>
                                        <label for="editRangeMin">Min</label>
                                    </th>
                                    <td>
                                        <input type="number" id="editRangeMin" name="editRangeMin" class="regular-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="editRangeMax">Max</label>
                                    </th>
                                    <td>
                                        <input type="number" id="editRangeMax" name="editRangeMax" value="" class="regular-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="editRangeDefault">Value</label>
                                    </th>
                                    <td>
                                        <input type="number" id="editRangeDefault" name="editRangeDefault" value="" class="regular-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="editRangeStep">Step</label>
                                    </th>
                                    <td>
                                        <input type="number" id="editRangeStep" name="editRangeStep" value="" class="regular-text">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div style="display:inline-flex; width:100%; justify-content:end; margin-top: 10px">
                <button type="button" class="button" id="dialogClose" value="default">Close</button>
                <button type="button" class="button button-primary" id="editSendAjaxRequestToAdd" value="default">Update</button>
            </div>
        </div>
    </dialog>
    <!-- ======================================= -->
    <!-- END: EDIT DIALOG BOX -->
    <!-- ======================================= -->


    <script>
        var addRegistrationPage = document.getElementById('addRegistrationPage')

        function checkRegistration() {
            if (addRegistrationPage.checked) {
                addRegistrationPage.value = true;
            } else {
                addRegistrationPage.value = null;
            }
        }

        // Checking User List
        var user_list = document.getElementById('userList')

        function checkUserList() {
            if (user_list.checked) {
                user_list.value = true;
            } else {
                user_list.value = null;
            }
        }

        document
            .getElementById('BrAddField')
            .addEventListener('click', function() {
                document.getElementById('brAddForm').style.display = 'block';
            });

        var optionData = Array();

        function editCheckRegistration() {
            if (document.getElementById('editAddRegistrationPage').checked) {
                document.getElementById('editAddRegistrationPage').value = true;
            } else {
                document.getElementById('editAddRegistrationPage').value = null;
            }
        }

        // Checking User List
        var edit_user_list = document.getElementById('editUserList')

        function editCheckUserList() {
            if (edit_user_list.checked) {
                edit_user_list.value = true;
            } else {
                edit_user_list.value = null;
            }
        }

        var editDialog = function() {
            var jsonData = JSON.parse(this.getAttribute("data-json"));
            var fieldName = this.getAttribute("data");
            console.log(jsonData, 'jsonData...');
            document.getElementById('editDialog').showModal();

            // Filling data into fields. 
            document.getElementById("editFieldLabel").value = jsonData.fieldLabel;
            document.getElementById("editFieldname").value = jsonData.fieldName;
            document.getElementById("editFieldnameOld").value = jsonData.fieldName;
            document.getElementById("editFieldPlaceholder").value = jsonData.fieldPlaceholder;
            document.getElementById("editFieldType").value = jsonData.fieldType;
           
            // Range
            document.getElementById("editRangeMin").value = jsonData.rangeMin;
            document.getElementById("editRangeMax").value = jsonData.rangeMax;
            document.getElementById("editRangeDefault").value = jsonData.rangeDefault;
            document.getElementById("editRangeStep").value = jsonData.rangeStep;
            document.getElementById('editAddRegistrationPage').checked = jsonData.addRegistrationPage;
            document.getElementById('editUserList').checked = jsonData.userList;


            // Opening the Options if the data is of type radio,select
            if (jsonData.fieldType == "radio" || jsonData.fieldType == "select") {
                document.getElementById('edit-options-for-input').style.display = 'block';
            } else {
                document.getElementById('edit-options-for-input').style.display = 'none';
            }

            // Opening the range option if the data is range
            if (jsonData.fieldType == "range") {
                document.getElementById('edit_value_for_range').style.display = "block";
            } else {
                document.getElementById('edit_value_for_range').style.display = "none"
            }

            console.log("option", jsonData.optionData)
            optionData = jsonData.optionData;
            editAddFieldOptionToDom();

            function editDeleteOption(index) {
                optionData.splice(index, 1);
                editAddFieldOptionToDom();
            }

            function editAddFieldOptionToDom() {
                var elContainer = document.getElementById("editExistingOptions");
                elContainer.innerHTML = "";

                // show the elContainer if there are options.
                for (let i = 0; i < optionData.length; i++) {
                    element = optionData[i];

                    var tr = document.createElement("tr");
                    var div = document.createElement("div");
                    div.classList = "button primary-button ";
                    div.addEventListener("click", function() {
                        if (confirm("Delete?")) {
                            editDeleteOption(i);
                        }
                    });
                    var label = document.createTextNode(element.label);
                    var optionValue = document.createTextNode(element.value);
                    var deleteIcon = document.createTextNode(" Delete  ");

                    td = document.createElement("td");
                    td1 = document.createElement("td");
                    td2 = document.createElement("td");
                    div.appendChild(deleteIcon);
                    td.appendChild(div)
                    td1.appendChild(label);
                    td2.appendChild(optionValue);
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td);


                    elContainer.appendChild(tr);

                }
            }

            document.getElementById("edit_add_option").addEventListener("click", editAddOptionsToOptionArray);

            function editAddOptionsToOptionArray() {
                if (document.getElementById('edit_option_label').value == "" && document.getElementById('edit_option_value').value == "") {
                    alert("should not be blank123");
                    return;
                }
                optionData.push({
                    value: document.getElementById("edit_option_value").value,
                    label: document.getElementById("edit_option_label").value,
                });
                console.log(optionData);
                document.getElementById('edit_option_label').value = "";
                document.getElementById('edit_option_value').value = "";
                editAddFieldOptionToDom()
            }
        }

        var editBraveField = document.getElementsByClassName("editBraveField");
        for (var i = 0; i < editBraveField.length; i++) {
            editBraveField[i].addEventListener('click', editDialog);
        }

        var closeDialog = function() {
            document.getElementById('editDialog').close();
        }

        document.getElementById("dialogClose").addEventListener('click', closeDialog);;




        // Adding Delete for the input fields. 
        var elements = document.getElementsByClassName("deleteField");
        var myFunction = function() {
            if (!confirm('Delete this field?')) {
                return;
            }
            var fieldName = this.getAttribute("data");

            this.innerHTML = "Deleting..."

            const formData = new FormData();
            formData.append('action', 'brave_delete_field');
            formData.append('fieldname', fieldName);

            const formDataSP = new URLSearchParams(formData);
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            fetch(ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: formDataSP,
                })
                .then((response) => {
                    console.log(response);
                    return response.json()
                })
                .then((res) => {
                    console.log(res);
                    if (res.type === "success") {
                        console.log(res.message)
                        location.reload();
                    }
                }).catch((error) => {
                    console.log(error)
                })
        };

        for (var i = 0; i < elements.length; i++) {
            elements[i].addEventListener('click', myFunction, false);
        }


        // Field Up for the input fields. 
        var upElements = document.getElementsByClassName("upField");
        console.log("upField....", upElements)
        var fieldUp = function() {
            if (!confirm('Up Field this field?')) {
                return;
            }
            var fieldName = this.getAttribute("data");

            this.innerHTML = "Swapping up..."

            const formData = new FormData();
            formData.append('action', 'brave_up_field');
            formData.append('fieldname', fieldName);

            const formDataSP = new URLSearchParams(formData);
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            fetch(ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: formDataSP,
                })
                .then((response) => {
                    console.log(response);
                    return response.json()
                })
                .then((res) => {
                    console.log(res);
                    if (res.type === "success") {
                        console.log(res.message)
                        location.reload();
                    }
                }).catch((error) => {
                    console.log(error)
                })
        };

        var upElementLenght = upElements.length;

        for (var i = 0; i < upElementLenght; i++) {
            var item = upElements[i];
            if (upElementLenght == 1 || i == 0) {
                item.style.display = "none";
            } else {
                item.addEventListener('click', fieldUp, false);
            }
        }


        // Field Down for the input fields. 
        var downElements = document.getElementsByClassName("downField");
        var fieldDown = function() {
            if (!confirm('Down Field this field?')) {
                return;
            }
            var fieldName = this.getAttribute("data");

            this.innerHTML = "Swapping down..."

            const formData = new FormData();
            formData.append('action', 'brave_down_field');
            formData.append('fieldname', fieldName);

            const formDataSP = new URLSearchParams(formData);
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            fetch(ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: formDataSP,
                })
                .then((response) => {
                    console.log(response);
                    return response.json()
                })
                .then((res) => {
                    console.log(res);
                    if (res.type === "success") {
                        console.log(res.message)
                        location.reload();
                    }
                }).catch((error) => {
                    console.log(error)
                })
        };

        var downElementLenght = downElements.length;

        for (var i = 0; i < downElementLenght; i++) {
            var item = downElements[i];
            if (downElementLenght == 1 || (i + 1) == (downElementLenght)) {
                item.style.display = "none";
            } else {
                item.addEventListener('click', fieldDown, false);
            }
        }



        document.getElementById("closebrAddForm").addEventListener("click", function() {
            document.getElementById('brAddForm').style.display = 'none';
        })

        // Show and hide the optiosn based on the input type value. 
        document.getElementById("fieldType").addEventListener("change", function() {
            var fieldType = document.getElementById("fieldType").value;
            if (fieldType == "select" || fieldType == "radio") {
                document.getElementById("options-for-input").style.display = "block";
            } else {
                document.getElementById("options-for-input").style.display = "none";
            }
        })


        // For Dialog Box
        // Show and hide the option based on the edit input type value.
        document.getElementById('editFieldType').addEventListener('change', function() {
            var editFieldType = document.getElementById('editFieldType').value;
            if (editFieldType == 'select' || editFieldType == 'radio') {
                document.getElementById('edit-options-for-input').style.display = 'block';
            } else {
                document.getElementById('edit-options-for-input').style.display = 'none';
            }
        })

        // Show and hide the Range min & max value panel
        document.getElementById("fieldType").addEventListener("change", function() {
            var rangeFieldType = document.getElementById("fieldType").value;
            if (rangeFieldType == "range") {
                document.getElementById("value_for_range").style.display = "block";
            } else {
                document.getElementById("value_for_range").style.display = "none";
            }
        })

        // For Dialog Box
        // Show and hide the Range min & max value panel
        document.getElementById('editFieldType').addEventListener('change', function() {
            var editRangeFieldType = document.getElementById('editFieldType').value;
            if (editRangeFieldType == 'range') {
                document.getElementById('edit_value_for_range').style.display = 'block';
            } else {
                document.getElementById('edit_value_for_range').style.display = 'none';
            }
        })


        var field_options = [];


        function deleteOption(index) {
            field_options.splice(index, 1);
            addFieldOptionsToDom();
        }

        function addFieldOptionsToDom() {
            var elContainer = document.getElementById("existingOptions");
            elContainer.innerHTML = "";

            // show the elContainer if there are options.
            if (field_options.length > 0) {
                document.getElementById("optionsTable").style.display = "table";
            } else {
                document.getElementById("optionsTable").style.display = "none";
            }
            for (let i = 0; i < field_options.length; i++) {
                element = field_options[i];

                var tr = document.createElement("tr");
                var div = document.createElement("div");
                div.classList = "button primary-button ";
                div.addEventListener("click", function() {
                    if (confirm("Delete?")) {
                        deleteOption(i);
                    }
                });
                var label = document.createTextNode(element.label);
                var optionValue = document.createTextNode(element.value);
                var deleteIcon = document.createTextNode(" Delete  ");

                td = document.createElement("td");
                td1 = document.createElement("td");
                td2 = document.createElement("td");
                div.appendChild(deleteIcon);
                td.appendChild(div)
                td1.appendChild(label);
                td2.appendChild(optionValue);
                tr.appendChild(td1);
                tr.appendChild(td2);
                tr.appendChild(td);


                elContainer.appendChild(tr)
            }
        }

        document.getElementById("add_option").addEventListener("click", addOptionsToOptionArray);

        function addOptionsToOptionArray() {
            if (document.getElementById('option_label').value == "" && document.getElementById('option_value').value == "") {
                alert("should not be blank");
                return document.getElementById('option_label').value;
                return document.getElementById('option_value').value;
            }
            field_options.push({
                value: document.getElementById("option_value").value,
                label: document.getElementById("option_label").value,
            });
            console.log(field_options);
            document.getElementById('option_label').value = "";
            document.getElementById('option_value').value = "";
            addFieldOptionsToDom()
        }


        // Send data to the server 
        document.getElementById('sendAjaxRequestToAdd').addEventListener('click', function() {
            if (document.getElementById('fieldname').value == "") {
                alert("Field name should not be blank!");
                return;
            }
            if (document.getElementById('fieldLabel').value == "") {
                alert("Field label should not be blank!");
                return;
            }


            if (document.getElementById("fieldType").value == "range") {
                if (document.getElementById('rangeMin').value == "") {
                    alert("Min value should not be blank!");
                    return;
                }
                if (document.getElementById('rangeMax').value == "") {
                    alert("Max value should not be blank!");
                    return;
                }
                if (document.getElementById('rangeDefault').value == "") {
                    alert("Value should not be blank!");
                    return;
                }

                // Minimum Value should be less than Max value
                if (parseInt(document.getElementById("rangeMin").value) >= parseInt(document.getElementById("rangeMax").value)) {
                    alert("Minimum Range should be less than Maximum Range");
                    return;
                }
                if (parseInt(document.getElementById("rangeDefault").value) <= parseInt(document.getElementById("rangeMin").value)) {
                    alert("Default Value should be greater than Range Minimum");
                    return;
                }
                if (parseInt(document.getElementById("rangeDefault").value) >= parseInt(document.getElementById("rangeMax").value)) {
                    alert("Default Value should be less than Range Maximum");
                    return;
                }
            }

            document.getElementById("sendAjaxRequestToAdd").innerHTML = "Adding..."

            const formData = new FormData();
            formData.append('action', 'brave_add_field');
            formData.append('fieldname', document.getElementById('fieldname').value);
            formData.append('fieldLabel', document.getElementById('fieldLabel').value);
            formData.append('fieldPlaceholder', document.getElementById('fieldPlaceholder').value);
            formData.append('addRegistrationPage', document.getElementById('addRegistrationPage').value);
            formData.append('userList', document.getElementById('userList').value);
            formData.append('rangeMin', document.getElementById('rangeMin').value);
            formData.append('rangeMax', document.getElementById('rangeMax').value);
            formData.append('rangeDefault', document.getElementById('rangeDefault').value);
            formData.append('rangeStep', document.getElementById('rangeStep').value);
            formData.append('fieldType', document.getElementById('fieldType').value);
            formData.append('optionData', JSON.stringify(field_options));

            const formDataSP = new URLSearchParams(formData);
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            fetch(ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: formDataSP,
                })
                .then((response) => {
                    console.log(response);
                    return response.json()
                })
                .then((res) => {
                    console.log(res);
                    if (res.type == "not_unique") {
                        document.getElementById("sendAjaxRequestToAdd").innerHTML = "Add Field"
                        alert(res.message);

                    }

                    if (res.type === "success") {
                        console.log(res.message)
                        location.reload();
                    }
                }).catch((error) => {
                    console.log(error)
                })
        });
        editFieldType


        // EDIT BOX: Send data to the server 
        document.getElementById('editSendAjaxRequestToAdd').addEventListener('click', function() {
            if (document.getElementById('editFieldname').value == "") {
                alert("Field name should not be blank!");
                return;
            }
            if (document.getElementById('editFieldLabel').value == "") {
                alert("Field label should not be blank!");
                return;
            }


            if (document.getElementById("editFieldType").value == "range") {
                if (document.getElementById('editRangeMin').value == "") {
                    alert("Min value should not be blank!");
                    return;
                }
                if (document.getElementById('editRangeMax').value == "") {
                    alert("Max value should not be blank!");
                    return;
                }
                if (document.getElementById('editRangeDefault').value == "") {
                    alert("Value should not be blank!");
                    return;
                }

                // Minimum Value should be less than Max value
                if (parseInt(document.getElementById("editRangeMin").value) >= parseInt(document.getElementById("editRangeMax").value)) {
                    alert("Minimum Range should be less than Maximum Range");
                    return;
                }
                if (parseInt(document.getElementById("editRangeDefault").value) <= parseInt(document.getElementById("editRangeMin").value)) {
                    alert("Default Value should be greater than Range Minimum");
                    return;
                }
                if (parseInt(document.getElementById("editRangeDefault").value) >= parseInt(document.getElementById("editRangeMax").value)) {
                    alert("Default Value should be less than Range Maximum");
                    return;
                }
            }

            document.getElementById("editSendAjaxRequestToAdd").innerHTML = "Updating..."

            const formData = new FormData();
            formData.append('action', 'edit_brave_add_field');
            formData.append('editFieldName', document.getElementById('editFieldname').value);
            formData.append('editFieldnameOld', document.getElementById('editFieldnameOld').value);
            formData.append('editFieldLabel', document.getElementById('editFieldLabel').value);
            formData.append('editFieldPlaceholder', document.getElementById('editFieldPlaceholder').value);
            formData.append('editAddRegistrationPage', document.getElementById('editAddRegistrationPage').value);
            formData.append('editAddRegistrationPage', document.getElementById('editAddRegistrationPage').value);
            formData.append('editUserList', document.getElementById('editUserList').value);
            formData.append('editRangeMin', document.getElementById('editRangeMin').value);
            formData.append('editRangeMax', document.getElementById('editRangeMax').value);
            formData.append('editRangeDefault', document.getElementById('editRangeDefault').value);
            formData.append('editRangeStep', document.getElementById('editRangeStep').value);
            formData.append('editFieldType', document.getElementById('editFieldType').value);
            formData.append('optionData', JSON.stringify(optionData));

            const formDataSP = new URLSearchParams(formData);
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            fetch(ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: formDataSP,
                })
                .then((response) => {
                    console.log(response);
                    return response.json()
                })
                .then((res) => {
                    console.log(res);
                    if (res.type == "not_unique") {
                        document.getElementById("editSendAjaxRequestToAdd").innerHTML = "Update"
                        alert(res.message);

                    }

                    if (res.type === "success") {
                        console.log(res.message)
                        location.reload();
                    }
                }).catch((error) => {
                    console.log(error)
                })
        });
    </script>
<?php
}

function array_swap(&$array, $swap_a, $swap_b)
{
    list($array[$swap_a], $array[$swap_b]) = array($array[$swap_b], $array[$swap_a]);
}

add_action('wp_ajax_brave_up_field', 'brUpField');

function brUpField()
{

    if (!current_user_can('administrator')) {
        $response = array(
            "message" => "Requires Admin Access",
            "type" => "error"
        );
    }

    $fieldname = $_POST['fieldname'];
    $fields = get_option("braveWPUser");


    if ($fields == "") {
        $fields = array();
        add_option("braveWPUser", "");
    } else {
        $fields = json_decode($fields, true);
    }

    $indexOfItem = -1;
    for ($i = 0; $i < count($fields); $i++) {
        if ($fields[$i]["fieldName"] == $fieldname) {
            $indexOfItem = $i;
        }
    }

    if ($indexOfItem > 0) {
        array_swap($fields, $indexOfItem - 1, $indexOfItem);
    }

    $updateString = json_encode($fields);

    update_option("braveWPUser", $updateString);

    $response = array(
        "message" => "Form Field swapped successfully",
        "type" => "success"
    );

    echo json_encode($response);

    wp_die();
}

add_action('wp_ajax_brave_down_field', 'brDownField');

function brDownField()
{

    if (!current_user_can('administrator')) {
        $response = array(
            "message" => "Requires Admin Access",
            "type" => "error"
        );
    }

    $fieldname = $_POST['fieldname'];
    $fields = get_option("braveWPUser");


    if ($fields == "") {
        $fields = array();
        add_option("braveWPUser", "");
    } else {
        $fields = json_decode($fields, true);
    }

    $indexOfItem = -1;
    for ($i = 0; $i < count($fields); $i++) {
        if ($fields[$i]["fieldName"] == $fieldname) {
            $indexOfItem = $i;
        }
    }

    if ($indexOfItem < count($fields) - 1) {
        array_swap($fields, $indexOfItem, $indexOfItem + 1);
    }

    $updateString = json_encode($fields);

    update_option("braveWPUser", $updateString);

    $response = array(
        "message" => "Form Field swapped successfully",
        "type" => "success"
    );

    echo json_encode($response);

    wp_die();
}



add_action('wp_ajax_brave_delete_field', 'brDeleteField');

function brDeleteField()
{

    if (!current_user_can('administrator')) {
        $response = array(
            "message" => "Requires Admin Access",
            "type" => "error"
        );
    }

    $fieldname = $_POST['fieldname'];
    $fields = get_option("braveWPUser");


    if ($fields == "") {
        $fields = array();
        add_option("braveWPUser", "");
    } else {
        $fields = json_decode($fields, true);
    }


    for ($i = 0; $i < count($fields); $i++) {
        # code...
        if ($fields[$i]["fieldName"] == $fieldname) {
            unset($fields[$i]);
            $fields = array_values($fields);
        }
    }

    $updateString = json_encode($fields);

    update_option("braveWPUser", $updateString);

    $response = array(
        "message" => "Form Field deleted successfully",
        "type" => "success"
    );

    echo json_encode($response);

    wp_die();
}

// creating a wordpress end point to send OTP
add_action('wp_ajax_edit_brave_add_field', 'brEditAddField');
function brEditAddField()
{
    $fields = get_option("braveWPUser");
    if ($fields == "") {
        $fields = array();
        add_option("braveWPUser", "");
    } else {
        $fields = json_decode($fields, true);
    }
    $fieldname = $_POST['editFieldName'];
    $fieldnameOld = $_POST['editFieldnameOld'];

    if (!current_user_can('administrator')) {
        $response = array(
            "message" => "Requires Admin Access",
            "type" => "error"
        );
        // Returning the message. 
        echo json_encode($response);
        wp_die();
    }


    $indexOfItem = -1;
    for ($i = 0; $i < count($fields); $i++) {
        if ($fields[$i]["fieldName"] == $fieldnameOld) {
            $indexOfItem = $i;
        }
    }


    for ($i = 0; $i < count($fields); $i++) {
        if ($i == $indexOfItem) {
            continue;
        }
        // print_r($fields);
        if ($fields[$i]["fieldName"] == $fieldname) {
            $response = array(
                "message" => "Field Name Already Exists",
                "type" => "not_unique"
            );
            // Returning the message. 
            echo json_encode($response);
            wp_die();
        }
    }
    $fieldType = $_POST['editFieldType'];
    $fieldLabel = $_POST['editFieldLabel'];
    $fieldPlaceholder = $_POST['editFieldPlaceholder'];
    $addRegistrationPage = $_POST['editAddRegistrationPage'];
    $userList = $_POST['editUserList'];
    $rangeMin = $_POST['editRangeMin'];
    $rangeMax = $_POST['editRangeMax'];
    $rangeDefault = $_POST['editRangeDefault'];
    $rangeStep = $_POST['editRangeStep'];
    $optionData = $_POST['optionData'];

    $fields[$indexOfItem] = array(
        "fieldName"                 =>  $fieldname,
        "fieldLabel"                =>  $fieldLabel,
        "fieldPlaceholder"          =>  $fieldPlaceholder,
        "addRegistrationPage"       =>  $addRegistrationPage,
        "userList"                  =>  $userList,
        "rangeMin"                  =>  $rangeMin,
        "rangeMax"                  =>  $rangeMax,
        "rangeDefault"              =>  $rangeDefault,
        "rangeStep"                 =>  $rangeStep,
        "fieldType"                 =>  $fieldType,
        "optionData"                =>  json_decode(stripslashes($optionData), true),
    );

    $updateString = json_encode($fields);



    update_option("braveWPUser", $updateString);


    $response = array(
        "message" => "Form Field added successfully",
        "type" => "success"
    );

    echo json_encode($response);

    wp_die();
}

// creating a wordpress end point to send OTP
add_action('wp_ajax_brave_add_field', 'brAddField');

function brAddField()
{
    $fields = get_option("braveWPUser");
    if ($fields == "") {
        $fields = array();
        add_option("braveWPUser", "");
    } else {
        $fields = json_decode($fields, true);
    }
    $fieldname = $_POST['fieldname'];
    if (!current_user_can('administrator')) {
        $response = array(
            "message" => "Requires Admin Access",
            "type" => "error"
        );
        // Returning the message. 
        echo json_encode($response);
        wp_die();
    }
    for ($i = 0; $i < count($fields); $i++) {
        if ($fields[$i]["fieldName"] == $fieldname) {
            $response = array(
                "message" => "Field Name Already Exists",
                "type" => "not_unique"
            );
            // Returning the message. 
            echo json_encode($response);
            wp_die();
        }
    }
    $fieldType = $_POST['fieldType'];
    $fieldLabel = $_POST['fieldLabel'];
    $fieldPlaceholder = $_POST['fieldPlaceholder'];
    $addRegistrationPage = $_POST['addRegistrationPage'];
    $userList = $_POST['userList'];
    $rangeMin = $_POST['rangeMin'];
    $rangeMax = $_POST['rangeMax'];
    $rangeDefault = $_POST['rangeDefault'];
    $rangeStep = $_POST['rangeStep'];
    $optionData = $_POST['optionData'];

    $fields[] = array(
        "fieldName"                 =>  $fieldname,
        "fieldLabel"                =>  $fieldLabel,
        "fieldPlaceholder"          =>  $fieldPlaceholder,
        "addRegistrationPage"       =>  $addRegistrationPage,
        "userList"                  =>  $userList,
        "rangeMin"                  =>  $rangeMin,
        "rangeMax"                  =>  $rangeMax,
        "rangeDefault"              =>  $rangeDefault,
        "rangeStep"                 =>  $rangeStep,
        "fieldType"                 =>  $fieldType,
        "optionData"                =>  json_decode(stripslashes($optionData), true),
    );

    $updateString = json_encode($fields);

    update_option("braveWPUser", $updateString);


    $response = array(
        "message" => "Form Field added successfully",
        "type" => "success"
    );

    echo json_encode($response);

    wp_die();
}

// Registration Field Style 
function register_field_style()
{ ?>
    <style type="text/css">
        .login form .input,
        .login select,
        .login textarea,
        .login input[type=color],
        .login input[type=date],
        .login input[type=datetime-local],
        .login input[type=month],
        .login input[type=number],
        .login input[type=search],
        .login input[type=tel],
        .login input[type=time],
        .login input[type=range],
        .login input[type=url],
        .login input[type=week],
        .login input[type=week] {
            font-size: 24px;
            line-height: 1.33333333;
            width: 100%;
            border-width: 0.0625rem;
            padding: 0.1875rem 0.3125rem;
            margin: 0 6px 16px 0;
            min-height: 40px;
            max-height: none;
        }

        .login form,
        .login input[type=radio] {
            font-size: 24px;
        }
    </style>
<?php
}

add_action('login_head', 'register_field_style');


// Profile page Style
function profile_style()
{ ?>
    <style type="text/css">
        .wp-core-ui .button {
            margin: 0 2px;
            display: flex;
            align-items: center;
            width: fit-content;
        }

        .wp-core-ui .button.deleteField {
            border: 1px solid #e39b9b !important;
            color: #d34343;
        }

        .wp-core-ui .button.deleteField:hover {
            background-color: #cc1818;
            color: white !important
        }

        .wp-core-ui .button.deleteField:focus {
            border-color: #cc1818;
            color: #cc1818;
            box-shadow: 0 0 0 1px #cc1818;
            outline: 2px solid transparent;
            outline-offset: 0;
        }

        #action-btn-group {
            display: flex;
            justify-content: end;
        }
    </style>
<?php }
add_action('edit_user_profile', 'profile_style');
add_action('user_new_form', 'profile_style');
add_action('edit_user_profile_update', 'profile_style');
add_action('personal_options_update', 'profile_style');
add_action('show_user_profile', 'profile_style');
