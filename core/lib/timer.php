<?php
    /**
     **************************************************************************
     * timer.php
     * Time Log
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
     * @example          $timer = new timer($debugger);
     * @example          $start = $timer->start('test');
     * @example          $end = $timer->end('test');
     * @example          $get = $timer->get('test');
     * @example          $get_all = $timer->get();
     **************************************************************************
     */

    namespace Mahan4;

    /**
     * Class timer
     */
    class timer
    {

        /**
         * @var array $Errors List Of Errors
         * @var array $blocks List To Keep Blocks Data
         * @var debugger|null $debugger List To Keep Blocks Data
         */
        public array $Errors;
        private array $blocks;
        private debugger|null $debugger=null;

        /**
         * genTime constructor.
         *
         * @param debugger|null $debugger
         */
        function __construct(debugger|null $debugger=null) {
            $this->debugger = $debugger;
            $this->start('page');
        }

        /**
         * Add Block Start Time
         *
         * @param string $block
         * @return mixed
         */
        public function start(string $block): mixed
        {
            if(isset($this->blocks[$block]['start'])) {
                $debug_backtrace = debug_backtrace();
                $caller = array_shift($debug_backtrace);
                $this->Errors['block_exist'] = 'Block: '.$block.' | '.$caller['file'].' Line '.$caller['line'];
                $this->debugger?->log("Start > $block",0,'timer', $this->Errors['block_exist']);
                return $this->Errors['block_exist'];

            }
            $this->blocks[$block]['start'] = microtime(true);
            $this->debugger?->log("Start > $block",1,'timer',$this->blocks[$block]['start']);
            return $this->blocks[$block]['start'];
        }

        /**
         * Add Block End Time
         *
         * @param ?string $block
         * @return mixed
         */
        public function end(?string $block): mixed
        {
            if ($block){
                if (!isset($this->blocks[$block]['start'])) {
                    $debug_backtrace = debug_backtrace();
                    $caller = array_shift($debug_backtrace);
                    $this->Errors['miss_start'] = 'Block: '.$block.' | '.$caller['file'].' Line '.$caller['line'];
                    $this->debugger?->log("End > $block",0,'timer', $this->Errors['miss_start']);
                    return $this->Errors['miss_start'];
                }
                $this->blocks[$block]['end']  = microtime(true);
                $this->blocks[$block]['time'] = round($this->blocks[$block]['end'] - $this->blocks[$block]['start'],4, PHP_ROUND_HALF_UP);
                $this->debugger?->log("End > $block",1,'timer',$this->blocks[$block]);
                return $this->blocks[$block]['time'];
            } else {
                foreach ($this->blocks as $block => $times) {
                    $this->blocks[$block]['end']  = microtime(true);
                    $this->blocks[$block]['time'] = round($this->blocks[$block]['end'] - $this->blocks[$block]['start'],4, PHP_ROUND_HALF_UP);
                    $this->debugger?->log("End > $block",1,'timer',$this->blocks[$block]);
                }
                return $this->blocks['page']['time'];
            }
        }

        /**
         * Get Block(s) Time
         *
         * @param string|null $block Null to list all blocks
         * @return mixed
         */
        public function get(string $block=null): mixed
        {
            if(!$block)
                return $this->Errors ?? $this->blocks;
            if(isset($this->blocks[$block]))
                return $this->Errors ?? $this->blocks[$block];
            else
                return false;
        }

    }