<?php
/*
Plugin Name: BP Easy Invitation Codes
Plugin URI: http://mediatricks.com
Description: BP Visitors have to enter an invitation code to register on your site. The easy way!
Version: 1.0
Author: Mediatricks
Author URI: http://mediatricks.com
License: GPLv2
*/

$bpeic_options = get_option( 'bpeic_options' );
$bpeic_fields = get_option( 'bpeic_fields' );
DEFINE( '___FILE___', __FILE__ );

if( !is_admin() ) :
	include( 'inc/front-end.php' );
else:
	include( 'inc/back-end.php' );
endif;