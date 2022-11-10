<?php

class wpUserField
{

    private $label;
    private $placeholder;
    private $name;
    private $value = "";
    private $inputType; // text,textarea,date,color,select,radio,checkbox....
    private $options = array();
    private $checkboxOptions;
    private $rangeMin;
    private $rangeMax;
    private $step;
    private $fieldWrapper = "form"; // stacked
    private $postFixHTML = "";
    private $showOnRegistration = 'yes';
    private $showOnUserListing = 'yes';


    // SHOW NEW FIELD 
    function showNewField()
    {
        if ($this->inputType == "text") {
            $this->textFieldNew();
        } elseif ($this->inputType == "select") {
            $this->selectFieldNew();
        } elseif ($this->inputType == "radio") {
            $this->radioFieldNew();
        } elseif ($this->inputType == "checkbox") {
            $this->checkboxFieldNew();
        } elseif ($this->inputType == "color") {
            $this->colorFieldNew();
        } elseif ($this->inputType == "date") {
            $this->dateFieldNew();
        } elseif ($this->inputType == "datetime-local") {
            $this->dateTimeLocalFieldNew();
        } elseif ($this->inputType == "month") {
            $this->monthFieldNew();
        } elseif ($this->inputType == "number") {
            $this->numberFieldNew();
        } elseif ($this->inputType == "password") {
            $this->passwordFieldNew();
        } elseif ($this->inputType == "search") {
            $this->searchFieldNew();
        } elseif ($this->inputType == "tel") {
            $this->telFieldNew();
        } elseif ($this->inputType == "time") {
            $this->timeFieldNew();
        } elseif ($this->inputType == "url") {
            $this->urlFieldNew();
        } elseif ($this->inputType == "week") {
            $this->weekFieldNew();
        } elseif ($this->inputType == "range") {
            $this->rangeFieldNew();
        } elseif ($this->inputType == "textarea") {
            $this->textAreaFieldNew();
        } else {
            $this->textFieldNew();
        }
    }

    function showField($user)
    {
        if ($this->inputType == "text") {
            $this->textField($user);
        } elseif ($this->inputType == "select") {
            $this->selectField($user);
        } elseif ($this->inputType == "radio") {
            $this->radioField($user);
        } elseif ($this->inputType == "checkbox") {
            $this->checkboxField($user);
        } elseif ($this->inputType == "color") {
            $this->colorField($user);
        } elseif ($this->inputType == "date") {
            $this->dateField($user);
        } elseif ($this->inputType == "datetime-local") {
            $this->dateTimeLocalField($user);
        } elseif ($this->inputType == "month") {
            $this->monthField($user);
        } elseif ($this->inputType == "number") {
            $this->numberField($user);
        } elseif ($this->inputType == "password") {
            $this->passwordField($user);
        } elseif ($this->inputType == "search") {
            $this->searchField($user);
        } elseif ($this->inputType == "tel") {
            $this->telField($user);
        } elseif ($this->inputType == "time") {
            $this->timeField($user);
        } elseif ($this->inputType == "url") {
            $this->urlField($user);
        } elseif ($this->inputType == "week") {
            $this->weekField($user);
        } elseif ($this->inputType == "range") {
            $this->rangeField($user);
        } elseif ($this->inputType == "textarea") {
            $this->textAreaField($user);
        } else {
            $this->textField($user);
        }
    }


    // MODIFY USER TABLE
    function manage_users_columns($column)
    {
        $column[$this->name] = $this->label;
        return $column;
    }

    // MODIFY USER TABLE ROW
    function manage_users_custom_column($val, $column_name, $user_id)
    {

        $udata = get_userdata($user_id);
        return get_the_author_meta($column_name, $user_id);
    }

   // MAKE REGISTERED DATE COLUMN SORTABE
   function manage_users_sortable_columns($columns)
   {
       return wp_parse_args(array($this->name => $this->name), $columns);
   }




