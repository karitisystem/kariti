<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../styles/styles.css">
    <title>Cadastro de Usuário</title>
  </head>
  <body>
    <?php
      if(!isset($_SESSION['name_user'])){
      $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
      header('Location: login.php');
      die();
      }

      if($_SESSION['id_user'] != '1'){
        $_GET['message'] = '<font color="red">VOCÊ NÃO TEM PERMISSÃO DE ACESSO A ESSA PÁGINA</font><br>';
            header('Location: login.php');
        die();
        }
    ?>
    <section class="window">
      <?php
        if(isset($_GET['message'])){
          if ($_GET['message'] != ''){
            echo '<div class="message_container">';
            echo '<div class="message">'.$_GET['message'].'</div>';
            echo '</div>';
            $_GET['message'] = '';
          }
        }
       ?>
      <div class="container all">
        <form action="settings.php" method="post">
          <div class="title">CADASTRO DE USUÁRIO</div>
          <div class="inner_container">
            <input class="input_text" type="text" placeholder="Digite o seu nome de usuário" name="nick_user" required>
            <input class="input_text" type="text" placeholder="Digite o seu nome completo" name="name_user" required>
            <input class="input_text" type="password" placeholder="Digite a sua senha" name="psw" required>
          </div>
          <button class="button" type="submit">CADASTRAR USUÁRIO</button>
        </form>
        <a href='../../menu'><img src="../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
        <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
      </div>

    </section>
  </body>
</html>
