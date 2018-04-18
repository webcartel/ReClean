<?php

function register_my_custom_submenu_page() {
	add_submenu_page( 'reclean', 'Резервное копирование базы данных', 'DB Backup', 'manage_options', 'my-custom-submenu-page', 'my_custom_submenu_page_callback' ); 
}
add_action('admin_menu', 'register_my_custom_submenu_page');



function my_custom_submenu_page_callback() {

	wp_enqueue_style( 'revimaincss', WCST_PLUGIN_DIR_URL . 'css/main.css' );
	wp_enqueue_script('revimainjs', WCST_PLUGIN_DIR_URL . 'js/main.js', array('jquery'), null, 'in_footer');

	?>
		<div class="wrap">
			<h2>Резервное копирование базы данных</h2>
			<div class="wcst_revicleaner_pannel wcst_clearfix">
				<div class="wcst_revicleaner_sub_pannel">
					<div class="wcst_revicleaner">
						<button id="create_db_backup_btn">Выполнить резервное копирование</button>
					</div>
					<table class="backup_files">
						<col width="30%">
						<col width="20%">
						<col width="20%">
						<col width="20%">
						<col width="10%">
						<tr class="header">
							<td><strong>Имя файла</strong></td>
							<td><strong>Размер файла в Мб</strong></td>
							<td><strong>Дата создания</strong></td>
							<td></td>
							<td></td>
						</tr>
						<?php foreach ( get_files( WCST_PLUGIN_UPLOADS_DIR_PATH ) as $file ) { ?>
						<tr class="backup_file">
							<td><a href="<?php echo WCST_PLUGIN_UPLOADS_DIR_URL . '/' . basename($file); ?>"><?php echo basename($file); ?></a></td>
							<td><?php echo round(filesize($file) / 1024 / 1024, 2) . ' Mb'; ?></td>
							<td><?php $filemtime = filemtime($file); echo date("d.m.Y", $filemtime).'&nbsp&nbsp&nbsp'.date("H:i:s", $filemtime); ?></td>
							<td><button class="restore_db_backup_btn" data-db-backup-file="<?php echo basename($file); ?>">Восстановить</button></td>
							<td><button class="delete_db_backup_btn" data-db-backup-file="<?php echo basename($file); ?>">X</button></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	<?php
}



function wcst_create_db_backup()
{
	include_once WCST_PLUGIN_DIR_PATH.'inc/backup-restore-class.php';
	$backup = new backup_restore( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, WCST_PLUGIN_UPLOADS_DIR_PATH.'/' );
	$dump_file_name = $backup->backup();
	header('Content-type: application/json');
	$filesize = round(filesize(WCST_PLUGIN_UPLOADS_DIR_PATH.'/'.$dump_file_name) / 1024 / 1024, 2) . ' Mb';
	$filemtime = filemtime(WCST_PLUGIN_UPLOADS_DIR_PATH.'/'.$dump_file_name);
	$date = date("d.m.Y", $filemtime).'&nbsp&nbsp&nbsp'.date("H:i:s", $filemtime);
	echo json_encode(array( "path" => WCST_PLUGIN_UPLOADS_DIR_URL.'/'.$dump_file_name, "filename" => $dump_file_name, "filesize" => $filesize, "date" => $date ));
	exit();
}
add_action( 'wp_ajax_create_db_backup_action', 'wcst_create_db_backup' );



function wcst_restore_db_backup()
{
	include_once WCST_PLUGIN_DIR_PATH.'inc/backup-restore-class.php';
	$backup  = new backup_restore( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );
	$message = $backup->restore( WCST_PLUGIN_UPLOADS_DIR_PATH.'/'.$_POST['db_backup_file'] );
	echo $message;
	exit();

}
add_action( 'wp_ajax_db_restore_action', 'wcst_restore_db_backup' );



function wcst_delete_db_backup()
{
	if ( unlink( WCST_PLUGIN_UPLOADS_DIR_PATH.'/'.$_POST['db_backup_file'] ) ) {
		echo 'Файл успешно удален';
	}
	else {
		echo 'Возникла ошибка';
	}
	exit();
}
add_action( 'wp_ajax_db_delete_action', 'wcst_delete_db_backup' );