<?php

function bpeic_l10n_init()
{
  load_plugin_textdomain( 'bpeic', '', dirname( plugin_basename( ___FILE___ ) ) . '/lang' );
}
add_action( 'admin_init','bpeic_l10n_init' );

include( 'fields.php' );

function bpeic_settings_action_links( $links, $file )
{
	array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=bpeic_add_code' ) . '">' . __( 'Add new code', 'bpeic' ) . '</a>' );
	return $links;
}
add_filter( 'plugin_action_links_'.plugin_basename( ___FILE___ ), 'bpeic_settings_action_links', 10, 2 );

function bpeic_admin_menu()
{
	// This little trick is here to avoid overwriting an existing menu added by another plugin.
	global $menu;
	$pos = null;
	for( $i=9; $i>=0; $i-- ){
		if( !isset( $menu[$i] ) ) {
			$pos = $i;
			break;
		}
	}
	// All my pages
	add_menu_page( __( 'Invitation Codes List', 'bpeic' ), __( 'Invitation Codes', 'bpeic' ), 'manage_options', 'bpeic_list_codes', 'bpeic_list_codes', plugins_url( '/images/icon.png', ___FILE___ ), $pos );
	add_submenu_page( 'bpeic_list_codes', __( 'Add new code', 'bpeic' ), __( 'Add new code', 'bpeic' ), 'manage_options', 'bpeic_add_code', 'bpeic_add_code' );
	add_submenu_page( 'bpeic_list_codes', __( 'Generate codes', 'bpeic' ), __( 'Generate codes', 'bpeic' ), 'manage_options', 'bpeic_rand_code', 'bpeic_rand_code' );
	add_submenu_page( 'bpeic_list_codes', __( 'Codes list (raw)', 'bpeic' ), __( 'Codes list (raw)', 'bpeic' ), 'manage_options', 'bpeic_raw_codes', 'bpeic_raw_codes' );
	add_submenu_page( 'bpeic_list_codes', __( 'Some options', 'bpeic' ), __( 'Some Options', 'bpeic' ), 'manage_options', 'bpeic_settings', 'bpeic_settings_page' );
	// and registered settings
	register_setting( 'bpeic_add_code', 'bpeic_field_code', 'bpeic_fields_cb' );
	register_setting( 'bpeic_rand_code', 'bpeic_field_prefix', 'bpeic_fields_cb2' );
	register_setting( 'bpeic_settings', 'bpeic_fields' );
}
add_action( 'admin_menu', 'bpeic_admin_menu' );	

function bpeic_raw_codes()
{
	global $bpeic_options;
?>
	<div class="wrap">
		<?php screen_icon( 'edit' ); ?>
		<h2><?php _e( 'Invitation Codes List', 'bpeic' ); ?>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes' ); ?>"><?php _e( 'Codes list', 'bpeic' ) ;?></a>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_add_code' ); ?>"><?php _e( 'Add new codes', 'bpeic' ) ;?></a>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_rand_code' ); ?>"><?php _e( 'Generate codes', 'bpeic' ) ;?></a>
		</h2>
		<h3><?php _e( 'Only not used codes', 'bpeic' ); ?></h3>
		<?php
		foreach( $bpeic_options['codes'] as $code=>$val )
			if( $val['leftcount'] == 0 )
				unset( $bpeic_options['codes'][$code] );
		$codes = !empty( $bpeic_options['codes'] ) ? implode( "\n", array_keys( $bpeic_options['codes'] ) ) : __( '-- No codes! Add one! --', 'bpeic' );
		?>
		<textarea cols="40" rows="10"><?php echo $codes; ?></textarea>
		<p><i><?php _e ( 'Tips: You can share these codes to allow users to register on your site/blog.', '' ); ?></i></p>
	</div>
<?php
}

