<?php

/*
 * Class for work with DB
 * wp-foto-vote
 *
 * @since 1.1
 */

defined('ABSPATH') or die("No script kiddies please!");

class FV_DB
{

        private $table_contests_name;
        private $table_competitors_name;
        private $table_votes_name;

        function __construct()
        {
                global $wpdb;

                $this->table_contests_name = $wpdb->prefix . "fv_contests";
                $this->table_competitors_name = $wpdb->prefix . "fv_competitors";
                $this->table_votes_name = $wpdb->prefix . "fv_votes";
                $table_subscr_name = $wpdb->prefix . "fv_subscribers";
        }

        public function clearAllData()
        {
                /*
                  if ( !defined('WP_UNINSTALL_PLUGIN') ) {
                  exit();
                  }
                 */
                global $wpdb;

                $sql = "DROP TABLE IF EXISTS `" . $this->table_contests_name . "`;";
                $wpdb->query($sql);
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

                $sql = "DROP TABLE IF EXISTS `" . $this->table_competitors_name . "`;";
                $wpdb->query($sql);
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

                $sql = " DROP TABLE IF EXISTS `" . $this->table_votes_name . "`;";
                $wpdb->query($sql);
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

                FvLogger::checkDbErrors();
                FvLogger::addLog('All tables deleted from database!');
                delete_option("fv_db_version");

                // reinstall tables
                $this->install();
        }

        /* ================================================ */

        /**
         * Get one record from table contests.
         * Use non-persistent cache (save results duplicates sql queries)
         *
         * @param int $contest_id Contest id
         * @return object Contest data
         */
        public function getContest($contest_id)
        {
                global $wpdb;
                //$wpdb->get_results('query', ARRAY_A);

                if (!$r = wp_cache_get($contest_id, 'getContest')) {
                        $r = $wpdb->get_row(
                            "SELECT *
						FROM {$this->table_contests_name} as c
						WHERE `id` = '$contest_id'; #getContest", OBJECT
                        );
                        FvLogger::checkDbErrors($r);
                        wp_cache_add($contest_id, $r, 'getContest');
                }
                return $r;
        }


