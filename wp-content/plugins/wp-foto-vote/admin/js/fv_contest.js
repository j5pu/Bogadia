// Var for save Thumbnail image to refresh after action
var imgToRefreshAfterRotate = null;

function fv_rotate_image(el, angle, contest_id, contestant_id, fv_nonce) {
	if ( confirm( fv_lang.rotate_confirm ) ) {
		jQuery(el).closest('td').append('<span class="spinner"></span>');
		// thumbnail image to refresh
		$imgToRefreshAfterRotate = jQuery(el).closest('tr').find('.img img');
		jQuery.growl.notice({ message: fv_lang.rotate_start.replace("*A*", angle) });

		jQuery.ajax({
			type: 'POST',
			url: fv.ajax_url,
			data: {action: 'fv_rotate_image', angle: angle, contest_id: contest_id, photo_id: contestant_id, fv_nonce: fv_nonce},
			success: function(data) {
				data = FvLib.parseJson(data);
				jQuery(el).closest('td').find('.spinner').remove();
				if( data.res == 'ok' ) {
					jQuery.growl.notice({ message: fv_lang.rotate_successful });
					// don't remember refresh thumbnail image
                    if (false == FvLib.strPos($imgToRefreshAfterRotate.attr("src"), 'cloudinary')) {
                        $imgToRefreshAfterRotate.attr("src", $imgToRefreshAfterRotate.attr("src") + "?rand=" + FvLib.randomStr(6));
                    } else {
                        $imgToRefreshAfterRotate.attr("src", '').closest('td').addClass('rotated');
                        jQuery.growl.notice({ message: 'You will see updated image after page reload.' });
                    }
                    $imgToRefreshAfterRotate = null;
                } else {
                    jQuery.growl.warning({ message: fv_lang.rotate_error });
                }
			}
		});
	}
}


// =============================================================================

var file_frame_list, fv_contest_id;

jQuery.fn.addPhotosList = function( fv_nonce ) {

	// If the media frame already exists, reopen it.
	if ( file_frame_list ) {
		file_frame_list.open();
		return;
	}

	// Create the media frame.
	file_frame_list = wp.media.frames.file_frame = wp.media({
		title: "Select photos",
		multiple: true
	});

	// When an image is selected, run a callback.
	file_frame_list.on( 'select', function() {
		var attachments = file_frame_list.state().get('selection').toJSON();

		//console.log( attachments );
		var photos_arr = {};
		for (var N=0; N < attachments.length; N++) {
			photos_arr[N] = {
				'id': attachments[N].id,
				'sizes': attachments[N].sizes
			}
		}

		fv_form_contestants(photos_arr, fv_contest_id, fv_nonce);

		//UnploadInput.val( attachment.sizes.full.url )
		//UnploadInputID.val( attachment.id );
		//jQuery( button ).parents('div').find( "img" ). attr( "src", attachment.sizes.thumbnail.url );
	});

	// Finally, open the modal
	file_frame_list.open();
}

function fv_many_contestants (el, contest_id, fv_nonce) {
	fv_contest_id = contest_id;
	jQuery.fn.addPhotosList( fv_nonce );
}


function fv_save_contestants() {
	jQuery(".photos_list .photos_list_form").each( function(el, n) {
		fv_save_contestant( jQuery("#fv_popup .buttons .button"), fv_contest_id, new FormData(this) );
	});
}

// Edit contest page
function fv_form_contestants(photos_arr, contest_id, fv_nonce) {
	jQuery.growl.notice({ message: "Receive data" });

	jQuery.ajax({
		type: 'POST',
		url: fv.ajax_url,
		data: {action: 'fv_form_contestants', contest_id: contest_id, photos: photos_arr, fv_nonce: fv_nonce},
		success: function(data) {
			data = FvLib.parseJson(data);
			// console.log(data)
			if(data) {
				jQuery('#fv_popup .modal-content').html(data.html);
                jQuery('#fv_popup').modal();
				jQuery.growl.notice({ message: "Form ready" });
			}

		}
	});

}

