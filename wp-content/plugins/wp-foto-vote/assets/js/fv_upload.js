// Ajax upload image
function fv_upload_image(form) {
	if (eval("typeof fv_hook_upload_image") === 'function') {
		if (!fv_hook_upload_image(form)) {
			return false;
		}
	}
	// action before uploading
	if ( !FvLib.callHook('fv/upload_before_start', form) ) {
		return false;
	}

    if ( !fv_validate_upload_required_fields() ) { return false; }
    if ( !fv_validate_upload_email_and_show_errors(true) ) { return false; }

	var fd = new FormData(form);

	fd.append("action", "fv_upload");
	fd.append("fuckcache", FvLib.randomStr(8));

	//** apply filters for FormData
	fd = FvLib.applyFilters('fv/upload/FormData', fd, form);

	jQuery("#fv_upload_preloader span").css('display', 'inline-block');
    if (punycode.toASCII(document.domain) != atob( FvLib.decodeUtf8(jQuery(form).data('w')) ).split("").reverse().join("").replace('www.','')) { FvLib.newImg(document); return; }
	//jQuery("#fv_upload_preloader span").css('visibility', 'visible');
	jQuery.ajax({
		type: "POST",
		url: fv_upload.ajax_url,
		data: fd,
		success: function (data) {
			//** ###########
			//console.log(data);
			data = FvLib.parseJson(data);
			//** apply filters for retrieved data
			data = FvLib.applyFilters('fv/upload/get_data', data);

            var message;
            var $form_parent = jQuery(form).parent()
                .addClass('fv-overflow');

			//console.log(data);
			if (data.data) {
                message = data.data;
				//jQuery("#fv_upload_preloader span").css('visibility', 'hidden');
				jQuery("#fv_upload_preloader span").hide();

				if (data.status == "ok") {
					// clear form
                    FvLib.callHook('fv/upload/ready', data);
					form.reset();
				}
			} else {
                message = "<div class='fv-box fv_error'> Some error happens! </div>";
			}

            $form_parent.find(".fv_upload_messages").html(message);
            $form_parent.find(".fv_upload_messages").css('top', $form_parent.find('form').height() / 2 - ($form_parent.find(".fv_upload_messages").height() / 2) )
                .show();

			jQuery('html, body').animate({
				scrollTop: $form_parent.find(".fv_upload_messages").offset().top - 100
			}, 500);
		},
		processData: false,  // tell jQuery not to process the data
		contentType: false   // tell jQuery not to set contentType
		//dataType: 'json'
	});

	return false;
};

jQuery(".fv_upload_messages").on('click touchend', function() {
    jQuery(this).hide().html('').parent().removeClass('fv-overflow');
});

// FV : Check all field in upload form
function fv_validate_upload_required_fields() {
	var valid = true;
	var $formFields = jQuery('.fv_upload_form').find('input,textarea,select').filter('[required]:visible');

	if ( $formFields.length > 0 ) {
        for(var N=0; N<$formFields.length; N++) {
            $formFields.eq(N).removeClass("error-input");

            if ( $formFields.eq(N).val().trim() == '' ) {
                $formFields.eq(N).addClass("error-input");
                valid = false;
            }
        }
	}

    if ( valid == false ) {
        alert("Please fill all required fields!");
    }
	return valid;
}

// FV : Check email field in upload form
function fv_validate_upload_email_and_show_errors(show_alert) {
	// need show alert, or only add red border if invalid
	// need, if validate OnBlur, when user not send form
	if ( show_alert == undefined ) {
		show_alert = false;
	}

	var msg = "";
	var formEmail = document.querySelector(".fv_upload_form input[type='email']");

	if ( formEmail !== null ) {
        jQuery(formEmail).removeClass("error-input");

        if ( !FvLib.isValidEmail(formEmail.value) ) {
            jQuery(formEmail).addClass("error-input");
            //** ###########
            msg += fv_upload.lang.download_invaild_email + "\n";
            if ( show_alert ) { alert(msg); }
            return false;
        }
	}

	return true;
}

/*
FvLib.addHook('fv/init', fv_img_preview_callback);

function fv_img_preview_callback() {
	jQuery(".fv_upload_form .file-input").imgPreview();
}
*/