        /**
         * getContests
         *
         * Return array of contest as OBJECT
         *
         * @param array $args ['status'=>'*', 'onpage'=>10, 'page'=>0]
         */
        public function getContests($args)
        {
                global $wpdb;

                //* Define the array of defaults
                $defaults = array(
                    'status' => '*',
                    'onpage' => 10,
                    'page' => 0,
                );
                //* Parse incomming $args into an array and merge it with $defaults
                extract(wp_parse_args($args, $defaults), EXTR_SKIP);

                //$wpdb->get_results('query', ARRAY_A);

                $sql = "SELECT contests.*,
                    COUNT(competitors.id) as competitors_count,
                    (SELECT COUNT(*) FROM {$this->table_competitors_name} "
                    . "WHERE {$this->table_competitors_name}.status = '1' AND contests.id = {$this->table_competitors_name}.contest_id  ) as competitors_count_onmoderation,
                    SUM(competitors.`votes_count`) as votes_count_summary
                FROM {$this->table_contests_name} as contests            
                LEFT JOIN {$this->table_competitors_name} as competitors ON contests.id = competitors.contest_id
                GROUP BY contests.`name`, contests.`created` ";

                //. "LEFT JOIN {$this->table_competitors_name} as vote ON vote.id = vote_ip.vote_id "

                if ($onpage > 0 && $page >= 1) {
                        //  LIMIT [offset,] rows]
                        $sql .= " LIMIT " . (($page - 1) * $onpage) . ", " . $onpage;
                        //var_dump($sql);
                }

                $r = $wpdb->get_results($sql);

                $r2 = array();
                foreach ($r as $res) {
                        $r2[$res->id] = $res;
                }
                //var_dump($r2);

                FvLogger::checkDbErrors($r);
                return $r2;
        }

        public function getContestsCount($status = '*')
        {
                global $wpdb;
                // вернем количество результатов для пагинации
                $sql = "SELECT COUNT(id) FROM {$this->table_contests_name};";
                // Если указана выборка по статусам
                if ($status !== '*' && !is_array($status)) {
                        $sql .= " AND `status` = '{$status}'";
                } elseif (is_array($status)) {
                        foreach ($status as $key => $value) {
                                $sql .= " OR `status` = '{$value}'";
                        }
                }

                $r = $wpdb->get_var($sql);

                FvLogger::checkDbErrors($r);
                return $r;
        }

        /*
         * удалем из бд запись
         */

        public function deleteContest($id)
        {
                global $wpdb;
                //$wpdb->get_results('query', ARRAY_A);
                $r = $wpdb->query(
                    $sql = $wpdb->prepare(
                        "
                    DELETE FROM {$this->table_contests_name}
                     WHERE id = %d
                    ", $id
                    )
                );

                return $r;
        }

        /* ================================================ */

        public function getCompItems($contest_id, $status = '*', $onpage = 0, $page = 1, $order_type = 'oldest')
        {
                global $wpdb;

                if (empty($contest_id)) {
                        return false;
                }

                //$wpdb->get_results('query', ARRAY_A);

                $sql = "SELECT *
                FROM {$this->table_competitors_name}
                WHERE `contest_id` = '$contest_id'
                ";
                // Если указана выборка по статусам
                if ($status !== '*' && !is_array($status)) {
                        $sql .= " AND `status` = '{$status}'";
                } elseif (is_array($status)) {
                        foreach ($status as $key => $value) {
                                $sql .= " OR `status` = '{$value}'";
                        }
                }

                if ($order_type) {
                        // ORDER BY field + direction
                        $sql .= $this->getCompOrderFiled($order_type);
                }

                if ($page !== false && $onpage > 0 && $page >= 1) {
                        //  LIMIT [offset,] rows]
                        $sql .= " LIMIT " . (($page - 1) * $onpage) . ", " . $onpage;
                        //var_dump($sql);
                }

                $sql = apply_filters( 'fv/public/show_contest/get_comp_items_query', $sql);

                //var_dump($sql);

                $r = $wpdb->get_results($sql . "; #getCompItems");
                $r2 = array();
                foreach ($r as $res) {
                        $r2[$res->id] = $res;
                }
                //var_dump($r2);

                FvLogger::checkDbErrors($r);
                return $this->unsplashe($r2);
        }

        public function getCompItemsOnModeration()
        {
                global $wpdb;

                $sql = sprintf("SELECT `competitors`.*, `contests`.`name` as contest_name FROM `%s` as `competitors` LEFT JOIN `%s` as `contests` ON `contests`.`id` = `competitors`.`contest_id` WHERE `competitors`.`status` = '%d' ORDER BY `competitors`.`added_date` DESC;", $this->table_competitors_name, $this->table_contests_name, ST_MODERAION);

                $r = $wpdb->get_results($sql);
                $r2 = array();
                foreach ($r as $res) {
                        $r2[$res->id] = $res;
                }

                FvLogger::checkDbErrors($r);
                return $this->unsplashe($r2);
        }

        public function getCompItemsOnModerationCount()
        {
                global $wpdb;

                $sql = sprintf("SELECT COUNT(`id`) FROM `%s` WHERE `status` = '%d'", $this->table_competitors_name, ST_MODERAION);

                $r = $wpdb->get_var($sql);

                FvLogger::checkDbErrors();
                return intval($r);
        }

        public function getCompItemsCount($contest_id, $status = '*')
        {
                global $wpdb;
                // вернем количество результатов для пагинации
                $sql = "SELECT COUNT(id)
                FROM {$this->table_competitors_name}
                WHERE `contest_id` = '$contest_id'
                ";
                // Если указана выборка по статусам
                if ($status !== '*' && !is_array($status)) {
                        $sql .= " AND `status` = '{$status}'";
                } elseif (is_array($status)) {
                        foreach ($status as $key => $value) {
                                $sql .= " OR `status` = '{$value}'";
                        }
                }

                $sql = apply_filters( 'fv/public/show_contest/get_comp_items_count_query', $sql);

                $r = $wpdb->get_var($sql  . "; #getCompItemsCount");

                FvLogger::checkDbErrors($r);
                return $r;
        }

        /**
         *  get most voted items in contest
         *
         * @param int $contest_id
         * @param int $limit
         * @return array    Most voted photos
         */
        public function getMostVotedItems($contest_id, $limit = 3)
        {
                global $wpdb;

                $r = $wpdb->get_results(
                    "
                SELECT * 
                FROM {$this->table_competitors_name}
                WHERE `contest_id` = '{$contest_id}' AND `status` = '" . ST_PUBLISHED  .
                "' ORDER BY votes_count DESC
                LIMIT {$limit};  #getMostVotedItems
                "
                );

                $r2 = array();
                foreach ($r as $res) {
                        $r2[$res->id] = $res;
                }

                FvLogger::checkDbErrors($r2);
                return $r2;
        }

        public function getCompItem($id, $contest_id = 0)
        {
                global $wpdb;

                $contest_id = (int)$contest_id;
                $and = '';
                if ($contest_id > 0) {
                        $and = " AND `contest_id` = '$contest_id' ";
                }

                $r = $wpdb->get_row(
                    "SELECT *
                    FROM {$this->table_competitors_name}
                    WHERE `id` = '{$id}' {$and};  #getCompItem", OBJECT
                );

                FvLogger::checkDbErrors($r);
                return $this->unsplashe($r);
        }

        /*
         * увеличивем счетчик
         */

        public function increaseItemCountVotes($id)
        {
            // $votes_count_old used for check, that we work with latest VOTES ID
                global $wpdb;

                //$wpdb->get_results('query', ARRAY_A);
                /*$r = $wpdb->update(
                    $this->table_competitors_name,
                    array(
                        'votes_count' => $votes_count_new,
                    ),
                    array(
                        'id' => $id,
                        'votes_count'=> '`votes_count` + 1'
                    ),
                    array(
                        '%d',
                    ),
                    array('%d')
                );
                */

                $r = $wpdb->query(
                    'UPDATE ' . $this->table_competitors_name .
                    ' SET `votes_count` = `votes_count` + 1' .
                    ' WHERE `id` = ' . intval($id) . ';'
                );

                FvLogger::checkDbErrors($r);
                return $r;
    }

        /*
         * удалем из бд запись
         */

        public function deleteCompItem($id)
        {

                global $wpdb;
                //$wpdb->get_results('query', ARRAY_A);
                $r = $wpdb->query(
                    $sql = $wpdb->prepare(
                        "
                    DELETE FROM {$this->table_competitors_name}
                     WHERE id = %d
                    ", $id
                    )
                );
                FvLogger::checkDbErrors($r);
                return $r;
        }

        /*
         * удалем из бд запись
         */

        public function approveCompItem($id)
        {

                global $wpdb;
                //$wpdb->get_results('query', ARRAY_A);
                $r = $wpdb->query(
                    $sql = $wpdb->prepare(
                        "
                    UPDATE {$this->table_competitors_name}
                    SET status = %d
                    WHERE id = %d
                    ", ST_PUBLISHED, $id
                    )
                );
                FvLogger::checkDbErrors($r);
                return $r;
        }

        /*
         * удалем из бд записи
         */

        public function deleteCompItems($contest_id)
        {
                global $wpdb;
                //var_dump($contest_id);
                //$wpdb->get_results('query', ARRAY_A);
                $r = $wpdb->query(
                    $sql = $wpdb->prepare(
                        "
                    DELETE FROM {$this->table_competitors_name}
                     WHERE contest_id = %d
                    ", $contest_id
                    )
                );
                FvLogger::checkDbErrors($r);
                return $r;
        }

        /*
         * Returns ips
         * var $ip
         * var $date_start - unixTimestamp
         */

        public function getIpInfo($ip = false, $uid, $date_start = 0, $contest_id = 0, $social = false, $user_id = false)
        {
                global $wpdb;

                $where2 = '';
                if (is_array($social) && count($social) == 2) {
                        $where2 = " OR (`soc_network` = '" . $social['soc_network'] . "' AND `soc_uid` = '" . $social['soc_uid'] . "' )";
                }

                $where = '';
                if ($ip && $uid) {
                        $where = "( `ip` = '{$ip}' OR `uid` = '{$uid}' {$where2} )";
                } else {
                        $where = "( `uid` = '{$uid}' {$where2} )";
                }
                if ($date_start > 0) {
                        $date_start = date("Y-m-d H:i:s", (int)$date_start);
                        $where .= ' AND date(`changed`) >= date(\'' . $date_start . '\')';
                }
                if ($contest_id > 0) {
                        $where .= ' AND `contest_id` = ' . (int)$contest_id;
                }

                $check_ip = $wpdb->get_results("SELECT * FROM {$this->table_votes_name} WHERE {$where}; #getIpInfo");
                if (isset($check_ip)) {
                        foreach ($check_ip as $k => $ip) {
                                $check_ip[$k]->changed = strtotime($ip->changed);
                        }
                }
                FvLogger::checkDbErrors();
                return $check_ip;
        }



    public function getSubsrStats($page = 0, $datefrom = 0)
    {
        global $wpdb;
        // выбере результаты постраничу по 15 на страницу
        $start = FV_RES_OP_PAGE * $page;
        if ($page = 999) {
            $start = 0;
            $count = 4999;
        } else {
            $count = FV_RES_OP_PAGE;
        }

        $where = '';
        if ($datefrom > 0) {
            $where = 'AND date(`changed`) >= date(now()-interval ' . $datefrom . ' day)';
        }
        $res['r'] = $wpdb->get_results("SELECT * FROM {$this->table_votes_name} WHERE `email` <> '' " . $where . " LIMIT {$start}, " . $count);
        // вернем количество результатов для пагинации
        $res['count_rows'] = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_votes_name} WHERE `email` <> ''");

        FvLogger::checkDbErrors($res);
        return $res;
    }

