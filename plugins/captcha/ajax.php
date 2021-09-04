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

use Mahan4\debugger;
use Mahan4\m;

function test(?debugger $debugger) : string {
    $debugger?->log('Captcha','1','AJAX', 'Test Loaded');
    return 99999999;
}