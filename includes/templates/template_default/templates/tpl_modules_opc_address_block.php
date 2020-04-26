<?php
// -----
// Part of the One-Page Checkout plugin, provided under GPL 2.0 license by lat9 (cindy@vinosdefrutastropicales.com).
// Copyright (C) 2017-2020, Vinos de Frutas Tropicales.  All rights reserved.
//
// This module is included by tpl_modules_opc_billing_address.php and tpl_modules_opc_shipping_address.php and
// provides a common-formatting for those two address-blocks.
//
?>
<!--bof address block -->
<?php
// -----
// Sanitize module input values.
//
if (!isset($opc_address_type) || !in_array($opc_address_type, array('bill', 'ship'))) {
    trigger_error("Unknown value for opc_address_type ($opc_address_type).", E_USER_ERROR);
    exit();
}

// -----
// Start address formatting ...
//
$which = $opc_address_type;
$address = $_SESSION['opc']->getAddressValues($which);
if ($address['validated']) {
    $display_condensed_address = true;
    $address_form_class = ' class="hiddenField"';
} else {
    $display_condensed_address = false;
    $address_form_class = '';
}

// -----
// The first section of an address-block contains the condensed formatting of the address, to reduce
// on-screen real-estate required.
//
if ($display_condensed_address) {
?>
<div id="address-<?php echo $which; ?>">
    <div class="floatingBox back"><?php echo zen_address_format(zen_get_address_format_id($address['country_id']), $address, true, '', '<br>'); ?></div>
<?php
if (!$opc_disable_address_change) {
?>
    <div class="floatingBox forward opc-right" id="opc-<?php echo $which; ?>-edit"><?php echo zen_image_button(BUTTON_IMAGE_EDIT_SMALL, BUTTON_EDIT_SMALL_ALT); ?></div>
<?php
}
?>
</div>
<div class="clearBoth"></div>
<?php
}

// -----
// The second section contains the address-form through which an address can be changed, if enabled.  If the
// address can't be changed, perform a quick return so that the form elements aren't rendered.
//
if ($opc_disable_address_change) {
    return;
}
?>
<div id="address-form-<?php echo $which; ?>"<?php echo $address_form_class; ?>>
<?php
// -----
// If the address can be changed and an account-bearing customer has previously-defined addresses, create a dropdown list
// from which they can select.
//
// Note: Checking for more than two (2) entries, since the "Choose from previous selections" is
// pre-populated!
//
if (!$opc_disable_address_change) {
    $address_selections = $_SESSION['opc']->formatAddressBookDropdown();
    if (count($address_selections) > 2) {
        $selected = $_SESSION['opc']->getAddressDropDownSelection($which);
?>
    <div id="choices-<?php echo $which; ?>"><?php echo zen_draw_pull_down_menu("address-$which", $address_selections, $selected); ?></div>
<?php
    }
}


if (ACCOUNT_GENDER == 'true') {
    $field_name = "gender[$which]";
    $male_id = "gender-male-$which";
    $female_id = "gender-female-$which";
    echo '<span class="custom-control custom-radio custom-control-inline">' . zen_draw_radio_field ($field_name, 'm', ($address['gender'] == 'm'), "id=\"$male_id\"") . 
    "<label class=\"custom-control-label radioButtonLabel\" for=\"$male_id\">" . MALE . '</label></span><span class="custom-control custom-radio custom-control-inline">' . 
    zen_draw_radio_field ($field_name, 'f', ($address['gender'] == 'f'), "id=\"$female_id\"") . 
    "<label class=\"custom-control-label radioButtonLabel\" for=\"$female_id\">" . FEMALE . '</label></span>' . 
    (zen_not_null(ENTRY_GENDER_TEXT) ? '<span class="alert">' . ENTRY_GENDER_TEXT . '</span>': ''); 
?>
      <br class="clearBoth" />
<?php
}

