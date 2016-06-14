<?php

add_action( 'admin_enqueue_scripts', 'eighttracks_pointer_header' );
function eighttracks_pointer_header() {
	$enqueue = false;

	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

	if ( ! in_array( 'eighttracks_pointerwidgets4', $dismissed ) ) {
		$enqueue = true;
		add_action( 'admin_print_footer_scripts', 'eighttracks_pointer_footer' );
	}
    
	if ( ! in_array( 'eighttracks_pointerposts4', $dismissed ) ) {
		$enqueue = true;
		add_action( 'admin_print_footer_scripts', 'eighttracks_pointer_footer2' );
	}

	if ( $enqueue ) {
		// Enqueue pointers
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_style( 'wp-pointer' );
	}
}

function eighttracks_pointer_footer() {
	$pointer_content = '<h3>Welcome 8tracks Users!</h3>';
	$pointer_content .= '<p>You can add mixes to your sidebar from the widgets page.</p>';
    
?>

<script type="text/javascript">// <![CDATA[
jQuery(document).ready(function($) {
	$('#menu-appearance').pointer({
		content: '<?php echo $pointer_content; ?>',
		position: {
			edge: 'left',
			align: 'center'
		},
		close: function() {
			$.post( ajaxurl, {
				pointer: 'eighttracks_pointerwidgets4',
				action: 'dismiss-wp-pointer'
			});
		}
	}).pointer('open');
});
// ]]></script>
<?php
}

function eighttracks_pointer_footer2() {
	$pointer_content = '<h3>Welcome 8tracks Users!</h3>';
	$pointer_content .= '<p>You can add mixes to your posts by pasting your shortcode from 8tracks.com, or with our handy visual editor button.</p>';
    
?>

<script type="text/javascript">// <![CDATA[
jQuery(document).ready(function($) {
	$('#menu-posts').pointer({
		content: '<?php echo $pointer_content; ?>',
		position: {
			edge: 'left',
			align: 'center'
		},
		close: function() {
			$.post( ajaxurl, {
				pointer: 'eighttracks_pointerposts4',
				action: 'dismiss-wp-pointer'
			});
		}
	}).pointer('open');
});
// ]]></script>
<?php
}

?>