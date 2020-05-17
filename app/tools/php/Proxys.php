<?php
    function GetNewProxy($timeout=60){
        if(file_exists(PROC.'/chk/rec/cookie.txt'))
            unlink(PROC.'/chk/rec/cookie.txt');
            
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => "https://api.proxyscrape.com/?request=getproxies&proxytype=http&timeout=$timeout&country=all&ssl=all&anonymity=all",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(''),
            CURLOPT_USERAGENT => "$_SERVER[HTTP_USER_AGENT]",
            CURLOPT_COOKIESESSION => true,
            CURLOPT_COOKIEJAR =>  PROC.'/chk/rec/cookie.txt',
            CURLOPT_COOKIE => PROC.'/chk/rec/cookie.txt',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        );
        curl_setopt_array($curl,$options);
        $res = curl_exec($curl);
        $str = str_replace(" ","\n",$res);
        
        $f = fopen(PROC."/chk/rec/proxylist.txt","w");
        fwrite($f,$str);
        fclose($f);

        curl_close($curl);
    }
    function TestProxy($timeout=60){    
        $list = file_get_contents(PROC."/chk/rec/proxylist.txt");
        $proxys = explode("\n",$list);
        $ative = fopen(PROC."/chk/rec/proxisAtive.txt","w");
        foreach($proxys as $p){
            $proxy = explode(":",$p);
            $host = $proxy[0];
            $port = $proxy[1];
            if($con = @fsockopen($host,$port,$errCode,$errStr,$timeout)){
                fwrite($ative,$p."\n");
                fclose($con);
            }
        }
        fclose($active);
    }