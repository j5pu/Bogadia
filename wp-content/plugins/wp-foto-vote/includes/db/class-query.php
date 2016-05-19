<?php

//namespace WordPress\ORM;
//https://github.com/doctrine/dbal/blob/master/lib/Doctrine/DBAL/Query/QueryBuilder.php

defined('ABSPATH') or die("No script kiddies please!");

/*
 * Abstract class, represents classik actions with database, use WordPress functions
 * http://habrahabr.ru/post/154245/
 */

if ( class_exists('FvModel') ) {
    return;
}

class FvModel extends FvQuery {
}

/**
 * Progressively build up a query to get results using an easy to understand
 * DSL.
 *
 * @author Brandon Wamboldt <brandon.wamboldt@gmail.com>
 */
class FvQuery {

	/**
	 * @var string
	 */
	const ORDER_ASCENDING = 'ASC';

	/**
	 * @var string
	 */
	const ORDER_DESCENDING = 'DESC';

	/**
	 * @var string
	 */
	protected $what_field = "";
	
	/**
	 * @var integer
	 */
	protected $limit = 0;

	/**
	 * @var integer
	 */
	protected $offset = 0;

	/**
	 * @var array
	 */
	protected $where = array();

	/**
	 * @var string
	 */
	protected $sort_by = array();
    //'id' => 'ASC'

	/**
	 * @var string
	 */
	//protected $order = ;

    /**
	 * @var string
	 */
	protected $group = '';

	/**
	 * @var array
	 */
	protected $join = array();

	/**
	 * @var string|null
	 */
	protected $search_term = null;

	/**
	 * @var array
	 */
	protected $search_fields = array();

	/**
	 * @var string
	 */
	protected $model;

	/**
	 * @var string
	 */
	protected $primary_key;

	/**
	 * @param string $model
	 */
	public function __construct() {
		$this->primary_key = 'id';
		//$this->query = $query;
	}

	/**
	 * Return the string representation of the query.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->compose_query();
	}

	/* ===========================================
	 * PUBLIC ACTION FUNCTIONS
	 ============================================ */

	/**
	 * Compose & execute our query.
	 *
	 * @return OBJECT row
	 */
	public function findRow() {
		global $wpdb;

		$result = $wpdb->get_row( $this->compose_query(false), OBJECT );
		return $result;
	}	 
	 
	/**
	 * Compose & execute our query.
	 *
	 * @param  boolean $only_count Whether to only return the row count
	 * @param  boolean $get_var
     *
	 * @return array
	 */
	public function find($only_count = false, $get_var = false) {
		global $wpdb;

		//$query = $this->query;
		// Query
		if ($only_count) {
			return (int) $wpdb->get_var($this->compose_query(true));
		} elseif ($get_var) {
			return $wpdb->get_var( $this->compose_query(true) );
		}

		$results = $wpdb->get_results($this->compose_query(false));
		/*
		  if ($results) {
		  foreach ($results as $index => $result) {
		  $results[$index] = $query::create((array) $result);
		  }
		  }
		 */
        $this->checkDbErrors();
		return $results;
	}
	
	/**
	 * Compose & execute our query.
	 *
	 * @return string
	 */
	public function findVar() {
		global $wpdb;

		//$query = $this->query;
		// Query
		return $wpdb->get_var( $this->compose_query(false) );
	}


    /**
     * find records with All fields in table by Primary KEY
     * @since     1.0.0
     *
     * @param   $id             int
     * @param   $from_cache    bool
     * @return  object
     */
    public function findByPK($id, $from_cache = false) {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT * FROM " . $this->tableName() . " WHERE `" . $this->primary_key . "` = %d; ", $id
        );

