<?php

/**
 * Simple debug class
 *
 * Check, if Debug enables, Output data, else add to Log
 *
 * @author МаксК / maxim-kaminsky.com
 * http://habrahabr.ru/post/134557/
 */

defined('ABSPATH') or die("No script kiddies please!");

if ( class_exists('FvDebug') ) {
    return;
}

class FvDebug {
    /**
     * Class instance.
     *
     * @since 2.2.084
     *
     * @var object
     */

    const LVL_NONE = 0;

    const LVL_CODE_VOTE = 1;   // 0001
    const LVL_CODE_UPLOAD = 2; // 0010
    const LVL_CODE_TPL = 4; // 0010

    const LVL_CODE = 8;                // 0100

    const LVL_MAIL = 32;            // 1000

    const LVL_SQL = 64;            // 1000
    const LVL_SQL_DATA = 128;            // 1000

    const LVL_ALL = 512;     // 11111

    public static function init_lvl() {

        if ( FvFunctions::ss('debug-vote', false) ) {
            FV::$DEBUG_MODE = FV::$DEBUG_MODE | self::LVL_CODE_VOTE;
        }

        if ( FvFunctions::ss('debug-upload', false) ) {
            FV::$DEBUG_MODE = FV::$DEBUG_MODE | self::LVL_CODE_UPLOAD;
        }

        if ( FvFunctions::ss('debug-sql', false) ) {
            FV::$DEBUG_MODE = FV::$DEBUG_MODE | self::LVL_SQL;
        }

        if ( FvFunctions::ss('log-emails', false) ) {
            FV::$DEBUG_MODE = FV::$DEBUG_MODE | self::LVL_MAIL;
        }
    }

    public static function add( $msg, $obg = '', $echo = true ) {
        if ( !defined('DOING_AJAX') && function_exists('dbgx_trace_var') ) {
            //, $msg
            dbgx_trace_var($obg);
        } else {
            FvLogger::addLog($msg, $obg);
        }
    }


    public static function addSql( $sql, $model_class ) {
        if ( FV::$DEBUG_MODE & self::LVL_SQL ) {
            self::add('sql - ' . $model_class, $sql);
        }
        if ( FV::$DEBUG_MODE & self::LVL_SQL_DATA ) {
            global $wpdb;
            self::add('sql result - ' . $model_class, $wpdb->last_result);
        }
        return $sql;
    }

}