    public function getVoteStats($page = 1, $datefrom = 0, $contest_id = false, $orderby = false, $order = 'ASC', $contestant_id = 0)
        {
                global $wpdb;
                // выбере результаты постраничу по 15 на страницу
                $start = FV_RES_OP_PAGE * ($page - 1);
                if ($page == 999) {
                        $start = 0;
                        $count = 4999;
                } else {
                        $count = FV_RES_OP_PAGE;
                }

                $where_arr = array();
                if ($datefrom > 0) {
                        $where_arr[] = sprintf("date(`changed`) >= date(now()-interval %s day)", $datefrom);;
                }
                if ($contest_id > 0) {
                        $where_arr[] = sprintf("`votes`.`contest_id` = '%d'", (int)$contest_id);
                }
                if ($contestant_id > 0) {
                        $where_arr[] = sprintf("`votes`.`vote_id` = '%d'", (int)$contestant_id);
                }
                $where_sql = $this->generateWhereSQL($where_arr);

                $order_sql = '';
                if ($orderby && $order) {
                        $order_sql = sprintf(" ORDER BY `votes`.`%s` %s", $orderby, $order);
                }

                $res['r'] = $wpdb->get_results("SELECT votes.*, contests.name as contest_name, competitors.name as competitor_name FROM {$this->table_votes_name} as votes "
                    . "LEFT JOIN {$this->table_contests_name} as contests ON votes.contest_id = contests.id "
                    . "LEFT JOIN {$this->table_competitors_name} as competitors ON votes.vote_id = competitors.id "
                    . $where_sql . $order_sql . " LIMIT {$start}, " . $count);

                $res['r'] = $this->unsplashe($res['r']);

                // вернем количество результатов для пагинации
                $res['count_rows'] = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_votes_name} as votes {$where_sql};");;

