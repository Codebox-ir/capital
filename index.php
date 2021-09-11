<?php
    /**
     **************************************************************************
     * index.php
     * Routing workflow start point
     **************************************************************************
     * @package          Mahan 4
     * @category         Routing
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          1.0
     * @since            1.0 First time
     * @deprecated       -
     * @link             -
     * @see              -
     * @example          -
     **************************************************************************
     */

    namespace Mahan4;

    use Exception;

    /**
     * PHP Version
     */
    if(PHP_MAJOR_VERSION < 8)
        die('PHP v8 required, current version is '.phpversion());

    /**
     * Global mahan array
     */
    $m_global=array(
        'header'          => ['<!-- Mahan4 Global Header -->'.PHP_EOL],
        'footer'          => ['<!-- Mahan4 Global Footer -->'.PHP_EOL],
        'date'            => date("Y-m-d")
    );

    /**
     * Load M
     */
    if(file_exists('core/m.php'))
        require_once 'core/m.php';
    else
        die("<strong>core/m.php</strong> is not exist!");
    $m_global['client_ip'] = m::clientIP();

    /**
     * Load Config
     */
    try {
        define('MAHAN', m::ini("core/mahan.config.ini"));
    } catch(Exception $e) {
        die($e->getMessage());
    }
    try {
        define('APP', m::ini("core/app.config.ini"));
    } catch(Exception $e) {
        die($e->getMessage());
    }
    ini_set('memory_limit', MAHAN['INI']['memory_limit']);
    date_default_timezone_set(APP['CONFIG']['Timezone']);
    mb_internal_encoding('utf-8');
    mb_http_output('utf-8');
    mb_language('uni');
    mb_regex_encoding('utf-8');
    define('APP_URL',APP['WS']['protocol'] . APP['WS']['url']);
    define('APP_URL_PORT',APP['WS']['protocol'] . APP['WS']['url'] . ':' . APP['WS']['port']);

    /**
     * Autoloader - composer
     * For add dynamic
     * $autoloader = m::include('vendor/autoload.php',true);
     */
    try {
        m::include('vendor/autoload.php',true);
    } catch(Exception $e) {
        die($e->getMessage());
    }

    /**
     * Core Static files
     */
    try {
        if(($_GET['M4debugger'] ?? false) || APP['CONFIG']['debugger']==2) {
            $m_global['header'][] = m::css('core/style.css');
            $m_global['header'][] = m::js('core/jquery-3.6.0.min.js',true);
            $m_global['header'][] = m::js('core/json-view.js',false);
        }
    } catch(Exception $e) {
        die($e->getMessage());
    }

    /**
     * Debugger
     */
    $_SESSION['M4']['DEV'] = $_GET['M4dev'] ?? ($_SESSION['M4']['DEV'] ?? false);
    $debugger = null;

    try {
        if((APP['CONFIG']['debugger'] && $_SESSION['M4']['DEV']) || APP['CONFIG']['debugger']==2) {
            $debugger = new debugger();
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(0);
        }
    } catch(Exception $e) {
        die($e->getMessage());
    }

    /**
     * Time Log - Page
     */
    $timer = (MAHAN['LIB']['timer']) ? new timer($debugger) : null;

    /**
     * iSQL - Main & Remote Database
     */
    $db_main = (MAHAN['LIB']['isql_main']) ? new i_mysql(APP['MySQL_Main'], $debugger) : null;
    $db_remote = (MAHAN['LIB']['isql_remote']) ? new i_mysql(APP['MySQL_Remote'], $debugger) : null;

    /**
     * iSQLite - Mahan
     */
    try {
        $db_mahan = new i_sqlite('core/db/mahan.db', $debugger);
    } catch (Exception $e) {
        die($e->getMessage());
    }

    /**
     * Session
     */
    $session = (MAHAN['LIB']['session']) ? new session($db_mahan, $debugger) : null;
    $session?->start();

    /**
     * Error Log
     */
    ini_set('log_errors', 1);
    ini_set('error_log',m::errorLogPath());

    /**
     * Dynamic Token Checker
     */
    $_SESSION['M4']['TOKENS']['ajax'] = md5(APP['TOKEN']['ajax'].session_id());
    $_SESSION['M4']['TOKENS']['uid_old'] = $_SESSION['M4']['TOKENS']['uid_new'] ?? null;
    $m_global['uid'] = md5(APP['TOKEN']['mahan'].m::randomString());

    /**
     * Echo Ajax Token
     */
    $m_global['header'][] = '<meta name="app-t" content="'.md5(APP['TOKEN']['ajax'].session_id()).'">';

    /**
     * User
     */
    $user = (MAHAN['LIB']['user']) ?  new user(($_SESSION['M4']['user']['id'] ?? 0), $debugger) : null;

    /**
     * Routing
     */
    $view = array();
    define('ASSETS_PATH',APP['WS']['protocol'].APP['WS']['url'].'assets/'.APP['CONFIG']['ui'].'/');
    try {
        m::include('routing.php', 1);
        if(APP['CONFIG']['maintenance'] && !$_SESSION['M4']['DEV']) {
            $view = $routing['maintenance']['/'] ?? false;
            $debugger=null;
        } else {
            $requested_view = $_SERVER['REDIRECT_URL'] ?? '//';
            $req_view = trim($requested_view, '/');
            $req_units = explode('/', $req_view);
            if($req_units[0]=='ajax') {
                $ajax = new ajax($debugger);
                $class = $req_units[2] ?? 'global';
                $function = $req_units[1] ?? null;
                $ajax->act('Mahan4\AJAX\\'.$function, $class);
            } else {
                if($req_units[5] ?? false)
                    $view = $routing[$req_units[0]][$req_units[1]][$req_units[2]][$req_units[3]][$req_units[4]][$req_units[5]]['/'] ?? false;
                else if($req_units[4] ?? false)
                    $view = $routing[$req_units[0]][$req_units[1]][$req_units[2]][$req_units[3]][$req_units[4]]['/'] ?? false;
                else if($req_units[3] ?? false)
                    $view = $routing[$req_units[0]][$req_units[1]][$req_units[2]][$req_units[3]]['/'] ?? false;
                else if($req_units[2] ?? false)
                    $view = $routing[$req_units[0]][$req_units[1]][$req_units[2]]['/'] ?? false;
                else if($req_units[1] ?? false)
                    $view = $routing[$req_units[0]][$req_units[1]]['/'] ?? false;
                else if($req_units[0] ?? false)
                    $view = $routing[$req_units[0]]['/'] ?? false;
                else
                    $view = $routing['/'] ?? false;
                if(!$view)
                    $view = $routing['404']['/'] ?? false;

                /**
                 * Make page
                 */
                $view = new view($view, $debugger);
                $view->generate();

            }
        }
    } catch (Exception $e) {
        die($e->getMessage());
    }

    /**
     * End Action for page
     */
    $timer->end(null);
    $_SESSION['M4']['TOKENS']['uid_new'] = $m_global['uid'];
    $debugger?->log("Data",1,'Session', $_SESSION);
    if($req_units[0]!='ajax')
        echo $debugger?->get('ui');
    exit;


    // Incloud Process
    // Incloud Plugin
    // Run Filters
    // Make Cache
    // Add Log
    // Incloud UI


##################### Temporary Codes

/* creat Table Sample
$cols = array(
        "id         INT     PRIMARY KEY     NOT NULL",
        "name       CHAR(50)",
        "data       json"
);
try {
    m::print($db_mahan->iAddTable('test',$cols,1));
} catch (Exception $e) {
    $debugger?->log("iAddTable",0,'SQLite', $e->getMessage());
}
*/