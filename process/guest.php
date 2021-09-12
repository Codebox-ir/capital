<?php

use Mahan4\m;

if(isset($_SESSION['M4']['user']) && $_SESSION['M4']['user']['id']>0){
    header('Location: dashboard');
    exit;
}

$csc = new cscList\cscList();
    $this->Page_DATA->countries = $csc->countries();

    $captcha = m::plugin("captcha");
    $captcha->new();

