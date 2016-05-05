jQuery(document).ready(function(){
    if(cookie_val){
        if(cookie_val < 2){
            cookie_val++;
            localStorage.setItem('bogatitial', cookie_val);
        }else{
            localStorage.removeItem('bogatitial');
        }
    }else{
        jQuery('#interstitialModal').delay(2000).modal({show:true, backdrop: 'static'});
        localStorage.setItem('bogatitial', 1);
    }
});