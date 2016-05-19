<?php
/**
 * Class for provide upload form rendering and other (save, get structure, etc).
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class Fv_Form_Helper {

    /**
     * Is theme supports leaders block
     *
     * @since    2.2.081
     * @access   public
     * @var      bool
     */
    protected static $optionName = 'fv-form-fields';

    /**
     * AJAX save data from From builder on click `Save`
     *
     * @return void
     * @output string ('ok'/'no')
     */
    public static function AJAX_save_form_structure() {
        $json_data = file_get_contents('php://input');

        check_ajax_referer();
        if ( !empty($json_data) ) {
            update_option( self::$optionName, $json_data );
            die( 'ok' );
        }
        die( 'no' );
    }

    public static function AJAX_reset_form_structure() {
        check_ajax_referer();
        update_option( self::$optionName, self::get_default_form_structure() );
        die( fv_json_encode(self::get_default_form_structure()) );
    }

    public static function get_form_structure() {
        $structure = get_option( self::$optionName );
        if ( empty($structure) ) {
            return self::get_default_form_structure();
        }
        return $structure;
    }

    public static function get_form_structure_obj() {
        return json_decode( self::get_form_structure() );
    }

    public static function get_default_form_structure() {
        return '{"fields":[{"label":"Photo name","field_type":"text","required":true,"field_options":'.
        '{"size":"small","save_to":"name","default_value":"display_name","description":"Enter photo name"},'.
        '"cid":"c20","placeholder":"name"},{"label":"Your email","field_type":"email","required":true,"field_options":'.
        '{"description":"","default_value":"email","save_to":"user_email"},"cid":"c24","placeholder":"This is email"},'.
        '{"label":"Select file","field_type":"file","required":true,"field_options":{},"cid":"image","placeholder":"Enter photo name"}]}';
    }

    public static function _get_photo_data_from_POST($form_data, $structure) {
        if ( !is_object($structure) && !isset($structure->fields) ) {
            FvLogger::addLog('Fv_Form_Helper::_get_photo_email_from_POST - $structure error');
        }
        //var_dump($form_data);
        //var_dump($structure);
        //die();
        $new_photo = array( 'name' => '', 'description' => '', 'full_description' => '', 'user_email' => '', 'upload_info' => '' );
        foreach($structure->fields as $field) {

            if ( !isset($form_data[$field->cid]) ) {
                //FvLogger::addLog('Fv_Form_Helper::_get_photo_email_from_POST field not exists in $form_data - ' . $field->cid);
                continue;
            }
            if ( is_array($form_data[$field->cid]) ) {
                $form_data[$field->cid] = implode(';', $form_data[$field->cid]);
            }

            if ( isset($field->field_options->save_to) && array_key_exists($field->field_options->save_to, $new_photo) ) {
                switch ($field->field_options->save_to) {
                    case 'name':
                        if ( strlen($new_photo['name']) > 1 ) { $new_photo['name'] .= '; '; }
                        $new_photo['name'] .= sanitize_text_field($form_data[$field->cid]);
                        break;
                    case 'description':
                        if ( strlen($new_photo['description']) > 1 ) { $new_photo['description'] .= '; '; }
                        $new_photo['description'] .= sanitize_text_field($form_data[$field->cid]);
                        break;
                    case 'full_description':
                        if ( strlen($new_photo['full_description']) > 1 ) { $new_photo['full_description'] .= '; '; }
                        $new_photo['full_description'] .= wp_kses($form_data[$field->cid], 'default');
                        break;
                    case 'user_email':
                        $new_photo['user_email'] = sanitize_email($form_data[$field->cid]);
                        break;
                }
            } else {
                $new_photo['upload_info'][$field->label] = sanitize_text_field($form_data[$field->cid]);
            }
        }

        if ( is_array($new_photo['upload_info']) && count($new_photo['upload_info']) > 0 ) {
            $new_photo['upload_info'] = json_encode($new_photo['upload_info']);
        }


        return $new_photo;
    }

    /**
     * Render upload form fields
     *
     * @param array $public_translated_messages
     * @param object $contest
     * @param bool $show_labels
     */
    public static function render_form($public_translated_messages, $contest, $show_labels = true) {
        $fields = apply_filters('fv/public/render_upload_form/filter_fields', self::get_form_structure_obj()->fields);

        $eol = "\n";
        $html ="";
        $c = 1;
        $cSectionBreak = 1;
        $html .= '<fieldset>';
        foreach ($fields as $field) :
            if ($field->field_type !== 'section_break'):
                $html .= '<div class="fv_wrapper">' . $eol;
                    if ( $show_labels ) {
                        $html .= self::display_label($field, $c, $contest) . $eol;
                    }
                    $html .= self::display_field($field, $c, $contest) . $eol;
                $html .= '</div>' . $eol;
                $c++;
            else:
                $html .= '</fieldset>';
                $html .= '<legend>' . apply_filters('fv/public/upload_form/section_break', $field->label, $field, $cSectionBreak) . '</legend>';
                $html .= '<fieldset>';
                $cSectionBreak++;
            endif;
        endforeach;

        $html .= apply_filters("fv_upload_form_rules_filer", '', $c);

        $html .= '</fieldset>';

        $html .= '<div style="clear:both;overflow:hidden">' .
                    '<button type="submit" class="fv-upload-btn">' .
                        '<span id="fv_upload_preloader"> <span class="fvicon-spinner icon rotate-animation"></span> </span>' .
                        $public_translated_messages['upload_form_button_text'] .
                    '</button>' .
                    apply_filters("fv_upload_form_rules_hook", '', $c) .
                '</div>';

        echo $html;
    }

    /**
     * Generate HTML for displaying fields
     * @param  array $field     Field data
     * @param  int $c           Counter
     * @param  object $contest
     * @return string
     */
    public static function display_label($field, $c, $contest) {
        $html = '<label>';
        $html .=  apply_filters('fv/public/upload_form/label', $field->label, $field, $c, $contest);
        $html .= '</label>';
        return $html;
    }

    /**
     * Generate HTML for displaying fields
     *
     * @param  array $field Field data
     * @param  int $c Field number
     * @param  object $contest*
     *
     * @return string
     */
    public static function display_field($field, $c, $contest) {

        if ( !isset($field->cid) ) {
            FvLogger::addLog("Fv_Form_Helper display_field error - no `cid` | Line: " . __LINE__);
            return "Form error!";
        }

        $html = '';
        //$this->settings_base
        $option_name = "form[" . $field->cid . "]";

        if (get_current_user_id() > 0) {
            $user_info = get_userdata( get_current_user_id() );
        }

        if ( empty($field->class) ) {
            $field->class = 'form-control';
        }
        if ( empty($field->id) ) {
            $field->id = '';
        }
        if ( empty($field->placeholder) ) {
            $field->placeholder = '';
        }
        if ( empty($field->field_type) ) {

        }
        if ( empty($field->field_options) ) {
            $field->field_options = new stdClass();
        }

        // Set default value
        $data = '';
        if ( !empty($field->field_options->default_value) && get_current_user_id() > 0 ) {
            switch($field->field_options->default_value) {
                case 'display_name':
                    $data = $user_info->display_name;
                    break;
                case 'first_name':
                    $data = $user_info->first_name;
                    break;
                case 'last_name':
                    $data = $user_info->last_name;
                    break;
                case 'email':
                    $data = $user_info->user_email;
                    break;
            }
        }

        if ( empty($field->field_options->description) ) {
            $field->field_options->description = '';
        }
        // for radio and select
        if ( empty($field->field_options->options) ) {
            $field->field_options->options = array();
        }

        $required = '';
        if ( !empty($field->required) && $field->required == true ) {
            $required = 'required';
        }

        // Try remove ID attr
        //id="' . esc_attr($field->id) . '"
        switch ($field->field_type) {

            case 'text':
                $pattern = '';
                if ( isset($field->field_options->minlength) && $field->field_options->minlength > 0
                    && isset($field->field_options->maxlength) && $field->field_options->maxlength > 3 )
                {
                    $pattern = ' pattern=".{' . $field->field_options->minlength . ',' .  $field->field_options->maxlength . '}" ';
                }

                $html .= '<input class="' . esc_attr($field->class) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . $data . '" ' . $required . $pattern . '/>' . "\n";
                break;

            case 'website':
                $html .= '<input class="' . esc_attr($field->class) . '" type="url" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . $data . '" ' . $required . '/>' . "\n";
                break;

            case 'email':
                $html .= '<input autocomplete="on" class="' . esc_attr($field->class) . '" type="email" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . $data . '" ' . $required . '/>' . "\n";
                break;

            case 'number':
                $max = '';
                $min = '';
                $units = '';
                if ( isset($field->field_options->max) ) {
                    $max = ' max="' . $field->field_options->max . '" ';
                }
                if ( isset($field->field_options->min) ) {
                    $min = ' min="' . $field->field_options->min . '" ';
                }
                if ( isset($field->field_options->units) ) {
                    $units = $field->field_options->units;
                }
                $html .= '<div><input style="display: inline-block;" class="' . esc_attr($field->class) . '" type="number" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" value="' . $data . '" ' . $required . $min . $max . '/> ' . $units . "</div>\n";
                break;

            case 'textarea':
                $html .= '<textarea class="' . esc_attr($field->class) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field->placeholder) . '" ' . $required . '>' . esc_attr($data) . '</textarea>' . "\n";
                break;

            case 'checkbox':
                $checked = '';
                if ($data) {
                    $checked = 'checked="checked"';
                }
                $html .= '<input type="' . $field->field_type . '" name="' . esc_attr($option_name) . '" ' . $checked . ' ' . $required . '/>' . "\n";
                break;

            case 'checkbox_multi':
                foreach ($field->field_options->options  as $k => $opt) {
                    $html .= '<label for="' . esc_attr($field->cid . '_' . $k) . '" class="checkbox_input"><input type="checkbox" ' . checked($opt->checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($opt->label) . '" id="' . esc_attr($field->cid . '_' . $k) . '" /> ' . $opt->label . '</label> ';
                }
                break;

            case 'radio':
                foreach ($field->field_options->options as $k => $opt) {
                    $html .= '<label for="' . esc_attr($field->cid. '_' . $k) . '" class="radio_input"><input type="radio" ' . checked($opt->checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($opt->label) . '" id="' . esc_attr($field->cid. '_' . $k) . '"  ' . $required . '/> ' . $opt->label . '</label> ';
                }
                break;

            case 'select':
                $html .= '<select name="' . esc_attr($option_name) . '" class="' . esc_attr($field->class) . '" ' . $required . '>';
                if ( !empty($field->include_blank_option) && $field->include_blank_option) {
                    $html .= '<option ' . selected(true) . ' value="">' . __("Select value", 'fv') . '</option>';
                }

                foreach ($field->field_options->options as $k => $opt) {
                    $html .= '<option ' . selected($opt->checked, true, false) . ' value="' . esc_attr($opt->label) . '">' . $opt->label . '</option>';
                }
                $html .= '</select> ';
                break;

            case 'select_multi':
                $html .= '<select name="' . esc_attr($option_name) . '[]" multiple="multiple" ' . $required . '>';
                foreach ($field->field_options->options as $k => $v) {
                    $selected = false;
                    if (in_array($k, $data)) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '" />' . $v . '</label> ';
                }
                $html .= '</select> ';
                break;

            case 'file':
                if ( apply_filters( 'fv/public/upload_form/custom_file_input/uses', false, $contest ) === false ) {
                    $required = '';
                    $placeholder = !empty($field->placeholder) ? $field->placeholder : '' ;
                    foreach( self::_get_file_inputs() as $file_input_name => $file_input_params ):
                        $required = $file_input_params['required'] == true ? 'required="required"' : '';

                        $html .= '<div class="fv-file-wrapper">';
                        $html .= '<input type="file" name="' . $file_input_name . '" class="file-input" ' . $required . ' accept="image/*">' . "\n";
                        if ( isset($file_input_params['photo_name_input']) && $file_input_params['photo_name_input'] == true ) {
                            $html .= '<input type="text" placeholder="'. $placeholder .'" name="'. $file_input_name .'-name" class="form-control form-control-short foto-async-name" ' . $required . '>' . "\n";
                        }
                        $html .= '</div>';
                    endforeach;
                } else {
                    $html = apply_filters('fv/public/upload_form/custom_file_input', $html, $field, $contest);
                }
                break;
        }

        if ( !empty($field->field_options->description) ) {
            switch ($field->field_type) {

                case 'checkbox_multi':
                case 'radio':
                case 'select_multi':
                    $html .= '<span class="description">' . $field->field_options->description . '</span>';
                    break;
                /*case 'file':
                    break;*/
                default:
                    $html .= '<span class="description">' . $field->field_options->description . '</span>' . "\n";
                    break;
            }
        }

        return $html;
    }

    /**
     * Return file inputs array for Generate form and Saving data from form
     *
     * @param mixed $field
     * @return array ['name (string)'=>'required (string)']
     */
    public static function _get_file_inputs($field=false) {
        if ( $field === false ) {
            $field = self::_get_file_field_from_form_structure();
        }
        $inputs = array('foto-async-upload' => array('required'=>true, 'photo_name_input'=>!empty($field->field_options->multi_show_photo_name)) );
        if ( !empty($field->field_options->multi_upload) && isset($field->field_options->multi_count) && $field->field_options->multi_count > 1 ) {
            for ($N = 2; $field->field_options->multi_count >= $N; $N++) :
                $inputs['foto-async-upload-' . $N] = array( 'required'=>false, 'photo_name_input'=>!empty($field->field_options->multi_show_photo_name) );
            endfor;
        }
        return $inputs;
    }

    /**
     * Return file field from Form structure object
     *
     * @param mixed $form_structure
     * @return object
     */
    public static function _get_file_field_from_form_structure($form_structure=false) {
        if ( $form_structure === false ) {
            $form_structure = Fv_Form_Helper::get_form_structure_obj();
        }
        foreach($form_structure->fields as $field) {
            if ( $field->field_type == 'file' ) {
                return $field;
            }
        }
    }
}


add_filter('fv/public/upload_form/label', 'fv_filter_public_upload_form_label', 10, 4);

function fv_filter_public_upload_form_label ($label, $field, $c, $contest) {
    if ( $field->required ) {
        $rq = ' <span class="red_star">*</span>';
    } else {
        $rq = " *";
    }
    return '<div class="number">' . $c . '</div>' . $label . $rq;
}