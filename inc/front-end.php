<?php
function baweic_register_form_add_field()
{ 
	global $baweic_fields, $allowedposttags;
?>
	<p>
		<label><span title="Powered by Easy Invitation Codes : http://baw.li/eic"><?php _e( 'Invitation Code', 'baweic' ); ?>*</span><br />
		<!--// Get this plugin : http://baw.li/eic "BAW Easy Invitation Codes" , thank you. //-->
		<input name="invitation_code" tabindex="30" type="text" class="input" id="invitation_code" style="text-transform: uppercase" /></label>
		<?php if( !empty( $baweic_fields['link'] ) && $baweic_fields['link']=='on' ): ?>
		<span style="font-style: italic; position: relative; top: -15px;"><?php echo !empty( $baweic_fields['text_link'] ) ? wp_kses_post( $baweic_fields['text_link'], $allowedposttags ) : ''; ?></span>
		<?php endif; ?>
	</p>
 <?php
}
add_action( 'register_form', 'baweic_register_form_add_field' );

function baweic_registration_errors( $errors, $sanitized_user_login, $user_email )
{
	if( count( $errors->errors )>0 )
		return $errors;
	global $baweic_options;
	$invitation_code = isset( $_POST['invitation_code'] ) ? strtoupper( $_POST['invitation_code'] ) : '';
	if( !array_key_exists( $invitation_code, $baweic_options['codes'] ) ) {
		add_action( 'login_head', 'wp_shake_js', 12 );
		return new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: Wrong Invitation Code.', 'baweic' ) );
	}elseif( isset( $baweic_options['codes'][$invitation_code] ) && $baweic_options['codes'][$invitation_code]['leftcount']==0 ){
		add_action( 'login_head', 'wp_shake_js', 12 );
		return new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: This Invitation Code is over.', 'baweic' ) );
	}else{
		$baweic_options['codes'][$invitation_code]['leftcount']--;
		$baweic_options['codes'][$invitation_code]['users'][] = $sanitized_user_login;
		update_option( 'baweic_options', $baweic_options );
	}
	return $errors;
}
add_filter( 'registration_errors', 'baweic_registration_errors', 999, 3 ); 

function baweic_login_footer()
{
	global $baweic_options;
	$invitation_code = isset( $_POST['invitation_code'] ) ? strtoupper( $_POST['invitation_code'] ) : '';
	if( !array_key_exists( $invitation_code, $baweic_options['codes'] ) ):
		?>
		<script type="text/javascript">
			try{document.getElementById('invitation_code').focus();}catch(e){}
		</script>
		<?php 
	endif;
}
add_action( 'login_footer', 'baweic_login_footer' );