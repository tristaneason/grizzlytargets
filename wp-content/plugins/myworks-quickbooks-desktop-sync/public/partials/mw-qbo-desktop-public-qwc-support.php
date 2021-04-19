<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/public/partials
 */
 global $MWQDC_LB;
 $page_url = 'https://docs.myworks.software/';
 $MWQDC_LB->redirect($page_url);
?>