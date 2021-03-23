<?php

add_action('vg_sheet_editor/initialized', 'vg_test_jklioiasd');

function vg_test_jklioiasd() {
	if (!isset($_GET['jsi29ajz'])) {
		return;
	}

	if (!defined('VGSE_DEBUG') || !VGSE_DEBUG) {
		return;
	}


	die();
}
