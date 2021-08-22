<?php
    /**
     **************************************************************************
     * m.php
     * Mahan Main Functions
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
     * @see              -
     * @example          -
     **************************************************************************
     */

    namespace Mahan4;

    use Exception;

    /**
     * Class m
     */
    class m
    {
        /**
         * Log Errors
         */
        public static function errorLogPath() : string {
            global $m_global;
            $folder = 'files/logs/'.$m_global['date'];
            if (!file_exists($folder))
                mkdir($folder, 0777, true);
            $ip = preg_replace('/:/', '_', $m_global['client_ip']);;
            return $folder.'/'.$ip.'_'.session_id().'.log';
        }

        /**
         * CSRF
         * @param null $token
         * @return int
         */
        public static function csrf() : bool {
            return (time() < $_SESSION['M4']['TOKENS']['expired']) ? 1 : 0;
        }

        /**
         * Include and include php files
         *
         * @param string $path
         * @param bool $required
         * @param bool $once
         * @return mixed
         * @throws Exception
         * @noinspection PhpIncludeInspection
         */
        public static function include(string $path, bool $required=false, bool $once=true): mixed
        {
            if(file_exists($path)) {
                if ($required)
                    return ($once) ? require_once $path : require $path;
                else
                    return ($once) ? include_once $path : include $path;
            } else {
                throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
            }
        }

        /**
         * Link css file
         *
         * @param string $path
         * @return string
         */
        public static function css(string $path): string
        {
            return '<link rel="stylesheet" type="text/css" href="'.$path.'">';
        }

        /**
         * Add css
         *
         * @param string $code
         * @param string|null $pos
         * @return void
         */
        public static function addCSS(string $code, string $pos=null): void
        {
            if($pos==null) echo "<style>$code</style>";
            global $m_global;
            $m_global[$pos][] = "<style>$code</style>";
        }

        /**
         * Link js file
         *
         * @param string $path
         * @param string|null $defer
         * @return string
         */
        public static function js(string $path, ?string $defer='defer'): string
        {
            return "<script src='$path'$defer></script>";
        }

        /**
         * Add js
         *
         * @param string $code
         * @param string|null $pos
         * @return void
         */
        public static function addJS(string $code, string $pos=null): void
        {
            if($pos==null)
                echo "<script>$code</script>";
            global $m_global;
            $m_global[$pos][] = "<script>$code</script>";
        }

        /**
         * JS console.log
         *
         * @param string $value
         * @return void
         * @noinspection BadExpressionStatementJS
         * @noinspection JSVoidFunctionReturnValueUsed
         */
        public static function jsConsole(string $value): void
        {
            echo "<script>console.log('$value')</script>";
        }

        /**
         * JS alert
         *
         * @param array|string $value
         * @param bool $toStr
         * @return void
         * @noinspection BadExpressionStatementJS
         * @noinspection JSUnresolvedFunction
         * @noinspection JSVoidFunctionReturnValueUsed
         */
        public static function jsAlert(array|string $value, bool $toStr=false): void
        {
            if($toStr==false)
                echo "<script>alert($value)</script>";
            else
                echo "<script>alert(JSON.stringify($value))</script>";
        }

        /**
         * Load and pars ini files
         *
         * @param string $path
         * @param bool $section
         * @return array
         * @throws Exception
         */
        public static function ini(string $path, bool $section=true): array
        {
            if(file_exists($path))
                return parse_ini_file($path, $section);
            throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
        }

        /**
         * Print stylish
         *
         * @param $input
         * @param bool $block
         * @param bool $eol
         * @return void
         */
        public static function print($input, bool $block=false, bool $eol=false): void
        {
            $debug_backtrace   = debug_backtrace();
            $file = file($debug_backtrace[0]['file']);
            $line  = $file[$debug_backtrace[0]['line']-1];
            $var_name  = trim(preg_replace('#(.*)'.__FUNCTION__.' *?\( *?(.*) *?\)(.*)#i', '$2', $line));
            echo ($block) ? "<$block>" : null;
            echo ($eol) ? PHP_EOL : null;
            echo '<pre class="debugger"><h5 class="white">'.$var_name.PHP_EOL.'</h5><code>';
            if(is_array($input)) {
                print_r($input);
            } else {
                var_dump($input);
            }
            echo '</code></pre>';
            echo ($eol) ? PHP_EOL : null;
            echo ($block) ? "</$block>" : null;
        }

        /**
         * Get Variable Name
         *
         * @return string
         */
        public static function varName(): string
        {
            $bt   = debug_backtrace();
            $file = file($bt[0]['file']);
            $src  = $file[$bt[0]['line']-1];
            $pat = '#(.*)'.__FUNCTION__.' *?\( *?(.*) *?\)(.*)#i';
            $var  = preg_replace($pat, '$2', $src);
            return trim($var);
        }

        /**
         * Generate Random String
         * @param int|null $length
         * @return string
         */
        public static function randomString(?int $length=16): string  {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for($i=0; $i<$length; $i++)
                $randomString .= $characters[rand(0, $charactersLength-1)];
            return $randomString;
        }

        /**
         * Get client IP
         * @return string return clint IP or false
         */
        public static function clientIP() : string {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            return ($ip) ?? '';
        }


    }