<?php
session_start();

require_once 'def.php';
require_once PROC.'/db/database.php';
require_once TOOLS.'/php/functions.php';

$db = new database($infoDb);

$common = new commons();

if(!isset($_SESSION['ip_user']))
      $common->GetIp("init");

if($common->CheckIpFirewallReject($_SESSION['ip_user']))
  die("voce não vale oq esse site tem a oferecer...");

    if(!isset($_SESSION['logado'])){
        if(isset($_REQUEST['pot'])){
            $req = $_REQUEST['pot'];
                switch($req){
                    case 'login':
                        include_once PROC.'/login/login.php';
                        break;
                    case 'deslogar':
                        $common->DetructSessions();
                        break;
                    default:
                        $common->DetructSessions("pg=erro&error=404");
                        break;
                }
        }
        else{
            include_once VIEW_DESLOGADO.'/header.php';
            $get = '';
            if(isset($_GET['pg'])){
                $get = $_GET['pg'];
            }
            switch($get){
                case 'index':
                    include_once VIEW_DESLOGADO.'/index.php';
                    break;
                case 'login':
                    include_once VIEW_DESLOGADO.'/index.php';
                    break;
                case 'erro':
                    include_once VIEW.'/error.php';
                    break;
                default:
                    include_once VIEW_DESLOGADO.'/index.php';
                    break;
            }
            include_once VIEW_DESLOGADO.'/footer.php';
        }
    }
else{
    $idUser = $_SESSION['usuario']['id_user'];
    $sql = "SELECT * FROM login_controll JOIN users ON users.id_user = login_controll.id_user WHERE users.id_user = $idUser";
    $conn = $db->Connect();
    if($res = $conn->query($sql)){
        if($res->num_rows == 1){
            $dados = $res->fetch_assoc();
            if($dados['session'] != $_SESSION['dados_login']['session']){
                $common->DetructSessions("pg=index&erro=alguem entrou na sua conta");
            }
        }else{
            $common->FirewallReject($_SESSION['ip_user']);
            $common->DetructSessions("pg=index&erro=sua requisicao foi negada");
        }
    }else
        $common->DetructSessions("pg=index&erro=contate com o adm erro na index");
    
$conn->close();
$db->Close();
      //dupla verificação de sessão logado
        if(isset($_SESSION['usuario']) && isset($_SESSION['logado']) && $_SESSION['usuario']['privilegio'] == 'vip'){
            if(isset($_REQUEST['pot'])){
                $req = $_REQUEST['pot'];
                switch($req){
                    case 'checkers':
                        include_once PROCVIP.'/chk/index.php';
                        break;
                    case 'deslogar';
                        $common->DetructSessions();
                        break;
                    default:
                        header("Location: ?pg=error&error=404");
                        break;
                }
            }else{
                $get = '';
                if(isset($_GET['pg']))
                    $get = $_GET['pg'];
                
                include_once VIP.'/header.php';
                switch($get){
                    case 'index':
                        include_once VIP.'/index.php';
                        break;
                    case 'checkers':
                        include_once VIP.'/chk/checkers.php';
                        break;
                    case 'erro':
                        include_once VIEW.'/error.php';
                        break;
                    default:
                        include_once VIP.'/index.php';
                        break;
                }
                include_once VIP.'/footer.php';
            }
        }else if(isset($_SESSION['usuario']) && isset($_SESSION['logado']) && $_SESSION['usuario']['privilegio'] == 'basic'){
            if(isset($_REQUEST['pot'])){
                $req = $_REQUEST['pot'];
                switch($req){
                    case 'checkers':
                        include_once PROCBASIC.'/chk/index.php';
                        break;
                    case 'deslogar';
                        $common->DetructSessions();
                        break;
                    default:
                        header("Location: ?pg=error&error=404");
                        break;
                }
            }else{
                $get = '';
                if(isset($_GET['pg'])){
                    $get = $_GET['pg'];
                }
                include_once BASIC.'/header.php';
                switch($get){
                    case 'index':
                        include_once BASIC.'/index.php';
                        break;
                    case 'checkers':
                        include_once BASIC.'/chk/checkers.php';
                        break;
                    case 'erro':
                        include_once VIEW.'/error.php';
                        break;
                    default:
                        include_once BASIC.'/index.php';
                        break;
                }
                include_once BASIC.'/footer.php';
            }
        }else if(isset($_SESSION['usuario']) && isset($_SESSION['logado']) && $_SESSION['usuario']['privilegio'] == 'superior'){
            if(isset($_REQUEST['pot'])){
                $req = $_REQUEST['pot'];
                switch($req){
                    case 'checkers':
                        include_once PROCSUPERIOR.'/chk/index.php';
                        break;
                    case 'deslogar';
                        include_once PROC.'/deslogar.php';
                        break;
                    default:
                        header("Location: ?pg=error&error=404");
                        break;
                }
            }else{
                $get = '';
                if(isset($_GET['pg'])){
                    $get = $_GET['pg'];
                }
                include_once SUPERIOR.'/header.php';
                switch($get){
                    case 'index':
                        include_once SUPERIOR.'/index.php';
                        break;
                    case 'checkers':
                        include_once SUPERIOR.'/chk/checkers.php';
                        break;
                    case 'erro':
                        include_once VIEW.'/error.php';
                        break;
                    default:
                        include_once SUPERIOR.'/index.php';
                        break;
                }
                include_once SUPERIOR.'/footer.php';
            }
        }else
            $common->DetructSessions("pg=index&erro=algo deu errado no seu login contate o adm");
        
    }