function bpeic_settings_page()
{
	settings_errors();
	add_settings_section( 'bpeic_settings', __( 'Add new code', 'bpeic' ), '__return_false', 'bpeic_settings' );
	add_settings_field( 'bpeic_field_code', __( 'Add link', 'bpeic' ), 'bpeic_field_link', 'bpeic_settings', 'bpeic_settings' );
	add_settings_field( 'bpeic_field_count', __( 'Text link', 'bpeic' ), 'bpeic_field_text_link', 'bpeic_settings', 'bpeic_settings' );
?>
	<div class="wrap">
		<?php screen_icon( 'edit' ); ?>
		<h2><?php _e( 'Invitation Codes Settings', 'bpeic' ); ?>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes' ); ?>"><?php _e( 'Codes list', 'bpeic' ) ;?></a>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_add_code' ); ?>"><?php _e( 'Add new codes', 'bpeic' ) ;?></a>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_rand_code' ); ?>"><?php _e( 'Generate codes', 'bpeic' ) ;?></a>
		</h2>

		<form action="options.php" method="post">
			<?php settings_fields( 'bpeic_settings' ); ?>
			<?php do_settings_sections( 'bpeic_settings' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}	

function bpeic_add_code()
{
	settings_errors();
	add_settings_section( 'bpeic_add_code', __( 'Add new code', 'bpeic' ), '__return_false', 'bpeic_add_code' );
	add_settings_field( 'bpeic_field_code', __( 'Code', 'bpeic' ), 'bpeic_field_code', 'bpeic_add_code', 'bpeic_add_code' );
	add_settings_field( 'bpeic_field_count', __( 'Max count', 'bpeic' ), 'bpeic_field_count', 'bpeic_add_code', 'bpeic_add_code' );
?>
	<div class="wrap">
		<?php screen_icon( 'edit' ); ?>
		<h2><?php _e( 'Invitation Codes, add one!', 'bpeic' ); ?>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes' ); ?>"><?php _e( 'Codes list', 'bpeic' ) ;?></a>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_rand_code' ); ?>"><?php _e( 'Generate codes', 'bpeic' ) ;?></a>
		</h2>

		<form action="options.php" method="post">
			<?php settings_fields( 'bpeic_add_code' ); ?>
			<?php do_settings_sections( 'bpeic_add_code' ); ?>
			<?php submit_button( __( 'Add new code', 'bpeic' ) ); ?>
		</form>
	</div>
<?php
}

function bpeic_rand_code()
{
	settings_errors();
	add_settings_section( 'bpeic_rand_code', __( 'Add auto generated codes', 'bpeic' ), '__return_false', 'bpeic_rand_code' );
	add_settings_field( 'bpeic_field_prefix', __( 'Code prefix', 'bpeic' ), 'bpeic_field_prefix', 'bpeic_rand_code', 'bpeic_rand_code' );
	add_settings_field( 'bpeic_field_length', __( 'Length', 'bpeic' ), 'bpeic_field_length', 'bpeic_rand_code', 'bpeic_rand_code' );
	add_settings_field( 'bpeic_field_howmany', __( 'How many codes', 'bpeic' ), 'bpeic_field_howmany', 'bpeic_rand_code', 'bpeic_rand_code' );
	add_settings_field( 'bpeic_field_count', __( 'Max count', 'bpeic' ), 'bpeic_field_count', 'bpeic_rand_code', 'bpeic_rand_code' );
?>
	<div class="wrap">
		<?php screen_icon( 'edit' ); ?>
		<h2><?php _e( 'Invitation Codes, generate some!', 'bpeic' ); ?>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes' ); ?>"><?php _e( 'Codes list', 'bpeic' ) ;?></a>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_add_code' ); ?>"><?php _e( 'Add new code', 'bpeic' ) ;?></a>
		</h2>

		<form action="options.php" method="post">
			<?php settings_fields( 'bpeic_rand_code' ); ?>
			<?php do_settings_sections( 'bpeic_rand_code' ); ?>
			<?php submit_button( __( 'Generate codes', 'bpeic' ) ); ?>
		</form>
	</div>
<?php
}

