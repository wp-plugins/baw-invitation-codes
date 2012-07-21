<?php
/*
Plugin Name: BAW Easy Invitation Codes
Plugin URI: http://boiteaweb.fr/eic
Description: Visitors have to enter an invitation code to register on your blog. The easy way!
Version: 1.0.2
Author: Juliobox
Author URI: http://boiteaweb.fr
License: GPLv2
*/

$baweic_options = get_option( 'baweic_options' );
$baweic_fields = get_option( 'baweic_fields' );
DEFINE( '___FILE___', __FILE__ );

if( !is_admin() ) :
	include( 'inc/front-end.php' );
else:
	include( 'inc/back-end.php' );
endif;