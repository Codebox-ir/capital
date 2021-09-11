<?php
    /**
     **************************************************************************
     * userForm.php
     * User Manager
     **************************************************************************
     * @package          Mahan 4
     * @category         Core library
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          1.0
     * @since            4.0 First time
     * @deprecated       -
     * @link             -
     * @see              -
     **************************************************************************
     */

    namespace Mahan4;

    /**
     * Class user
     * @database users
     */
    class user
    {

        public int $ID;
        public static string $DB_TABLE='users';
        private string $error='';
        private ?i_mysql $db;
        private ?debugger $debugger;

        /**
         * user constructor.
         */
        function __construct(int $id=0, debugger|null $debugger=null) {
            $this->debugger = $debugger;
            global $db_main;
            $this->db = $db_main;
            if($this->db==null)
                $this->error =  'No database connection!';
            if($this->db->isTable(self::$DB_TABLE)==false)
                $this->error = "table doesn't exist!";
            if($this->error){
                $this->debugger?->log('Initial','0','user Lib', $this->error);
                $this->db = null;
            }
            else
                $this->ID = $id;
        }

        /**
         * Add User
         */
        public function add(array $data) : int
        {
            $id = $this->db?->insert(self::$DB_TABLE, $data);
            $this->debugger?->log('Add', boolval($id),'user Lib', json_encode($data));
            if($id)
                $this->sync($id);
            return $id;
        }

        /**
         * Delete User
         */
        public function delete(int $id) : bool
        {
            if($_SESSION['M4']['user']['id']===$id){
                $this->debugger?->log('Delete', 0,'user Lib', "Self delete, user id:$id");
                return false;
            }
            $result = $this->db?->deleteId(self::$DB_TABLE, $id);
            $this->debugger?->log('Delete', $result,'user Lib', "User id:$id");
            return $result;
        }

        /**
         * Update User
         */
        public function update(array $data,?int $id=0) : bool
        {
            if($id==0)
                $id = $this->ID;
            $result = $this->db?->updateId(self::$DB_TABLE, $data, $id);
            $data['id']=$id;
            $this->debugger?->log('Update', $result,'user Lib', json_encode($data));
            if($result)
                $this->sync($id);
            return $result;
        }

        /**
         * Select User By Id
         */
        public function selectId(?int $id=0) : array
        {
            if($id==0)
                $id = $this->ID;
            $result = $this->db?->selectId(self::$DB_TABLE, $id);
            $this->debugger?->log('Select', boolval($result),'user Lib', json_encode($result));
            return $result;
        }


        /**
         * Select User By Id
         */
        public function selectEmail(string $email) : ?array
        {
            $where = "email='$email'";
            $result = $this->db?->selectRow(self::$DB_TABLE, $where);
            $this->debugger?->log('Select', boolval($result),'user Lib', json_encode($result));
            return $result;
        }

        /**
         * Sync User Session/Database
         */
        public function sync(int $id) : void
        {
            if(isset($_SESSION['M4']['user']) && $_SESSION['M4']['user']['id']===$id)
                $_SESSION['M4']['user'] = $this->select($id);
            $this->debugger?->log('Synced', 1,'user Lib', "User id:$id");
        }

    }