                FvLogger::checkDbErrors();
                return $res;
        }


        public function clearVoteStats($contest_id)
        {
                global $wpdb;
                // очистим все записи по голосованию
                $r = $wpdb->query(
                    $sql = $wpdb->prepare(
                        "
                    DELETE FROM {$this->table_votes_name}
                     WHERE contest_id = %d
                    ", $contest_id
                    )
                );

                FvLogger::checkDbErrors($r);
                return $r;
        }

        /*
         * Get all Photos from one contest for math Next and Prev photos ID
         */
        public function getCompItemsNav($contest_id, $order)
        {
                global $wpdb;
                //$wpdb->get_results('query', ARRAY_A);

                $r = $wpdb->get_results(
                    "SELECT `id`
                FROM {$this->table_competitors_name}               
                WHERE `contest_id` = '{$contest_id}' " . $this->getCompOrderFiled($order) . ";"
                );
                FvLogger::checkDbErrors();

                return $r;
        }

        private function getCompOrderFiled($order_type)
        {
                $order = ' ORDER BY';
                switch ($order_type) {
                        case 'newest':
                                $order .= ' `added_date` DESC ';
                                break;
                        case 'oldest':
                                $order .= ' `added_date` ASC ';
                                break;
                        case 'popular':
                                $order .= ' `votes_count` DESC ';
                                break;
                        case 'unpopular':
                                $order .= ' `votes_count` ASC ';
                                break;
                        case 'random':
                                $order .= ' RAND() ';
                                break;
                        case 'alphabetical-az':
                                $order .= ' `name` ASC ';
                                break;
                        case 'alphabetical-za':
                                $order .= ' `name` DESC ';
                                break;
                        default:
                                $order .= ' `added_date` ASC ';
                                break;
                }
                return $order;
        }

        /*
         * generateWhereSQL
         */

        private function generateWhereSQL($where_arr)
        {
                $where_sql = '';
                if (count($where_arr) > 0) {
                        $where_sql = 'WHERE ' . $where_arr[0];
                        unset($where_arr[0]);
                        foreach ($where_arr as $where_string) {
                                $where_sql .= 'AND ' . $where_string;
                        }
                }
                return $where_sql;
        }

        private function checkRequiredVariables($args, $required)
        {
                foreach ($required as $key) {
                        if (array_key_exists($key, $args)) {
                                return false;
                        }
                }
                return true;
        }

        private function unsplashe($data)
        {
                if (is_object($data)) {
                        if (isset($data->name)) {
                                $data->name = stripslashes($data->name);
                        }
                        if (isset($data->description)) {
                                $data->description = stripslashes($data->description);
                        }
                        if (isset($data->full_description)) {
                                $data->full_description = stripslashes($data->full_description);
                        }
                        if (isset($data->additional)) {
                                $data->additional = stripslashes($data->additional);
                        }
                } elseif (is_array($data)) {
                        foreach ($data as $item) {
                                if (isset($item->name)) {
                                        $item->name = stripslashes($item->name);
                                }
                                if (isset($item->description)) {
                                        $item->description = stripslashes($item->description);
                                }
                                if (isset($item->full_description)) {
                                    $item->full_description = stripslashes($item->full_description);
                                }
                                if (isset($item->additional)) {
                                        $item->additional = stripslashes($item->additional);
                                }
                        }
                }
                return $data;
        }

}

