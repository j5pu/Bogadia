var gallery = {
    photos: [],
    init: function(){
        jQuery('.contestant-photo').each(function(){
            gallery.photos.push({scr: jQuery(this).attr('src')});
        });
        jQuery('#main_photo_holder').magnificPopup({
            delegate: 'a',
            gallery: {
                enabled: true
            },
            type: 'image' // this is default type
        });
    },
};

function change_main_photo() {
    jQuery.ajax({
            beforeSend: function(){
                console.log('preparando inscripción');
            },
            method: "POST",
            url: "/wp-content/plugins/boga-contest/new_contestant.php",
            data: {
                main: user_id,
                contest_id: jQuery('#participate').data("contestid")
            }
        })
        .done(function( msg ) {
            alert('exito ' + msg);
            if(msg != '0'){
                window.location = window.location.href + msg;
            }
        })
        .fail(function( msg ) {
            alert('fallo' + msg);
        })
    ;
}

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
                    vote.button.html('<img src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif" style="width: 4%">');
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
                }
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
    typewatch_options: {
    callback: function ()
    {
        toolbar.search = jQuery("#search_query_input").val();
        toolbar.new_query();
    },
    wait: 1000,
    highlight: true,
    captureLength: 0
    },
    new_query: function(){
        jQuery.ajax({
                beforeSend: function(){
                    jQuery('#contestants_container').html('<img class="image-responsive" style="margin: 0 auto;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif" style="width: 15%">');
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/toolbar.php",
                data: {
                    filter: toolbar.filter,
                    search: toolbar.search,
                    slug: jQuery('#toolbar').data('slug')
                }
            })
            .done(function( msg ) {
                jQuery('#contestants_container').html(msg);
            })
            .fail(function( msg ) {
                alert('fallo' + msg);
            });
    },
    bind_events: function(){
        jQuery("input[type=radio][name=optradio]").on('click', function(){
            toolbar.filter = jQuery("input[type=radio][name=optradio]:checked").val();
            toolbar.new_query();
        });
        jQuery("#search_query_input").typeWatch( toolbar.typewatch_options );
        jQuery("#search_query_input").on( 'change', function(){
            jQuery('#contestants_container').html('<img class="image-responsive" style="margin: 0 auto;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif" style="width: 15%">');
        } );
    }
};

