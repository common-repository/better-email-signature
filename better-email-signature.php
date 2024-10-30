<?php
/**
 * @package Better-Email-Signature
 */
/*
Plugin Name: Better Email Signature
Plugin URI: http://joomportal.com
Description: This plugin allows you add some signatures and let you choose signature that will be append to every outgoing email that WordPress sends.
Version: 1.0.0
Author: JoomPortal
Author URI: http://joomportal.com
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined ('ABSPATH')) die ('No direct access allowed');

define ('BETTER_EMAIL_SIGNATURE_VERSION', '1.0.0');
define ('ADDEMAILSIG_SLUG', "better-email-signature");
define ('ADDEMAILSIG_DIR', WP_PLUGIN_DIR . '/' . ADDEMAILSIG_SLUG);

// Include file for admin configuration option page
if (is_admin()) require_once( ADDEMAILSIG_DIR . "/config-options.php");

add_action( 'phpmailer_init', 'better_email_signature_hook_phpmailer_init' );

function better_email_signature_hook_phpmailer_init($mobj) {
  
  if (!is_object($mobj) || !is_a($mobj, 'PHPMailer')) return;

  if ( ($options = get_option('better_email_signature_options')) == false ) return;

  $useWhat = (int)$options['use'];
  $signature = $options['text'][$useWhat];

  // Get the original body message
  $body = ($mobj->ContentType == "text/plain") ? $mobj->Body : $mobj->AltBody;

  // Append chossen signature
  if (!preg_match("/^-- /",$body)) $body .= "\n-- \n".$signature;

  // Set the new body message
  if ($mobj->ContentType == "text/plain") {
    $mobj->Body = $body;
  } else {
    $mobj->AltBody = $body;
  }

}