    public function fieldWrapperForm($labelHtml, $inputHtml, $postFixHTML)
    {
?>

    <table class="form-table">
            <tr>
                <th><?php echo wp_kses($labelHtml); ?></th>
                <td>
                    <div style="display: flex; gap:20px">
                    <?php echo wp_kses($inputHtml); ?>
                    </div>
                </td>
                <td>
                <?php echo wp_kses($postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }

    public function fieldWrapperRegistrationForm($labelHtml, $inputHtml, $postFixHTML)
    {
    ?>
        <p>
            <?php echo wp_kses($labelHtml); ?><div> <?php echo wp_kses($inputHtml); ?></div>
           
        </p>
    <?php
    }


    // =========================================
    // TEXT FIELD
    // =========================================
    public function textFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"text\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"text\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    // Text Field ends. 


    public function textField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="text" id="<?php echo  wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }



    private function buildSelect()
    {

        $select = "<select id=\"{$this->name}\" name=\"{$this->name}\">";
        for ($i = 0; $i < count($this->options); $i++) {
            $select .= "<option value=\"{$this->options[$i][0]}\">{wp_kses($this->options[$i][1])}</option>";
        }
        $select .= "</select>";
        return $select;
    }



    // =========================================
    // SELECT FIELD
    // =========================================
    public function selectFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                $this->buildSelect(),
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                $this->buildSelect(),
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }


    public function selectField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th>
                    <label for="<?php echo  wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label>
                </th>
                <td>
                    <select id="<?php echo  wp_kses($this->name); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($this->value); ?>">
                        <?php
                        for ($i = 0; $i < count($this->options); $i++) { ?>
                            <option <?php
                                    if ($fieldValue == $this->options[$i][0]) {
                                        echo "selected";
                                    }
                                    ?> value="<?php echo wp_kses($this->options[$i][0]); ?>">
                                <?php echo wp_kses(wp_kses($this->options[$i][1])); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }



    public function buildRadio()
    {
        $radio = "";
        for ($i = 0; $i < count($this->options); $i++) {
            $radio .= "<div >";
            $radio .= "<input style='margin-right:5px;' type=\"radio\" id=\"{$this->name}{$i}\" name=\"{$this->name}\" value=\"{$this->options[$i][0]}\">";
            $radio .= "<label for=\"{$this->name}{$i}\">{wp_kses($this->options[$i][1])}</label><br>";
            $radio .= "</div>";
        }
        return $radio;
    }




