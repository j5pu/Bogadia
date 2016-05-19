<?php
/**
 * Gallery style widget
 *
 * @link       http://wp-vote.net
 * @since      2.2.05
 *
 * @package    wp-foto-vote
 * @subpackage wp-foto-vote/includes/widget-gallery
 * @author    Maxim Kaminsky <wp-vote@hotmail.com>*
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// TODO: change 'Widget_Name' to the name of your plugin
class Widget_FV_Gallery extends WP_Widget
{

    /**
     *
     * Unique identifier for your widget.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    2.2.05
     *
     * @var      string
     */
    protected $widget_slug = 'widget-fv-gallery';

    /* -------------------------------------------------- */
    /* Constructor
      /*-------------------------------------------------- */

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct()
    {
        // TODO: update description
        parent::__construct(
            $this->get_widget_slug(), __('Photo contest gallery', $this->get_widget_slug()),
            array(
                'classname' => $this->get_widget_slug() . '-class',
                'description' => __('Shows list photos from selected contest.', $this->get_widget_slug())
            )
        );

        // Register admin styles and scripts
        //add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
        //add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
        // Register site styles and scripts
        //add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
        //add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );
        // Refreshing the widget's cached output with each new post
        //add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
        //add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
        //add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
    }

// end constructor

    /**
     * Return the widget slug.
     *
     * @since    2.2.05
     *
     * @return    Plugin slug variable.
     */
    public function get_widget_slug()
    {
        return $this->widget_slug;
    }

    /* -------------------------------------------------- */
    /* Widget API Functions
      /*-------------------------------------------------- */

    /**
     * Outputs the content of the widget.
     *
     * @param array args  The array of form elements
     * @param array instance The current instance of the widget
     */
    public function widget($args, $instance)
    {

        wp_enqueue_style($this->get_widget_slug() . '-widget-styles', plugins_url('wp-foto-vote/assets/css/fv_widget.css'));

        // Check if there is a cached output
        $cache = wp_cache_get( $this->get_widget_slug(), 'widget' );
        //$cache = '';

        if (!is_array($cache))
            $cache = array();

        if (!isset($args['widget_id']))
            $args['widget_id'] = $this->id;

        if (isset($cache[$args['widget_id']]))
            return print $cache[$args['widget_id']];

        // go on with your widget logic, put everything into a string and …


        extract($args, EXTR_SKIP);

        $widget_string = $before_widget;

        // TODO: Here is where you manipulate your widget's values based on their input fields
        ob_start();
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Photo contest', 'fv') : $instance['title']);
        $contest_id = empty($instance['contest_id']) ? 'false' : (int)$instance['contest_id'];
        $shows_count = empty($instance['shows_count']) ? '3' : (int)$instance['shows_count'];
        $shows_sort = empty($instance['shows_sort']) ? 'popular' : sanitize_text_field($instance['shows_sort']);
        $show_photo_size = empty($instance['show_photo_size']) ? '1/3' : $instance['show_photo_size'];
        $link = empty($instance['link']) ? '' : sanitize_text_field($instance['link']);
        switch ($show_photo_size) {
            case '1/1':
                $block_size = '100%';
                break;
            case '1/3':
                $block_size = '33.2%';
                break;
            case '1/4':
                $block_size = '25%';
                break;
            default:
                $block_size = '50%';
                break;
        }


        if ($contest_id) {
            $my_db = new FV_DB;
            $r = $my_db->getCompItems($contest_id, ST_PUBLISHED, $shows_count, 1, $shows_sort);
            include plugin_dir_path(__FILE__) . 'views/widget.php';
        }
        $widget_string .= ob_get_clean();
        $widget_string .= $after_widget;


        $cache[$args['widget_id']] = $widget_string;

        wp_cache_set($this->get_widget_slug(), $cache, 'widget');

        print $widget_string;
    }

// end widget

    public function flush_widget_cache()
    {
        wp_cache_delete($this->get_widget_slug(), 'widget');
    }

    /**
     * Processes the widget's options to be saved.
     *
     * @param array new_instance The new instance of values to be generated via the update.
     * @param array old_instance The previous instance of values before the update.
     */
    public function update($new_instance, $old_instance)
    {
        $this->flush_widget_cache();

        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['contest_id'] = isset($new_instance['contest_id']) ? (int)$new_instance['contest_id'] : '';
        $instance['shows_count'] = isset($new_instance['shows_count']) ? (int)$new_instance['shows_count'] : '';
        $instance['shows_sort'] = isset($new_instance['shows_sort']) ? strip_tags($new_instance['shows_sort']) : '';
        $instance['show_photo_size'] =isset($new_instance['show_photo_size']) ?  strip_tags($new_instance['show_photo_size']) : '';
        $instance['link'] = isset($new_instance['link']) ? strip_tags($new_instance['link']) : '';
        //$instance['design'] = strip_tags($new_instance['design']);

        return $instance;
    }

// end widget

    /**
     * Generates the administration form for the widget.
     *
     * @param array instance The array of keys and values for the widget.
     */
    public function form($instance)
    {

        // TODO: Define default values for your variables
        $instance = wp_parse_args((array)$instance, array(
                'title' => __('Contest', 'fv'),
                'contest_id' => false,
                'shows_count' => '3',
                'shows_sort' => 'popular',
                'show_photo_size' => '1/2',
                'link' => '',
                //'design' => 'default'
            )
        );
        // TODO: Store the values of the widget in their own variable
        foreach ($instance as $key => $value) {
            $instance[$key] = esc_attr($value);
        }
        // exract array into varibales
        extract($instance, EXTR_SKIP);

        $contest_list = ModelContest::query()->find();
        // Display the admin form
        include plugin_dir_path(__FILE__) . 'views/admin.php';
    }

// end form

    /* -------------------------------------------------- */
    /* Public Functions
      /*-------------------------------------------------- */

    /**
     * Registers and enqueues admin-specific styles.
     */
    public function register_admin_styles()
    {

        //wp_enqueue_style($this->get_widget_slug() . '-admin-styles', plugins_url('css/admin.css', 'wp-foto-vote'));
    }

// end register_admin_styles

    /**
     * Registers and enqueues admin-specific JavaScript.
     */
    public function register_admin_scripts()
    {

        //wp_enqueue_script( $this->get_widget_slug().'-admin-script', plugins_url( 'js/admin.js', 'wp-foto-vote' ), array('jquery') );
    }

// end register_admin_scripts

    /**
     * Registers and enqueues widget-specific styles.
     */
    public function register_widget_styles()
    {

    }

// end register_widget_styles

    /**
     * Registers and enqueues widget-specific scripts.
     */
    public function register_widget_scripts()
    {

        //wp_enqueue_script($this->get_widget_slug() . '-script', plugins_url('wp-foto-vote/js/fv-widget.js'), array('jquery'));
    }

// end register_widget_scripts
}

// end class
add_action('widgets_init', create_function('', 'register_widget("Widget_FV_Gallery");'));
