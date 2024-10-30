<?php
/**
 * 
 * Check if CF7 is installed and activated.
 * Deliver a message to install CF7 if not.
 * Inspired from the: Contact Form 7 Honeypot
 * URL http://www.wordpress.org/plugins/contact-form-7-honeypot
 * 
 */
//add_action( 'admin_init', 'cf7msb_has_parent_plugin' );
function cf7msb_has_parent_plugin() {
	if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		add_action( 'admin_notices', 'cf7msb_nocf7_notice' );

		//deactivate_plugins( CF7Msb_PLUGIN ); 

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

function cf7msb_nocf7_notice() { ?>
	<div class="error">
		<p>
			<?php echo sprintf(
				__('%s must be installed and activated for the Contact Form 7 Manual Spam Blocker plugin to work.', 'cf7-manual-spam-blocker'),
				'<a href="'.admin_url('plugin-install.php?tab=search&s=contact+form+7').'">Contact Form 7</a>'
			); ?>
		</p>
	</div>
	<?php
}


/**
 *
 * Initialize the shortcode
 * Add the Honeypot form tag in CF7 tags
 * 
 */
add_action('wpcf7_init', 'cf7msb_add_form_tag', 10);
function cf7msb_add_form_tag() {

	// Test if new 4.6+ functions exists
	if (function_exists('wpcf7_add_form_tag')) {
		wpcf7_add_form_tag( 
			'cf7msb_honeypot', 
			'cf7msb_formtag_handler', 
			array( 
				'name-attr' => true, 
				'do-not-store' => true,
				'not-for-mail' => true
			)
		);
	} else {
		wpcf7_add_shortcode( 'cf7msb_honeypot', 'cf7msb_formtag_handler', true );
	}
}


/**
 * 
 * Form Tag handler
 * HTML of honeypot form tag in form
 * 
 */
function cf7msb_formtag_handler( $tag ) {

	// Test if new 4.6+ functions exists
	$tag = (class_exists('WPCF7_FormTag')) ? new WPCF7_FormTag( $tag ) : new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( 'text' );
	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_option( 'id', 'id', true );
	$atts['id'] = !empty($atts['id']) ? $atts['id'] : '';

	$atts['message'] = 'Not for humans.';
	$atts['name'] = $tag->name;
	$atts['type'] = $tag->type;
	$atts['validation_error'] = $validation_error;
	$atts['css'] = apply_filters('cf7msb_honeypot_container_css', 'display:none !important; visibility:hidden !important;');

	$el_css = 'style="'.$atts['css'].'"';
	//$el_css = ''; 

	$html = '<span class="wpcf7-form-control-wrap ' . $atts['name'] . '-wrap" '.$el_css.'>';
	$html .= '<label for="' . $atts['id'] . '" class="hp-message">'.$atts['message'].'</label>';
	$html .= '<input id="' . $atts['id'] . '" class="' . $atts['class'] . '"  type="text" name="' . $atts['name'] . '" value="" size="40" tabindex="-1" />';
	$html .= $validation_error . '</span>';

	// Hook for filtering finished Honeypot form element.
	return apply_filters('cf7msb_honeypot_html_output',$html, $atts);
}


/**
 * 
 * CF7MSB Honeypot Validation Filter
 * 
 */
add_filter( 'wpcf7_validate_cf7msb_honeypot', 'cf7msb_honeypot_validate' ,10,2);
function cf7msb_honeypot_validate( $result, $tag ) {
	
	// Test if new 4.6+ functions exists
	$tag = (class_exists('WPCF7_FormTag')) ? new WPCF7_FormTag( $tag ) : new WPCF7_Shortcode( $tag );

	$name = $tag->name;

	$value = isset( $_POST[$name] ) ? sanitize_text_field($_POST[$name]) : '';
	
	if ( $value != '' || !isset( $_POST[$name] ) ) {
		$result['valid'] = false;
		$result['reason'] = array( $name => wpcf7_get_message( 'spam' ) );
		//$result->invalidate($tag, wpcf7_get_message('spam')); 
	}

	return $result;
}


/**
 * 
 * Tag generator
 * 		Adds Honeypot to the CF7 form editor
 * 
 */
add_action( 'wpcf7_admin_init', 'cf7msb_honeypot_add_tag_generator', 35 );
function cf7msb_honeypot_add_tag_generator() {
	if (class_exists('WPCF7_TagGenerator')) {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'cf7msb_honeypot', __( 'cf7msb_honeypot', 'cf7-manual-spam-blocker' ), 'wpcf7_tg_pane_cf7msb_honeypot' );
	} else if (function_exists('wpcf7_add_tag_generator')) {
		wpcf7_add_tag_generator( 'cf7msb_honeypot', __( 'cf7msb_honeypot', 'cf7-manual-spam-blocker' ),	'wpcf7-tg-pane-cf7msb_honeypot', 'wpcf7_tg_pane_cf7msb_honeypot' );
	}
}

function wpcf7_tg_pane_cf7msb_honeypot($contact_form, $args = '') {
	if (class_exists('WPCF7_TagGenerator')) {
		$args = wp_parse_args( $args, array() );
		$description = __( "Generate a form-tag for a spam-stopping honeypot field. For more details, see %s.", 'cf7-manual-spam-blocker' );
		$desc_link = '<a href="https://wordpress.org/plugins/cf7-manual-spam-blocker/" target="_blank">'.__( 'WordPress Spam Blocker', 'cf7-manual-spam-blocker' ).'</a>';
		?>
		<div class="control-box">
			<fieldset>
				<!--<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>-->

				<table class="form-table"><tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'cf7-manual-spam-blocker' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /><br>
							<em><?php echo esc_html( __( 'Change "cf7msb_honeypot" to be something more general like "email1".', 'cf7-manual-spam-blocker' ) ); ?></em>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'ID (optional)', 'cf7-manual-spam-blocker' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class (optional)', 'cf7-manual-spam-blocker' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
						</td>
					</tr>
				</tbody></table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="cf7msb_honeypot" class="tag code" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'cf7-manual-spam-blocker' ) ); ?>" />
			</div>

			<br class="clear" />
		</div>
	<?php } else { ?>
		<div id="wpcf7-tg-pane-honeypot" class="hidden">
			<form action="">
				<table>
					<tr>
						<td>
							<?php echo esc_html( __( 'Name', 'cf7-manual-spam-blocker' ) ); ?><br />
							<input type="text" name="name" class="tg-name oneline" /><br />
							<em><small><?php echo esc_html( __( 'For better security, change "honeypot" to something less bot-recognizable.', 'cf7-manual-spam-blocker' ) ); ?></small></em>
						</td>
						<td></td>
					</tr>
					
					<tr>
						<td colspan="2"><hr></td>
					</tr>

					<tr>
						<td>
							<?php echo esc_html( __( 'ID (optional)', 'cf7-manual-spam-blocker' ) ); ?><br />
							<input type="text" name="id" class="idvalue oneline option" />
						</td>
						<td>
							<?php echo esc_html( __( 'Class (optional)', 'cf7-manual-spam-blocker' ) ); ?><br />
							<input type="text" name="class" class="classvalue oneline option" />
						</td>
					</tr>
					<tr>
						<td colspan="2"><hr></td>
					</tr>			
				</table>
				
				<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'cf7-manual-spam-blocker' ) ); ?><br /><input type="text" name="cf7msb_honeypot" class="tag" readonly="readonly" onfocus="this.select()" /></div>
			</form>
		</div>
	<?php }
}
