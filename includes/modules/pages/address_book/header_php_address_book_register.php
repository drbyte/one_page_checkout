<?php
// -----
// Part of the One-Page Checkout plugin, provided under GPL 2.0 license by lat9 (cindy@vinosdefrutastropicales.com).
// Copyright (C) 2017, Vinos de Frutas Tropicales.  All rights reserved.
//
$needs_address_update = false;
if (isset($_SESSION['opc']) && $_SESSION['opc']->accountRegistrationEnabled()) {
    $needs_address_update = $_SESSION['opc']->customerAccountNeedsPrimaryAddress();
}