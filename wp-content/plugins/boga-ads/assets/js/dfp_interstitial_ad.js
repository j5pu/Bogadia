jQuery(document).ready(function(){
    var cookie_val = localStorage.getItem('bogatitial');
    if(cookie_val){
        if(cookie_val < 2){
            cookie_val++;
            localStorage.setItem('bogatitial', cookie_val);
        }else{
            localStorage.removeItem('bogatitial');
        }
    }else{
        var tracking_link = jQuery('.ad_link').attr('href');
        jQuery('#trackinglink').attr('href', tracking_link);
        jQuery('.shareaholic-share-buttons-container.floated').hide();
        jQuery('#interstitialModal').delay(100).modal({show:true, backdrop: 'static'});
        localStorage.setItem('bogatitial', 1);
    }
    jQuery('#close-buton').on('click', function(){
        jQuery('.shareaholic-share-buttons-container.floated').show('slow');
    });
});