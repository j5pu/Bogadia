var grid = 0;
var contestant_grid = 0;
var first_time_main_photo_set = 0;
var delete_contestant_clicks = 0;

var gallery = {
    photos: [],
    init: function(){
        jQuery('.contestant-photo').each(function(){
            gallery.photos.push({scr: jQuery(this).attr('src')});
        });
        jQuery('#main').magnificPopup({
            delegate: '.main_photo_holder_link',
            gallery: {
                enabled: true
            },
            type: 'image' // this is default type
        });
    },
};

var progress_bar = {
    move: function (value){
        jQuery("#upload_progress_bar").css('width', value);
        jQuery("#upload_progress_bar_text").html(value);
    },
    show: function (){
        jQuery("#progress_bar_container").slideDown('fast');
    },
    hide: function (){
        jQuery("#progress_bar_container").slideUp('slow');
    }
};

var progress_gallery_bar = {
    move: function (value){
        jQuery("#upload_progress_gallery_bar").css('width', value);
        jQuery("#upload_progress_gallery_bar_text").html(value);
    },
    show: function (){
        jQuery("#progress_gallery_bar_container").slideDown('fast');
    },
    hide: function (){
        jQuery("#progress_gallery_bar_container").slideUp('slow');
    }
};

var vote = {
    voter_id: '',
    contestant_id: '',
    user_id: '',
    button: '',
    new_vote: function(){
        vote.voter_id = jQuery('#current-user-data-holder').data('currentuserid');
        if(vote.voter_id == '0') {
            jQuery('#bogacontest_login_modal').modal({show:true});
            jQuery('#bogacontest_up_login_action_after_login').val('vote');
        }else{
            jQuery.ajax({
                beforeSend: function(){
                    jQuery.Velocity.RunSequence([
                        {e: jQuery('#vote_text'), p:'fadeOut'},
                        {e: jQuery('#vote_loader'), p:'fadeIn'},
                    ]);
                    vote.button.html('<img src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="width: 12%">');
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/new_vote.php",
                data: {
                    voter_id: vote.voter_id,
                    contestant_id: vote.contestant_id,
                    user_id: vote.user_id
                }
            })
            .done(function( msg ) {
                vote.button.html(msg);
                if(msg == '¡Genial! Voto contado'){
                    var vote_div = jQuery('#votes-' + vote.contestant_id);
                    var votes = parseInt(vote_div.data('votes'));
                    vote_div.html((votes + 1) + ' votos');
                    ga('send', 'event', 'bogacontest', 'vote', 'click');
                    $mcGoal.processEvent('vote');
                    fbq('trackCustom', 'vote', {
                        voter_id: vote.voter_id,
                        contestant_id: vote.contestant_id,
                        value: 0.00,
                        currency: 'USD'
                    });
                }
            })
            .fail(function( msg ) {
                alert('fallo' + msg);
            });
        }
    }
};

var delete_contestant = {
    voter_id: '',
    contestant_id: '',
    user_id: '',
    button: '',
    new_vote: function(){
        delete_contestant.voter_id = jQuery('#current-user-data-holder').data('currentuserid');
        if(delete_contestant.voter_id == '0') {
            jQuery('#bogacontest_login_modal').modal({show:true});
            jQuery('#bogacontest_up_login_action_after_login').val('vote');
        }else{
            jQuery.ajax({
                beforeSend: function(){
                    jQuery.Velocity.RunSequence([
                        {e: jQuery('#vote_text'), p:'fadeOut'},
                        {e: jQuery('#vote_loader'), p:'fadeIn'},
                    ]);
                    delete_contestant.button.html('<img src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="width: 12%">');
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/delete_contestant.php",
                data: {
                    voter_id: delete_contestant.voter_id,
                    contestant_id: delete_contestant.contestant_id,
                    user_id: delete_contestant.user_id
                }
            })
            .done(function( msg ) {
                delete_contestant.button.html(msg);
            })
            .fail(function( msg ) {
                alert('fallo' + msg);
            });
        }
    }
};

var toolbar = {
    filter: '',
    search: '',
    offset: 0,
    exclude: [],
    typewatch_options: {
    callback: function ()
    {
        toolbar.search = jQuery("#search_query_input").val();
        toolbar.exclude = [];
        toolbar.offset = 0;
        toolbar.new_query();
        document.activeElement.blur();
        toolbar.hide_show_load_more();
    },
    wait: 1000,
    highlight: true,
    captureLength: 0
    },
    new_query: function(){
        jQuery.ajax({
                beforeSend: function(){
                    if (toolbar.exclude === undefined || toolbar.exclude.length == 0) {
                        jQuery.Velocity.RunSequence([
                            {e: jQuery('#contestants_container'), p: 'fadeOut'},
                            {e: jQuery('#toolbar_loader'), p: 'fadeIn'}
                        ]);
                    }else{
                        jQuery.Velocity.RunSequence([
                            {e: jQuery('#load_more_text'), p: 'fadeOut'},
                            {e: jQuery('#load_more_loader'), p: 'fadeIn'}
                        ]);
                    }
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/toolbar.php",
                data: {
                    filter: toolbar.filter,
                    search: toolbar.search,
                    exclude: toolbar.exclude,
                    offset: toolbar.offset,
                    slug: jQuery('#toolbar').data('slug'),
                    contest_id: jQuery('#participate').data("contestid")
                }
            })
            .done(function( msg ) {
                if (toolbar.exclude === undefined || toolbar.exclude.length == 0){
                    jQuery('#contestants_container').html(msg);
                    setTimeout(function()
                    {
                        jQuery.Velocity.RunSequence([
                            {e: jQuery('#toolbar_loader'), p:'fadeOut'},
                            {e: jQuery('#contestants_container'), p:'fadeIn'}
                        ]);
                        setTimeout(function()
                        {
                            grid.masonry('reloadItems');
                            grid.imagesLoaded().progress( function() {
                                grid.masonry('layout');
                            });
                            toolbar.hide_show_load_more();
                        }, 500);
                    }, 1000);
                }else{
                    var $data = jQuery(msg);
                    $data.velocity('fadeOut', { duration: 50 });
                    jQuery('#contestants_container').append($data);

                    $data.imagesLoaded().progress(function(imgLoad, image) {
                        var $item = jQuery( image.img ).parents('.grid-item');
                        $item.velocity('fadeIn');
                        setTimeout(function(){
                            grid.masonry('appended', $item);
                        }, 750);

                    }).done( function( instance ) {
                        toolbar.hide_show_load_more();
                        jQuery.Velocity.RunSequence([
                            {e: jQuery('#load_more_loader'), p: 'fadeOut'},
                            {e: jQuery('#load_more_text'), p: 'fadeIn'}
                        ]);
                    });
                    toolbar.exclude = [];
                }
            })
            .fail(function( msg ) {
                jQuery('#contestants_container').html('<h3>Ups! Inténtalo de nuevo.</h3>');
                setTimeout(function()
                {
                    jQuery.Velocity.RunSequence([
                        {e: jQuery('#toolbar_loader'), p:'fadeOut'},
                        {e: jQuery('#contestants_container'), p:'fadeIn'}
                    ]);
                }, 1000);
            });
    },
    bind_events: function(){
        jQuery("input[type=radio][name=optradio]").on('click', function(){
            toolbar.filter = jQuery("input[type=radio][name=optradio]:checked").val();
            toolbar.exclude = [];
            toolbar.offset = 0;
            toolbar.new_query();
        });
        jQuery("#load_more").on('click', function(){
            jQuery('.mini_image').each(function(){
                toolbar.exclude.push(jQuery(this).data('contestant_id'));
            });
            toolbar.offset += 1;
            toolbar.filter = jQuery("input[type=radio][name=optradio]:checked").val();

            toolbar.new_query();
        });
        jQuery("#search_query_input").typeWatch( toolbar.typewatch_options );
        jQuery("#search_query_input").on( 'change', function(){
            jQuery('#contestants_container').html('<img class="image-responsive" src="/wp-content/plugins/boga-contest/assets/img/BoganimationN2.gif" style="width: 15%; margin: 0 auto;">');
        } );
        toolbar.hide_show_load_more();
    },
    hide_show_load_more: function(){
        var total_contestants = jQuery('#toolbar_counter_number').data('total_contestant');
        var current_contestants = jQuery('.mini_image').length;
        var load_button = jQuery('#load_more');

        if (total_contestants > current_contestants){
            if (load_button.css('display') == 'none') {
                load_button.velocity('fadeIn');
            }
        }else{
            if (load_button.css('display') != 'none') {
                load_button.velocity('fadeOut');
            }
        }
    }
};

var photo_manager = {
    selected_photo: '',
    main: 0,
    upload_to_main: 0,
    check_main: function(){
        if ( jQuery("#no_main_photo").length > 0){
            photo_manager.main = 1;
        }else{
            photo_manager.main = 0;
        }
    },
    bind_events: function()
    {
        jQuery('#delete').on('click', function(){
            jQuery('#bogacontest_manager_modal').modal('toggle');
        });
        jQuery('#delete_selected_photo').on('click', function(){
            photo_manager.selected_photo = jQuery("input[type=radio][name=photo_to_edit]:checked").val();
            photo_manager.delete();
        });

        jQuery('#upload_alias').on('click', function(){
            photo_manager.upload_to_main = 0;
            upload_button_clicked = 1;
            jQuery('#upload').val(null);
            jQuery('#upload').click();
        });
        jQuery('#upload_main_alias').on('click', function(){
            photo_manager.upload_to_main = 1;
            jQuery('#upload').val(null);
            jQuery('#upload').click();
        });
        jQuery('#upload').on('change', function(){
            photo_manager.upload(photo_manager.upload_to_main);
        });
    },
    upload: function(upload_to_main)
    {
        var selected_progress_bar = "";

        if (upload_to_main == 1)
        {
            selected_progress_bar = progress_bar;
        }else
        {
            selected_progress_bar = progress_gallery_bar;
        }

        var nonce = jQuery('#upload').data('nonce');
        var formData = new FormData();
        var fileInputElement = document.getElementById("upload");
        var xhr = new XMLHttpRequest();
        var ori = 0;

        selected_progress_bar.show();
        selected_progress_bar.move('10%');

        loadImage.parseMetaData(fileInputElement.files[0], function(data) {
            if (data.exif) {
                ori = data.exif.get('Orientation');
            }
            var loaded_info = loadImage(
                fileInputElement.files[0],
                function (img) {
                    selected_progress_bar.move('20%');
                    var context = img.getContext("2d");

/*                    var logo = new Image();
                    logo.onload = function () {*/
/*
                    context.drawImage(logo, 10, 10, 200, 151.06);
*/


                    var image_resize = img.toDataURL('image/jpeg', 0.9);

                    formData.append("action", "upload-attachment");
                    formData.append("async-upload", image_resize);
                    formData.append("name", fileInputElement.files[0].name);
                    formData.append("_wpnonce", nonce);
                    formData.append("main", upload_to_main);
                    formData.append("contestant_id", jQuery('#upload').data("contestantid"));
                    formData.append("contest_slug", jQuery('#toolbar').data('slug'));
                    progress_bar.move('30%');

                    xhr.onreadystatechange=function()
                    {
                        if (xhr.readyState==1){
                            progress_bar.move('40%');
                        }
                        if (xhr.readyState==2){
                            progress_bar.move('50%');
                        }
                        if (xhr.readyState==3){
                            progress_bar.move('60%');
                        }
                        if (xhr.readyState==4 && xhr.status==200)
                        {
                            var response = jQuery.parseJSON(xhr.responseText);
                            var image_url = response.url;
                            var post_id = response.id;
                            var upload_button = jQuery('#upload_alias');


                            selected_progress_bar.move('70%');

                            // Colocamos la nueva imagen en la galeria
                            if (upload_to_main == 1)
                            {
                                // si la foto se sube como principal
                                var main_photo = jQuery("#main_photo");
                                var old_photo = main_photo.attr('src');
                                main_photo.attr('src', image_url);
                                jQuery('#main_photo_link').attr('href', image_url);
                                main_photo.attr('id', 'main_photo');
                                main_photo.parent().attr('id', 'gallery_image_container_' + post_id);
                                main_photo.parent().css('visibility', 'visible');
                                var main_upload_button = jQuery('#upload_main_alias');

                                if (main_upload_button.hasClass('btn-primary'))
                                {
                                    main_upload_button.html('Cambia tu foto principal');
                                    main_upload_button.removeClass('btn-primary');
                                    main_upload_button.addClass('btn-default');
                                }
                                jQuery('meta[property="og:image"]').attr(image_url);
                                jQuery('meta[property="twitter:image"]').attr(image_url);

                                if(!main_photo.hasClass('fake_main_photo')){
                                    image_url = old_photo;
                                    ga('send', 'event', 'bogacontest', 'contestant_change_main_photo', 'upload_photo');
                                }else{
                                    image_url = 0;
                                    main_photo.removeClass('fake_main_photo');
                                    if (upload_button.hasClass('btn-default'))
                                    {
                                        upload_button.removeClass('btn-default ');
                                        upload_button.addClass('btn-primary');
                                    }
                                    first_time_main_photo_set = 1;
                                    ga('send', 'event', 'bogacontest', 'new_contestant_upload_main_photo', 'upload_photo');
                                    $mcGoal.processEvent('new_contestant_upload_main_photo');
                                    fbq('track', 'CompleteRegistration', {
                                        content_name: 'new_contestant',
                                        status: 'main_photo_uploaded',
                                        value: 0.00,
                                        currency: 'USD'
                                    });
                                }
                            }
                            if (image_url != 0){
                                jQuery('#fake_photo_1, #fake_photo_2, #fake_photo_3, #fake_photo_4').hide('slow');
                                // Colocamos la nueva foto o la antigua foto principal en la galeria
                                var gallery = jQuery('#gallery');

                                var num_photos = jQuery('.contestant-photo').length;
                                var str = '';

                                str = str + '<div id="gallery_image_container_' + post_id + '" class="col-xs-4 col-sm-4 col-md-4 col-lg-3" style="padding: 1px !important; height: 100px; overflow-y: hidden;">';
                                str = str + '<a class="main_photo_holder_link"  href="' + image_url + '">';
                                str = str + '<img id="contestant-' + (num_photos + 1) + '" class="img-responsive contestant-photo" src="' + image_url + '" >';
                                str = str + '</a>';
                                str = str + '</div>';

                                gallery.prepend(str);

                                progress_bar.move('90%');

                                // Colocamos la nueva foto o la antigua foto principal en el photo manager
                                var photo_manager = jQuery('#photo_manager_select');

                                var num_photos = jQuery('.manager_photo').length;
                                var str = '';


                                str = str + '<div id="manager_image_container_' + post_id + '" class="col-xs-4 col-sm-4 col-md-4" style="margin-bottom: 15px;">';
                                str = str + '<label class="manager_photo" >';
                                str = str + '<input type="radio" name="photo_to_edit" value="' + post_id + '" />';
                                str = str + '<img id="manager-contestant-' + (num_photos + 1) + '" class="img-responsive contestant-photo" src="' + image_url + '" >';
                                str = str + '</label>';
                                str = str + '</div>';

                                photo_manager.prepend(str);

                                if (upload_button.hasClass('btn-primary') && upload_button_clicked == 1)
                                {
                                    var preview_button = jQuery('#edit');
                                    upload_button.removeClass('btn-primary');
                                    upload_button.addClass('btn-default');
                                    preview_button.removeClass('btn-default ');
                                    preview_button.addClass('btn-primary');
                                }

                            }

                            selected_progress_bar.move('100%');

                            setTimeout(function()
                            {
                                selected_progress_bar.hide();
                            }, 1000);
                            setTimeout(function()
                            {
                                selected_progress_bar.move('0%');
                            }, 1500);
                        }
                    };
                    xhr.open("POST","/wp-content/plugins/boga-contest/new_photo.php",true);
                    xhr.send(formData);
                    selected_progress_bar.move('20%');
/*
                    }
*/
/*
                    logo.src = "/wp-content/plugins/boga-contest/assets/img/logo_tnsprnte-min.png";
*/
                },
                {
                    maxWidth: 900,
                    maxHeight: 450,
                    minWidth: 300,
                    minHeight: 150,
                    canvas: true,
                    orientation: ori,
                }
            );
        });


    },
    delete: function (){
        jQuery.ajax({
                beforeSend: function(){
                    console.log('borrando foto');
                    jQuery('#delete_selected_photo').html('<img class="img-responsive" style="margin: 0 auto; width: 50px !important;" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif">');
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/delete_photo.php",
                data: {
                    post_id: photo_manager.selected_photo,
                    contestant_id: jQuery('#upload').data("contestantid")
                }
            })
            .done(function( response ) {
                jQuery('#delete_selected_photo').html(response);
                if (response == 'Foto borrada con éxito'){
                    jQuery('#gallery_image_container_' + photo_manager.selected_photo).hide('slow');
                    jQuery('#manager_image_container_' + photo_manager.selected_photo).velocity('slow');
                    jQuery('#manager_image_container_' + photo_manager.selected_photo).remove();
                    jQuery('#bogacontest_manager_modal').on('hidden.bs.modal', function () {
                        jQuery('#delete_selected_photo').html('Borrar foto');
                    });
                }
            })
            .fail(function( msg ) {
                alert('fallo' + msg);
            })
        ;
    }
};

function new_contestant(){
    var user_id = jQuery('#current-user-data-holder').data('currentuserid');
    if(user_id == '0') {
        jQuery('#bogacontest_login_modal').modal({show:true});
        jQuery('#bogacontest_up_login_action_after_login').val('participate');
    }else{
        jQuery.ajax({
                beforeSend: function(){
                    jQuery.Velocity.RunSequence([
                        {e: jQuery('#participate_text'), p:'fadeOut'},
                        {e: jQuery('#participate_loader'), p:'fadeIn'},
                    ]);
                    jQuery('#login_succes_loader').velocity('fadeIn');

                    jQuery('#form_wrapper').html('<div class="text-center"><h3 style="color: red;">Un momento por favor, estamos comprobando tus datos...</h3></div>');
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/new_contestant.php",
                data: {
                    user_id: user_id,
                    contest_id: jQuery('#participate').data("contestid"),
                    contest_slug: jQuery('#toolbar').data('slug')
                }
            })
            .done(function( msg ) {
                msg = JSON.parse(msg);
                jQuery('#form_wrapper').html('<div class="text-center" style="color: grey;"><h3 style="color: red;">' + msg.message + '</h3></div>');
                jQuery('#bogacontest_up_login_action_after_login').val('redirect');
                login.action_after_login(msg);
            })
            .fail(function( msg ) {
                jQuery('#form_wrapper').html('<div class="text-center" style="color: grey;"><h3 style="color: red;">' + 'Upps! Ha ocurrido un error. Vuelve a intentarlo por favor.' + '</h3></div>');
            })
        ;
    }
}

var login = {
    recapcha: '',
    validate_email: function(emailAddress) {
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        return pattern.test(emailAddress);
    },
    bind_events: function(){
        jQuery('#login_button').on('click', function(){
            jQuery('#bogacontest_up_login_action_after_login').val('redirect');
            jQuery('#bogacontest_login_modal').modal({show:true});
        });
        jQuery('#bogacontest_fb_login').on('click', function(){
            login.with_facebook();
        });
        jQuery('#bogacontest_login_body').on('submit', '#register_form_form', function(e)
        {
            login.recapcha = grecaptcha.getResponse();
            if( jQuery('#bogacontest_up_login_username').val().length > 0 && login.recapcha.length > 0) {
                login.register_with_user_password();
            }
            e.preventDefault();
        });
        jQuery('#go_back').on('click', function(){
            jQuery.Velocity.RunSequence([
                {e: jQuery('#second_form'), p:'transition.slideRightBigOut'},
                {e: jQuery('#first_form'), p:'transition.slideLeftBigIn'}
            ]);
        });
        jQuery('#login_form_form').on('submit', function(e){
            if( !login.validate_email( jQuery('#bogacontest_up_login_email').val()) ) {
                jQuery('#email_validate_text').velocity('fadeIn');
            }else{
                jQuery('#email_validate_text').velocity('fadeOut');
                login.login_with_user_password(1);
            }
            e.preventDefault();
        });
        jQuery('#bogacontest_up_login_2').on('click', function(e){
            if( !login.validate_email( jQuery('#bogacontest_up_login_email').val()) ) {
                jQuery('#email_validate_text').velocity('fadeIn');
            }else{
                jQuery('#email_validate_text').velocity('fadeOut');
                login.login_with_user_password(0);
            }
            e.preventDefault();
        });
    },
    login_with_user_password: function (continue_to_register){
        jQuery.ajax({
            beforeSend: function(){
                if (continue_to_register == 1){
                    jQuery.Velocity.RunSequence([
                        {e: jQuery('#login_text'), p:'fadeOut'},
                        {e: jQuery('#login_loader'), p:'fadeIn'}
                    ]);
                }else{
                    jQuery.Velocity.RunSequence([
                        {e: jQuery('#login_2_text'), p:'fadeOut'},
                        {e: jQuery('#login_loader_2'), p:'fadeIn'}
                    ]);
                }
            },
            type: 'POST',
            dataType: 'html',
            url: jQuery('#bogacontest_up_login').data('ajaxurl'),
            data: {
                'action': 'bogacontest_ajax_login',
                'email': jQuery('#bogacontest_up_login_email').val(),
                'password': jQuery('#bogacontest_up_login_password').val(),
                'security': jQuery('#bogacontest_up_login_security').val(),
                'contest_id': jQuery('#participate').data("contestid"),
                'continue_to_register': continue_to_register
            }
        }).done(function(data){
            data = JSON.parse(data);
            if (continue_to_register == 1){
                jQuery.Velocity.RunSequence([
                    {e: jQuery('#login_loader'), p:'fadeOut'},
                    {e: jQuery('#login_text'), p:'fadeIn'}
                ]);
            }else{
                jQuery.Velocity.RunSequence([
                    {e: jQuery('#login_loader_2'), p:'fadeOut'},
                    {e: jQuery('#login_2_text'), p:'fadeIn'}
                ]);
            }

            jQuery('#email_validate_text').html(data.message);
            jQuery('#register_help_text').html(data.message);

            if (data.case == 0 && continue_to_register==1)
            {
                // Continuar con registro
                jQuery.Velocity.RunSequence([
                    {e: jQuery('#first_form'), p:'transition.slideLeftBigOut'},
                    {e: jQuery('#second_form'), p:'transition.slideRightBigIn'}
                ]);
                jQuery('#bogacontest_up_login_username').focus();
            }

            if (data.loggedin == true)
            {
                jQuery('#current-user-data-holder').data('currentuserid', data.user_id);
                login.action_after_login(data);
            }
        });
    },
    register_with_user_password: function (){
        jQuery.ajax({
            beforeSend: function(){
                jQuery.Velocity.RunSequence([
                    {e: jQuery('#register_text'), p:'fadeOut'},
                    {e: jQuery('#register_loader'), p:'fadeIn'}
                ]);
            },
            type: 'POST',
            dataType: 'html',
            url: jQuery('#bogacontest_up_login').data('ajaxurl'),
            data: {
                'action': 'bogacontest_ajax_register',
                'username': jQuery('#bogacontest_up_login_username').val(),
                'email': jQuery('#bogacontest_up_login_email').val(),
                'password': jQuery('#bogacontest_up_login_password').val(),
                'security': jQuery('#bogacontest_up_register_security').val(),
                'g-recaptcha-response': login.recapcha
            }
        }).done(function(data){
            data = JSON.parse(data);
            jQuery('#register_help_text').html(data.message);
            jQuery.Velocity.RunSequence([
                {e: jQuery('#register_loader'), p:'fadeOut'},
                {e: jQuery('#register_text'), p:'fadeIn'}
            ]);
            if (data.loggedin == true){
                jQuery('#current-user-data-holder').data('currentuserid', data.user_id);
                login.action_after_login(data);
            }
        });
    },
    with_facebook: function(){
        function fb_intialize_share(FB_response, token){
            FB.api( '/me', 'GET', {
                    fields : 'id,email,verified,name',
                    access_token : token
                },
                function(FB_userdata){
                    jQuery.ajax({
                        beforeSend: function(){
                            jQuery('#bogacontest_fb_login').html('<img class="image-responsive" style="margin: 0 auto; width: 25px !important;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif">');
                        },
                        type: 'POST',
                        dataType: 'html',
                        url: fbAjaxUrl,
                        data: {"action": "fb_intialize", "FB_userdata": FB_userdata, "FB_response": FB_response}
                    }).done(function(user){
                        user = JSON.parse(user);
                        if( user.error ) {
                            jQuery('#bogacontest_fb_login').html(user.error);
                        }else{
                            jQuery('#current-user-data-holder').data('currentuserid', user.user_id);
                            jQuery('#bogacontest_fb_login').html(user.message);
                            login.action_after_login(user);
                        }
                    }).fail(function(user){

                    })

                    ;
                }
            );
        }

        if (navigator.userAgent.match('CriOS')) {
            window.open('https://www.facebook.com/dialog/oauth?client_id=' + jQuery('#compartir_opinion').data('appid') + '&redirect_uri=' + document.location.href + '&scope=email,public_profile,publish_actions&response_type=token', '', null);
            jQuery("#fb-root").bind("facebook:init", function () {
                var accToken = jQuery.getUrlVar('#access_token');
                if (accToken) {
                    var fbArr = {scopes: "email,public_profile,publish_actions"};
                    return fb_intialize_share(fbArr, accToken);
                }
            });
        } else {
            FB.login(function (FB_response) {
                    if (FB_response.authResponse) {
                        return fb_intialize_share(FB_response, '');
                    }
                },
                {
                    scope: 'email,public_profile',
                    auth_type: 'rerequest',
                    return_scopes: true
                });
        }
    },
    action_after_login: function(data){
        var action = jQuery('#bogacontest_up_login_action_after_login').val();
        if (action == 'vote'){
            vote.new_vote();
            jQuery('#bogacontest_login_modal').modal('toggle');
        }
        else if (action == 'participate'){
            new_contestant();
        } else if(action == 'redirect') {
            if (data.contestant_id){
                if (data.new == 1){
                    ga('send', 'event', 'bogacontest', 'new_contestant_register', 'register');
                    window.location = '/concursos/' + jQuery('#toolbar').data('slug') + '/' + data.contestant_id + '?edit=true&new=true';
                }else{
                    ga('send', 'event', 'bogacontest', 'contestant_editting', 'login');
                    window.location = '/concursos/' + jQuery('#toolbar').data('slug') + '/' + data.contestant_id + '?edit=true';
                }
            }
        }
    }
};

function bogacontest_bind_share_buttons(){
    jQuery('#bogacontest_whatsapp').on('click', function(){
        ga('send', 'event', 'bogacontest', 'click_whatsapp', 'share');
    });
    jQuery('#bogacontest_facebook').on('click', function(){
        ga('send', 'event', 'bogacontest', 'click_facebook', 'share');
    });
    jQuery('#bogacontest_twitter').on('click', function(){
        ga('send', 'event', 'bogacontest', 'click_twitter', 'share');
    });
    jQuery('#bogacontest_pinterest').on('click', function(){
        ga('send', 'event', 'bogacontest', 'click_pinterest', 'share');
    });
}

jQuery(document).ready(function()
{
    jQuery('.vote').on('click', function(){
        vote.contestant_id = jQuery(this).data('id');
        vote.user_id = jQuery(this).data("contestantuserid");
        vote.voter_id = jQuery('#current-user-data-holder').data('currentuserid');
        vote.button = jQuery(this);
        vote.new_vote();
    });
    jQuery('#delete_contestant').on('click', function(){
        delete_contestant_clicks ++;
        if (delete_contestant_clicks == 2){
            delete_contestant.contestant_id = jQuery(this).data('id');
            delete_contestant.user_id = jQuery(this).data("contestantuserid");
            delete_contestant.voter_id = jQuery('#current-user-data-holder').data('currentuserid');
            delete_contestant.button = jQuery(this);
            delete_contestant.new_vote();
        }else{
            jQuery(this).html('Pulsa de nuevo para confirmar');
        }
    });
    jQuery('#back_to_edit').on('click', function(){
        window.location = '/concursos/' + jQuery('#toolbar').data('slug') + '/' + jQuery(this).data('nicename') + '?edit=true';
    });
    jQuery('#participate, #participate_menu').on('click', function(e){
        new_contestant();
        e.preventDefault();
    });
    bogacontest_bind_share_buttons();
    login.bind_events();
    toolbar.bind_events();
    photo_manager.bind_events();
    gallery.init();
    jQuery('#bogacontest_login_modal').on('show.bs.modal', function(){
        jQuery.magnificPopup.close();
        var modal = jQuery('.modal-dialog');
        modal.velocity('transition.flipYIn');
        modal.velocity("scroll", { duration: 1000, easing: "spring" })
    });

    jQuery('#edit').on('click', function(){
        if(first_time_main_photo_set == 1){
            ga('send', 'event', 'bogacontest', 'new_contestant_finish', 'click');
            window.location = '/concursos/' + jQuery('#toolbar').data('slug') + '/' + jQuery(this).data('nicename') + '/?status=complete';
        }else{
            ga('send', 'event', 'bogacontest', 'contestant_edit_finish', 'click');
            window.location = '/concursos/' + jQuery('#toolbar').data('slug') + '/' + jQuery(this).data('nicename');
        }
    });

    contestant_grid = jQuery('.contestant_grid').masonry({
        itemSelector: '.contestant_grid-item',
        columnWidth: '.contestant_grid-sizer',
        percentPosition: true
    });
    contestant_grid.imagesLoaded().progress( function() {
        contestant_grid.masonry('layout');
    });

    grid = jQuery('.grid').masonry({
        itemSelector: '.grid-item',
        columnWidth: '.grid-sizer',
        percentPosition: true
    });
    grid.imagesLoaded().progress( function(imgLoad, image) {
        var $item = jQuery( image.img ).parents('.grid-item');
        $item.velocity('fadeIn');
        setTimeout(function(){
            grid.masonry('layout');
        }, 500);
    });
/*
    jQuery(window).scroll(function(){
        jQuery("#interaction_buttons_wrapper").css("top",Math.max(0,2-jQuery(this).scrollTop()));
    });*/
/*    var interaction_buttons = jQuery('#interaction_buttons_wrapper');
    var end_image = jQuery('#image_bottom').offset().top;

    jQuery(window).scroll(function() {
        var currentScroll = jQuery(window).scrollTop();

        if (currentScroll > end_image){
/!*            jQuery('#interaction_buttons_wrapper').css({
                position: 'fixed',
            });*!/
/!*            jQuery('#interaction_buttons').css({
                position: 'static',
            });*!/
            if (interaction_buttons.css('display') != 'none') {
                interaction_buttons.velocity('fadeOut');
            }

        }
        if (currentScroll < (end_image - 25)){
/!*            jQuery('#interaction_buttons_wrapper').css({
                position: 'static'
            });
            jQuery('#interaction_buttons').css({
                position: 'fixed',

            });*!/
            if (interaction_buttons.css('display') == 'none'){
                interaction_buttons.velocity('fadeIn');
            }

        }
    });*/

});