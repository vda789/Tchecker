<?php
 class commons{
    public function SaveFile($filename,$write){
        $file = fopen($filename,"ab");
        $data = date("d/m/Y H:i:s");
        fwrite($file,$write."\n");
        fclose($file);
    }
    function GetRandomMail($user){
        $dataPessoa = file_get_contents(PROC."/chk/rec/pessoas.json");
        $json = json_decode($dataPessoa,true);
        $d = file(PROC."/chk/rec/dominio.txt");
        $dominio = $d[rand(0,count($d) - 1)];
        $nome = trim($json[rand(0,count($json))]["name"]);
        $nome = str_replace(" ","",$nome);
        return "$nome@$dominio";
    }
    function Get4String($string,$start,$end){
        $s = explode($start,$string);
        $s = explode($end,$s[1]);
        return $s[0];
    }
    function GetRandomProxy($user){
        $proxys = file(PROC."/chk/rec/proxisAtive$user.txt");
        $proxy = $proxys[rand(0,count($proxys))];
        return $proxy;
    }
    function encript($chave){
         $chave = md5($chave);
         $cript = md5("c0mI4PuT4DAtU4mAe");
         $y = 0;
         for($i=0;$i<strlen($chave);++$i){
           if($cript[$y] == '\0')
             $y=0;

           $aux = $chave[$i] ^ $cript[$y];
           $chave[$i] = $aux;
           $y++;
         }
         return $chave;
     }
  public function createToken(){
     $alpha = "ABCDEFGHIJKLMNOPKQRSUVWXYZ1234567890";
     $token = '';
     for($i=0; $i < 16;++$i){
       $rand = $alpha[rand(0,strlen($alpha)-1)];
       $token[$i] = $rand;
     }
     return $token;
   }
   public function FirewallReject($ip){
       $file = fopen(LOG."/FirewallReject.log","ab");
       $data = date("d/m/Y H:i:s");
       $message = "data[$data] ip[$ip]\n";
       fwrite($file,$message);
       fclose($file);
   }
   public function CheckIpFirewallReject($ip){
     $file = file_get_contents(LOG."/FirewallReject.log");
     if(strpos($file,$ip) === false){
       return false;
     }else{
       return true;
     }
   }
   public function DetructSessions($pg="pg=index"){
     session_unset();
     session_destroy();
     header("Location: ?$pg");
   }
   public function GetIp($req){
     $proxy_headers = array(
         'HTTP_VIA',
         'HTTP_X_FORWARDED_FOR',
         'HTTP_FORWARDED_FOR',
         'HTTP_X_FORWARDED',
         'HTTP_FORWARDED',
         'HTTP_CLIENT_IP',
         'HTTP_FORWARDED_FOR_IP',
         'VIA',
         'X_FORWARDED_FOR',
         'FORWARDED_FOR',
         'X_FORWARDED',
         'FORWARDED',
         'CLIENT_IP',
         'FORWARDED_FOR_IP',
         'HTTP_PROXY_CONNECTION',
         'HTTP_CF_CONNECTING_IP',
         'HTTP_X_SUCURI_CLIENTIP',
         'HTTP_INCAP_CLIENT_IP'
     );
     $prox = false;
     $ip = '';
     //verificação de proxy
     foreach($proxy_headers as $proxy){
       if(isset($_SERVER[$proxy])){
         $msg = json_encode(array('ip'=>"$_SERVER[$proxy]",'proxy'=> true,'request'=>"$req"));
         $this->SaveFile(LOG."/principal.json",$msg);
         $ip = $_SERVER[$proxy];
         $prox = true;
         break;
       }
     }
     //se não usar proxy
     if(!$prox){
         $msg = json_encode(array('ip'=>"$_SERVER[REMOTE_ADDR]",'proxy'=> false,'request'=>"$req",'data'=>date("d-m-Y H:i:s")));
         $this->SaveFile(LOG."/principal.json",$msg);
         $ip = $_SERVER['REMOTE_ADDR'];
     }

     if(!isset($_SESSION['ip_user'])){
       $_SESSION['ip_user'] = $ip;
     }
   }
}
