<?php
    //gravando o ip do remetente
    $common->GetIp("login");
    //verifica ip no firewall
    if($common->CheckIpFirewallReject($_SESSION['ip_user']))
      die(json_encode(['ip'=>"$_SESSION[ip_user]",'erro' => 'voce nao pode mais acessar esse site']));

    //verificação do post
    if(!isset($_POST) || !isset($_POST['captcha']) || !isset($_POST['login']) || !isset($_POST['pass']) || !isset($_POST['token'])){
      if(isset($_SESSION['burl'])){
        $common->FirewallReject($_SESSION['ip_user']);
        unset($_SESSION['burl']);
        die(json_encode(['erro'=>'seu ip foi gravado!, voce nao pode mais retornar ao site...']));
      }
      //se tentar burlar o post
      $_SESSION['burl'] = true;
      die(json_encode(['erro'=>'tente uma gracinha novamente que iremos tomar providencias!']));
    }
    //verifica se parametros vazios
    if(empty($_POST['captcha']) || empty($_POST['token']) || empty($_POST['login']) || empty($_POST['pass'])){
      die(json_encode(['erro'=>'coloque parametros validos!']));
    }
    //recebe os parametros filtrando tudo
    $token = trim(strip_tags(filter_input(INPUT_POST,'token',FILTER_SANITIZE_SPECIAL_CHARS)));
    $captcha = trim(strip_tags(filter_input(INPUT_POST,'captcha',FILTER_SANITIZE_SPECIAL_CHARS)));
    $login = strip_tags(filter_input(INPUT_POST,'login',FILTER_SANITIZE_SPECIAL_CHARS));
    $senha = strip_tags(filter_input(INPUT_POST,'pass',FILTER_SANITIZE_SPECIAL_CHARS));

//verificação do captcha
    if($captcha != $_SESSION['captcha'] || strlen($_SESSION['captcha']) != 7){
      $_SESSION['captcha'] == $common->createToken();
      exit(json_encode(['erro'=>'captcha code is invalid...']));
    }    
    
//verifica token
    if($token != $_SESSION['token'] || strlen($_SESSION['token']) != 16){
      $_SESSION['burl'] = true;
      $_SESSION['token'] = $common->createToken();
      die(json_encode(['erro'=>'token is invalid... voce está em perigo xD']));
    }

    //verifica se login vazios
    if(empty($login) || empty($senha))
        die(json_encode(['erro'=>"login ou senha vazios",'login'=>$login,'senha'=>$senha]));

    $_SESSION['token'] = $common->createToken();
    $_SESSION['captcha'] = $common->createToken();
    //Criptografa a senha
    /*
      implementação da criptografia da senha
      $senha = $common->encript($senha);
    */

    //fazendo a consulta
    $sql = "SELECT * FROM users JOIN privilegios ON privilegios.id_privilegio = users.id_privilegio WHERE users.login='$login'";
    $conn = $db->Connect();
    $login = $db->Filter($login);
    $senha = $db->Filter($senha);
    $dados_usuario = [];

    if($res = $conn->query($sql)){
      if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
          if(password_verify($senha,$row['password'])){
              $dados_usuario = $row;
              break;
          }else
              die(json_encode(["erro"=>"usuario ou senhas incorretos"]));           
        }
      }else{
        $conn->close();
        die(json_encode(["erro"=>"usuario ou senhas incorretos"]));
      }
    }
    else{
      echo "erro: ".$conn->errno();
      $conn->close();
      die("erro php sql");
    }
    $conn->close();
    $db->Close();
    unset($dados_usuario['password']);
    $_SESSION['usuario'] = $dados_usuario;
    $dataFinal = strtotime($dados_usuario['dataFinal']);
    //veficação de data do privilegio
    if(($dataFinal - strtotime("now")) <= 0)
        die(json_encode(['erro'=>"voce nao pode mais acessar a dash, renove seu login!",'ultima data de pagamento' => $dados_usuario['dataFinal']]));
    
    /*

      verificando dados do login

    */
    $id_user = $dados_usuario['id_user'];
    $sql = "SELECT * FROM login_controll WHERE id_User = $id_user";
    $conn = $db->Connect();

    if($res = $conn->query($sql)){
      if($res->num_rows == 1){

        $dados_login = $res->fetch_assoc();
        if(!isset($_COOKIE['PHPSESSID']) || $_COOKIE['PHPSESSID'] == ''){
          $_SESSION['burl'] = true;
          die( json_encode(["erro"=>'não tente mais gracinhas']));
        }
        $phpid = $db->Filter($_COOKIE['PHPSESSID']);

        $sql = "UPDATE login_controll SET session='$phpid' WHERE id_User = $id_user";
        if($res = $conn->query($sql))
            $_SESSION['dados_login'] = $dados_login;
        else
            die( json_encode(["erro"=>"erro ao entrar contate o adm"]));

      }else if($res->num_rows == 0){
        if(!isset($_COOKIE['PHPSESSID']) || $_COOKIE['PHPSESSID']=''){
          $_SESSION['burl'] = true;
          die(json_encode(["erro"=>'não tente mais gracinhas']));
        }
        $token = $common->createToken();
        $phpid = $db->Filter($_COOKIE['PHPSESSID']);
        $sql = "INSERT INTO login_controll(id_login_controll,id_User,session,token) VALUES (null,$id_user,'$phpid','$token')";
        if($res = $conn->query($sql)){
          $_SESSION['dados_login'] = array("id_User"=>"$id_user","session"=>"$phpid","token"=>"$token"); 
        }else
          die(json_encode(["erro"=>"erro ao entrar contate o adm"]));
      }
      else{
        die(json_encode(['erro'=>'erro na sua requisicao']));
      }
    }else
      die(json_encode(["erro"=>"erro de programacao"]));
    $conn->close();
    $db->Close();
      /*
      sessões ativas
        session usuario
        session dados_login
        session ip_user
      */
    if(isset($_SESSION['token'])){
      unset($_SESSION['token']);
    }
    if(isset($_SESSION['captcha'])){
      unset($_SESSION['captcha']);
    }
    $_SESSION['logado'] = true;
    echo json_encode(["success"=>"you successfully logged in"]);