<div class="wrap">
    <h2><?php _e('Upload form builder', 'fv') ?></h2>
    <p>
        <?php _e('Please be careful with parameter "Save to photo field"! <br/>
        All fields exept `user_email` appeds data, this means if you select save 2 fields into `description`,
        for first will be writen top field, after into end `description` will be added next input value.<br/>
        But for `email` you will see just last Email input value.' , 'fv') ?>
    </p>
    <p>
        <?php _e('<strong>Note:</strong> When you will used multiupload, please be sure, now limit by image size works just for 1 field and Upload limit count +1 by upload step now (example - you set up limit as 2 and shows 5 file fields, than user can upload 2*5 images max).' , 'fv') ?>
    </p>
    <div class='fb-main'></div>

    <script>
        FvLib.addHook('doc_ready', function() {
            var data = <?php echo Fv_Form_Helper::get_form_structure(); ?>;
            var fb = new Formbuilder({
                selector: '.fb-main',
                bootstrapData: data.fields
            });

            /*fb.on('save', function(payload){
                console.log(payload);
            });*/

            Formbuilder.options.HTTP_ENDPOINT = "<?php echo add_query_arg( 'action', '', wp_nonce_url( admin_url('admin-ajax.php') ) ); ?>";
            Formbuilder.options.HTTP_SAVE_ACTION = "fv_save_form_structure";
            Formbuilder.options.HTTP_RESET_ACTION = "fv_reset_form_structure";
            Formbuilder.options.HTTP_METHOD = "POST";
            //Formbuilder.submitField = data.submitField;
        });
    </script>

    <style>
        .fb-main {
            background-color: #fff;
            border-radius: 5px;
            min-height: 600px;
        }
        .fb-main {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px 0 0;
            position: relative;
            font-family: 'Source Sans Pro','Open Sans',Tahoma;
        }
    </style>
</div>