function fv_unsplashe($data)
{
        if (is_object($data)) {
            if (isset($data->name)) {
                $data->name = stripslashes($data->name);
            }
            if (isset($data->description)) {
                $data->description = stripslashes($data->description);
            }
            if (isset($data->full_description)) {
                $data->full_description = stripslashes($data->full_description);
            }
            if (isset($data->additional)) {
                $data->additional = stripslashes($data->additional);
            }
        } elseif (is_array($data)) {
            foreach ($data as $item) {
                if (isset($item->name)) {
                    $item->name = stripslashes($item->name);
                }
                if (isset($item->description)) {
                    $item->description = stripslashes($item->description);
                }
                if (isset($item->full_description)) {
                    $item->full_description = stripslashes($item->full_description);
                }
                if (isset($item->additional)) {
                    $item->additional = stripslashes($item->additional);
                }
            }
        }
        return $data;
}


class ModelContest extends FvQuery
{

        /**
         * Returns the static query of the specified class.
         * @param string $className active record class name.
         * @return FvModel the static query class
         */
        public static function query($className = __CLASS__)
        {
                return new $className();
        }

        public function tableName()
        {
                global $wpdb;
                return $wpdb->prefix . "fv_contests";
        }

        public function fields()
        {
                return array(
                        //'id' => '%d',
                    'name' => '%s',
                    'date_start' => '%s',
                    'date_finish' => '%s',
                    'upload_date_start' => '%s',
                    'upload_date_finish' => '%s',
                    'soc_title' => '%s',
                    'soc_description' => '%s',
                    'soc_picture' => '%s',
                    'user_id' => '%d',
                    'upload_enable' => '%d',
                    'security_type' => '%s',
                    'voting_frequency' => '%s',
                    'max_uploads_per_user' => '%d',
                    'status' => '%d',
                    'show_leaders' => '%d',
                    'lightbox_theme' => '%s',
                    'upload_theme' => '%s',
                    'timer' => '%s',
                    'sorting' => '%s',
                    'moderation_type' => '%s',
                    'page_id' => '%d',
                    'cover_image' => '%d',
                    'type' => '%d',
                );
        }

