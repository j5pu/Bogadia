var galeria = [];

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

function new_vote(contestant_id){
    var voter_id = jQuery('#current-user-data-holder').data('currentuserid');
    if(voter_id == '0') {
        jQuery('#login_show').click();
    }else{
        jQuery.ajax({
            beforeSend: function(){
                jQuery('.vote').html('<img src="/wp-content/plugins/boga-contest/assets/img/spinner2.gif" style="width: 4%">');
            },
            method: "POST",
            url: "/wp-content/plugins/boga-contest/new_vote.php",
            data: {
                voter_id: voter_id,
                contestant_id: contestant_id,
                user_id: jQuery('#current-user-data-holder').data('contestantuserid')

    }
            })
            .done(function( msg ) {
                jQuery('.vote').html(msg);
                if(msg == '¡Genial! Tu voto ha sido contabilizado'){
                    var vote_div = jQuery('#votes-' + contestant_id);
                    var votes = parseInt(vote_div.data('votes'));
                    vote_div.html((votes + 1) + ' votos');
                }
            })
            .fail(function( msg ) {
                alert('fallo' + msg);
            });
    }
}

function new_contestant(){
    var user_id = jQuery('#current-user-data-holder').data('currentuserid');
    if(user_id == '0') {
        jQuery('#login_show').click();
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

jQuery(document).ready(function()
{
    jQuery('.vote').on('click', function(){
        new_vote(jQuery(this).data("id"));
    });
    jQuery('#participate').on('click', function(){
        new_contestant();
    });
    jQuery('#upload_alias').on('click', function(){
        jQuery('#upload').click();
    });
    jQuery('#upload').on('change', function(){
        new_photo();
    });
    jQuery('.contestant-photo').each(function(){
        galeria.push({scr: jQuery(this).attr('src')});
    });
/*    jQuery('.contestant-photo').magnificPopup({
        items: galeria,
        gallery: {
            enabled: true
        },
        type: 'image' // this is default type
    });*/
    jQuery('#main_photo_holder').magnificPopup({
        delegate: 'a',
        gallery: {
            enabled: true
        },
        type: 'image' // this is default type
    });
});