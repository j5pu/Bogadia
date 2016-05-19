/*
 * Functions for Settings page
 *
 * Plugin Name: WP Foto Vote
 * Plugin URI: http://wp-vote.net
 * Author: Maxim K
 * Author URI: http://maxim-kaminsky.com/
 */

var CodeMirrorEditor;
FvLib.addHook('doc_ready', function() {
	// load css editor for additional CSS styles editor
    CodeMirrorEditor = CodeMirror.fromTextArea(
		  document.querySelector("textarea[name='fotov-custom-css']"),
		  {
			  extraKeys: { "Ctrl-Space": "autocomplete" }
		  }
	);

    jQuery('.fv-colorpicker .color').wpColorPicker();

});

function fv_reset_color(el, default_color)
{
    jQuery(el).parent().find('.wp-color-picker').iris('color', default_color);
    jQuery(el).parent().append("<i>Color reset to default. Don't remember save options.</i>");
}

// Hide / show not needed blocks
function fv_upload_limit_dimensions (el) {
    jQuery(".dimensions-toggle").hide();
    if ( el.value ) {
        jQuery(".limit-dimensions-" + el.value).fadeIn();
    }
}

function fv_save_settings (form) {
    document.querySelector("textarea[name='fotov-custom-css']").value = CodeMirrorEditor.getValue();
    //var b =  $(this).serialize();
    jQuery.post( 'options.php', jQuery(form).serialize() )
        .error(function() {
            if ( jQuery.growl !== undefined ) {
                jQuery.growl.warning({ message: 'Some error on saving Settings!' });
            } else {
                alert('Some error on saving Settings!');
            }
        }).success( function() {
            if ( jQuery.growl !== undefined ) {
                jQuery.growl.notice({ message: 'Settings saved!' });
            } else {
                alert('Settings saved!');
            }
            //jQuery('#fv-settings-updated').fadeIn();
        });
    return false;
}

jQuery(".switch-toggle-label").on("click", function() {
    jQuery(this).parent().toggleClass('switch-toggle-checked').find('input')
        .val( 1^this.previousElementSibling.value );
});