<?php
/**
 * Plugin Name:		UNC CloudApps Email Configuration
 * Plugin URI:		http://cloudapps.unc.edu
 * Description:		Configure email settings (relay.unc.edu) for UNC CloudApps (OpenShift) WordPress.
 * Version:             1.0.0
 * Author:              Michael McNeill (UNC ITS Middleware)
 * License:             GPL-2.0+
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) OR exit;

function unc_wp_smtp_relay( $phpmailer ) {
	$phpmailer->IsSMTP();
	$phpmailer->SMTPAuth    = false;
	$phpmailer->Host        = 'relay.unc.edu';
	$phpmailer->Port        = 25;
}
add_action( 'phpmailer_init', 'unc_wp_smtp_relay' );
