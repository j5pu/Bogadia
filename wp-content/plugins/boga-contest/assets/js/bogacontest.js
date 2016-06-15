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

function new_photo(){
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

            // Comprobamos si hay foto principal
            var main = 0;
            if ( jQuery("#no_main_photo").length > 0){
                main = 1;
            }

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
                        main: main,
                        contestant_id: jQuery('#upload').data("contestantid")
                    }
                })
                .done(function(  ) {
                    progress_bar.move('90%');

                    // Colocamos la nueva imagen
                    if (main == 0){
                        var gallery = jQuery('#gallery');
                        var num_photos_last_row = gallery.find('div:last-child').find('div').length;
                        var num_photos = jQuery('.contestant-photo').length;
                        var str = '';

                        if (num_photos_last_row == 4) {
                            str = '<div class="row gallery-row" style="">';
                        }

                        str = str + '<div class="col-xs-6 col-sm-6 col-md-3" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
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
}

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

var login = {
    bind_events: function(){
        jQuery('#login_button').on('click', function(){
            jQuery('#bogacontest_up_login_action_after_login').val('log');
            jQuery('#bogacontest_login_modal').modal({show:true});
        });
        jQuery('#bogacontest_fb_login').on('click', function(){
            login.with_facebook();
        });
        jQuery('#bogacontest_up_login').on('click', function(){
            login.with_user_password();
        });
    },
    with_user_password: function (){
        jQuery.ajax({
            beforeSend: function(){
                jQuery('#bogacontest_up_login').html('<img class="image-responsive" style="margin: 0 auto;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif" style="width: 5%">');
            },
            type: 'POST',
            dataType: 'html',
            url: jQuery('#bogacontest_up_login').data('ajaxurl'),
            data: {
                'action': 'bogacontest_ajax_login',
                'username': jQuery('#bogacontest_up_login_username').val(),
                'password': jQuery('#bogacontest_up_login_password').val(),
                'security': jQuery('#bogacontest_up_login_security').val()
            }
        }).done(function(data){
            data = JSON.parse(data);
            if (data.loggedin == true){
                jQuery('#current-user-data-holder').data('currentuserid', data.user_id);
                login.action_after_login();
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
                            jQuery('#bogacontest_fb_login').html('<img class="image-responsive" style="margin: 0 auto;" src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif" style="width: 5%">');
                        },
                        type: 'POST',
                        dataType: 'html',
                        url: fbAjaxUrl,
                        data: {"action": "fb_intialize", "FB_userdata": FB_userdata, "FB_response": FB_response},
                    }).done(function(user){
                        user = JSON.parse(user);
                        if( user.error ) {
                        }else{
                            jQuery('#current-user-data-holder').data('currentuserid', user.user_id);
                            login.action_after_login();
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
    action_after_login: function(){
        var action = jQuery('#bogacontest_up_login_action_after_login').val();
        if (action == 'vote'){
            vote.new_vote();
        }
        if (action == 'participate'){
            new_contestant();
        }
        jQuery('#login_button').hide('slow');
        jQuery('#logout_button').show('slow');
        jQuery('#bogacontest_login_modal').modal('toggle');
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
                if(msg == '¡Genial! Tu voto ha sido contabilizado'){
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
    captureLengƒth: 0
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
                    console.log('preparando inscripción');
                },
                method: "POST",
                url: "/wp-content/plugins/boga-contest/new_contestant.php",
                data: {
                    user_id: user_id,
                    contest_id: jQuery('#participate').data("contestid")
                }
            })
            .done(function( msg ) {
                alert('exito ' + msg);
                if(msg != '0'){
                    var href = window.location.href;
                    var last_char = href.slice(-1);
                    if (!(last_char == '/')){
                        window.location = window.location.href + '/' + msg;
                    }else{
                        window.location = window.location.href + msg;
                    }
                }
            })
            .fail(function( msg ) {
                alert('fallo' + msg);
            })
        ;
    }
}

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
        new_contestant(jQuery(this));
    });
    jQuery('#upload_alias').on('click', function(){
        jQuery('#upload').click();
    });
    jQuery('#upload').on('change', function(){
        new_photo();
    });
    login.bind_events();
    toolbar.bind_events();
    gallery.init();
});