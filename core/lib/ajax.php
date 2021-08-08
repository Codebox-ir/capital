<?php
    /**
     **************************************************************************
     * ajax.php
     * Ajax
     **************************************************************************
     * @package          Mahan 4
     * @category         Core library
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          3.0
     * @since            2.0 First time
     * @deprecated       -
     * @link             -
     * @see              -
     **************************************************************************
     */

    namespace Mahan4;

    use stdClass;

    /**
     * Class Ajax
     */
    class ajax
    {
        private ?debugger $debugger;
        public $TOKEN;
        private $mahan_token;
        private $ajax_token;
        private $csrf;
        private $CLASS;

        /**
         * Ajax constructor.
         * @param debugger|null $debugger
         */
        function __construct(?debugger $debugger=null)
        {
            $this->debugger = $debugger;
            $this->ajax_token = $_REQUEST['token'] ?? null;
            $this->csrf = m::csrf() && $this->ajax_token==md5(APP['TOKEN']['ajax'].session_id());
        }

        /**
         * Action
         */
        public function act (?string $function=null, string $class) : void
        {
            header('Content-Type: application/json');
            $output = new stdClass();
            if ($this->csrf) {
                $_SESSION['M4']['TOKENS']['expired'] = time()+APP['CONFIG']['csrf'];
                $this->debugger?->log("Data",1,'Session', $_SESSION);
                $class_file = 'process/ajax/'.$class.'.php';
                (file_exists($class_file))
                    ? require_once($class_file)
                    : die("class '$class' is missing!");
                $output->res = (function_exists($function))
                    ? $function($this->debugger)
                    : "Function '$function' is missing!";
            } else {
                $output->error = 'CSRF Token is not Valid';
            }
            $output->debugger = $this->debugger?->get();
            echo json_encode($output);
        }

    }