echo $_SESSION['opc']->formatAddressElement($which, 'firstname', $address['firstname'], ENTRY_FIRST_NAME, TABLE_CUSTOMERS, 'customers_firstname', ENTRY_FIRST_NAME_MIN_LENGTH, ENTRY_FIRST_NAME_TEXT);
    
echo $_SESSION['opc']->formatAddressElement($which, 'lastname', $address['lastname'], ENTRY_LAST_NAME, TABLE_CUSTOMERS, 'customers_lastname', ENTRY_LAST_NAME_MIN_LENGTH, ENTRY_LAST_NAME_TEXT);

if (ACCOUNT_COMPANY == 'true') {
    echo $_SESSION['opc']->formatAddressElement($which, 'company', $address['company'], ENTRY_COMPANY, TABLE_ADDRESS_BOOK, 'entry_company', ENTRY_COMPANY_MIN_LENGTH, ENTRY_COMPANY_TEXT);
}

echo $_SESSION['opc']->formatAddressElement($which, 'street_address', $address['street_address'], ENTRY_STREET_ADDRESS, TABLE_ADDRESS_BOOK, 'entry_street_address', ENTRY_STREET_ADDRESS_MIN_LENGTH, ENTRY_STREET_ADDRESS_TEXT);

if (ACCOUNT_SUBURB == 'true') {
    echo $_SESSION['opc']->formatAddressElement($which, 'suburb', $address['suburb'], ENTRY_SUBURB, TABLE_ADDRESS_BOOK, 'entry_suburb', 0, ENTRY_SUBURB_TEXT);
}

echo $_SESSION['opc']->formatAddressElement($which, 'city', $address['city'], ENTRY_CITY, TABLE_ADDRESS_BOOK, 'entry_city', ENTRY_CITY_MIN_LENGTH, ENTRY_CITY_TEXT);

if (ACCOUNT_STATE == 'true') {
    $state_zone_id = "stateZone-$which";
    $zone_field_name = "zone_id[$which]";
    $state_field_name = "state[$which]";
    $state_field_id = "state-$which";
?>
      <label class="inputLabel"><?php echo ENTRY_STATE; ?></label>
<?php    
    if ($address['show_pulldown_states']) {
        echo zen_draw_pull_down_menu($zone_field_name, zen_prepare_country_zones_pull_down($address['country'], $address['zone_id']), $address['zone_id'], "id=\"$state_zone_id\"");
        if (zen_not_null(ENTRY_STATE_TEXT)) {
            echo '<span class="alert">' . ENTRY_STATE_TEXT . '</span>';
        }
        echo '<br />';
    } else {
        echo zen_draw_hidden_field($zone_field_name, $address['zone_name']);
    }
    
    echo zen_draw_input_field($state_field_name, $address['state'], zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_state', '40') . " id=\"$state_field_id\"");
    if (zen_not_null(ENTRY_STATE_TEXT)) {
        echo '<span class="alert">' . ENTRY_STATE_TEXT . '</span>';
    }
?>
      <br class="clearBoth" />
<?php
}

echo $_SESSION['opc']->formatAddressElement($which, 'postcode', $address['postcode'], ENTRY_POST_CODE, TABLE_ADDRESS_BOOK, 'entry_postcode', ENTRY_POSTCODE_MIN_LENGTH, ENTRY_POST_CODE_TEXT);

$field_name = "zone_country_id[$which]";
$field_id = "country-$which";
?>
      <label class="inputLabel" for="<?php echo $field_id; ?>"><?php echo ENTRY_COUNTRY; ?></label>
      <?php echo zen_get_country_list($field_name, $address['country'], "id=\"$field_id\"") . 
      (zen_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="alert">' . ENTRY_COUNTRY_TEXT . '</span>' : ''); ?>
      <div class="clearBoth"></div>
      
      <div id="messages-<?php echo $which; ?>"></div>
</div>
<!--eof address block -->
