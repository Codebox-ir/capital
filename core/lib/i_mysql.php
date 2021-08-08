<?php
    /**
     **************************************************************************
     * i_mysql.php
     * Database Adaptor using mysqli for MySQL and MariaDB
     **************************************************************************
     * @package          Mahan 4
     * @category         Routing
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          3.7.0
     * @since            2.4 First time
     * @deprecated       -
     * @link             -
     * @see              -
     * @example          -
     * @ToDo             MYSQLI_ASYNC / fetch_row / Left Join / Union / Distinct / index Offer
     * @ToDo             Optimize / Reaper / Check
     **************************************************************************
     */

    namespace Mahan4;

    use mysqli;
    use mysqli_result;

    /**
     * Class MySQL Adaptor
     */
    class i_mysql
    {
        /**
         * @var object $Link MySQL Connection object
         * @var array $Logs Query logs
         * @var string $prefix Database tables prefix
         * @var debugger $database Query logs
         * @var debugger $debugger Query logs
         */
        public mysqli|null|bool $Link;
        public array $Logs;
        private string $prefix;
        private string $database;
        private debugger|null $debugger;

        /**
         * Constructor
         *
         * @param array $database Database Connection Information
         */
        function __construct(array $database,debugger|null $debugger=null)
        {
            $this->debugger = $debugger;
            $this->setDatabase($database['database']);
            $this->_link($database);
        }

        /**
         * Destructor
         */
        function __destruct()
        {
            mysqli_close($this->Link);
        }

        /**
         * Datetime Now
         *
         * @param string|null $type
         * @return string
         */
        public static function dateNow(string $type=null): string
        {
            if($type=='start') return date("y-m-d").' 00:00:00';
            if($type=='end') return date("y-m-d").' 23:59:59';
            return date("y-m-d H:i:s");
        }

        /**
         * Date Sub
         *
         * @param int $days
         * @return string
         */
        public static function dateSub(int $days=0): string
        {
            return "DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY)";
        }

        /**
         * Select Database
         *
         * @param string $database
         * @return void
         */
        public function setDatabase(string $database): void
        {
            $this->database = $database;
            $this->debugger?->log("Set database",1,'MySQL', $this->database);
        }

        /**
         * Set Prefix
         *
         * @param string $prefix
         * @return void
         */
        public function setPrefix(string $prefix): void
        {
            $this->prefix = $this->escape($prefix);
            $this->debugger?->log("Set prefix",1,'MySQL', $this->prefix);
        }

        /**
         * Link to database
         *
         * @param $database
         */
        private function _link($database): void
        {
            $this->Link = mysqli_connect($database['hostname'], $database['username'], $database['password'], $database['database'], $database['port'], $database['socket']);
            $this->setPrefix($database['prefix']);
            if (mysqli_connect_errno())
                $this->debugger?->log("Connection",0,'MySQL', mysqli_connect_error());
            else
                $this->debugger?->log("Connection",1,'MySQL', 'iSQL: 3 | RDBMS: '.$this->ver());
            mysqli_set_charset($this->Link,'utf8');
            mb_internal_encoding('utf-8');
            mb_http_output('utf-8');
            mb_language('uni');
            mb_regex_encoding('utf-8');
        }

        /**
         * Run Query
         *
         * @param  string $sql Generated query
         * @param  bool $insert Must return inserted id
         * @return bool|mysqli_result Return mySQL Object or false on error
         */
        public function Run(string $sql, bool $insert=false): mysqli_result|bool
        {
            $result = mysqli_query($this->Link, $sql) ?? false;
            if(!$result) {
                $error =  "Error: ".mysqli_error($this->Link);
                $this->Logs[] = array(
                    'sql'    =>  $sql,
                    'result' =>  $error
                );
                $this->debugger?->log("Query",0,'MySQL', end($this->Logs));
                return $error;
            }
            if ($insert) {
                $result = mysqli_insert_id($this->Link);
                $this->Logs[] = array(
                    'sql'    =>  $sql,
                    'result' =>  $result
                );
                $this->debugger?->log("Insert",1,'MySQL', end($this->Logs));
                return $result;
            }
            $this->Logs[] = array(
                'sql'    =>  $sql,
                'result' =>  true
            );
            $this->debugger?->log("Query",1,'MySQL', end($this->Logs)['sql']);
            return $result;
        }

        /**
         * Escape inputs
         *
         * @param array|string $input Auto detect if array pass and do escape for all value, but only support 1 level
         * @return bool|array|string
         */
        public function escape(array|string $input): bool|array|string
        {
            $escaped = null;
            if (is_array($input)) {
                foreach ($input as $key => $value)
                {
                    $escaped_items[] = $input;
                    $key = mysqli_real_escape_string($this->Link, $key);
                    $value = mysqli_real_escape_string($this->Link, $value);
                    $escaped[$key] = $value;
                }
            } else {
                $escaped_items[] = $input;
                $escaped = mysqli_real_escape_string($this->Link, $input);
            }
            $this->Logs[] = $escaped_items ?? null;
            return ($escaped) ?? false;
        }

        /**
         * Query Maker
         * Mixed Limit,Order and group with query
         *
         * @param string $sql Generated query
         * @param string|null $limit Add 'LIMIT' to SQL, pass through on null.
         * @param string|null $order Add 'ORDER BY' to SQL, pass through on null.
         * @param string|null $group Add 'GROUP BY' to SQL, pass through on null.
         * @param string|null $having Add 'HAVING' to SQL, pass through on null.
         * @return  array|false Return array if any result; false on error or empty result.
         */
        public function query(string $sql, string $limit=null, string $order=null, string $group=null, string $having=null): bool|array
        {
            $escaped = $this->escape(array(
                'group' => $group,
                'order' => $order,
                'limit' => $limit
            ));
            if($group)
                $sql.=" GROUP BY ".$escaped['group'];
            if($having)
                $sql.=" HAVING ".$escaped['group'];
            if($order)
                $sql.=" ORDER BY ".$escaped['order'];
            if($limit)
                $sql.=" LIMIT ".$escaped['limit'];
            $result = $this->Run($sql);
            if(!is_object($result))
                return false;
            $output=array();
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                $output[] = $row;
            mysqli_free_result($result);
            return $output;
        }

        /**
         * Version
         * Get database server version.
         *
         * @return string|false   Return version(string) or false on error.
         */
        public function ver(): bool|string
        {
            return $this->query("SELECT version() as ver")[0]['ver'];
        }

        /**
         * Creat Table
         *
         * @param string $name
         * @param array|null $columns
         * @param bool|null $check_exist
         * @return bool
         */
        public function addTable(string $name,?array $columns, ?bool $check_exist=false) :bool
        {
            $name = $this->escape($name);
            $exist = ($check_exist) ? " IF NOT EXISTS " : "";
            $columns = implode(',', $columns);
            $sql = "CREATE TABLE $exist $name ($columns)";
            return $this->Run($sql);
        }

        /**
         * Table exist
         * Check if the table exist or not.
         *
         * @param string $table Table name.
         * @return bool Return true if table is exist and false if not exist.
         */
        public function isTable(string $table): bool
        {
            $table = $this->prefix.$this->escape($table);
            $result = $this->Run("show tables like '$table'");
            return boolval(mysqli_fetch_array($result, MYSQLI_ASSOC));
        }

        /**
         * Table info
         * Get the table information.
         *
         * @return array|false Return array if any result; false on error or empty result.
         */
        public function tableList(): bool|array
        {
            $result = $this->query("SHOW TABLES");
            foreach ($result as $table) $tables[] = array_values($table)[0];
            return $tables ?? false;
        }

        /**
         * Table info
         * Get the table information.
         *
         * @param string $table Table name.
         * @return array|false Return array if any result; false on error or empty result.
         */
        public function tableInfo(string $table): bool|array
        {
            $table = $this->prefix.$this->escape($table);
            $result = $this->query("show table status from ".$this->database." WHERE Name='$table'");
            return $result[0] ?? false;
        }

        /**
         * Table columns
         * Get the table columns list.
         *
         * @param string $table Table name.
         * @return array|false Return array if any result; false on error or empty result.
         */
        public function tableCols(string $table): bool|array
        {
            $table = $this->prefix.$this->escape($table);
            $result = $this->query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE TABLE_NAME='$table' AND TABLE_SCHEMA='$this->database'");
            if (!$result)
                return false;
            $columns = array();
            foreach ($result as $col)
                $columns[] = $col['COLUMN_NAME'];
            return $columns;
        }

        /**
         * table flush
         * Truncate the table.
         *
         * @param string $table Table name.
         * @return bool
         */
        public function flush(string $table): bool
        {
            $table = $this->prefix.$this->escape($table);
            return boolval($this->Run("TRUNCATE TABLE `$table`"));
        }

        /**
         * Delete row by key and val.
         * Only affect on first row (limit 1)
         *
         * @param string $table    Table name.
         * @param int|string $id    Row key value
         * @param string $key   Key column
         * @return bool
         */
        public function deleteId(string $table, int|string $id, string $key='id'): bool
        {
            $table = $this->prefix.$this->escape($table);
            $id = $this->escape($id);
            $key = $this->escape($key);
            return boolval($this->Run("DELETE FROM `$table` WHERE `$key`='$id' LIMIT 1"));
        }

        /**
         * Delete Any
         * Delete multi row.
         *
         * @param string $table Table name.
         * @param string|null $where WHERE Condition
         * @return bool
         */
        public function deleteAny(string $table, string $where=null): bool
        {
            $table = $this->prefix.$this->escape($table);
            $sql = "DELETE FROM $table";
            if ($where)
                $sql .= " WHERE $where";
            return boolval($this->Run($sql));
        }

        /**
         * Insert
         * Insert row.
         *
         * @param string $table Table name.
         * @param array $input Contain value for key same as every column
         * @return mysqli_result|bool|int Return inserted id if any result; false on error.
         */
        public function insert(string $table, array $input, bool $update=false): mysqli_result|bool|int
        {
            $table = $this->prefix.$this->escape($table);
            $escaped_data = $this->escape($input);
            $columns = implode(", ",array_keys($escaped_data));
            $values  = implode("', '", $escaped_data);
            $command = ($update) ? 'REPLACE' : 'INSERT';
            $sql = "$command INTO `$table` ($columns) VALUES ('$values')";
            return $this->Run($sql,1);
        }

        /**
         * Update Query
         *
         * @param string $table Table name.
         * @param array $data Contain value for key same as every column
         * @return string Mixed sql query for update
         */
        private function _updateSQL(string $table, array $data): string
        {
            $table = $this->prefix.$this->escape($table);
            $escaped_data = $this->escape($data);
            $sql    = "UPDATE `$table` SET";
            foreach ($escaped_data as $column => $value) {
                $sql    .= " $column='$value'";
                end($escaped_data);
                if(($column === key($escaped_data)))
                    break;
                $sql .=  ',';
            }
            return $sql;
        }

        /**
         * Update row by id
         *
         * @param string $table Table name.
         * @param array $data Contain value for key same as every column
         * @param int $id row id
         * @param string $key
         * @return bool
         */
        public function updateId(string $table, array $data, int $id, string $key='id'): bool
        {
            $table   = $this->prefix.$this->escape($table);
            $key     = $this->escape($key);
            $id      = $this->escape($id);
            $sql     = $this->_updateSQL($table, $data);
            $sql    .= " WHERE `$key`=$id LIMIT 1";
            return boolval($this->Run($sql));
        }

        /**
         * Update Multi row by id
         *
         * @param string $table Table name.
         * @param array $data Contain value for key same as every column
         * @param string $ids Ids separated by ','
         * @param string $key
         * @return bool
         */
        public function updateIdMulti(string $table, array $data, string $ids, string $key='id'): bool
        {
            $table   = $this->prefix.$this->escape($table);
            $ids     = $this->escape($ids);
            $sql     = $this->_updateSQL($table, $data);
            $sql    .= " WHERE `$key` IN ($ids)";
            return boolval($this->Run($sql));
        }

        /**
         * Update multi row
         *
         * @param string $table Table name.
         * @param array $data Contain value for key same as every column
         * @param string|null $where WHERE Condition
         * @return bool
         */
        public function updateAny(string $table, array $data, string $where=null): bool
        {
            $table     = $this->prefix.$this->escape($table);
            $sql       = $this->_updateSQL($table, $data);
            if ($where)
                $sql .= " WHERE $where";
            return boolval($this->Run($sql));
        }

        /**
         * Increase column value
         *
         * @param string $column Target $column
         * @param string|null $where WHERE Condition
         * @param int $value value to add
         * @param string $table Table name
         * @return bool
         */
        public function increase(string $table, string $column, string $where=null, int $value=1): bool
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $value      = $this->escape($value);
            $sql        = "UPDATE $table SET $column=$column+$value";
            if ($where)
                $sql .= " WHERE $where";
            return boolval($this->Run($sql));
        }

        /**
         * Decrease value
         *
         * @param string $table Table name
         * @param string $column Target $column
         * @param string|null $where WHERE Condition
         * @param int $value
         * @return bool
         */
        public function decrease(string $table, string $column, string $where=null, int $value=1): bool
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $value      = $this->escape($value);
            $sql        = "UPDATE $table SET $column=$column-$value";
            if ($where)
                $sql .= " WHERE $where";
            return boolval($this->Run($sql));
        }

        /**
         * Append string
         *
         * @param string $table Table name
         * @param string $column Target $column
         * @param string $string Appended String
         * @param string|null $where WHERE Condition
         * @return bool
         */
        public function append(string $table, string $column, string $string, string $where=null): bool
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $string     = $this->escape($string);
            $sql        = "UPDATE $table SET $column = CONCAT($column, '$string')";
            if ($where)
                $sql .= " WHERE $where";
            return boolval($this->Run($sql));
        }

        /**
         * Prepend string
         *
         * @param string $table Table name
         * @param string $column Target $column
         * @param string $string Appended String
         * @param string|null $where WHERE Condition
         * @return bool
         */
        public function prepend(string $table, string $column, string $string, string $where=null): bool
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $string     = $this->escape($string);
            $sql        = "UPDATE $table SET $column = CONCAT('$string', $column)";
            if ($where)
                $sql .= " WHERE $where";
            return boolval($this->Run($sql));
        }


        /**
         * Check if row exist
         *
         * @param string $table Table name
         * @param string|null $where WHERE Condition
         * @return int|false
         */
        public function exist(string $table, string $where=null): bool|int
        {
            $table      = $this->prefix.$this->escape($table);
            $sql        = "SELECT * FROM $table";
            if ($where)
                $sql .= " WHERE $where";
            $result     = $this->query($sql);
            return ($result) ? count($result) : false;
        }

        /**
         * Count rows
         *
         * @param string $table Table name
         * @param string|null $where    WHERE Condition
         * @param string $col   Target $column
         * @return int|false
         */
        public function count(string $table, string $where=null, string $col='id'): bool|int
        {
            $table      = $this->prefix.$this->escape($table);
            $sql        = "SELECT COUNT($col) as count FROM $table";
            if ($where)
                $sql .= " WHERE $where";
            return $this->query($sql,1)[0]['count'];
        }

        /**
         * Sum column
         *
         * @param string $table Table name
         * @param string $column Target $column
         * @param string|null $where WHERE Condition
         * @return int|bool
         */
        public function sum(string $table, string $column, string $where=null): bool|int
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $sql        = "SELECT SUM($column) as sum FROM $table";
            if ($where)
                $sql .= " WHERE $where";
            return $this->query($sql,1)[0]['sum'];
        }

        /**
         * Average column.
         * @param string $table Table name
         * @param string $column Target $column
         * @param string|null $where WHERE Condition
         * @return int|bool
         */
        public function avg(string $table, string $column, string $where=null): bool|int
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $sql        = "SELECT AVG($column) as avg FROM $table";
            if ($where)
                $sql .= " WHERE $where";
            return $this->query($sql,1)[0]['avg'];
        }

        /**
         * Select Rows
         *
         * @param string $table Table name
         * @param string $column Target $column
         * @param string|null $where WHERE Condition
         * @param string|null $group Group By
         * @param string|null $limit
         * @param string|null $order
         * @return array|bool
         */
        public function select(string $table, string $column='*',string $where=null, string $group=null, string $limit=null, string $order=null): bool|array
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $sql        = "SELECT $column FROM $table";
            if ($where)
                $sql .= " WHERE $where";
            return $this->query($sql, $limit, $order, $group);
        }

        /**
         * Select Row
         *
         * @param string|null $where
         * @param string|null $order
         * @param string $table Table name
         * @return array|bool
         */
        public function selectRow(string $table, string $where=null, string $order=null): bool|array
        {
            $table  = $this->prefix.$this->escape($table);
            $sql    = "SELECT * FROM $table";
            if ($where)
                $sql .= " WHERE $where";
            return $this->query($sql, 1, $order)[0] ?? false;
        }

        /**
         * Select row by id
         *
         * @param int $id target id
         * @param string $column select column
         * @param string $table Table name
         * @return array|bool
         */
        public function selectId(string $table, int $id, string $column='*'): bool|array
        {
            $table      = $this->prefix.$this->escape($table);
            $column     = $this->escape($column);
            $id         = intval($this->escape($id));
            return $this->query("SELECT $column FROM $table WHERE id=$id",1)[0] ?? false;
        }

        /**
         * Select All
         *
         * @param string|null $limit
         * @param string|null $order
         * @param string $table Table name
         * @return array|bool
         */
        public function selectAll(string $table, ?string $limit=null, ?string $order=null): bool|array
        {
            $table = $this->prefix.$this->escape($table);
            $sql = "SELECT * FROM $table";
            return $this->query($sql, $limit, $order);
        }

    }