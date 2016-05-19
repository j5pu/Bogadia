<table class="form-table">

    <!-- ============ Leaders Vote ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Voting', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Use fast voting option?', 'fv') ?> (beta):</th>
        <?php echo fv_get_td_tooltip_code( __('Will be used SHORTINIT wordpress feature, that allows decrease memory and sql usage into 30-200%.', 'fv') ); ?>
        <td>
            <?php fv_admin_echo_switch_toggle( 'fv[fast-ajax]', FvFunctions::ss('fast-ajax', true) ); ?> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(Allows decrease memory and sql usage into about 50-300%.) Disable it, if have voting troubles.</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable anti fraud system?', 'fv') ?> (beta):</th>
        <?php echo fv_get_td_tooltip_code( __('After enabling this, you can see in Votes Log fraud score', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[anti-fraud]" <?php checked( FvFunctions::ss('anti-fraud', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(This will add at least one query to voting process. <a target="_blank" href="http://docs.wp-vote.net/#anti-fraud-system">Read more</a>)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Save reCAPTCHA result in session?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('If you enable this, than user need math reCAPTCHA once in 30 minutes. Can be not complete secure, but more accessible to users and less server load.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[recaptcha-session]" <?php checked( FvFunctions::ss('recaptcha-session', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(This will save reCAPTCHA result in session for 30 mins.)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable voting debug?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Save all Unsuccessful Voting attempts to later inspect it.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[debug-vote]" <?php checked( FvFunctions::ss('debug-vote', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(This will save all Unsuccessful Voting attempts with all data to Debug log. Please don't remember disable this, for do not pollute the log.)</small>
        </td>
    </tr>
    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Social networks, on using Social Login vote security?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - how social network user can use for vote.', 'fv') ); ?>
        <td class="socials">
            <span><?php _e('Facebook', 'fv') ?>:</span>
            <input type="checkbox" name="fv[voting-slogin-fb]" <?php checked( FvFunctions::ss('voting-slogin-fb', false) ); ?>/> <?php _e('Show', 'fv') ?> <small>(if set FB APP id, used self Hosted login, else Ulogin service)</small><br />

            <span><?php _e('Google+', 'fv') ?>:</span>
            <input type="checkbox" name="fv[voting-slogin-gp]" <?php checked( FvFunctions::ss('voting-slogin-gp', false) ); ?>/> <?php _e('Show', 'fv') ?> <small>(used  Ulogin service)</small><br />

            <span><?php _e('Twitter', 'fv') ?>:</span>
            <input type="checkbox" name="fv[voting-slogin-tw]" <?php checked( FvFunctions::ss('voting-slogin-tw', false) ); ?>/> <?php _e('Show', 'fv') ?> <small>(used  Ulogin service)</small><br />

            <span><?php _e('Odnoklasniki', 'fv') ?>:</span>
            <input type="checkbox" name="fv[voting-slogin-ok]" <?php checked( FvFunctions::ss('voting-slogin-ok', false) ); ?>/> <?php _e('Show', 'fv') ?> <small>(used  Ulogin service)</small><br />

            <span><?php _e('Vkontake', 'fv') ?>:</span>
            <input type="checkbox" name="fv[voting-slogin-vk]" <?php checked( FvFunctions::ss('voting-slogin-vk', false) ); ?>/> <?php _e('Show', 'fv') ?> <small>(used  Ulogin service)</small><br />

            <span><?php _e('Mail.ru', 'fv') ?>(мой мир):</span>
            <input type="checkbox" name="fv[voting-slogin-mailru]" <?php checked( FvFunctions::ss('voting-slogin-mailru', false) ); ?>/> <?php _e('Show', 'fv') ?> <small>(used  Ulogin service)</small><br />

        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Hide all social buttons after voting?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you don\'t want see a social buttons after voting - check it', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[voting-noshow-social]" <?php checked( FvFunctions::ss('voting-noshow-social', false) ); ?>/> <?php _e('No show', 'fv') ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Or hide some social buttons after voting:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - how social buttons not show after voting', 'fv') ); ?>
        <td class="socials">
            <div>
                <span><?php _e('Vkontake', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-vk]', FvFunctions::ss('voting-noshow-vk') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Facebook', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-fb]', FvFunctions::ss('voting-noshow-fb') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Twitter', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-tw]', FvFunctions::ss('voting-noshow-tw') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Odnoklasniki', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-ok]', FvFunctions::ss('voting-noshow-ok') ); ?> Hide it
            </div>
            <div>
                <span><?php _e('Google+', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-gp]', FvFunctions::ss('voting-noshow-gp') ); ?> Hide it
            <div>
                <span><?php _e('Pinterest', 'fv') ?>:</span>
                <?php fv_admin_echo_switch_toggle( 'fv[voting-noshow-pi]', FvFunctions::ss('voting-noshow-pi') ); ?> Hide it
            </div>
        </td>
    </tr>

    <!-- ============ Leaders Vote ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Social counter', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable social counter?', 'fv') ?> (beta):</th>
        <?php echo fv_get_td_tooltip_code( __('After enabling this each photo will have counter with total sharing count.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[soc-counter]" <?php checked( FvFunctions::ss('soc-counter', false) ); ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(Please disable not used networks, because getting data from all networks increases user PC loading)</small>
            <br/><small>Note: this option now good works with 'Ajax' or 'Infinity loading' pagination</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Count social votes:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - from what social networks count shares', 'fv') ); ?>
        <td class="socials">
            <span><?php _e('Facebook', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-fb]" <?php checked( FvFunctions::ss('soc-counter-fb', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query for all photos)</small><br />

            <span><?php _e('Twitter', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-tw]" <?php checked( FvFunctions::ss('soc-counter-tw', false) ); ?>/> <?php _e('Count', 'fv') ?><small>(One browser network query per photo) -
            <a target="_blank" href="https://twittercommunity.com/t/a-new-design-for-tweet-and-follow-buttons/52791">Not supported by Twitter now</a></small> <br />

            <span><?php _e('Google+', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-gp]" <?php checked( FvFunctions::ss('soc-counter-gp', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query per photo)</small><br />

            <span><?php _e('Pinterest', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-pi]" <?php checked( FvFunctions::ss('soc-counter-pi', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query per photo)</small><br />

            <span><?php _e('Vkontake', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-vk]" <?php checked( FvFunctions::ss('soc-counter-vk', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query per photo)</small><br />

            <span><?php _e('Odnoklasniki', 'fv') ?>:</span>
            <input type="checkbox" name="fv[soc-counter-ok]" <?php checked( FvFunctions::ss('soc-counter-ok', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query per photo)</small><br />

            <span><?php _e('Mail.ru', 'fv') ?>(мой мир):</span>
            <input type="checkbox" name="fv[soc-counter-mailru]" <?php checked( FvFunctions::ss('soc-counter-mailru', false) ); ?>/> <?php _e('Count', 'fv') ?> <small>(One browser network query for all photos)</small><br />
        </td>
    </tr>

</table>