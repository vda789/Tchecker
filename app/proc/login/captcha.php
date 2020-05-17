<?php
    session_start();
    $rand = md5(rand());
    $captcha_Code = substr($rand,0,7);
    $captcha_Code = str_replace("0","1",$captcha_Code);
    if(isset($_SESSION['captcha'])){
        unset($_SESSION['captcha']);
    }
    $_SESSION['captcha'] = $captcha_Code;

    header("Content-Type: image/png");
    $image = imagecreatefrompng("bgcaptcha.png");
    $font = dirname(__FILE__)."/font/anonymous.ttf";
    $corCaptcha = imagecolorallocate($image,64,60,65);

    imagettftext($image,150,10,10,300,$corCaptcha,$font,$captcha_Code);
    imagepng($image);
    imagedestroy($image);
