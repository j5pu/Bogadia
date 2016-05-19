<?php
    // flat for Russian lang
    $isRus = ( get_bloginfo( 'language' ) == 'ru-RU' ) ? true : false;
    $supportEmail = ( $isRus ) ? 'ru@wp-vote.net' : 'support@wp-vote.net';
?>

 <div class="meta-box">
    <div class="postbox">
            <h3>
                    <span><?php _e('Setup Tutorial', 'fv') ?></span>
            </h3>
            <div class="inside">
                <?php if ( $isRus ): ?>
                    <a href="https://www.youtube.com/watch?v=xeuY6aaTeKY" target="_blank" class="docs_flex">
                        <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/youtube_admin.png') ?>" width="100%" alt="" >
                        <span>Часть 1. Установка плагина</span>
                    </a>

                    <a href="https://www.youtube.com/watch?v=5dhExAcLW74" target="_blank" class="docs_flex">
                        <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/youtube_admin.png') ?>" width="100%" alt="" >
                        <span>Часть 2. Настройки плагина</span>
                    </a>

                    <br/>
                    <a href="https://www.youtube.com/watch?v=nrDK6V9Dfew" target="_blank">
                        <span class="typcn typcn-social-youtube"></span> Создание пользовательской темы
                    </a>
                <?php else: ?>
                    <a href="https://www.youtube.com/watch?v=FtLgESz41HI" target="_blank" class="docs_flex">
                        <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/youtube_admin.png') ?>" alt="" >
                        <span>How to install plugin and create contest</span>
                    </a>
                    <br/>
                    <a href="http://docs.wp-vote.net/" target="_blank"><span class="typcn typcn-link-outline"></span> Documentation (in process)</a>
                    <br/><a href="https://www.youtube.com/watch?v=M3zsCeyKUpM" target="_blank"><span class="typcn typcn-social-youtube"></span> Create custom theme</a>
                <?php endif; ?>
            </div>
    </div>

     <div class="postbox sidebar-addons">
         <?php
         $ad_sidebar_id = rand(1, 3);
         ?>
         <h3>
             <span><?php _e('Addons', 'fv') ?></span>
         </h3>
         <div class="inside">
             <a href="http://wp-vote.net/ad_sidebar-<?php echo $ad_sidebar_id; ?>" target="_blank">
                 <img src="<?php echo 'http://wp-vote.net/show/ad_sidebar-'  . $ad_sidebar_id . '.png'; ?>" alt="addons"/>
             </a>
         </div>
     </div>

     <div class="postbox">
         <?php
         $defaults = array('key'=>'', 'valid'=>0, 'expiration'=>'Key not entered!');
         $key_arr = get_option('fotov-update-key', $defaults);
         if ( !$key_arr ) {
             $key_arr = $defaults;
         }
         //var_dump($key_arr);
         ?>

         <h3>
             <span><?php _e('Updating', 'fv') ?> :: <?php echo FV::VERSION ?></span>
         </h3>
         <div class="inside">
             <div class="gadash-title">
                 <a href="#" target="_blank">
                     <img width="32" src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/update.png') ?>" >
                 </a>
             </div>
             <div class="gadash-desc">
                 <strong><?php _e('Status: ', 'fv') ?></strong>
                 <?php if ( $key_arr['valid'] ):
                     echo __('update active until ', 'fv') . $key_arr['expiration'];
                 else:
                     echo __('update inactive - ', 'fv') . __($key_arr['expiration'], 'fv') ;
                     ?>
                     <br/><a href="http://wp-vote.net/extending_license" target="_blank">Extend license >></a>
                 <?php endif; ?>
                 <br/><?php echo ($key_arr['key'])? __('<strong>You key</strong>: ', 'fv') . $key_arr['key'] : __('Key not entered!', 'fv'); ?>
             </div>
         </div>
     </div>


        <div class="postbox">
                <h3>
                        <span><?php _e('Support &amp; Reviews', 'fv') ?></span>
                </h3>
                <div class="inside">
                        <div class="gadash-title">
                            <a href="http://wp-vote.net/contact-us/" target="_blank">
                                <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/hire_me.png') ?>" width="32">
                            </a>
                        </div>
                        <div class="gadash-desc"><?php _e('Need customization or freelance developer? Write to', 'fv') ?> <br/> <strong><?php echo $supportEmail; ?></strong></div>
                        <br/>
                        <div class="gadash-title">
                            <a href="http://wp-vote.net/contact-us/" target="_blank">
                                <img src="<?php echo plugins_url('wp-foto-vote/assets/img/admin/help.png') ?>" >
                            </a>
                        </div>
                        <div class="gadash-desc"><?php _e('Need support? Write to', 'fv') ?> <br/> <strong><?php echo $supportEmail; ?></strong></div>
                        <br>
                        <div class="gadash-title">
                                <a href="http://wp-vote.net/testimonials/">
                                    <img src="<?php  echo plugins_url('wp-foto-vote/assets/img/admin/star.png') ?>">
                                </a>
                        </div>
                        <div class="gadash-desc"><?php _e('Your feedback and review are both important, <a href="http://wp-vote.net/testimonials/" target="_blank">write you testimonial</a>!', 'fv') ?>
                        </div>
                </div>
        </div>
     <!--
        <div class="postbox">
                <h3>
                        <span>Further Reading</span>
                </h3>
                <div class="inside">
                        <div class="gadash-title">
                                <a href="http://deconf.com/wordpress/"><img src="http://wp-vote.net/wp-content/plugins/google-analytics-dashboard-for-wp/admin/images/wp.png" style="outline: rgb(255, 8, 0) dashed 1px;"></a>
                        </div>
                        <div class="gadash-desc">Other <a href="http://deconf.com/wordpress/" style="outline: rgb(255, 8, 0) dashed 1px;">WordPress Plugins</a> written by the same author.</div>
                        <br>
                        <div class="gadash-title">
                                <a href="http://deconf.com/clicky-web-analytics-review/"><img src="http://wp-vote.net/wp-content/plugins/google-analytics-dashboard-for-wp/admin/images/clicky.png" style="outline: rgb(255, 8, 0) dashed 1px;"></a>
                        </div>
                        <div class="gadash-desc"><a href="http://deconf.com/clicky-web-analytics-review/" style="outline: rgb(255, 8, 0) dashed 1px;">Web Analytics</a> service with visitors tracking at IP level.</div>
                </div>
        </div>
     -->
</div>