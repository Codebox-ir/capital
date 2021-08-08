<?php

    namespace Mahan4\AJAX;

    use Mahan4\debugger;
    use Mahan4\m;

    function test(?debugger $debugger) : string {
        $debugger?->log('test','1','AJAX', 'Good Morning Milad');
        return m::randomString(12);
    }