    // =========================================
    // Radio New  FIELD
    // =========================================
    public function radioFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                $this->buildRadio(),
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                $this->buildRadio(),
                $this->postFixHTML
            );
        }
    }




    public function radioField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID);
    ?>
  
        <table class="form-table">
            <tr>
                <th><?php echo wp_kses($this->label); ?></th>
                <td>
                    <div style="display: flex; gap:20px">
                        <?php
                        for ($i = 0; $i < count($this->options); $i++) { ?>
                            <div>
                                <input type="radio" id="<?php echo wp_kses($this->options[$i][0]); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($this->options[$i][0]); ?>" class="" <?php
                                                                                                                                                                                        if ($fieldValue == $this->options[$i][0]) {
                                                                                                                                                                                            echo "checked";
                                                                                                                                                                                        } ?> />
                                <label for="<?php echo wp_kses($this->options[$i][0]); ?>">
                                    <?php echo wp_kses($this->options[$i][1]); ?>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }

    // =========================================
    // CHECKEDBOX NEWFIELD
    // =========================================
    public function checkboxFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"checkbox\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\">",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "",
                "<input type=\"checkbox\" id=\"{$this->name}\" name=\"{$this->name}\" value=\"{$this->value}\">
                <label for=\"{$this->name}\">{$this->label}</label>",
                $this->postFixHTML
            );
        }
    }

    public function checkboxField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><?php echo wp_kses($this->label); ?></th>
                <td>
                    <input type="checkbox" id="<?php echo wp_kses($this->name); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($this->checkboxOptions); ?>" class="regular-text" <?php
                                                                                                                                                                                if ($fieldValue == $this->checkboxOptions) {
                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                } ?> />
                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }

    // =========================================
    // COLOR FIELD
    // =========================================
    public function colorFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"color\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"color\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function colorField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="color" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // Date FIELD
    // =========================================
    public function dateFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"date\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"date\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function dateField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="date" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // DATE TIME LOCAL FIELD
    // =========================================
    public function dateTimeLocalFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"datetime-local\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"datetime-local\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function dateTimeLocalField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="datetime-local" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // MONTH FIELD
    // =========================================
    public function monthFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"month\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"month\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function monthField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="month" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // NUMBER FIELD
    // =========================================
    public function numberFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"number\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"number\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function numberField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="number" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // PASSWORD FIELD
    // =========================================
    public function passwordFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"password\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"password\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function passwordField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="password" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // SEARCH FIELD
    // =========================================
    public function searchFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"text\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"search\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function searchField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="search" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />
                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // TELEPHONE NUMBER FIELD
    // =========================================
    public function telFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"tel\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"tel\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function telField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="tel" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo  wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // TIME FIELD
    // =========================================
    public function timeFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"time\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"time\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function timeField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="time" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo  wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }

    // =========================================
    // URL FIELD
    // =========================================
    public function urlFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"url\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"url\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function urlField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="url" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo  wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // WEEK FIELD
    // =========================================
    public function weekFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"week\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<input type=\"week\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" />",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function weekField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <input type="week" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo  wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" class="regular-text" />

                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // RANGE FIELD
    // =========================================
    public function rangeFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<div style='display: flex;'>
                <input type=\"range\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" min=\"{$this->rangeMin}\" max=\"{$this->rangeMax}\" value=\"{$this->value}\" class=\"regular-text\" oninput=\"this.nextElementSibling.value = this.value\"  /> <output><?php echo wp_kses($this->value); ?></output>
                </div>",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<div style='display: flex;'>
                <input type=\"range\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" min=\"{$this->rangeMin}\" max=\"{$this->rangeMax}\" value=\"{$this->value}\" class=\"regular-text\" oninput=\"this.nextElementSibling.value = this.value\"  /> <output><?php echo wp_kses($this->value); ?></output>
                </div>",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function rangeField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                   <div style='display: flex;'>
                   <input type="range" id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo  wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($this->value); ?>" min="<?php echo wp_kses($this->rangeMin); ?>" max="<?php echo wp_kses($this->rangeMax); ?>" class="regular-text" oninput="this.nextElementSibling.value = this.value" step="<?php echo wp_kses($this->step); ?>" />
                    <output><?php echo wp_kses($this->value); ?></output>
                   </div>
                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
    <?php
    }


    // =========================================
    // TEXTAREA FIELD
    // =========================================
    public function textAreaFieldNew()
    {
        if ($this->fieldWrapper == "form") {
            return $this->fieldWrapperForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<textarea id=\"{$this->name}\" rows=\"\3\"\ placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" /></textarea>",
                $this->postFixHTML
            );
        }
        if ($this->fieldWrapper == "stacked") {
            return $this->fieldWrapperRegistrationForm(
                "<label for=\"{$this->name}\">{$this->label}</label>",
                "<textarea id=\"{$this->name}\" rows=\"\3\"\ placeholder=\"{$this->placeholder}\" name=\"{$this->name}\" value=\"\" class=\"regular-text\" /></textarea>",
                $this->postFixHTML
            );
        }

    ?>
    <?php
    }

    public function textAreaField($user)
    {
        $fieldValue = get_the_author_meta($this->name, $user->ID)
    ?>
        <table class="form-table">
            <tr>
                <th><label for="<?php echo wp_kses($this->name); ?>"><?php echo wp_kses($this->label); ?></label></th>
                <td>
                    <textarea id="<?php echo wp_kses($this->name); ?>" placeholder="<?php echo  wp_kses($this->placeholder); ?>" name="<?php echo wp_kses($this->name); ?>" value="<?php echo wp_kses($fieldValue); ?>" rows="3" class="regular-text"><?php echo wp_kses($fieldValue); ?></textarea>
                </td>
                <td>
                    <?php echo wp_kses($this->postFixHTML); ?>
                </td>
            </tr>
        </table>
<?php
    }

    function updateFieldIntoDatabase($user_id)
    {
        update_user_meta($user_id, $this->name, $_POST[$this->name]);
    }

    /**
     * Get the value of label
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * Set the value of label
     *
     * @return  self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of inputType
     */
    public function getInputType()
    {
        return $this->inputType;
    }

    /**
     * Set the value of inputType
     *
     * @return  self
     */
    public function setInputType($inputType)
    {
        $this->inputType = $inputType;

        return $this;
    }



    /**
     * Get the value of options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value of options
     *
     * @return  self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the value of placeholder
     */
    public function getPlaceholder()
    {
        return wp_kses($this->placeholder);
    }

    /**
     * Set the value of placeholder
     *
     * @return  self
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }



    /**
     * Get the value of checkboxOptions
     */
    public function getCheckboxOptions()
    {
        return $this->checkboxOptions;
    }

    /**
     * Set the value of checkboxOptions
     *
     * @return  self
     */
    public function setCheckboxOptions($checkboxOptions)
    {
        $this->checkboxOptions = $checkboxOptions;

        return $this;
    }

    /**
     * Get the value of rangeMin
     */
    public function getRangeMin()
    {
        return $this->rangeMin;
    }

    /**
     * Set the value of rangeMin
     *
     * @return  self
     */
    public function setRangeMin($rangeMin)
    {
        $this->rangeMin = $rangeMin;

        return $this;
    }

    /**
     * Get the value of rangeMax
     */
    public function getRangeMax()
    {
        return $this->rangeMax;
    }

    /**
     * Set the value of rangeMax
     *
     * @return  self
     */
    public function setRangeMax($rangeMax)
    {
        $this->rangeMax = $rangeMax;

        return $this;
    }

    /**
     * Get the value of postFixHTML
     */
    public function getPostFixHTML()
    {
        return $this->postFixHTML;
    }

    /**
     * Set the value of postFixHTML
     *
     * @return  self
     */
    public function setPostFixHTML($postFixHTML)
    {
        $this->postFixHTML = $postFixHTML;

        return $this;
    }

    /**
     * Get the value of fieldWrapper
     */
    public function getFieldWrapper()
    {
        return $this->fieldWrapper;
    }

    /**
     * Set the value of fieldWrapper
     *
     * @return  self
     */
    public function setFieldWrapper($fieldWrapper)
    {
        $this->fieldWrapper = $fieldWrapper;

        return $this;
    }

    /**
     * Get the value of value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return  self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of step
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Set the value of step
     *
     * @return  self
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }


    /**
     * Get the value of showOnRegistration
     */
    public function getShowOnRegistration()
    {
        return $this->showOnRegistration;
    }

    /**
     * Set the value of showOnRegistration
     *
     * @return  self
     */
    public function setShowOnRegistration($showOnRegistration)
    {
        $this->showOnRegistration = $showOnRegistration;

        return $this;
    }

    /**
     * Get the value of showOnUserListing
     */ 
    public function getShowOnUserListing()
    {
        return $this->showOnUserListing;
    }

    /**
     * Set the value of showOnUserListing
     *
     * @return  self
     */ 
    public function setShowOnUserListing($showOnUserListing)
    {
        $this->showOnUserListing = $showOnUserListing;

        return $this;
    }
}
