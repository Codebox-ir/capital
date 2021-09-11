<?php


use Mahan4\m;

$csc = new cscList\cscList();
    $this->Page_DATA->countries = $csc->countries();

    $captcha = m::plugin("captcha");
    $captcha->new();