function bpeic_fields_cb2( $val )
{
	global $bpeic_options;
	$prefix = trim( $val );
	$count = isset( $_POST['bpeic_field_count'] ) ? (int)$_POST['bpeic_field_count'] : 1;
	$length = isset( $_POST['bpeic_field_length'] ) ? (int)$_POST['bpeic_field_length'] : 8;
	$howmany = isset( $_POST['bpeic_field_howmany'] ) ? (int)$_POST['bpeic_field_howmany'] : 5;
	if( $count<1 ):
		add_settings_error( 'bpeic', '', __( 'How many time this code can be used?', 'bpeic' ) . sprintf( __( ' (Minimum %d)', 'bpeic' ), 1 ), 'error' );
	elseif( $length<4 || $length>16 ):
		add_settings_error( 'bpeic', '', __( 'Incorrect length.', 'bpeic' ) . sprintf( __( ' (Minimum %d)', 'bpeic' ), 4 ) . sprintf( __( ' (Maximum %d)', 'bpeic' ), 16 ), 'error' );
	elseif( $howmany<1 ):
		add_settings_error( 'bpeic', '', __( 'How many codes do you need?', 'bpeic' ) . sprintf( __( ' (Minimum %d)', 'bpeic' ), 1 ), 'error' );
	else:
		$temp = array();
		$i=1;
		while( $i<=$howmany ):
			$temp = strtoupper( $prefix . wp_generate_password( $length, false ) );
			if( !in_array( $temp, $bpeic_options['codes'] ) ):
				$i++;
				$bpeic_options['codes'][$temp] = array( 'maxcount'=>$count, 'leftcount'=>$count, 'users'=>'' );
			endif;
		endwhile;
		add_settings_error( 'bpeic', '', sprintf( __( '%d code(s) have been added. <a href="%s">Check the codes list &raquo;</a>', 'bpeic' ), $howmany, admin_url( 'admin.php?page=bpeic_list_codes' ) ), 'updated' );
		update_option( 'bpeic_options', $bpeic_options );
	endif;
	return false;
}

function bpeic_fields_cb( $val )
{
	global $bpeic_options;
	$code = trim( strtoupper( $val ) );
	$count = isset( $_POST['bpeic_field_count'] ) ? (int)$_POST['bpeic_field_count'] : 1;
	if( isset( $bpeic_options['codes'][$code] ) ):
		add_settings_error( 'bpeic', '', sprintf( __( 'The code <i>%s</i> already exists. Please choose another one.', 'bpeic' ), esc_html( $code ) ), 'error' );
	elseif( $count<1 ):
		add_settings_error( 'bpeic', '', __( 'How many time this code can be used?', 'bpeic' ) . sprintf( __( ' (Minimum %d)', 'bpeic' ), 1 ), 'error' );
	elseif( $code=='' ):
		add_settings_error( 'bpeic', '', __( 'Empty code ...', 'bpeic' ), 'error' );
	else:
		add_settings_error( 'bpeic', '', sprintf( __( 'The code <i>%s</i> have been added. <a href="%s">Check the codes list &raquo;</a>', 'bpeic' ), esc_html( $code ), admin_url( 'admin.php?page=bpeic_list_codes' ) ), 'updated' );
		create_invitation_code( $code, $count );
	endif;
	return false;
}

function create_invitation_code( $code, $count=1 )
{
	global $bpeic_options;
	$count = (int)$count>0 ? $count : 1;
	if( isset( $bpeic_options['codes'][$code] ) || trim( $code )=='' ):
		return false;
	else:
		$bpeic_options['codes'][$code] = array( 'maxcount'=>$count, 'leftcount'=>$count, 'users'=>'' );
		update_option( 'bpeic_options', $bpeic_options );
		return true;
	endif;
}

function bpeic_activation()
{
	add_option( 'bpeic_options', array( 'codes' => array( 'INVITATION' => array( 'maxcount'=>999999, 'leftcount'=>999999, 'users'=>'' ) ) ) );
	add_option( 'bpeic_fields', array( 'link' => 'on', 'text_link'=> sprintf( __( 'Need an invitation code? <a href="mailto:%s">Contact us!</a>', 'bpeic' ), get_option( 'admin_email' ) ) ) );
}
register_activation_hook( ___FILE___, 'bpeic_activation' );

