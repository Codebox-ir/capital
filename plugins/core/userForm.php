<?php
    /**
     **************************************************************************
     * userForm.php
     * User Manager - Sanitize Forms
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

    namespace Mahan4\Plugins;

    use cscList\cscList;
    use Mahan4\sanitize;
    use Exception;

    /**
     * Class user
     */
    class userForm
    {

        static array $REQUIRED = [
            'email',
            'f_name',
            'l_name',
            'country',
            'city'
        ];

        /**
         * Email
         * @throws Exception
         */
        public static function email(mixed $value) :string
        {
            if(sanitize::isEmail($value) && (strlen($value)>4))
                return strtolower($value);
            throw new Exception('Email address is not valid!');
        }

        /**
         * First Name
         * @throws Exception
         */
        public static function f_name(mixed $value) :string
        {
            if(preg_match("/^([a-zA-Z' ]+)$/", $value) && (strlen($value)>2))
                return ucfirst($value);
            throw new Exception('First Name is not valid!');
        }

        /**
         * Last Name
         * @throws Exception
         */
        public static function l_name(mixed $value) :string
        {
            if(preg_match("/^([a-zA-Z' ]+)$/", $value) && (strlen($value)>2))
                return ucfirst($value);
            throw new Exception('Last Name is not valid!');
        }

        /**
         * Country
         * @throws Exception
         */
        public static function country(mixed $value) :string
        {
            $cscList = new cscList();
            if($cscList->isCountry($value))
                return $value;
            throw new Exception('Country is not contain in our list!');
        }

        /**
         * City
         * @throws Exception
         */
        public static function city(mixed $value) :string
        {
            if(preg_match("/^([a-zA-Z' ]+)$/", $value) && (strlen($value)>2))
                return $value;
            throw new Exception('City Name is not valid!');
        }

        /**
         * Password New
         * @throws Exception
         */
        public static function password_new(mixed $value) :string
        {
            if(sanitize::isMix($value,1,1,1)==null && (strlen($value)>5))
                return password_hash($value, PASSWORD_DEFAULT);
            throw new Exception('Password is not strong!');
        }

        /**
         * Password Get
         * @throws Exception
         */
        public static function password(mixed $value) :string
        {
            if(sanitize::isMix($value,1,1,1)==null && (strlen($value)>5))
                return $value;
            throw new Exception('Password is not strong!');
        }

    }