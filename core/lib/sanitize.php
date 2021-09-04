<?php
    /**
     **************************************************************************
     * sanitize.php
     * Sanitize Solutions
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
     * Class sanitize
     */
    class sanitize
    {

        /**
         * Is Float
         */
        public static function isFloat(mixed $value) :bool
        {
            return ctype_digit($value);
        }

        /**
         * Is Integer
         */
        public static function isInt(mixed $value) :bool
        {
            return is_numeric($value);
        }

        /**
         * Contain Number
         */
        public static function haveNumber(mixed $value) :bool
        {
            return preg_match('@[0-9]@', $value);
        }

        /**
         * Contain Lower Case Character
         */
        public static function haveLowerCase(mixed $value) :bool
        {
            return preg_match('@[a-z]@', $value);
        }

        /**
         * Contain Upper Case Character
         */
        public static function haveUpperCase(mixed $value) :bool
        {
            return preg_match('@[A-Z]@', $value);
        }

        /**
         * Is Mix
         * return null when is mix.
         */
        public static function isMix(mixed $value, bool $number=false, bool $uppercase=false, bool $lowercase=false) :string
        {
            $e = null;
            if($number) $e .= (self::haveNumber($value)) ? '' : 'Number missing ';
            if($lowercase) $e .= (self::haveLowerCase($value)) ? '' : 'Lowercase missing ';
            if($uppercase) $e .= (self::haveUpperCase($value)) ? '' : 'Uppercase missing ';
            return ($e);
        }

        /**
         * is Email
         */
        public static function isEmail(string $value) :bool
        {
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        }

    }