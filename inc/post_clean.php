<?php

function checkbox_revicleaner()
{
	wp_enqueue_style( 'revimaincss', WCST_PLUGIN_DIR_URL . 'css/main.css' );
	wp_enqueue_script('revimainjs', WCST_PLUGIN_DIR_URL . 'js/main.js', array('jquery'), null, 'in_footer');

	global $wpdb;
	global $post;
	$id = $post->ID;
	$revcount = $wpdb->query("SELECT ID FROM wp_posts WHERE post_type = 'revision' AND post_parent = $id");
	if ( $revcount > 0 )
	{
		?>
		<div id="revicleaner" class="wcst_revicleaner">
			<button id="revicleanbtn" name="revicleanpostid" value="<?php global $post; echo $post->ID; ?>">Очистить редакции</button>
		</div>
		<?php
	}
}
add_action( 'post_submitbox_start', 'checkbox_revicleaner' );



function reviclean()
{
	// sleep(5);
	global $wpdb;
	$id = $_POST['revicleanpostid'];
	$revcount = $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'revision' AND post_parent = $id");
}
add_action( 'wp_ajax_revicleanaction', 'reviclean' );