// Edit contest page
function fv_form_contestant(el, contest_id, contestant_id, fv_nonce) {
	jQuery.growl.notice({ message: "Receive data" });
	jQuery(el).closest('td').append('<span class="spinner"></span>');
	jQuery.ajax({
		type: 'GET',
		url: fv.ajax_url,
		data: {action: 'fv_form_contestant', contest_id: contest_id, contestant_id: contestant_id, fv_nonce: fv_nonce},
		//processData: false,  // tell jQuery not to process the data
		contentType: false,   // tell jQuery not to set contentType
		success: function(data) {
			data = FvLib.parseJson(data);
			jQuery(el).closest('td').find('.spinner').remove();
			// console.log(data)
			if(data) {
				//jQuery('#fv_popup .body').html(data.html);
				jQuery('#fv_popup .modal-content').html(data.html);
				//jQuery('#fv_popup').bPopup();
				jQuery('#fv_popup').modal();
			}

		}
	});
}

// get an associative array of form values
function fv_get_form_data(selector) {
	var $inputs = jQuery(selector);
	// get an associative array of just the values.
	var values = {};
	$inputs.each(function() {
		values[this.name] = jQuery(this).val();
	});
	return values;
}

// Edit contest page
function fv_save_contestant(el, contest_id, form_data) {
	jQuery(el).closest('div').append('<span class="spinner"></span>');

	if ( typeof(form_data) == 'undefined' ) {
		form_data = new FormData( document.querySelector('#fv_popup form') );
		//form_data = fv_get_form_data('#fv_popup form input, #fv_popup form textarea');
	}

	form_data.append("action", "fv_save_contestant");
	form_data.append("contest_id", contest_id);

	jQuery.ajax({
		type: 'POST',
		url: fv.ajax_url,
		data: form_data,
		//data: {action: 'fv_save_contestant', form: form_data, contest_id: contest_id},
		processData: false,  // tell jQuery not to process the data
		contentType: false,   // tell jQuery not to set contentType
		success: function(data) {
			data = FvLib.parseJson(data);

			jQuery(el).closest('div').find('.spinner').remove();
			// console.log(data)
			if( data.html && data.id && !data.add ) {
				jQuery('#table_units tr.id'+data.id).replaceWith( data.html );
			} else if ( data.html && data.id && data.add ) {
				contestTable.row.add( jQuery(data.html) ).draw();
				contestTable.page( 'last' ).draw( false );
				//jQuery('#table_units tbody').append( data.html );

			}
			jQuery('#fv_popup').modal('hide');
			jQuery.growl.notice({ message: fv_lang.saved });

            // custom message
            if ( typeof(data.notify) != "undefined" ) {
			    jQuery.growl( data.notify );
            }
		}
	});

}

// Edit contest and moderation page
function fv_delete_contestant(el, id, constest_id, fv_nonce) {
	if ( confirm( fv_lang.delete_confirmation ) ) {
		jQuery(el).closest('td').append('<span class="spinner"></span>');
		jQuery.get(fv.ajax_url, {action: 'fv_delete_constestant', constestant_id: id, constest_id: constest_id, fv_nonce: fv_nonce },
			  function(data){
				  data = FvLib.parseJson(data);

				  jQuery(el).closest('td').find('.spinner').remove();
				  if (data.res == 'deleted') {
					  jQuery(el).closest('tr').fadeOut().remove();
				  }
				  jQuery.growl.warning({ message: fv_lang.contestant_and_photo_deleted });
			  });
	} // END :: if

	return false;
};

// Moderation page
function fv_approve_contestant(el, id, constest_id, fv_nonce) {
	jQuery(el).closest('td').append('<span class="spinner"></span>');
	jQuery.get(fv.ajax_url, {action: 'fv_approve_constestant', constestant_id: id, constest_id: constest_id, fv_nonce: fv_nonce },
		  function(data){
			  data = FvLib.parseJson(data);

			  jQuery(el).closest('td').find('.spinner').remove();
			  if (data.res == 'approved') {
				  jQuery(el).closest('tr').fadeOut().remove();
				  jQuery.growl.notice({ message: fv_lang.contestant_approved });
			  }
		  });

	return false;
};

function changeStatus(el, newStatus){
	//console.log( jQuery(this).parents('.sv_unit') );

	remStatuses( jQuery(el).parents('.sv_unit') );
	jQuery(el).parents('.sv_unit').find('input.status').val(newStatus);
	jQuery(el).parents('.sv_unit').addClass('status'+newStatus);
	jQuery(el).parents('.sv_unit').find('.foto_status').text( fv_lang.form_pohto_status[newStatus] );
	return false;
}

