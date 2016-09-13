(function ($) {
    $(function () {
        $('.asap-tab').click(function(){
           var attr_id = $(this).attr('id');
           var id = attr_id.replace('asap-tab-','');
           $('.asap-tab').removeClass('asap-active-tab');
           $(this).addClass('asap-active-tab'); 
           $('.asap-section').hide();
           $('#asap-section-'+id).show();
        });
        
        $('input[name="add_account_submit"]').click(function(){
           if($(this).closest('form').find('select[name="network_name"]').val()==''){
            return false;
           } 
        });
        
        $('#asap-fb-authorize-ref').click(function(){
           $('input[name="asap_fb_authorize"]').click(); 
        });
        
        $('#asap-linkedin-authorize-ref').click(function(){
           $('input[name="asap_linkedin_authorize"]').click(); 
        });
        $('#asap-tumblr-authorize-ref').click(function(){
           $('input[name="asap_tumblr_authorize"]').click(); 
        });
        
        $('.asap-bitly-check').click(function(){
           if($(this).is(':checked')){
            $('.asap-bitly-ref').show();
            }
            else
            {
                $('.asap-bitly-ref').hide();
            }
        });
        $('.asap-tab-nav .asap-tab').click(function(){
           var href = $(this).attr('data-href');
           window.location = href;
        });
          });//document.ready close
}(jQuery));