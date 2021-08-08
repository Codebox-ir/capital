<?php
    /**
     **************************************************************************
     * debugger.php
     * Debugger system
     **************************************************************************
     * @package          Mahan 4
     * @category         Core library
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          4.0
     * @since            1.0 First time
     * @deprecated       -
     * @link             -
     * @see              -
     * @example          $debugger->log('Insert',0,'db','12');
     **************************************************************************
     */

    namespace Mahan4;

    /**
     * Class debugger
     */
    class debugger
    {

        /**
         * @var array $success Success
         * @var array $Error Errors
         * @var array $Logs Category base
         * @var int $LogId Count Logs
         */
        public array $Error=array();
        public array $Logs=array();
        private int $_logId=0;

        /**
         * debugger constructor.
         */
        function __construct() {
            error_reporting(E_ALL & ~E_NOTICE);
            ini_set('ignore_repeated_errors', false);
            ini_set('display_errors', true);
            ini_set('log_errors', true);
        }

        /**
         * Add Log
         *
         * @param string $title
         * @param int $status
         * @param string $cat
         * @param string|array|null $extra
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         */
        public function log(string $title, int $status=1, string $cat='core', string|array $extra=null): array
        {
            $this->_logId++;
            $status = ($status) ? 'success' : 'error';
            $debug_backtrace = debug_backtrace();
            $caller = array_shift($debug_backtrace);
            $log = array(
              'id' => $this->_logId,
              'cat' => $cat,
              'title' => $title,
              'extra' => $extra,
              'file' => $caller['file'],
              'line' => $caller['line'],
              'status' => $status,
              'time' => microtime(true)
            );
            if($status=='error') $this->Error[$cat][] = $log;
            $this->Logs[$cat][] = $log;
            return $log;
        }

        /**
         * UI generator
         *
         * @param array $log_list
         * @param string $title
         * @param string $background
         * @return string
         */
        private function _ui(array $log_list, string $title, string $background='dark'): string
        {
            $output = "<table><thead><tr class='bg-$background'><th>$title</th></tr></thead><tbody>";
            foreach ($log_list as $cat=>$logs) {
                $output .= '<tr><th colspan="8"></th></tr>';
                $output .= '<tr class="bg-dark '.$cat.'"><th colspan="8">'.strtoupper($cat).' ('.count($logs).') '."</th></tr>";
                $output .= '<tr class="bg-dark ">';
                $output .= '<td>#</td>';
                $output .= '<td>Cat</td>';
                $output .= '<td>Title</td>';
                $output .= '<td>Data</td>';
                $output .= '<td>File</td>';
                $output .= '<td>Line</td>';
                $output .= '<td>Status</td>';
                $output .= '<td>Time</td>';
                $output .= '</tr>';
                foreach ($logs as $log) {
                    $output .= '<tr class="bg-'.$log['cat'].'">';
                    $output .= '<td>'.$log['id'].'</td>';
                    $output .= '<td>'.strtoupper($log['cat']).'</td>';
                    $output .= '<td>'.$log['title'].'</td>';
                    if (is_array($log['extra']))
                        $output .= "<td class='m-extra jsonView'>".json_encode($log['extra'])."</td>";
                    else
                        $output .= "<td class='m-extra'>".$log['extra']."</td>";
                    $output .= '<td>'.$log['file'].'</td>';
                    $output .= '<td>'.$log['line'].'</td>';
                    $output .= '<td class="bg-'.$log['status'].'">'.ucfirst($log['status']).'</td>';
                    $output .= '<td>'.$log['time'].'</td>';
                    $output .= '</tr>';
                }
            }
            $output .= '</tbody></table>';
            return $output;
        }

        /**
         * Get Logs
         *
         * @param string|null $type
         * @return string|array
         */
        public function get(string $type=null): array|string
        {
            if($type=='ui') {
                $ui = "<div id='debugger' class='debugger'><h4>Mahan 4 Debugger</h4>";
                if($this->Error)
                    $ui .= $this->_ui($this->Error,'Errors','error');
                if($this->Logs)
                    $ui .= $this->_ui($this->Logs,'All Logs');
                $ui .= '</div>
                    <script>
                        $( document ).ready(function() {
                            $(".jsonView").each(function(index) {
                              $(this).JSONView($(this).html(), { collapsed: true });
                            });
                        });
                    </script>';
                return $ui;
            } else {
                $data['Error']=$this->Error;
                $data['Logs'] =$this->Logs;
                return $data;
            }
        }

    }