function remStatuses(el) {
	jQuery(el).removeClass('status0');
	jQuery(el).removeClass('status1');
	jQuery(el).removeClass('status2');
}


function fv_clear_stats(contest_id) {
	if ( confirm(fv_lang.clear_stats_alert) ) {
		jQuery('.clear_ip').append('<span class="spinner"></span>');
		jQuery.get(fv.ajax_url, {action: 'fv_clear_contest_stats', contest_id: contest_id },
			  function(data){
				  data = FvLib.parseJson(data);
				  if (data.res = 'cleared') {
					  jQuery('.clear_ip .spinner').remove();
					  jQuery('.clear_ip').append('<span class="result">'+fv_lang.clear_stats_cleared+'</span>');
					  jQuery.growl.notice({ message: fv_lang.clear_stats_cleared });
				  }
			  });
	} // END :: if

	return false;
};

function fv_clear_votes(contest_id) {
	if ( confirm(fv_lang.reset_votes_alert) ) {
		jQuery('.clear_votes').append('<span class="spinner"></span>');
		jQuery.get(fv.ajax_url, {action: 'fv_reset_contest_votes', contest_id: contest_id },
			  function(data){
				  data = FvLib.parseJson(data);
				  if (data.res = 'ok') {
					  jQuery('.clear_votes .spinner').remove();
					  jQuery('.clear_votes').append('<span class="result">'+fv_lang.reset_votes_ready+'</span>');
					  jQuery.growl.notice({ message: fv_lang.reset_votes_ready });
				  }
			  });
	} // END :: if

	return false;
};

function fv_count_chars(val) {
    jQuery(val).next().text(val.value.length);
};

/* =================================
 postbox hide/show
 ================================= */

jQuery('.handlediv').click(function() {
	if ( jQuery(this).parent().hasClass('closed') ) {
		jQuery(this).parent().removeClass('closed');
		if( FvLib.isSupportsHtml5Storage() ) {
			localStorage.setItem( 'fv.' + jQuery(this).attr('id') , 'opened');
		}
	} else {
		jQuery(this).parent().addClass('closed');
		if( FvLib.isSupportsHtml5Storage() ) {
			localStorage.setItem( 'fv.' + jQuery(this).attr('id') , 'closed');
		}
	}
});


// Get data from LocalStorage and hide needed Boxes
if( FvLib.isSupportsHtml5Storage() ) {

	jQuery('.handlediv').each(function() {
		if ( localStorage.getItem('fv.' + jQuery(this).attr('id')) == 'closed' ) {
			jQuery(this).parent().addClass('closed');
		}
	});

}


/* =================================
 Image UPLOAD
 ================================= */

var fvMediaUploader, targetElUlr, targetElId, thumbEl;

function fv_wp_media_upload(targetSelUrl, targetSelId, thumbSel) {
	targetElUlr = null;
	targetElId = null;
	thumbEl = null;

	targetElUlr = document.querySelector(targetSelUrl);
	targetElId = document.querySelector(targetSelId);
	if ( typeof thumbSel != "undefined" ) {
		thumbEl = document.querySelector(thumbSel);
	}
	// check is selector exists
	if ( !targetElUlr || !targetElId ) {
		jQuery.growl.warning({ message: 'Problem with uploading :: unknown target!' });
		return;
	}

	// If the uploader object has already been created, reopen the dialog
	if (typeof fvMediaUploader != "undefined") {
		fvMediaUploader.open();
		return;
	}
	// Extend the wp.media object
	fvMediaUploader = wp.media.frames.file_frame = wp.media({
		title: 'Choose Image',
		button: {
			text: 'Choose Image'
		}, multiple: false });

	//jQuery.extend( fvMediaUploader, {name :wp_media} );

	// When a file is selected, grab the URL and set it as the text field's value
	fvMediaUploader.on('select', function() {
		var attachment = fvMediaUploader.state().get('selection').first().toJSON();

		targetElUlr.value = attachment.sizes.full.url;
		targetElId.value = attachment.id;

		if ( thumbEl ) {
			thumbEl.src = attachment.sizes.thumbnail.url;
		}
	});

	// Fix for bootstrap modal
	fvMediaUploader.on('close',function() {
		if ( jQuery('#fv_popup').is(":visible") ) {
			jQuery('body').addClass('modal-open');
		}
	});
	// Open the uploader dialog
	fvMediaUploader.open();
}