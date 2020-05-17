<?php
  if(isset($_SESSION['token'])){
    unset($_SESSION['token']);
  }
  $_SESSION['token'] = $common->createToken();
?>

<div class="container">
    <h1> Login</h1>
    <form method="post" action="?pot=login">
       nome: <input type="text" required name="login" id="login"><br/><br>
       senha: <input type="password" required name="pass" id="senha"><br/><br>
       <input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'] ?>">
       <img src="app/proc/login/captcha.php" alt="captcha" width="400px" height="200px">
       <br>
       <input type="text" name="captcha" id="captcha" value="">
       <input type="submit" value="submit">
    </form>
    <div class="erros"></div>
</div>
<script>
$(document).ready(()=>{
  $("form").submit(()=>{

    var login = $("#login").val();
    var senha = $("#senha").val()
    var captcha = $("#captcha").val();
    var token = $("#token").val();
    $.ajax({
      url:"?pot=login",
      type: "POST",
      async: true,
      data:{"login":login,"pass":senha,"captcha":captcha,"token":token},
      datatype:"json",
      success: (result)=>{
          $(".erros").html(result);
      }
    });
    return false;
  });
});

</script>