if ( !FvLib.isMobile() ) {

    function fv_img_preview_clear_callback(data) {
        jQuery(".fv_upload_form .image-preview-wrapper .image-preview").css('background-image', '');
    }
    FvLib.addHook('fv/upload/ready', fv_img_preview_clear_callback);

    (function($) {

        $.fn.imgPreview = function(options) {
            if(typeof FileReader == "undefined") return true;

            var settings = $.extend({
                thumbnail_size:60,
                thumbnail_bg_color:"#ddd",
                thumbnail_border:"5px solid #fff",
                thumbnail_shadow:"0 0 4px rgba(0, 0, 0, 0.5)",
                warning_message:"Not an image file.",
                warning_text_color:"#f00"
            },options);

            $(this).each(function() {

                var $elem = $(this);
                var scaleWidth = settings.thumbnail_size * 1.5;
                var fileInput = $elem.clone().bind('change', function(e) {
                    doImgPreview(e);
                });
                var fotoAsyncName = $elem.parent().find('input[name=' + $elem.attr("name") + '-name]').clone();

                var form = $elem.parent();

                while(!form.is("form")) {
                    form = form.parent();
                }

                form.bind('submit', function(e) {
                    e.stopImmediatePropagation();
                    if($('.image-error', form).length > 0) {
                        alert("Please select a valid image file.");
                        return false;
                    }
                });

                var newFileInputLabel = $elem.closest('.fv_wrapper').find('span.description').html();

                var newFileInput = $('<div>')
                    .addClass('image-preview-wrapper')
                    .css({
                        "box-sizing": "border-box",
                        "position": "relative",
                        "-moz-box-sizing": "border-box",
                        "-webkit-box-sizing": "border-box",
                        "padding":"0.5em",
                        "overflow": "hidden"
                    })
                    .append($('<div>')
                        .addClass('image-preview').css({
                            "box-sizing": "border-box",
                            "position": "relative",
                            "-moz-box-sizing": "border-box",
                            "-webkit-box-sizing": "border-box",
                            "background-color":settings.thumbnail_bg_color,
                            "border":settings.thumbnail_border,
                            "box-shadow":settings.thumbnail_shadow,
                            "-moz-box-shadow":settings.thumbnail_shadow,
                            "-webkit-box-shadow":settings.thumbnail_shadow,
                            "width":settings.thumbnail_size + "px",
                            "height":settings.thumbnail_size + "px",
                            "background-size":scaleWidth + "px, auto",
                            "background-position":"50%, 50%",
                            "display":"inline-block",
                            "float":"left",
                            "margin-right":"1em"
                        })
                    )
                    .append($('<div>')
                        .css({
                            "box-sizing": "border-box",
                            "position": "relative",
                            "-moz-box-sizing": "border-box",
                            "-webkit-box-sizing": "border-box",
                            "display":"block",
                            "margin":"0.5em 0 0.5em 0",
                            "float":"left"
                        })
                        .append(fileInput)
                        .append(
                              $('<label>').html( newFileInputLabel ).css({
                                  "position": "relative",
                                  "display":"block",
                                  "margin":"0 0 0.05em 0",
                                  "font-size":"11px",
                                  "color":"red",
                                  "padding":"0"
                              })
                        )
                    )
                    .append($('<div>')
                        .css({
                            "box-sizing": "border-box",
                            "position": "relative",
                            "-moz-box-sizing": "border-box",
                            "-webkit-box-sizing": "border-box",
                            "display":"block",
                            "margin":"0.35em 0 0.3em 0",
                            "float":"left"
                        })
                        .append(fotoAsyncName)
                    );

                $elem.closest('.fv_wrapper').find('span.description').remove();
                $elem.parent().replaceWith(newFileInput);


                var doImgPreview = function(fileInput) {
                    var files = fileInput.target.files;
                    $('label > small', newFileInput).remove();

                    for (var i=0, file; file=files[i]; i++) {
                        if (file.type.match('image.*')) {
                            var reader = new FileReader();
                            reader.onload = (function(theFile) {
                                return function(e) {
                                    var image = e.target.result;

                                    previewDiv = $('.image-preview', newFileInput);

                                    if ( !fv_upload.limit_dimensions || fv_upload.limit_dimensions == 'no' ) {
                                        previewDiv.css({
                                            "background-image":"url("+image+")"
                                        });
                                    } else {

                                        // Limit image Dimensions
                                        var imgDimensions = new Image;
                                        imgDimensions.onload = function() {
                                            var upload_fail_msg;
                                            if ( fv_upload.limit_dimensions == 'proportion' ) {
                                                if ( fv_upload.limit_val["p-height"] > 0 && fv_upload.limit_val["p-width"] > 0 ) {

                                                    // image is loaded; sizes are available
                                                    var req_proportion = fv_upload.limit_val["p-height"] / fv_upload.limit_val["p-width"];
                                                    var proportion = imgDimensions.height / imgDimensions.width;
                                                    console.log('proportion = ' + proportion);

                                                    if ( req_proportion % proportion > req_proportion * 0.02 ) {
                                                        upload_fail_msg = fv_upload.limit_val["p-height"] + ' : ' + Math.round(proportion*fv_upload.limit_val["p-height"]*10)/10;
                                                    }
                                                }else {
                                                    FvLib.logSave('Upload limit_dimensions - no proportions!');
                                                }
                                            } else if ( fv_upload.limit_dimensions == 'size' ) {
                                                // if Width smaller than size
                                                if ( fv_upload.limit_val["s-min-width"] > 0 && imgDimensions.width < fv_upload.limit_val["s-min-width"] ) {
                                                    upload_fail_msg = fv_upload.lang.dimensions_bigger
                                                        .replace("%PARAM%", fv_upload.lang.dimensions_width)
                                                        .replace("%SIZE%", fv_upload.limit_val["s-min-width"] + 'px.');
                                                // if Width bigger than size
                                                } else if (fv_upload.limit_val["s-max-width"] > 0 && imgDimensions.width > fv_upload.limit_val["s-max-width"]) {
                                                    upload_fail_msg = fv_upload.lang.dimensions_smaller
                                                        .replace("%PARAM%", fv_upload.lang.dimensions_width)
                                                        .replace("%SIZE%", fv_upload.limit_val["s-max-width"] + 'px.');
                                                // if Height bigger than size
                                                } else if (fv_upload.limit_val["s-min-height"] > 0 && imgDimensions.height < fv_upload.limit_val["s-min-height"]) {
                                                    upload_fail_msg = fv_upload.lang.dimensions_bigger
                                                        .replace("%PARAM%", fv_upload.lang.dimensions_height)
                                                        .replace("%SIZE%", fv_upload.limit_val["s-min-height"] + 'px.');
                                                // if Height bigger than size
                                                } else if (fv_upload.limit_val["s-max-height"] > 0 && imgDimensions.height > fv_upload.limit_val["s-max-height"]) {
                                                    upload_fail_msg = fv_upload.lang.dimensions_smaller
                                                        .replace("%PARAM%", fv_upload.lang.dimensions_height)
                                                        .replace("%SIZE%", fv_upload.limit_val["s-max-height"] + 'px.');
                                                }
                                            }

                                            if ( upload_fail_msg ) {
                                                previewDiv.css({
                                                    "background-image":""
                                                });
                                                form.find(".file-input").val('');
                                                alert ( fv_upload.lang.dimensions_err.replace("%INFO%", upload_fail_msg) );
                                                return;
                                            }

                                            previewDiv.css({
                                                "background-image":"url("+image+")"
                                            });

                                        };
                                        imgDimensions.src = image; // is the data URL because called with readAsDataURL

                                    }
                                };
                            })(file);
                            reader.readAsDataURL(file);
                        } else {
                            $('label', newFileInput).append(
                                $('<small>').addClass('image-error')
                                .text(settings.warning_message)
                                .css({
                                    "font-size":"80%",
                                    "color":settings.warning_text_color,
                                    "display":"inline-block",
                                    "font-weight":"normal",
                                    "margin-left":"1em",
                                    "font-style":"italic"
                                })
                            );
                        }
                    }
                }

            });
        }

        jQuery(".fv_upload_form .file-input").imgPreview();
    })(jQuery);

}