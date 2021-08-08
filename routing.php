<?php
    /**
     **************************************************************************
     * routing.php
     * Routing view
     **************************************************************************
     * @package          Mahan 4
     * @category         Routing
     * @author           Milad Abooali <m.abooali@hotmail.com>
     * @copyright        2012 - 2021 (c) Codebox
     * @license          https://codebox.ir/cbl  CBL v1.0
     **************************************************************************
     * @version          1.0
     * @since            4.0 First time
     * @deprecated       -
     * @link             -
     * @see              -
     * @example          -
     **************************************************************************
     */

    namespace Mahan4;

    global $routing;

    // Test Section
    $routing['test']['/'] = [
        'ui'        =>  'test',
        'cache'     =>  true,
        'plugin'    =>  false
    ];
    $routing['test']['a']['/'] = [
        'ui'        =>  'aaaaaaaaa',
        'cache'     =>  true,
        'plugin'    =>  false
    ];
    $routing['test']['b']['/'] = [
        'ui'        =>  'bbbbbbb',
        'cache'     =>  false,
        'plugin'    =>  false
    ];

    // Home
    $routing['/'] = $routing['home']['/'] = $routing['index']['/'] = [
        'process'   =>  'main/test.php',
        'ui'        =>  'home',
        'cache'     =>  true,
        'plugin'    =>  false
    ];

    // Error 404
    $routing['maintenance']['/'] = [
        'ui'        =>  'maintenance',
        'cache'     =>  true,
        'plugin'    =>  false
    ];

    // Error 404
    $routing['404']['/'] = [
        'ui'        =>  '404',
        'cache'     =>  false,
        'plugin'    =>  false
    ];
