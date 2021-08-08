<?php
    /**
     **************************************************************************
     * session.php
     * Session Manager
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
     * Class Session Manager
     * @ToDo  save in database
     */
    class session
    {

        private ?debugger $debugger;
        private i_sqlite $db;

        /**
         * Session Manager constructor.
         */
        function __construct(i_sqlite $db, ?debugger $debugger=null)
        {
            $this->debugger = $debugger;
            $this->db = $db;

            // Session Settings
            if(session_status() == PHP_SESSION_NONE && !headers_sent()) {
                if (session_save_path() != 'core/session')
                    session_save_path('core/session');
                ini_set('session.gc_probability', 1);
            }
        }

        /**
         * Start New Session
         * @return void
         */
        public function start(): void
        {
            if(session_status() == PHP_SESSION_NONE && !headers_sent()) {
                session_name('M4');
                session_start();
                $this->debugger?->log('Started','1','Session', session_id());
            }
        }

        /**
         * Renew Session
         * @return void
         */
        public function renew(): void
        {
            $this->end();
            $this->start();
        }

        /**
         * End M Session Item
         * @return void
         */
        public function end(): void
        {
            $this->start();
            $this->debugger?->log('Ended','1','Session', session_id());
            $_SESSION=[];
            session_destroy();
        }


    }