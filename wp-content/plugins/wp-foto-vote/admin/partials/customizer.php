<?php

?>
<h3>Customizer</h3>

<div class="pure-g">
        <div class="pure-u-sm-2-3">
                <div class="color-picker" style="position:relative;">
                        <input type="text" name="123" class="color" value="#111122" />
                        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
                </div>

        </div>
        <div class="pure-u-sm-1-3">
                <iframe id="customizer_preview" src="<?php echo plugins_url( "wp-foto-vote/admin/partials/iframe_preview.php?theme=default" ) ?>" width="350" height="300"></iframe>
        </div>

</div>