        public function install() {
            //! More - http://wordpress.stackexchange.com/a/78670
            $sql = "CREATE TABLE " . $this->tableName() . " (
                   id int(7) NOT NULL AUTO_INCREMENT,
                   created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                   date_start TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:00',
                   date_finish TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:000',
                   upload_date_start TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:00',
                   upload_date_finish TIMESTAMP NOT NULL DEFAULT '2015-01-01 01:00:00',
                   name varchar(255) NOT NULL,
                   soc_title varchar(255) NOT NULL,
                   soc_description varchar(255) NOT NULL,
                   soc_picture varchar(255) NOT NULL,
                   user_id int(7) DEFAULT '0',
                   upload_enable int(3) NOT NULL DEFAULT '0',
                   security_type varchar(20) NOT NULL DEFAULT 'default',
                   voting_frequency varchar(20) NOT NULL DEFAULT 'onceFall',
                   show_leaders int(3) NOT NULL DEFAULT '0',
                   lightbox_theme varchar(25) NOT NULL DEFAULT 'imageLightbox_default',
                   upload_theme varchar(25) NOT NULL DEFAULT 'default',
                   timer varchar(15) NOT NULL DEFAULT 'no',
                   sorting varchar(15) NOT NULL DEFAULT 'newest',
                   moderation_type varchar(10) NOT NULL DEFAULT 'pre',
                   max_uploads_per_user int(5) NOT NULL DEFAULT '0',
                   page_id INT (8) DEFAULT NULL,
                   cover_image INT(5) DEFAULT NULL,
                   type INT(2) DEFAULT 0,
                   status INT( 2 ) NOT NULL DEFAULT '0',
                   PRIMARY KEY  (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            FvLogger::checkDbErrors();
        }

}

class ModelVotes extends FvQuery
{

        /**
         * Returns the static query of the specified class.
         * @param string $className active record class name.
         * @return FvModel the static query class
         */
        public static function query($className = __CLASS__)
        {
                return new $className();
        }

        public function tableName()
        {
                global $wpdb;
                return $wpdb->prefix . "fv_votes";
        }

        public function fields()
        {
                return array(
                    'contest_id' => '%d',
                    'post_id' => '%d',
                    'ip' => '%s',
                    'uid' => '%s',
                    'score' => '%d',
                    'changed' => '%s',
                    'vote_id' => '%d',
                    'browser' => '%s',
                    'display_size' => '%s',
                    'b_plugins' => '%s',
                    'b_fonts' => '%s',
                    'referer' => '%s',
                    'os' => '%s',
                    'country' => '%s',
                    'name' => '%s',
                    'email' => '%s',
                    'hash' => '%s',
                    'user_id' => '%d',
                    'soc_network' => '%s',
                    'soc_uid' => '%s',
                    'soc_profile' => '%s',
                    'fb_pid' => '%s',
                );
        }

