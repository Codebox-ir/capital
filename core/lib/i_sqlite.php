<?php
    /**
     **************************************************************************
     * i_sqlite.php
     * Database Adaptor for SQLite
     **************************************************************************
     * @package          Mahan 4
     * @category         Routing
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          1.0
     * @since            4.0 First time
     * @deprecated       -
     * @link             -
     * @see              -
     * @example          -
     **************************************************************************
     */

    namespace Mahan4;


    use SQLite3;

    /**
     * Class SQLite Adaptor
     * @ToDo  more methods
     */
    class i_sqlite extends SQLite3
    {
        private ?debugger $debugger;
        private array $iLog;

        /**
         * Constructor
         */
        function __construct(string $db_path,debugger|null $debugger=null) {
            $this->debugger = $debugger;
            SQLite3::enableExceptions(true);
            $this->iSetDatabase($db_path);
        }

        /**
         * Destructor
         */
        function __destruct()
        {
            // $this->finalize();
            $this->close();
        }

        /**
         * Select Database
         * @param string $db_path
         * @return void
         */
        public function iSetDatabase(string $db_path): void
        {
            $this->open($db_path);
            $this->debugger?->log("Set database",1,'SQLite', $db_path);
        }

        /**
         * Exec SQL
         * @param string $sql
         * @param bool $insert
         * @return int|bool
         */
        public function iRun(string $sql,bool $insert=false): int|bool
        {
            $result = $this->exec($sql);
            if(!$result) {
                $error =  "Error: ".$this->lastErrorMsg();
                $this->iLog[] = array(
                    'sql'    =>  $sql,
                    'result' =>  $error
                );
                $this->debugger?->log("Exec",0,'SQLite', end($this->iLog));
                return false;
            }
            if ($insert) {
                $result = $this->lastInsertRowID();
                $this->iLog[] = array(
                    'sql'    =>  $sql,
                    'result' =>  $result
                );
                $this->debugger?->log("Insert",1,'SQLite', end($this->iLog));
                return $result;
            }
            $this->iLog[] = array(
                'sql'    =>  $sql,
                'result' =>  true
            );
            $this->debugger?->log("Exec",1,'SQLite', end($this->iLog)['sql']);
            return true;
        }

        /**
         * Escape inputs
         *
         * @param array|string $input Auto detect if array pass and do escape for all value, but only support 1 level
         * @return bool|array|string
         */
        public function iEscape(array|string $input): bool|array|string
        {
            $escaped = null;
            if (is_array($input)) {
                foreach ($input as $key => $value)
                {
                    $escaped_items[] = $input;
                    $key = $this->escapeString($key);
                    $value = $this->escapeString($value);
                    $escaped[$key] = $value;
                }
            } else {
                $escaped_items[] = $input;
                $escaped = $this->escapeString($input);
            }
            $this->iLog[] = $escaped_items ?? null;
            return ($escaped) ?? false;
        }

        /**
         * Creat Table
         * @param string $name
         * @param array|null $columns
         * @param bool|null $check_exist
         * @return bool
         */
        public function iAddTable(string $name,?array $columns, ?bool $check_exist=false) :bool
        {
            $name = $this->iEscape($name);
            $exist = ($check_exist) ? " IF NOT EXISTS " : "";
            $columns = implode(',', $columns);
            $sql = "CREATE TABLE $exist $name ($columns)";
            return $this->iRun($sql);
        }

    }