var photo_manager = {
    selected_photo: '',
    main: 0,
    check_main: function(){
        if ( jQuery("#no_main_photo").length > 0){
            photo_manager.main = 1;
        }else{
            photo_manager.main = 0;
        }
    },
    bind_events: function(){
        jQuery('#delete').on('click', function(){
            jQuery('#bogacontest_manager_modal').modal('toggle');
        });
        jQuery('#delete_selected_photo').on('click', function(){
            photo_manager.selected_photo = jQuery("input[type=radio][name=photo_to_edit]:checked").val();
            photo_manager.delete();
        });
/*        jQuery("input[type=radio][name=photo_to_edit]").on('click', function(){
            jQuery(this).parent().parent().css('border', '2px solid black');
            jQuery(this).parent().parent().css('-webkit-box-shadow', '0px 4px 18px -5px rgba(0,0,0,0.75)');
            jQuery(this).parent().parent().css('--moz-box-shadow', '0px 4px 18px -5px rgba(0,0,0,0.75)');
            jQuery(this).parent().parent().css('box-shadow', '0px 4px 18px -5px rgba(0,0,0,0.75)');
        });*/
        jQuery('#upload_alias').on('click', function(){
            jQuery('#upload').click();
        });
        jQuery('#upload').on('change', function(){
            photo_manager.upload();
        });
    },

    upload: function(){
        var nonce = jQuery('#upload').data('nonce');
        var formData = new FormData();
        var fileInputElement = document.getElementById("upload");
        var xhr = new XMLHttpRequest();

        progress_bar.show();
        progress_bar.move('10%');

        formData.append("action", "upload-attachment");
        formData.append("async-upload", fileInputElement.files[0]);
        formData.append("name", fileInputElement.files[0].name);
        formData.append("_wpnonce", nonce);

        xhr.onreadystatechange=function(){
            if (xhr.readyState==1){
                progress_bar.move('30%');
            }
            if (xhr.readyState==2){
                progress_bar.move('40%');
            }
            if (xhr.readyState==3){
                progress_bar.move('50%');
            }
            if (xhr.readyState==4 && xhr.status==200){
                var response = jQuery.parseJSON(xhr.responseText);
                var image_url = response.data.url;
                var post_id = response.data.id;

                photo_manager.check_main();
                var there_is_main = photo_manager.main;

                progress_bar.move('60%');

                jQuery.ajax({
                        beforeSend: function(){
                            console.log('preparando foto');
                            progress_bar.move('60%');
                        },
                        method: "POST",
                        url: "/wp-content/plugins/boga-contest/new_photo.php",
                        data: {
                            path: image_url,
                            main: there_is_main,
                            contestant_id: jQuery('#upload').data("contestantid"),
                            post_id: post_id
                        }
                    })
                    .done(function(  ) {
                        progress_bar.move('90%');
                        // Colocamos la nueva imagen en la galeria
                        if (there_is_main == 0){
                            var gallery = jQuery('#gallery');
                            var num_photos_last_row = gallery.find('div:last-child').find('div').length;
                            var num_photos = jQuery('.contestant-photo').length;
                            var str = '';

                            if (num_photos_last_row == 4) {
                                str = '<div class="row gallery-row" style="">';
                            }

                            str = str + '<div id="gallery_image_container_' + post_id + '" class="col-xs-6 col-sm-6 col-md-3" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
                            str = str + '<a id="main_photo_holder" href="' + image_url + '">';
                            str = str + '<img id="contestant-' + (num_photos + 1) + '" class="img-responsive contestant-photo" src="' + image_url + '" >';
                            str = str + '</a>';
                            str = str + '</div>';

                            if (num_photos_last_row == 4) {
                                str = str + '</div>';
                                gallery.append(str);
                            }else{
                                gallery.find('div:last-child').append(str);
                            }
                        }else{
                            var main_photo = jQuery("#no_main_photo");
                            main_photo.attr('src', image_url);
                            main_photo.attr('id', 'main_photo');
                            main_photo.parent().attr('id', 'gallery_image_container_' + post_id);
                            main_photo.parent().css('visibility', 'visible');
                        }

                        // Colocamos la foto subida en el photo manager
                        var photo_manager = jQuery('#photo_manager_select');
                        var num_photos_last_row = photo_manager.find('div:last-child').find('div').length;
                        var num_photos = jQuery('.manager_photo').length;
                        var str = '';

                        if (num_photos_last_row == 3) {
                            str = '<div class="row gallery-row" style="">';
                        }

                        str = str + '<div id="manager_image_container_' + post_id + '" class="col-xs-4 col-sm-4 col-md-4" style="height: 100px; overflow: hidden;margin-bottom: 15px;">';
                        str = str + '<label class="manager_photo" >';
                        str = str + '<input type="radio" name="photo_to_edit" value="' + post_id + '" />';
                        str = str + '<img id="manager-contestant-' + (num_photos + 1) + '" class="img-responsive contestant-photo" src="' + image_url + '" >';
                        str = str + '</label>';
                        str = str + '</div>';

                        if (num_photos_last_row == 3) {
                            str = str + '</div>';
                            photo_manager.append(str);
                        }else{
                            photo_manager.append(str);
                        }

                        progress_bar.move('100%');

                        setTimeout(function(){
                            progress_bar.hide();
                        }, 1000);

                    })
                    .fail(function( msg ) {
                        alert('fallo' + msg);
                    })
                ;
            }
        };
        xhr.open("POST","/wp-admin/async-upload.php",true);
        xhr.send(formData);
        progress_bar.move('20%');
    },
    delete: function (){
        jQuery.ajax({
                beforeSend: function(){
                    console.log('borrando foto');
                    jQuery('#delete_selected_photo').html('<img class="img-responsive" style="margin: 0 auto; width: 25px !important;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif">');
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
                    if (jQuery('#gallery_image_container_' + photo_manager.selected_photo).data('main') == '1'){
                        jQuery('#gallery_image_container_' + photo_manager.selected_photo).css('visibility', 'hidden');
                        var main_photo = jQuery("#main_photo");
                        main_photo.attr('id', 'no_main_photo');
                    }else{
                        jQuery('#gallery_image_container_' + photo_manager.selected_photo).hide('slow');
                    }
                    jQuery('#manager_image_container_' + photo_manager.selected_photo).fadeOut('slow');
                    jQuery('#manager_image_container_' + photo_manager.selected_photo).remove();
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
                    change_text_animate('#bogacontest_login_body', '<div class="text-center"><h3>Te estamos inscribiendo en el concurso</h3><img class="image-responsive" style="margin: 0 auto; width: 25px !important;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif"></div>');
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/new_contestant.php",
                data: {
                    user_id: user_id,
                    contest_id: jQuery('#participate').data("contestid")
                }
            })
            .done(function( msg ) {
                msg = JSON.parse(msg);
                change_text_animate('#bogacontest_login_body', '<div class="text-center"><h3>' + msg.message + '</h3><img class="image-responsive" style="margin: 0 auto; width: 25px !important;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif"></div>');
                jQuery('#bogacontest_up_login_action_after_login').val('redirect');
                login.action_after_login(msg);

            })
            .fail(function( msg ) {
                alert('fallo' + msg);
            })
        ;
    }
}

function change_text_animate(id, text) {
    jQuery(id).fadeOut(500).html(text).fadeIn(500);
}

var login = {
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
        jQuery('#bogacontest_up_login').on('click', function(){
            if( !login.validate_email( jQuery('#bogacontest_up_login_email').val()) ) {
                jQuery('#email_validate_text').show('slow');
            }else{
                jQuery('#email_validate_text').hide('slow');
                login.login_with_user_password();
            }
        });
        jQuery('#bogacontest_login_body').on('click', '#bogacontest_up_register', function(){

            login.register_with_user_password();
        });
        jQuery('#go_back').on('click', function(){
            jQuery('#second_form').hide('slow');
            jQuery('#first_form').delay(500).show('slow');
        });
    },
    login_with_user_password: function (){
        jQuery.ajax({
            beforeSend: function(){
                jQuery('#bogacontest_up_login').html('<img class="img-responsive" style="margin: 0 auto; width: 25px !important;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif">');
            },
            type: 'POST',
            dataType: 'html',
            url: jQuery('#bogacontest_up_login').data('ajaxurl'),
            data: {
                'action': 'bogacontest_ajax_login',
                'email': jQuery('#bogacontest_up_login_email').val(),
                'password': jQuery('#bogacontest_up_login_password').val(),
                'security': jQuery('#bogacontest_up_login_security').val(),
                'contest_id': jQuery('#participate').data("contestid")
            }
        }).done(function(data){
            data = JSON.parse(data);
            jQuery('#bogacontest_up_login').html('Entrar');
            if (data.loggedin == true){
                jQuery('#current-user-data-holder').data('currentuserid', data.user_id);
                login.action_after_login(data);
            }else{
                jQuery('#first_form').hide('slow');
                jQuery('#second_form').delay(500).show('slow');
            }
        });
    },
    register_with_user_password: function (){
        jQuery.ajax({
            beforeSend: function(){
                jQuery('#bogacontest_up_register').html('<img class="image-responsive" style="margin: 0 auto; width: 25px !important;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif">');
            },
            type: 'POST',
            dataType: 'html',
            url: jQuery('#bogacontest_up_login').data('ajaxurl'),
            data: {
                'action': 'bogacontest_ajax_register',
                'username': jQuery('#bogacontest_up_login_username').val(),
                'email': jQuery('#bogacontest_up_login_email').val(),
                'password': jQuery('#bogacontest_up_login_password').val(),
                'security': jQuery('#bogacontest_up_register_security').val()
            }
        }).done(function(data){
            data = JSON.parse(data);
            jQuery('#register_help_text').html(data.message);
            jQuery('#bogacontest_up_register').html('Registrarme');
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
                var href = window.location.href;
                var last_char = href.slice(-1);
                if (!(last_char == '/')){
                    window.location = window.location.href + '/' + data.contestant_id;
                }else{
                    window.location = window.location.href + data.contestant_id;
                }
            }
        }
        jQuery('#login_button').hide('slow');
        jQuery('#logout_button').show('slow');
    }
};

jQuery(document).ready(function()
{
    jQuery('.vote').on('click', function(){
        vote.contestant_id = jQuery(this).data('contestantuserid');
        vote.user_id = jQuery(this).data("id");
        vote.voter_id = jQuery('#current-user-data-holder').data('currentuserid');
        vote.button = jQuery(this);
        vote.new_vote();
    });
    jQuery('#participate').on('click', function(){
        new_contestant();
    });

    login.bind_events();
    toolbar.bind_events();
    photo_manager.bind_events();
    gallery.init();
});