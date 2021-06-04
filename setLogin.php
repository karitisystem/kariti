<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>getImages</title>
  </head>
  <body>
    <?php
        function getUser($nick_user){
      		try{
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:database.db');
      			//write SQL
      			$statement = $pdo->query("SELECT * FROM usuario WHERE nickname='".$nick_user."'");
      			//run the SQL
      			$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

      		}catch(PDOException $e){
      			$rows = 0;
      			echo "<pre>";
      			echo $e;
      			echo "</pre>";
      		}
      		return $rows;
      	}

      	$nick_usuario = $_POST['nick_user'];
        $psw = md5($_POST['psw']);

        $rows = getUser($nick_usuario);
        $_SESSION['id_user'] = $rows[0]['id_usuario'];
        $_SESSION['name_user'] = $rows[0]['nome'];


        if ($psw != $rows[0]['senha']){
          $_GET['message'] = '<font color="red">ID OU SENHA INCORRETOS</font><br>';
          header('Location:login.php?message='.$_GET['message']);
          die();
        }

        header('Location: menu.php?message='.$_GET['message']);

    ?>

  </body>
</html>
