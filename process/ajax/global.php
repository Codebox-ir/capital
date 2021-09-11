<?php
    /**
     **************************************************************************
     * global.php
     * Global - Ajax
     **************************************************************************
     * @package          Mahan 4
     * @category         Core library
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          1.0
     * @since            1.0 First time
     * @deprecated       -
     * @link             -
     * @see              \Mahan4\user
     **************************************************************************
     */

    namespace Mahan4\AJAX;

    use Exception;
    use Mahan4\debugger;
    use Mahan4\m;
    use Plugins\captcha\captcha;

    /**
     * Test Ajax call
     */
    function test(?debugger $debugger) : string {
        $debugger?->log('test','1','AJAX', 'Good Morning Milad');
        return m::randomString(12);
    }

    /**
     * Plugins
     * @throws Exception
     */
    function plugins(?debugger $debugger) {
        if($_REQUEST['plugin']) {
            if(!isset(APP['PLUGIN'][$_REQUEST['plugin']]))
                return "Please install plugin first!";
            else if(APP['PLUGIN'][$_REQUEST['plugin']]==0)
                return "Plugin is disabled!";
            $output = array();
            try {
                m::include('plugins/'.$_REQUEST['plugin'].'/ajax.php');
                $debugger?->log('Plugins','1','AJAX', $_REQUEST['plugin'].' is loaded.');
            }  catch(Exception $e) {
                $debugger?->log('Plugins','1','AJAX', $e);
            }
            if ($_REQUEST['call'] ?? false) {
                $function = "Plugins\\".$_REQUEST['plugin']."\\".$_REQUEST['call'];
                return (function_exists($function))
                    ? $function($debugger)
                    : "Function '$function' is missing!";
            } else {
                return false;
            }
        } else {
            $debugger?->log('Plugins','1','AJAX', 'Plugin is missing');
            return false;
        }

    }