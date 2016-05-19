<?php

defined('ABSPATH') or die("No script kiddies please!");
/**
 * The ajax functionality of the plugin.
 *
 * @package    FV
 * @subpackage FV/admin
 * @author     Maxim K <wp-vote@hotmail.com>
 */

class FV_Admin_Export
{

    /**
     * Run export
     * Check permissions
     *
     * @params $_POST['type'] int
     *
     * @return void
    */
    public static function run()
    {
        try {
                if ( !isset($_GET['type'])  ) {
                    die ( "incorrect type" );
                }
                if ( !FvFunctions::curr_user_can() || !check_ajax_referer('fv_export_nonce', 'fv_nonce', false) ) {
                    FvLogger::addLog("FV_Admin_Export::run - security error");
                    return;
                }

                $type = sanitize_title( $_GET['type'] );

                //$photo = ModelCompetitors::query()->where_all( array('id'=>(int)$_POST['photo_id'], 'contest_id'=>(int)$_POST['contest_id'] ) )->findRow();

                switch( $type ){
                    case 'contest_data':
                        self::export_contest_data();
                        break;
                    case 'log_list':
                        self::export_log_list();
                        break;
                    case 'subscribers_list':
                        self::export_subscribers_list();
                        break;
                    default:
                        do_action('fv/admin/export_data/custom', $type);
                        break;
                }

        } catch(Exception $ex) {
            //FvDebug::go("FV_Admin_Export::run - some error ", $ex);
        }

    }

    public static function export_contest_data(  ) {
        if ( isset($_GET["contest_id"]) ) {
            $contest_id = $_GET["contest_id"];
        }else {
            FvLogger::addLog("export_contest_data error - no contest_id");
            wp_die("Error!");
        }

        $filename = 'fv_contest_' . $contest_id . '_data.csv';
        self::output_header($filename);
        $fp= fopen('php://output', 'w');

        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        $data = ModelCompetitors::query()->where("contest_id", $contest_id)->find();

        $earr = array('Photo name', 'Description', 'Photo full url', 'User email', 'Votes count', 'Upload info', 'Added date', 'User id', 'User ip','Status');
        fputcsv( $fp, $earr, get_option('fv-export-delimiter', ';') );

        foreach ($data as $fields)
        {
            $earr = array(
                $fields->name,
                $fields->description,
                $fields->url,
                $fields->user_email,
                $fields->votes_count,
                FvFunctions::showUploadInfo($fields->upload_info),
                date("Y-m-d H:i", $fields->added_date),
                $fields->user_id,
                $fields->user_ip,
                fv_get_status_name($fields->status)
            );
            fputcsv( $fp, $earr, get_option('fv-export-delimiter', ';') );
        }
        unset($data);

        fclose($fp);
        exit();
    }


    public static function export_log_list(  ) {
        $datefrom = 0;
        if (isset($_GET['period'])) {
            $datefrom = (int)$_GET['period'];
        }

        $filename = 'fv_log_' . date('d-m-y') . '-from_' . $datefrom . '_days.csv';
        self::output_header($filename);
        $fp= fopen('php://output', 'w');

        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // получаем первый массив
        $my_db = new FV_DB;

        $stats = $my_db->getVoteStats(999, $datefrom);

        //fputcsv($fp, array('тест 12', 'тест 2'), ';');
        $earr = array('contest_id', 'contest_name', 'ip', 'country', 'changed' ,'competitor_id' ,'competitor_name', 'WP user id' ,'browser', 'b_plugins' ,'referer' ,'Fraud score' ,'soc_profile', 'name', 'email');
        fputcsv($fp, $earr, ';');

        foreach ($stats['r'] as $fields)
        {
            $earr = array($fields->contest_id, $fields->contest_name, $fields->ip, $fields->country, $fields->changed, $fields->vote_id, $fields->competitor_name, $fields->user_id, $fields->browser, $fields->b_plugins, $fields->referer,  $fields->score, $fields->soc_profile, $fields->name, $fields->email);
            fputcsv($fp, $earr, ';');

        }
        unset($data);
        // если еще есть товары, идем дальше, иначе выходим

        fclose($fp);
        exit();
    }


    public static function export_subscribers_list(  ) {
        $datefrom = 0;
        if (isset($_GET['period'])) {
            $datefrom = (int)$_GET['period'];
        }

        $filename = 'fv_subscribers_' . date('d-m-y') . '-from_' . $datefrom . '_days.csv';
        self::output_header($filename);
        $fp= fopen('php://output', 'w');

        //add BOM to fix UTF-8 in Excel
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // получаем первый массив
        $my_db = new FV_DB;

        $stats = $my_db->getSubsrStats(999, $datefrom);

        //fputcsv($fp, array('тест 12', 'тест 2'), ';');
        $earr = array('name', 'email');
        fputcsv($fp, $earr, ';');

        foreach ($stats['r'] as $fields)
        {
            if ($fields->email){
                $earr = array($fields->name, $fields->email);
                fputcsv($fp, $earr, ';');
            }
        }
        unset($data);
        // если еще есть товары, идем дальше, иначе выходим

        fclose($fp);
        exit();
    }


    public static function output_header( $filename )
    {
        header( "Content-Type: text/csv;charset=utf-8" );
        header( "Content-Disposition: attachment;filename=\"$filename\"" );
        header( "Content-Transfer-Encoding: binary" );
        header( "Pragma: no-cache" );
        header( "Expires: 0" );
    }

}
