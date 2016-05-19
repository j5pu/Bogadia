/*
Plugin Name: WP Foto Vote
Plugin URI: http://wp-vote.net
Author: Maxim K
Author URI: http://maxim-kaminsky.com/
*/

var contestTable = false;

FvLib.addHook('doc_ready', function() {
    if( fv.wp_lang == "ru-RU" ){
        var dt_lang = { language: {url: '//cdn.datatables.net/plug-ins/725b2a2115b/i18n/Russian.json'} };
    } else if( fv.wp_lang == "de-DE" ) {
        var dt_lang = {language: {url: '//cdn.datatables.net/plug-ins/725b2a2115b/i18n/German.json'} };
    }

    if ( jQuery.fn.DataTable !== undefined && jQuery('#table_units').length > 0 ) {
        contestTable = jQuery('#table_units').DataTable(dt_lang);
    }

    if ( jQuery.fn.datetimepicker !== undefined && jQuery('.datetime').length > 0 ) {
        jQuery('.datetime').datetimepicker(
            {
                //mask:'1111-19-09 29:59:09',
                format:'Y-m-d H:i:s',
                formatDate:'Y-m-d',
                formatTime:'H:i'
            });
    }

    // Hide notice button click
    jQuery('.hide-notice').click(function() {
        jQuery(this).closest('.bs-callout').fadeOut();
        // set COOKIE for not show this message
        if ( jQuery(this).prop('id') ) {
            FvLib.createCookie( jQuery(this).prop('id'), 1, 90 );
        }
    });

});

/*
 * Uses for run export some data as CSV
 *
 * For this we need compose URL and set it to Iframe src
 *
 * @param  type		string		#export type
 * @param  type		nonce		#WP nonce
 * @param  param	string		#export parameter
 * @param  value	mixed		#parameter value (example - contest_id)
 *
 * @return void
 */
function fv_export (type, nonce, param, value) {
	if ( type == undefined ) {
		jQuery.growl.error({ message: "Export error! Not set `type` param." });
		return;
	}
	// Export url like admin-ajax.php?action=fv_export&type=contest_data&contest_id=1
	var export_url = fv.ajax_url + '?action=fv_export&type=' + type + '&fv_nonce=' + nonce;
	if ( param != undefined ) {
		export_url += '&' + param + '=' + value;
	}
	export_url += '&fuckcache=' + FvLib.randomStr(6);

	var export_iframe = document.querySelector('#export_iframe');
	// if Iframe not exists
	if ( export_iframe == null ) {
		var export_iframe = document.createElement('iframe');
		export_iframe.id = 'export_iframe';
		export_iframe.style.display = "none";
		export_iframe.src = export_url;
		document.body.appendChild(export_iframe);
	} else {
		export_iframe.setAttribute('src', export_url);
	}
	jQuery.growl.notice({ message: "Export data runs." });
}