        public function install() {

            $sql = "CREATE TABLE " . $this->tableName() . " (
                    id int(16) NOT NULL AUTO_INCREMENT,
                    contest_id int(10) NOT NULL,
                    post_id int(10) NOT NULL,
                    vote_id int(5) NOT NULL,
                    ip varchar(45) NOT NULL,
                    uid varchar(25) NOT NULL,
                    score int(4) NOT NULL,
                    changed TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    browser VARCHAR( 255 ) NULL DEFAULT NULL,
                    display_size VARCHAR( 50 ) NULL DEFAULT NULL,
                    b_plugins VARCHAR( 80 ) NULL DEFAULT NULL,
                    b_fonts VARCHAR( 80 ) NULL DEFAULT NULL,
                    referer VARCHAR( 500 ) NULL,
                    os VARCHAR( 40 ) NULL,
                    country VARCHAR( 30 ) NULL,
                    name VARCHAR( 50 ) NULL,
                    email VARCHAR( 60 ) NULL,
                    user_id VARCHAR( 50 ) NULL,
                    soc_network VARCHAR( 50 ) NULL,
                    soc_uid VARCHAR( 50 ) NULL,
                    soc_profile VARCHAR( 255 ) NULL,
                    fb_pid VARCHAR( 255 ) NULL,
                    hash VARCHAR( 10 ) NULL,
                    PRIMARY KEY  (id),
                    KEY ip (ip),
                    KEY uid (uid),
                    KEY contest_id_a_changed (contest_id,changed),
                    KEY vote_id (vote_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            FvLogger::checkDbErrors();
        }

}

class ModelCompetitors extends FvQuery
{

        /**
         * Returns the static query of the specified class.
         * @param string $className active record class name.
         * @return FvModel the static query class
         */
        public static function query($className = __CLASS__)
        {
                return new $className();
        }


        public function tableName()
        {
                global $wpdb;
                return $wpdb->prefix . "fv_competitors";
        }

        public function fields()
        {
                return array(
                    'id' => '%d',
                    'name' => '%s',
                    'description' => '%s',
                    'full_description' => '%s',
                    'social_description' => '%s',
                    'additional' => '%s',
                    'url' => '%s',
                    'url_min' => '%s',
                    'options' => '%s',
                    'image_id' => '%d',
                    'contest_id' => '%d',
                    'votes_count' => '%d',
                    'status' => '%d',
                    'added_date' => '%s',
                    'upload_info' => '%s',
                    'user_email' => '%s',
                    'user_id' => '%d',
                    'user_ip' => '%s',
                );

        }

        /**
         * find records in table by Primary KEY and Unserialize options
         *
         * @param   $id             int
         * @param   $from_cache     bool
         *
         * @return        object
         */
        public function findByPK($id, $from_cache = false) {
            $photo = parent::findByPK($id, $from_cache = false);
            $photo->options = FvFunctions::getContestOptionsArr($photo->options);

            return fv_unsplashe($photo);
        }

    /**
         * Compose & execute our query and Unserialize options
         *
         * @return OBJECT row
         */
        public function findRow() {
            $photo = parent::findRow();
            if ( !empty($photo) ) {
                $photo->options = FvFunctions::getContestOptionsArr($photo->options);
            }

            return $photo;
        }

    /**
     * Compose & execute our query and Unserialize options
     *
     * @param  boolean $only_count  Whether to only return the row count
     * @param  boolean $get_var
     * @param  boolean $for_list    Is this query for "Show_contest" function ?
     *
     * @return array
     */
    public function find($only_count = false, $get_var = false, $for_list = false) {
        $res = parent::find($only_count, $get_var);
        if ( !$only_count ) {
            foreach ($res as $photo) {
                if ( isset($photo->options) ) {
                    $photo->options = FvFunctions::getContestOptionsArr($photo->options);
                }
            }

        }
        // Create array as ID => DATA Array
        if ( $for_list && !empty($res) ) {
            $r2 = array();
            foreach ($res as $res) {
                $r2[$res->id] = $res;
            }
            $res = $r2;
            unset($r2);
        }

        return fv_unsplashe($res);
    }

