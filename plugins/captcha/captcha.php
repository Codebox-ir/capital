<?php
/**
 **************************************************************************
 * captcha.php
 * Captcha Plugin
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

namespace Plugins\captcha;

use Exception;
use Mahan4\debugger;

/**
 * Class captcha
 * @package Plugins\captcha
 */
class captcha {

    private string $bg_path = 'plugins/captcha/backgrounds/';
    private string $font_path =  'plugins/captcha/fonts/';
    private string $error='';
    private ?debugger $debugger;
    private int $length=1;
    private array $config;

    /**
     * captcha constructor.
     */
    function __construct(?debugger $debugger=null) {
        $this->debugger = $debugger;
        if( !function_exists('gd_info') ) {
            $this->error = 'Required GD library is missing';
            $this->debugger?->log('Error','0','captcha', $this->error);
        } else {
            if (isset($_SESSION['plugins']['captcha']['length']))
                $this->length = ($_SESSION['plugins']['captcha']['length'] < 10) ? $_SESSION['plugins']['captcha']['length'] : 9;
            $rate = $this->length/3;
            $this->config = array(
                'code' => $_SESSION['plugins']['captcha']['code'] ?? null,
                'min_length' => 3+$rate,
                'max_length' => 4+$rate,
                'backgrounds' => array(
                    $this->bg_path.$this->length.'.png',
                    $this->bg_path.(($this->length == 1) ? 2 : ($this->length-1)).'.png',
                    $this->bg_path.(($this->length == 9) ? 7 : ($this->length+1)).'.png'
                ),
                'fonts' => array(
                    $this->font_path . 'times_new_yorker.ttf',
                    $this->font_path . 'font4.ttf',
                    $this->font_path . 'font5.ttf',
                    $this->font_path . 'font6.ttf',
                    $this->font_path . 'font7.ttf',
                    $this->font_path . 'font9.ttf',
                    $this->font_path . 'font12.ttf',
                    $this->font_path . 'font13.ttf',
                    $this->font_path . 'font14.ttf',
                    $this->font_path . 'font15.ttf',
                    $this->font_path . 'font16.ttf',
                    $this->font_path . 'font17.ttf',
                    $this->font_path . 'font18.ttf',
                    $this->font_path . 'font20.ttf',
                    $this->font_path . 'font21.ttf',
                    $this->font_path . 'font22.ttf',
                    $this->font_path . 'font23.ttf',
                    $this->font_path . 'font24.ttf',
                    $this->font_path . 'font25.ttf',
                    $this->font_path . 'font26.ttf',
                    $this->font_path . 'font27.ttf',
                    $this->font_path . 'font28.ttf',
                    $this->font_path . 'font29.ttf',
                    $this->font_path . 'font30.ttf',
                    $this->font_path . 'font31.ttf',
                    $this->font_path . 'font32.ttf',
                    $this->font_path . 'font34.ttf'
                ),
                'characters' => 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmknpqrstuvwxyz23456789',
                'min_font_size' => 9-$rate,
                'max_font_size' => 25+$rate,
                'color' => '#'.rand(1,9).rand(1,9).rand(1,9),
                'angle_min' => 0,
                'angle_max' => 120,
                'shadow' => true,
                'shadow_color' => '#FFF',
                'shadow_offset_x' => -1,
                'shadow_offset_y' => 1
            );

            // Restrict certain values
            if( $this->config['min_length'] < 1 )
                $this->config['min_length'] = 1;
            if( $this->config['angle_min'] < 0 )
                $this->config['angle_min'] = 0;
            if( $this->config['angle_max'] > 10 )
                $this->config['angle_max'] = 10;
            if( $this->config['angle_max'] < $this->config['angle_min'] )
                $this->config['angle_max'] = $this->config['angle_min'];
            if( $this->config['min_font_size'] < 10 )
                $this->config['min_font_size'] = 10;
            if( $this->config['max_font_size'] < $this->config['min_font_size'] )
                $this->config['max_font_size'] = $this->config['min_font_size'];

        }

    }

    /**
     * New Code
     */
    public function new() : void
    {
        $this->config['code'] = '';
        $length = mt_rand($this->config['min_length'], $this->config['max_length']);
        while(strlen($this->config['code']) < $length) {
            $characters = str_shuffle($this->config['characters']);
            $this->config['code'] .= substr($this->config['characters'], mt_rand() % (strlen($characters)), 1);
        }
        $this->debugger?->log('Captcha','1','Plugins', $this->config['code']);
        $_SESSION['plugins']['captcha']['code'] = $this->config['code'];
    }

    /**
     * HEX to RGB
     */
    private function hex2rgb(string $hex_str, bool $return_string = false, string $separator = ',') :bool|string|array
    {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = array();
        if( strlen($hex_str) == 6 ) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif(strlen($hex_str) == 3) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
          return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }

    /**
     * Render Captcha
     */
    public function render() : void
    {

        $background = $this->config['backgrounds'][mt_rand(0, count($this->config['backgrounds']) -1)];
        list($bg_width, $bg_height, $bg_type, $bg_attr) = getimagesize($background);

        $captcha = imagecreatefrompng($background);

        $color = $this->hex2rgb($this->config['color']);
        $color = imagecolorallocate($captcha, $color['r'], $color['g'], $color['b']);

        $angle = mt_rand($this->config['angle_min'], $this->config['angle_max'])*(mt_rand(0, 1) == 1 ? -1 : 1);

        $font = $this->config['fonts'][mt_rand(0, count($this->config['fonts']) - 1)];

        while(!file_exists($font))
            $font = $this->config['fonts'][mt_rand(0, count($this->config['fonts']) - 1)];

        $font_size = mt_rand($this->config['min_font_size'], $this->config['max_font_size']);
        $text_box_size = imagettfbbox($font_size, $angle, $font, $this->config['code']);

        $box_width = abs($text_box_size[6] - $text_box_size[2]);
        $box_height = abs($text_box_size[5] - $text_box_size[1]);
        $text_pos_x_min = 0;
        $text_pos_x_max = ($bg_width) - ($box_width);
        $text_pos_x = mt_rand($text_pos_x_min, $text_pos_x_max);
        $text_pos_y_min = $box_height;
        $text_pos_y_max = ($bg_height) - ($box_height / 2);
        if ($text_pos_y_min > $text_pos_y_max) {
            $temp_text_pos_y = $text_pos_y_min;
            $text_pos_y_min = $text_pos_y_max;
            $text_pos_y_max = $temp_text_pos_y;
        }
        $text_pos_y = mt_rand($text_pos_y_min, $text_pos_y_max);

        if( $this->config['shadow'] ){
            $shadow_color = $this->hex2rgb($this->config['shadow_color']);
            $shadow_color = imagecolorallocate(
                $captcha,
                $shadow_color['r'],
                $shadow_color['g'],
                $shadow_color['b']
            );
            imagettftext(
                $captcha,
                $font_size,
                $angle,
                $text_pos_x + $this->config['shadow_offset_x'],
                $text_pos_y + $this->config['shadow_offset_y'],
                $shadow_color,
                $font,
                $this->config['code']
            );
        }

        imagettftext(
            $captcha,
            $font_size,
            $angle,
            $text_pos_x,
            $text_pos_y,
            $color,
            $font,
            $this->config['code']
        );
        header("Content-type: image/png");
        imagepng($captcha);
    }

}

