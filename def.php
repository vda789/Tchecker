<?php
    define("VIEW",__DIR__."/app/View");
    define("VIEW_LOGADO",VIEW."/logado");
    define("VIEW_DESLOGADO",VIEW."/deslogado");
    define("VIP",VIEW_LOGADO.'/vip');
    define("BASIC",VIEW_LOGADO.'/basic');
    define("SUPERIOR",VIEW_LOGADO.'/superior');

    define("LOG",__DIR__."/logs");

    define("TOOLS",'app/tools');
    define("CSS",TOOLS."/css");

    define("PROC",__DIR__."/app/proc");

    define("PROCVIP",PROC."/.vip");
    define("PROCBASIC",PROC."/.basic");
    define("PROCSUPERIOR",PROC."/.superior");

    $infoDb = ['host' => "127.0.0.1",'user' => 'root','password' => '','database' => 'tcheka'];