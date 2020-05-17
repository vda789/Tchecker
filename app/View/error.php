<h1>Página de erro</h1>
    <a href="?pg=index">clique para voltar</a><br>
    <?php
        if(isset($_GET['error'])){
            $erro = strip_tags(filter_input(INPUT_POST,'error',FILTER_SANITIZE_SPECIAL_CHARS));
            echo "<h1>ERRO $erro </h1>";
        }
    ?>
