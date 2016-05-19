<?php
defined('ABSPATH') or die("No script kiddies please!");
?>

<div id="sv_dialog">
        <input type="hidden" name="fv_id" id="fv_id"/>
        <div id="info">
                <h3></h3>
                <?php if (!get_option('fotov-voting-noshow-social', false)): ?>
                        <div class="slogan"></div>
                        <span class="photo_url"><?php echo $page_url ?>=<span id="photo_id"></span></span>
                        <div id="icons">
                                <ul>
                                        <?php if (!get_option('fotov-voting-noshow-vk', false)): ?>
                                                <li><a href="#" onclick="return sv_vote_send('vk', this)"
                                                       target="_blank"><label
                                                                    style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/vk.png') ?>)"></label></a>
                                                </li>
                                        <?php endif; ?>
                                        <?php if (!get_option('fotov-voting-noshow-fb', false)): ?>
                                                <li><a href="#" onclick="return sv_vote_send('fb', this)"
                                                       target="_blank"><label
                                                                    style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/fb.png') ?>)"></label></a>
                                                </li>
                                        <?php endif; ?>
                                        <?php if (!get_option('fotov-voting-noshow-tw', false)): ?>
                                                <li><a href="#" onclick="return sv_vote_send('tw', this)"
                                                       target="_blank"><label
                                                                    style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/tw.png') ?>)"></label></a>
                                                </li>
                                        <?php endif; ?>
                                        <?php if (!get_option('fotov-voting-noshow-ok', false)): ?>
                                                <li><a href="#" onclick="return sv_vote_send('ok', this)"
                                                       target="_blank"><label
                                                                    style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/ok.png') ?>)"></label></a>
                                                </li>
                                        <?php endif; ?>
                                        <?php if (!get_option('fotov-voting-noshow-gp', false)): ?>
                                                <li><a href="#" onclick="return sv_vote_send('gp', this)"
                                                       target="_blank"><label
                                                                    style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/gp.png') ?>)"></label></a>
                                                </li>
                                        <?php endif; ?>
                                        <?php if (!get_option('fotov-voting-noshow-pi', false)): ?>
                                                <li><a href="#" onclick="return sv_vote_send('pi', this)"
                                                       target="_blank"><label
                                                                    style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/pi.png') ?>)"></label></a>
                                                </li>
                                        <?php endif; ?>
                                </ul>
                        </div>
                <?php endif; ?>
        </div>
        <div id="fv_subscribe_form" style="display: none;">
                <h3><?php echo $public_translated_messages['form_subsr_title'] ?></h3>

                <form>
                        <table>
                                <tr>
                                        <td><?php echo $public_translated_messages['form_subsr_name'] ?>:<span
                                                    class="required_input">*</span></td>
                                        <td><input type="text" name="fv_name" class="fv_name"
                                                   placeholder="<?php echo $public_translated_messages['form_subsr_name'] ?>"
                                                   required pattern=".{2,15}"/></td>
                                </tr>
                                <tr>
                                        <td><?php echo $public_translated_messages['form_subsr_email'] ?>:<span
                                                    class="required_input">*</span></td>
                                        <td><input type="email" name="fv_email" class="fv_email"
                                                   placeholder="<?php echo $public_translated_messages['form_subsr_email'] ?>"
                                                   required pattern=".{5,25}"/></td>
                                </tr>
                                <tr>
                                        <td>
                                                <span
                                                    id="agrees"><?php _e('We are not put your data to others.', 'fv') ?></span>
                                        </td>
                                        <td>
                                                <button type="button"
                                                        onclick="sv_vote(document.getElementById('fv_id').value, 'subscribe')"><?php echo $public_translated_messages['vote_button_text']; ?></button>
                                        </td>
                                </tr>
                        </table>
                </form>
        </div>
        <div id="fv_social_form" style="display: none;">
                <h3><?php echo $public_translated_messages['form_soc_title'] ?></h3>
                <div id="icons">
                        <ul>
                                <?php if (!get_option('fotov-voting-noshow-vk', false)): ?>
                                        <li><a href="#" onclick="fv_soc_autorization('vk', this); return false;"
                                               target="_blank"><label
                                                            style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/vk.png') ?>)"></label></a>
                                        </li>
                                <?php endif; ?>
                                <?php if (!get_option('fotov-voting-noshow-fb', false)): ?>
                                        <li><a href="#" onclick="fv_soc_autorization('fb', this); return false;"
                                               target="_blank"><label
                                                            style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/fb.png') ?>)"></label></a>
                                        </li>
                                <?php endif; ?>
                                <?php if (!get_option('fotov-voting-noshow-tw', false)): ?>
                                        <li><a href="#" onclick="fv_soc_autorization('tw', this); return false;"
                                               target="_blank"><label
                                                            style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/tw.png') ?>)"></label></a>
                                        </li>
                                <?php endif; ?>
                                <?php if (!get_option('fotov-voting-noshow-ok', false)): ?>
                                        <li><a href="#" onclick="fv_soc_autorization('ok', this); return false;"
                                               target="_blank"><label
                                                            style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/ok.png') ?>)"></label></a>
                                        </li>
                                <?php endif; ?>
                                <?php if (!get_option('fotov-voting-noshow-gp', false)): ?>
                                        <li><a href="#" onclick="fv_soc_autorization('gp', this); return false;"
                                               target="_blank"><label
                                                            style="background: url(<?php echo plugins_url('wp-foto-vote/assets/img/gp.png') ?>)"></label></a>
                                        </li>
                                <?php endif; ?>
                        </ul>
                </div>
        </div>
</div>

<?php if ($contest->security_type == "defaultAsocial" || $contest->security_type == "cookieAsocial"): ?>
        <script src="//ulogin.ru/js/ulogin.js"></script>

        <div class="uLogin" data-ulogin="display=panel;sort=default;fields=first_name,email;providers=vkontakte,twitter,google,odnoklassniki,facebook;hidden=other;redirect_uri=;callback=ulogin_data"></div>
<?php endif; ?>

<div id="fb-root"></div>