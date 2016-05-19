<div class="fv-countdown" data-image="<?php echo $this->addonUrl . 'images/sprites.png' ?>">
    <div class="fv-countdown--title"><?php echo wp_kses_post($this->_get_opt('text_before', '') ); ?></div>

    <em class="clock"></em>
    <div class="c-block c-block-<?php echo $this->_get_opt('days_count', 2); ?>"><div class="bl-inner"><span><?php echo $days_leave; ?></span></div>
        <span class="etitle etitle-1"> <?php echo fv_get_transl_msg('timer_days', 'days'); ?></span>
    </div>

    <div class="c-block c-block-2"><div class="bl-inner"><span><?php echo $hours_leave; ?></span></div>
        <span class="etitle etitle-2"> <?php echo fv_get_transl_msg('timer_hours', 'hours'); ?></span>
    </div>

    <div class="c-block c-block-2"><div class="bl-inner"><span><?php echo $minutes_leave; ?></span></div>
        <span class="etitle etitle-3"> <?php echo fv_get_transl_msg('timer_minutes', 'minutes'); ?></span>
    </div>

    <div class="c-block c-block-2"><div class="bl-inner"><span><?php echo $secs_leave; ?></span></div>
        <span class="etitle etitle-4"> <?php echo fv_get_transl_msg('timer_secs', 'seconds'); ?></span>
    </div>
</div>