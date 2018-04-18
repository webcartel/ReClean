<?php

add_action('admin_menu', function() {
	add_menu_page( 'Очистка базы данных', 'ReClean', 'manage_options', 'reclean', 'add_my_setting', WCST_PLUGIN_DIR_URL.'images/reclean-plugin-icon.png', 99 );
} );


function add_my_setting() {

	wp_enqueue_style( 'revimaincss', WCST_PLUGIN_DIR_URL . 'css/main.css' );
	wp_enqueue_script('revimainjs', WCST_PLUGIN_DIR_URL . 'js/main.js', array('jquery'), null, 'in_footer');

	global $wpdb;
	$records['revision']           = array( 'Редакции' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'"));
	$records['autodraft']          = array( 'Автоматические черновики' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'"));
	$records['draft']              = array( 'Черновики' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'"));
	$records['trash']              = array( 'Корзина' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'trash'"));
	$records['comment_moderation'] = array( 'Комментарии на модерации' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'"));
	$records['comment_spam']       = array( 'Комментарии (спам)' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'"));
	$records['comment_trash']      = array( 'Комментарии (корзина)' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'"));
	$records['postmeta']           = array( 'Произвольные поля записей' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT ID FROM $wpdb->posts)"));
	$records['commentmeta']        = array( 'Произвольные поля Комментариев' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)"));
	$records['relationships']      = array( 'Связи' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)"));
	$records['transient_feed']     = array( 'transient_feed' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'"));
	$records['files_without_post'] = array( 'files_without_pos' , $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '0'"));

	$db_info = get_db_size();

	?>
	<div class="wrap">
		<h2><?php echo get_admin_page_title() ?></h2>

		<div class="wcst_revicleaner_pannel wcst_clearfix">

			<div class="wcst_revicleaner_sub_pannel">

				<table class="elements_to_clean_up">
					<col>
					<col>
					<col width="200px">
					<tr>
						<td><strong>Вид записи</strong></td>
						<td><strong>Количество записей</strong></td>
						<td></td>
					</tr>
				<?php foreach ($records as $record_key => $record) { ?>
					<tr class="element_to_clean_up">
						<td><?php echo $record[0]; ?></td>
						<td class="count"><?php if ( $record[1] > 0 ) { echo '<span class="red">'.$record[1].'</span>'; } else { echo '<span class="green">'.$record[1].'</span>'; } $amount = $record[1] + $amount; ?></td>
						<td data-id="<?php echo $record_key; ?>"><?php if ( $record[1] > 0 ) { ?><button class="clean-btn" id="<?php echo $record_key; ?>">Очистить</button><?php } else { ?><span class="wcst_green_checkmark"></span><?php } ?></td>
					</tr>
				<?php } ?>
					<tr class="clear">
						<td><strong>Всего записей для удаления</strong></td>
						<td class="count"><strong><?php if ( $amount > 0 ) { echo '<span class="red">'.$amount.'</span>'; } else { echo '<span class="green">'.$amount.'</span>'; } ?></strong></td>
						<td><?php if ( $amount > 0 ) { ?><button class="all-clean-btn">Очистить все</button><?php } else { ?><span class="wcst_green_checkmark"></span><?php } ?></td>
					</tr>
				</table>

			</div>

			<div class="wcst_revicleaner_sub_pannel">

				<table>
					<tr>
						<td><strong style="font-size: 16px; color: #515151;">Общий размер базы данных</strong></td>
						<td><strong style="font-size: 20px; color: #0073AA;"><?php echo round($db_info['db_size'], 2) . ' Mb'; ?></strong></td>
					</tr>
				</table>

				<table>
					<tr>
						<td><strong>Имя таблицы</strong></td>
						<td><strong>Размер</strong></td>
					</tr>
					<?php foreach ( $db_info['db_tables_size'] as $key => $table_size ) { ?>
					<tr>
						<td><?php echo $key; ?></td>
						<td><span style="color: #0073AA;"><?php echo round($table_size, 2) . ' Mb'; ?></span></td>
					</tr>
					<?php } ?>
				</table>

			</div>

		</div>

	</div>
	<?php

}



function wcst_all_clean()
{
	// sleep(5);
	global $wpdb;

	if ( $_POST['record_type'] == 'revision' ) {
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'autodraft' ) {
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'draft' ) {
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'draft'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'draft'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'trash' ) {
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'trash'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'trash'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'comment_moderation' ) {
		$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = '0'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'comment_spam' ) {
		$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'comment_trash' ) {
		$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'postmeta' ) {
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT ID FROM $wpdb->posts)");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id NOT IN (SELECT ID FROM $wpdb->posts)");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'commentmeta' ) {
		$wpdb->query("DELETE FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM $wpdb->comments)");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'relationships' ) {
		$wpdb->query("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM $wpdb->posts)");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'transient_feed' ) {
		$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'");
		echo $count;
		exit();
	}

	if ( $_POST['record_type'] == 'files_without_post' ) {
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '0'");
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '0'");
		echo $count;
		exit();
	}


}
add_action( 'wp_ajax_all_clean_action', 'wcst_all_clean' );
