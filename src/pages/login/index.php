<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../styles/styles.css">
    <title>Login</title>
  </head>
  <body>
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
      <div class="container">
        <form action="settings.php" method="post">
          <div class="title">LOGIN</div>

              <input type="text" placeholder="Digite o seu ID" class="input_text" name="nick_user" required>
              <br>
              <input type="password" placeholder="Digite a sua senha" class="input_text" name="psw" required>
              <br>
              <button type="submit" class="button">ENTRAR</button>
        </form>
      </div>
    </section>

  </body>
</html>
