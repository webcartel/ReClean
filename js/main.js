jQuery(document).ready(function( $ ) {

	// var loaderHtml = '<div class="wcst_loader_w loader"><div class="wcst_loader"></div></div>';
	var loaderHtml = '<div class="wcst_loader_2 loader"><span class="wcst_loader_2_b1"></span><span class="wcst_loader_2_b2"></span><span class="wcst_loader_2_b3"></span></div>';
	
	$('#revicleanbtn').on('click', function( e ) {
		e.preventDefault();
		$('#revicleanbtn').hide();
		$('#revicleaner').html(loaderHtml);
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: 'action=revicleanaction&reviclean=true&revicleanpostid=' + $(this).val(),
			success: function () {
				$('#revicleaner').html('<h4>Редакции очищены</h4>').fadeOut(5000);
				$('#misc-publishing-actions .misc-pub-section.misc-pub-revisions').hide();
			}
		});
	});


	// Очистка позиции
	$('.clean-btn').on('click', function( e ) {
		e.preventDefault();
		var btn = $(this);
		var btn_wrap = $(this).parent();
		btn.hide();
		btn_wrap.html(loaderHtml);
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: 'action=all_clean_action&record_type='+ btn.attr('id'),
			success: function ( data ) {
				btn_wrap.html('<span class="wcst_green_checkmark"></span>');
				btn_wrap.parent().find('.count').html('<span class="green">'+ data +'</span>');
			}
		});
	});

	// Полная очистка
	$('.all-clean-btn').on('click', function( e ) {
		e.preventDefault();
		var btn = $(this);
		var btn_wrap = $(this).parent();
		btn.hide();
		btn_wrap.html(loaderHtml);
		promise = $.when();
		$('.elements_to_clean_up .element_to_clean_up').each(function(index, el) {
			var element = $(this).find('td:last-child');
			var element_id = $(this).find('td:last-child').data('id');
			promise = promise.then(function(){
				return $.ajax({
					url: ajaxurl,
					type: 'POST',
					data: 'action=all_clean_action&record_type='+ element_id,
					success: function ( data ) {
						element.html('<span class="wcst_green_checkmark"></span>');
						element.parent().find('.count').html('<span class="green">'+ data +'</span>');
						element.parent().addClass('clear');
					}
				});
			});
		});
		promise.then(function(){
			btn_wrap.html('<span class="wcst_green_checkmark"></span>');
			btn_wrap.parent().find('.count').html('<span class="green">0</span>');
			$('.element_to_clean_up').toggleClass('clear');
		}).then(function(){
			btn_wrap.parent().addClass('clear');
		});
	});



	// Create backup
	$('#create_db_backup_btn').on('click', function( e ) {
		e.preventDefault();
		var btn = $(this);
		btn.hide();
		var btn_wrap = $(this).parent();
		btn_wrap.append(loaderHtml);
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: 'action=create_db_backup_action',
			success: function ( data ) {
				btn_wrap.find('.loader').hide();
				btn.show();
				$('.backup_files .header').after('<tr class="backup_file"><td><a href="' + data.path + '">' + data.filename + '</a></td><td>' + data.filesize + '</td><td>' + data.date + '</td><td><button class="restore_db_backup_btn" data-db-backup-file="' + data.filename + '">Восстановить</button></td><td><button class="delete_db_backup_btn" data-db-backup-file="' + data.filename + '">X</button></td></tr>');
			}
		});
	});



	// Restore backup
	$('.backup_files').on('click', '.backup_file td .restore_db_backup_btn', function( e ) {
		e.preventDefault();
		var btn = $(this);
		if ( confirm('Восстановить резервную копию базы данных из файла '+btn.data('db-backup-file')+'?') ) {
			btn.hide();
			var btn_wrap = $(this).parent();
			btn_wrap.append(loaderHtml);
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: 'action=db_restore_action&db_backup_file='+btn.data('db-backup-file'),
				success: function ( data ) {
					btn_wrap.find('.loader').hide();
					btn.show();
					alert(data);
				}
			});
		}
	});



	// Delete backup
	$('.backup_files').on('click', '.backup_file td .delete_db_backup_btn', function( e ) {
		e.preventDefault();
		var btn = $(this);
		if ( confirm('Удалить резервную копию базы данных '+btn.data('db-backup-file')+'?') ) {
			btn.hide();
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: 'action=db_delete_action&db_backup_file='+btn.data('db-backup-file'),
				success: function ( data ) {
					btn.parent().parent().fadeOut(500);
				}
			});
		}
	});
});