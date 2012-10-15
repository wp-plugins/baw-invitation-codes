<?php
function bpeic_register_form_add_field()
{ 
	global $bpeic_fields, $allowedposttags;
?>
	<p>
		<label><?php _e( 'Invitation Code', 'bpeic' ); ?></label>
<?php do_action( 'bp_invitation_code_errors' ); ?>
		<input name="invitation_code" tabindex="30" type="text" value="<?php
    echo empty($_POST['invitation_code'])?'':$_POST['invitation_code']; ?>" class="input" id="invitation_code" style="text-transform: uppercase" />
		<?php if( !empty( $bpeic_fields['link'] ) && $bpeic_fields['link']=='on' ): ?>
		<p class="invite-instructions"><?php echo !empty( $bpeic_fields['text_link'] ) ? wp_kses_post( $bpeic_fields['text_link'], $allowedposttags ) : ''; ?></p>
		<?php endif; ?>
	</p>
 <?php
}
add_action( 'register_form', 'bpeic_register_form_add_field' );

function bpeic_registration_errors( )
{
global $bp, $bpeic_options;
 
	$invitation_code = isset( $_POST['invitation_code'] ) ? strtoupper( $_POST['invitation_code'] ) : '';
	if( !array_key_exists( $invitation_code, $bpeic_options['codes'] ) ) {
		  $bp->signup->errors['invitation_code'] = 'The code you entered is not valid.';
	}
elseif ( isset( $bpeic_options['codes'][$invitation_code] ) && $bpeic_options['codes'][$invitation_code]['leftcount']==0 ){
		  $bp->signup->errors['invitation_code'] = 'This Invite Code has already been used.';

	}
}

function invite_code_update() {

//Update the Coupon Codes on successful registrations

global $bp, $bpeic_options;

                $invitation_code = isset( $_POST['invitation_code'] ) ? strtoupper( $_POST['invitation_code'] ) : '';
		$bpeic_options['codes'][$invitation_code]['leftcount']--;
		$bpeic_options['codes'][$invitation_code]['users'][] = $_POST['signup_username'];
		update_option( 'bpeic_options', $bpeic_options );
	
}

function bpeic_login_footer()
{
	global $bpeic_options;
	$invitation_code = isset( $_POST['invitation_code'] ) ? strtoupper( $_POST['invitation_code'] ) : '';
	if( !array_key_exists( $invitation_code, $bpeic_options['codes'] ) ):
		?>
		<script type="text/javascript">
			try{document.getElementById('invitation_code').focus();}catch(e){}
		</script>
		<?php 
	endif;
}
add_action( 'login_footer', 'bpeic_login_footer' );

function registration_add_code_invite(){ ?>

    <div class="register-section" id="profile-details-section">
   <h4><?php _e( 'Invite Code', 'buddypress' ); ?></h4>
    <?php 
    do_action('register_form'); ?>
</div>
<?php
 }
function add_invite_style() {
?>
<style> .invite-instructions {
color: #888;
margin: 15px 0 5px;
font-size: 12px;
} </style>
<?php
}

add_action('bp_after_account_details_fields', 'registration_add_code_invite',20);
add_action('bp_before_registration_confirmed', 'invite_code_update', 20);
add_action('bp_signup_validate', 'bpeic_registration_errors');
add_action('wp_head', 'add_invite_style');