    /**
     * Set query ORDER BY based on contest "sorting" field value
     *
     * @param  string $contest_order
     *
     * @return self FvQuery
     */
    public function set_sort_by_based_on_contest($contest_order) {
        switch ($contest_order) {
            case 'newest':
                $this->sort_by('added_date', $this::ORDER_DESCENDING);
                break;
            case 'oldest':
                $this->sort_by('added_date', $this::ORDER_ASCENDING);
                break;
            case 'popular':
                $this->sort_by('votes_count', $this::ORDER_DESCENDING);
                break;
            case 'unpopular':
                $this->sort_by('votes_count', $this::ORDER_ASCENDING);
                break;
            case 'random':
                $this->sort_by(' RAND() ', $this::ORDER_ASCENDING);
                break;
            case 'alphabetical-az':
                $this->sort_by('name', $this::ORDER_ASCENDING);
                break;
            case 'alphabetical-za':
                $this->sort_by('name', $this::ORDER_DESCENDING);
                break;
            default:
                $this->sort_by('added_date', $this::ORDER_ASCENDING);
                break;
        }
        return $this;
    }


    public function install() {
            $sql = "CREATE TABLE " . $this->tableName() . " (
                   id int(7) NOT NULL AUTO_INCREMENT,
                   contest_id int(7) NOT NULL,
                   name varchar(255) NOT NULL,
                   description varchar(500) DEFAULT NULL,
                   full_description varchar(1255) DEFAULT NULL,
                   social_description varchar(150) DEFAULT NULL,
                   additional varchar(255) DEFAULT NULL,
                   url varchar(255) NOT NULL,
                   url_min varchar(255) DEFAULT NULL,
                   options varchar(500) DEFAULT NULL,
                   image_id int(10) NOT NULL,
                   votes_count int(7) NOT NULL DEFAULT '0',
                   added_date bigint(11) NOT NULL DEFAULT '0',
                   upload_info varchar(1000) DEFAULT NULL,
                   user_email varchar(100) DEFAULT NULL,
                   user_id int(7) DEFAULT '0',
                   user_ip varchar(45) DEFAULT NULL,
                   status INT( 2 ) NOT NULL DEFAULT '0',
                   PRIMARY KEY  (id),
                   KEY contest_id_a_status (contest_id,status),
                   KEY votes_count (votes_count),
                   KEY added_date (added_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            FvLogger::checkDbErrors();
        }

}
/*
class ModelSubscribers extends FvQuery
{

    / **
     * Returns the static query of the specified class.
     * @param string $className active record class name.
     * @return FvModel the static query class
     * /
    public static function query($className = __CLASS__)
    {
        return new $className();
    }

    public function tableName()
    {
        global $wpdb;
        return $wpdb->prefix . "fv_subscribers";
    }

    public function fields()
    {
        return array(
            'contest_id' => '%d',
            'vote_id' => '%d',
            'referer' => '%s',
            'name' => '%s',
            'email' => '%s',
            'age' => '%s',
            'user_id' => '%d',
            'type' => '%s',
            'soc_network' => '%s',
            'sync' => '%d',
            'added' => '%d',
        );
    }


        public function install() {


                $sql = "CREATE TABLE " . $this->tableName() . " (
                        id int(10) NOT NULL AUTO_INCREMENT,
                        contest_id int(10) NOT NULL,
                        vote_id int(4) NOT NULL,
                        added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        referrer VARCHAR( 500 ) NULL,
                        name VARCHAR( 50 ) NULL,
                        email VARCHAR( 70 ) NULL,
                        user_id int(10) NULL,
                        type VARCHAR( 10 ) NULL,
                        sync int(2) NOT NULL DEFAULT '0',
                        soc_network VARCHAR( 40 ) NULL" . $sql_pk . ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
                FvLogger::checkDbErrors();
        }
}
*/
