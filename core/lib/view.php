<?php
    /**
     **************************************************************************
     * view.php
     * View Generator
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

    use Exception;
    use stdClass;

    /**
     * Class View
     */
    class view
    {

        const UI_PATH = 'ui/'.APP['CONFIG']['ui'].'/';
        private ?debugger $debugger;
        private array $view;
        private ?minifier $minifier=null;
        private $Global_DATA;
        private $Page_DATA;
        /**
         * View constructor.
         */
        function __construct(array $view, ?debugger $debugger=null)
        {
            $this->debugger = $debugger;
            $this->view = $view;
            global $m_global;
            $this->Global_DATA = $m_global;
            $this->Page_DATA = new stdClass();
            if(MAHAN['LIB']['minifier'] && !isset($view['no_minify'])) {
                $options = $view['minifier_option'] ?? null;
                $this->minifier = new minifier($options);
            }
        }

        /**
         * Process View
         * @return void
         */
        private function _process(): void
        {
            try {
                $path = 'process/'.$this->view['process'];
                (file_exists($path))
                    ? require_once $path
                    : throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        public function generate()
        {
            $_SESSION['M4']['TOKENS']['expired'] = time()+APP['CONFIG']['csrf'];
            if(isset($this->view['process']))
                $this->_process();
            if(!APP['CONFIG']['maintenance'] && APP['CONFIG']['cache'] && $this->view['cache']) {
                $file_name = str_replace('/','_', $_SERVER['REDIRECT_URL'] ?? '//');
                $cached_file_path = 'files/cache/'.$file_name.'.php';
                if(file_exists($cached_file_path) && (time()-APP['CONFIG']['cache_time'])<filemtime($cached_file_path))
                {
                    $file_content = "<!-- Cached version, generated ".date('Y-m-d H:i', filemtime($cached_file_path))." by Mahan4 -->".file_get_contents($cached_file_path);
                } else {
                    ob_start();
                    try {
                        $path = self::UI_PATH.$this->view['ui'].'.php';
                        (file_exists($path))
                            ? require_once $path
                            : throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                    $cache_file = fopen($cached_file_path, 'w');
                    $file_content = ($this->minifier) ? $this->minifier->minify(ob_get_contents()) : ob_get_contents();
                    fwrite($cache_file, $file_content);
                    fclose($cache_file);
                    ob_end_clean();
                }
            } else {
                ob_start();
                try {
                    $path = self::UI_PATH.$this->view['ui'].'.php';
                    (file_exists($path))
                        ? require_once $path
                        : throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
                } catch (Exception $e) {
                    die($e->getMessage());
                }
                $file_content = ($this->minifier) ? $this->minifier->minify(ob_get_contents()) : ob_get_contents();
                ob_end_clean();
            }
            $output = preg_replace('/<body>/', '<^^ flush(); ^^><body>', $file_content, 1);;
            $output = str_replace('<^^ ','<?php ', $output);
            $output = str_replace('<^^= ','<?= ', $output);
            $output = str_replace(' ^^>',' ?>', $output);
            eval ("?> $output");
        }

    }