        if ($from_cache && !$r = wp_cache_get($id, $this->tableName().'-findByPK', 'fv')) {
            $r = $wpdb->get_row($sql, OBJECT);
            $this->checkDbErrors();
            wp_cache_add($id, $r, $this->tableName().'-findByPK', 'fv');
            return $r;
        } elseif(!$from_cache) {
            $r = $wpdb->get_row($sql, OBJECT);
            $this->checkDbErrors();
        }
        return $r;
    }

	/**
	 * delete record in table
	 * @since     1.0.0
	 *
	 * @param   $id    int
	 * @return  bool    MySQL query result
	 */
	public function delete($id) {
		global $wpdb;

		$r = $wpdb->query(
				  $wpdb->prepare(
							 " DELETE FROM " . $this->tableName() . " WHERE `" . $this->primary_key . "` = '%d'; ", $id
				  )
		);
		$this->checkDbErrors();
		return $r;
	}

	/**
	 * Insert record into table using Wordpress Insert Function and return ID
	 * @since     1.0.0
	 *
	 * @param   $data        array
	 * @return  int        Last inserted ID
	 */
	public function insert($data) {
		global $wpdb;
		// Format for database (string, int)
		$sql_format = array();
		// Array - data to save
		$sql_data = array();
		$fields = $this->fields();
		foreach ($data as $key => $value) {
			if (isset($fields[$key])) {
				$sql_data[$key] = $value;
				$sql_format[] = $fields[$key];
			}
		}
		// do Query
		$wpdb->insert(
				$this->tableName(), $sql_data, $sql_format
		);
		$this->checkDbErrors();
		return $wpdb->insert_id;
	}

	/**
	 * Update record by simple condition using Wordpress Update Function
	 * Comdition may be array, int or false
	 * <code>
	 * Example 1:
	 * TestModel::query()->update( 
	 *		array('name'=>'Test'),				// DATA
	 *		array( 'count_product' =>'1')		// Condition
	 * );
	 * 
	 * Example 2:
	 * TestModel::query()->update( 
	 *		array('name'=>'Test'),				// DATA
	 *		3 )									// It means `Primary key` equal 3
	 * );
	 * </code>
	 * @since     1.0.0
	 * @param array $data
	 * @param mixed $condition  Record ID or
     *
	 * @return bool MySQL query result
	 */
	public function update($data, $condition = false) {
		global $wpdb;
		// Format for database (string, int)
		$sql_format = array();
		// Array - data to save
		$sql_data = array();
		$fields = $this->fields();
		foreach ($data as $key => $value) {
			if (isset($fields[$key])) {
				$sql_data[$key] = $value;
				$sql_format[] = $fields[$key];
			}
		}
		// may be primary key not set in fileds due to secure it for random change
		$fields[ $this->primary_key ] = '%d';
		// Format for database (string, int)
		$condition_format = array();
		// Array - data to save
		$condition_data = array();
		if (is_array($condition)) {
			foreach ($condition as $key => $value) {
				if (isset($fields[$key])) {
					$condition_data[$key] = $value;
					$condition_format[] = $fields[$key];
				}
			}
		} elseif (is_numeric($condition)) {
			$condition_data = array('id' => (int)$data['id']);
			$condition_format = '%d';
		} elseif ( $condition === false && isset($data[$this->primary_key]) ) {
			$condition_data = array($this->primary_key => (int)$data[$this->primary_key]);
			$condition_format = array('%d');
		}
		// do Query
		$r = $wpdb->update(
				$this->tableName(), $sql_data, $condition_data, $sql_format, $condition_format
		);
		$this->checkDbErrors();
		return $r;
	}


    /**
     * Update record by PK using Wordpress Update Function
     * <code>
     * Example 1:
     * TestModel::query()->update(
     *		array('name'=>'Test'),				// DATA
     *		10		// PK ID
     * );
     *
     * @since     1.0.0
     * @param array $data
     * @param int $pkID  Record ID
     *
     * @return bool MySQL query result
     */
    public function updateByPK($data, $pkID) {
        global $wpdb;
        // Format for database (string, int)
        $sql_format = array();
        // Array - data to save
        $sql_data = array();
        $fields = $this->fields();
        foreach ($data as $key => $value) {
            if (isset($fields[$key])) {
                $sql_data[$key] = $value;
                $sql_format[] = $fields[$key];
            }
        }
        // may be primary key not set in fileds due to secure it for random change
        $fields[ $this->primary_key ] = '%d';

        $condition_data = array($this->primary_key => (int)$pkID);
        // do Query
        $r = $wpdb->update(
            $this->tableName(), $sql_data, $condition_data, $sql_format, array('%d')
        );
        $this->checkDbErrors();
        return $r;
    }

	
	/* ===========================================
	 * END PUBLIC ACTION FUNCTIONS
	 ============================================ */

    /**
     * Reset one field, can used for change Sort / Where
     *
     * @param  string $param
     * @return FvQuery self
     */
    public function resetParam($param) {
        switch($param) {
            case 'sort_by':
                $this->sort_by = array();
                break;
            case 'where':
                $this->where = array();
                break;
            case 'join':
                $this->join = array();
                break;
        }
        return $this;
    }
	
	/**
	 * Set the fields to include in the search.
	 *
	 * @param  array $fields
	 */
	public function set_searchable_fields(array $fields) {
		$this->search_fields = $fields;
		
		return $this;		
	}

	/**
	 * Set the primary key column.
	 *
	 * @param string $primary_key
	 */
	public function set_primary_key($primary_key) {
		$this->primary_key = $primary_key;
		$this->sort_by = $primary_key;
		
		return $this;		
	}

	/**
	 * Set field, for get_var function
	 *
	 * @param  string $field	Ex.: SUM(id)
	 * @return FvQuery self
	 */
	public function what_field($field) {
		$this->what_field = sanitize_text_field($field);
		return $this;
	}
	
	/**
	 * Set the maximum number of results to return at once.
	 *
	 * @param  integer $limit
	 * @return FvQuery self
	 */
	public function limit($limit) {
		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * Set the offset to use when calculating results.
	 *
	 * @param  integer $offset
	 * @return FvQuery self
	 */
	public function offset($offset) {
		$this->offset = (int) $offset;

		return $this;
	}

	/**
	 * Set the column we should sort by.
	 *
	 * @param  string $sort_by_field
	 * @param  string $order
	 * @return FvQuery self
	 */
	public function sort_by($sort_by_field, $order = 'ASC') {
        if ( strlen($sort_by_field) > 1 ) {
            if ( $order != $this::ORDER_ASCENDING && $order != $this::ORDER_DESCENDING ) {
                $order = $this::ORDER_ASCENDING;
            }

            $this->sort_by[$sort_by_field] = $order;
		}

		return $this;
	}

	/**
	 * Set the order we should sort by.
	 *
	 * @param  string $order
	 * @return FvQuery self
	 */
	public function order($order) {
        trigger_error('This function is Deprecated since version 2.2.123. Use "sort_by($sort_by, $order)" with second parameter.', E_USER_NOTICE);
		//$this->order = $order;
		return $this;
	}

    /**
     * Set the group we should group by.
     *
     * @param  string $group
     * @return FvQuery self
     */
    public function group_by($group) {
        $this->group = $group;

        return $this;
    }

	/**
	 * Add a `=` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where($column, $value) {
		$this->where[] = array('type' => 'where', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add a `!=` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where_not($column, $value) {
		$this->where[] = array('type' => 'not', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add a `LIKE` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where_like($column, $value) {
		$this->where[] = array('type' => 'like', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add a `NOT LIKE` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where_not_like($column, $value) {
		$this->where[] = array('type' => 'not_like', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add a `<` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where_lt($column, $value) {
		$this->where[] = array('type' => 'lt', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add a `<=` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where_lte($column, $value) {
		$this->where[] = array('type' => 'lte', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add a `>` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where_gt($column, $value) {
		$this->where[] = array('type' => 'gt', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add a `>=` clause to the search query.
	 *
	 * @param  string $column
	 * @param  string $value
	 * @return FvQuery self
	 */
	public function where_gte($column, $value) {
		$this->where[] = array('type' => 'gte', 'column' => $column, 'value' => $value);

		return $this;
	}

	/**
	 * Add an `IN` clause to the search query.
	 *
	 * @param  string $column
	 * @param  array  $in
	 * @return FvQuery self
	 */
	public function where_in($column, array $in) {
		$this->where[] = array('type' => 'in', 'column' => $column, 'value' => $in);

		return $this;
	}

	/**
	 * Add a `NOT IN` clause to the search query.
	 *
	 * @param  string $column
	 * @param  array  $not_in
	 * @return FvQuery self
	 */
	public function where_not_in($column, array $not_in) {
		$this->where[] = array('type' => 'not_in', 'column' => $column, 'value' => $not_in);

		return $this;
	}

	/**
	 * Add an OR statement to the where clause (e.g. (var = foo OR var = bar OR
	 * var = baz)).
	 *
	 * @param  array $where
	 * @return FvQuery self
	 */
	public function where_any(array $where) {
		$this->where[] = array('type' => 'any', 'where' => $where);

		return $this;
	}

	/**
	 * Add an AND statement to the where clause (e.g. (var1 = foo AND var2 = bar
	 * AND var3 = baz)).
	 *
	 * @param  array $where
	 * @return FvQuery self
	 */
	public function where_all(array $where) {
		$this->where[] = array('type' => 'all', 'where' => $where);

		return $this;
	}

        /**
         * Add an AND statement to the where clause
         * date(field) >= date(param)
         *
         * @param  string $column
         * @param  timestamp $value
         * @return self
         */
        public function where_later($column, $value) {
                $this->where[] = array('type' => 'later', 'column' => $column, 'value' => $value);

                return $this;
        }

        /**
         * Add an AND statement to the where clause
         * date(field) <= date(param)
         *
         * @param  string $column
         * @param  string $value
         * @return FvQuery self
         */
        public function where_early($column, $value) {
                $this->where[] = array('type' => 'early', 'column' => $column, 'value' => $value);

                return $this;
        }

	/**
	 * Get models where any of the designated fields match the given value.
	 *
	 * @param  string $search_term
	 * @return FvQuery self
	 */
	public function search($search_term) {
		$this->search_term = $search_term;

		return $this;
	}

	/**
	 * Runs the same query as find, but with no limit and don't retrieve the
	 * results, just the total items found.
	 *
	 * @return integer
	 */
	public function total_count() {
		return $this->find(true);
	}

	/**
	  * Creates and adds a left join to the query.
	  *
	  * <code>
	  *     $qb = $conn->createQueryBuilder()
	  *         ->select('u.name')
	  *         ->from('users', 'u')
	  *         ->leftJoin('u', 'phonenumbers', 'p', 'p.is_primary = 1');
	  * </code>
	  *
	  * @param string $join      The table name to join.
	  * @param string $alias     The alias of the join table.
	  * @param string $condition The condition for the join.
	  * @param array $fields How fields take from join table
	  *
	  *  @return WSS_Model the static query class
	  */
	 public function leftJoin($join, $alias, $condition, $fields)
	 {
		$this->join[] = array(
					'joinType'      => 'left',
					'joinTable'     => $join,
					'joinAlias'     => $alias,
					'joinCondition' => $condition,			 
					'joinFields' => $fields			 
			);
		return $this;		 
	 }	

	/**
	 * Compose the actual SQL query from all of our filters and options.
	 *
	 * @param  boolean $only_count Whether to only return the row count
	 * @param  boolean $get_var Whether to only return the variable
	 * @return string
	 */
	public function compose_query($only_count = false) {
        //$query  = $this->query;
        $table = $this->tableName();
        $where = '';
        $group = '';
        $order = '';
        $limit = '';
        $offset = '';
        $fields = $this->fields();

        // Search
        if (!empty($this->search_term)) {
            $where .= ' AND (';

            foreach ($this->search_fields as $field) {
                $where .= '`t`.`' . $field . '` LIKE "%' . esc_sql($this->search_term) . '%" OR ';
            }

            $where = substr($where, 0, -4) . ')';
        }

        // Where

        foreach ($this->where as $q) {
            if ( isset($q['column']) && !isset( $fields[$q['column']] ) ) {
                continue;
            }
            // where


            if ($q['type'] == 'where') {
                      $where .= ' AND `t`.`' . $q['column'] . '` = "' . esc_sql($q['value']) . '"';
            }

            // where_not
            elseif ($q['type'] == 'not') {
                      $where .= ' AND `t`.`' . $q['column'] . '` != "' . esc_sql($q['value']) . '"';
            }

            // where_like
            elseif ($q['type'] == 'like') {
                      $where .= ' AND `t`.`' . $q['column'] . '` LIKE "%' . esc_sql($q['value']) . '%"';
            }

            // where_not_like
            elseif ($q['type'] == 'not_like') {
                      $where .= ' AND `t`.`' . $q['column'] . '` NOT LIKE "' . esc_sql($q['value']) . '"';
            }

            // where_lt
            elseif ($q['type'] == 'lt') {
                      $where .= ' AND `t`.`' . $q['column'] . '` < "' . esc_sql($q['value']) . '"';
            }

            // where_lte
            elseif ($q['type'] == 'lte') {
                      $where .= ' AND `t`.`' . $q['column'] . '` <= "' . esc_sql($q['value']) . '"';
            }

            // where_gt
            elseif ($q['type'] == 'gt') {
                      $where .= ' AND `t`.`' . $q['column'] . '` > "' . esc_sql($q['value']) . '"';
            }

            // where_gte
            elseif ($q['type'] == 'gte') {
                      $where .= ' AND `t`.`' . $q['column'] . '` >= "' . esc_sql($q['value']) . '"';
            }

            // where_early
            elseif ($q['type'] == 'early') {
                      $date = date("Y-m-d H:i:s", (int)$q['value']);
                      $where .= ' AND `t`.`' . $q['column'] . '` <= "' . esc_sql($date) . '"';
            }
            // where_later
            elseif ($q['type'] == 'later') {
                      $date = date("Y-m-d H:i:s", (int)$q['value']);
                      $where .= ' AND `t`.`' . $q['column'] . '` >= "' . esc_sql($date) . '"';
            }

            // where_in
            elseif ($q['type'] == 'in') {
                      $where .= ' AND `t`.`' . $q['column'] . '` IN (';

                      foreach ($q['value'] as $value) {
                                 $where .= '"' . esc_sql($value) . '",';
                      }

                      $where = substr($where, 0, -1) . ')';
            }

            // where_not_in
            elseif ($q['type'] == 'not_in') {
                      $where .= ' AND `t`.`' . $q['column'] . '` NOT IN (';

                      foreach ($q['value'] as $value) {
                                 $where .= '"' . esc_sql($value) . '",';
                      }

                      $where = substr($where, 0, -1) . ')';
            }

            // where_any
            elseif ($q['type'] == 'any') {
                      $where .= ' AND (';

                      foreach ($q['where'] as $column => $value) {
                                 if ( !is_array($value) ) {
                                      $where .= '`t`.`' . $column . '` = "' . esc_sql($value) . '" OR ';
                                 } else {
                                            foreach ($value as $column2 => $value2) :
                                                 $where .= '`t`.`' . $column2 . '` = "' . esc_sql($value2) . '" OR ';
                                            endforeach;
                                            //FvFunctions::dump( 'before 1: ' . $where);
                                            //$where = substr($where, 0, -5) . '")';
                                            //FvFunctions::dump('after 1: ' . $where);
                                 }
                      }
                      //FvFunctions::dump( 'before : ' . $where);
                      $where = substr($where, 0, -5) . '")';
                      //FvFunctions::dump( 'final 2: ' . $where);
            }

            // where_all
            elseif ($q['type'] == 'all') {
                      $where .= ' AND (';

                      foreach ($q['where'] as $column => $value) {
                                 $where .= '`t`.`' . $column . '` = "' . esc_sql($value) . '" AND ';
                      }

                      $where = substr($where, 0, -5) . ')';
            }
            // Finish where clause
        }

        if (!empty($where)) {
            $where = ' WHERE ' . substr($where, 5);
        }
        if (!$only_count) {
            // group
            if ( !empty($this->group) ) {
                $group = ' GROUP BY ' . $this->group;
            }

            if ( !empty($this->sort_by) && is_array($this->sort_by) ) {
                $order_arr = array();
                foreach($this->sort_by as $sort_field => $sort_order) {
                    // Order
                    if (strstr($sort_field, '(') !== false && strstr($sort_field, ')') !== false) {
                        // The sort column contains () so we assume its a function, therefore
                        // don't quote it
                        $order_arr[] = $sort_field . ' ' . $sort_order;
                    } else {
                        $order_arr[] = '`t`.`' . $sort_field . '` ' . $sort_order;
                    }
                }
                $order =' ORDER BY ' . implode(', ', $order_arr);
                unset($order_arr);
            }

            // Limit
            if ($this->limit > 0) {
                $limit = ' LIMIT ' . (int)$this->limit;
            }

            // Offset
            if ($this->offset > 0) {
                $offset = ' OFFSET ' . (int)$this->offset;
            }

            if ( !empty($this->what_field) ) {
                $what = $this->what_field;
            } else {
                $what = " `t`.* ";
            }
        }

        $join_sql = "";
        if ( !empty($this->join) ) {
             foreach ($this->join as $join) {
                $join_sql .= ' ' . strtoupper($join['joinType'])
                    . ' JOIN `' . $join['joinTable'] . '` ' . $join['joinAlias']
                    . ' ON ' . ((string) $join['joinCondition']);
                if (is_array( $join['joinFields']) ){
                    $what_arr = array();
                    foreach ($join['joinFields'] as $key => $field) {
                        if (strstr($field, '(') !== false && strstr($field, ')') !== false) {
                            $what_arr[] = $field . " as {$join['joinAlias']}_". sanitize_title_for_query($key);
                        } else {
                            $what_arr[] = $join['joinAlias'] . ".`" . $field . "` as {$join['joinAlias']}_{$field}";
                        }
                    }
                    $what .= ", " . implode(", ", $what_arr);
                }
             }
        }

        // Query
        if ($only_count) {
            $qurey_res = apply_filters('wporm_count_query', "SELECT COUNT(*) FROM `{$table}` t {$where};", $table);
        } else {
            $qurey_res = apply_filters('wporm_query', "SELECT {$what} FROM `{$table}` t {$join_sql} {$where}{$group}{$order}{$limit}{$offset};", $table);
        }

        return $qurey_res;
    }
	
	/**
	 * check errors, and record into file
	 * @since     1.0.0
	 * @return  void
	 */
	protected function checkDbErrors() {
		FvLogger::checkDbErrors();
	}	

}

/*
add_filter('wporm_query', 'fv_orm_log_queries', 10, 2);

function fv_orm_log_queries($sql, $model_class) {
	echo '<pre>' . $sql . '</pre>';
	return $sql;
};
*/