function bpeic_uninstaller()
{
	delete_option( 'bpeic_options' );
	delete_option( 'bpeic_fields' );
}
register_uninstall_hook( ___FILE___, 'bpeic_uninstaller' );

function bpeic_list_codes()
{ 
	global $bpeic_options;
	$admin_notices = array( 'updated' => array(), 'error'=>array() );
	if( isset( $_GET['action'], $_GET['_wpnonce'] ) ):
		switch( $_GET['action'] ):
			case 'delete' :
				if( isset( $_GET['code'], $bpeic_options['codes'][$_GET['code']] ) && wp_verify_nonce( $_GET['_wpnonce'], 'bpeic-' . $_GET['action'] . '-' . $_GET['code'] ) ):
					unset( $bpeic_options['codes'][$_GET['code']] );
					update_option( 'bpeic_options', $bpeic_options );
					$admin_notices['updated'][] = sprintf( __( 'The code <b>%s</b> have been successfully deleted.', 'bpeic' ), esc_html( $_GET['code'] ) );
				else:
					$admin_notices['error'][] = sprintf( __( 'The code <b>%s</b> have not been deleted.', 'bpeic' ), esc_html( $_GET['code'] ) );
				endif;
				break;
			case 'reset' : 
				if( wp_verify_nonce( $_GET['_wpnonce'], 'bpeic-' . $_GET['action'] ) ):
					$bpeic_options['codes'] = array();
					update_option( 'bpeic_options', $bpeic_options );
					$admin_notices['updated'][] = __( 'All codes are gone, nobody can register now :(', 'bpeic' );
				endif;
				break;
		endswitch;
	endif;
	// actions
	$counts['all'] = count( $bpeic_options['codes'] );
	$counts['used'] = 0;
	$counts['not_used'] = 0;
	if( $counts['all'] > 0 )
		foreach( $bpeic_options['codes'] as $c )
			if( $c['users'] == '' )
				$counts['not_used']++;
			else
				$counts['used']++;
?>
	<div class="wrap">
		<?php screen_icon( 'edit' ); ?>
		<h2><?php _e( 'Invitation Codes List', 'bpeic' ); ?>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_add_code' ); ?>"><?php _e( 'Add new code', 'bpeic' ) ;?></a>
		<a class="add-new-h2" href="<?php echo admin_url( 'admin.php?page=bpeic_rand_code' ); ?>"><?php _e( 'Generate codes', 'bpeic' ) ;?></a>
		<?php if( !empty( $_GET['s'] ) ) 
			printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( $_GET['s'] ) );
		?>
		</h2>
		<p>
		<?php
		foreach( $admin_notices['updated'] as $an )
			echo '<div class="updated"><p>' . $an . '</p></div>';
		foreach( $admin_notices['error'] as $an )
			echo '<div class="error"><p>' . $an . '</p></div>';
		unset( $an );
		?>
		</p>
		<ul class="subsubsub">
			<li class=""><a class="<?php echo empty( $_GET['status']) && empty( $_GET['s'] ) ? 'current' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes' ); ?>">All <span class="count">(<?php echo $counts['all']; ?>)</span></a> |</li>
			<li class=""><a class="<?php echo !empty( $_GET['status'] ) && $_GET['status']=='used' ? 'current' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes&status=used' ); ?>">Used <span class="count">(<?php echo $counts['used']; ?>)</span></a> |</li>
			<li class=""><a class="<?php echo !empty( $_GET['status'] ) && $_GET['status']=='not_used'  ? 'current' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes&status=not_used' ); ?>">Not used <span class="count">(<?php echo $counts['not_used']; ?>)</span></a></li>
		</ul>
		<form action="<?php echo admin_url( 'admin.php' ); ?>">
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="search-text" name="s" value="<?php _admin_search_query(); ?>" />
			<input type="hidden" id="page" name="page" value="bpeic_list_codes" />
			<?php submit_button( __( 'Search codes', 'bpeic' ), 'button', false, false, array('id' => 'search-submit') ); ?>
		</p>
		</form>
		<table id="codes_table" class="widefat plugins datatables">
			<thead>
				<tr>
					<th scope="col" width="350"><?php _e( 'Code', 'bpeic' ); ?></th>
					<th scope="col" width="350"><?php _e( 'Counter', 'bpeic' ); ?></th>
					<th scope="col" width="350"><?php _e( 'User(s)', 'bpeic' ); ?></th>
					<th scope="col"><?php _e( 'Action', 'bpeic' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col"><?php _e( 'Code', 'bpeic' ); ?></th>
					<th scope="col"><?php _e( 'Counter', 'bpeic' ); ?></th>
					<th scope="col"><?php _e( 'User(s)', 'bpeic' ); ?></th>
					<th scope="col"><?php _e( 'Action', 'bpeic' ); ?></th>
				</tr>
			</tfoot>
			<tbody class="codes_table">
			<?php
			$empty = true;
			if( isset( $bpeic_options['codes'] ) && count( $bpeic_options['codes'] ) > 0 ):
			foreach( $bpeic_options['codes'] as $code=>$infos ): 
				if( !empty( $_GET['status'] ) && ( ( $_GET['status']=='used' && $infos['users']=='' ) || ( $_GET['status']=='not_used' && $infos['users']!='' ) ) ) continue;
				if( !empty( $_GET['s'] ) && strstr( $code, strtoupper( $_GET['s'] ) )===false ) continue;
				$empty = false;
			?>
				<tr class="token">
					<td>
						<div class="activation">
							<pre><b><?php echo esc_html( $code ); ?></b></pre>
						</div>
					</td>
					<td>
						<div class="activation">
							<?php echo '<b>' . $infos['leftcount'] . '</b> / ' . (int)$infos['maxcount']; ?>
						</div>
					</td>
					<td>
						<div class="activation">
							<?php echo !empty( $infos['users'] ) ? implode( ', ', array_map( 'esc_html', $infos['users'] ) ) : '-'; ?>
						</div>
					</td>					
					<td>
						<div class="activation">
							<?php $nonce = wp_create_nonce( 'bpeic-delete-' . $code ); ?>
							<span class="trash"><a href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes&action=delete&code=' . esc_attr( $code ) . '&_wpnonce=' . $nonce ); ?>">Delete</a></span>
						</div>
					</td>
				</tr>
			<?php endforeach;
			else:
				echo '<tr><td colspan="4">' . __( 'No codes yet, <a href="admin.php?page=bpeic_add_code">add one</a>!', 'bpeic' ) . '</td></tr>';
				$empty = false;
			endif;
			if( $empty )
				echo '<tr><td colspan="4">' . __( 'No codes yet, <a href="admin.php?page=bpeic_add_code">add one</a>!', 'bpeic' ) . '</td></tr>';
			?>
			</tbody>
		</table>
		<?php $nonce = wp_create_nonce( 'bpeic-reset' ); ?>
		<p><a href="<?php echo admin_url( 'admin.php?page=bpeic_list_codes&action=reset&_wpnonce=' . $nonce ); ?>" class="button-secondary"><?php _e( 'Clear all codes', 'bpeic' ); ?></a></p>
	</div>
<?php }

function bpeic_admin_notice_noone()
{
	echo '<div class="error" id="message"><p>' . __( 'Nobody can register because you did not set any invitation codes, <a href="admin.php?page=bpeic_add_code">do it now</a>!', 'bpeic' ) . '</p></div>';
}

function bpeic_check_codes()
{
	global $bpeic_options;
	$codes = $bpeic_options['codes'];
	foreach( $codes as $code=>$val )
		if( $val['leftcount'] == 0 )
			unset( $codes[$code] );
	if( count( $codes ) == 0 ):
		$admin_notice = 
		add_action( 'admin_notices', 'bpeic_admin_notice_noone' );
	endif;
}
add_action( 'admin_head', 'bpeic_check_codes' );
