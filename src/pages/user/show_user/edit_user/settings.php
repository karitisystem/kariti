<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>getImages</title>
  </head>
  <body>
    <?php
      if(!isset($_SESSION['name_user'])){
    		header('Location: login.php');
    		die();
    	}
     	if($_SESSION['id_user'] != '1'){
        		header('Location: login.php');
    		die();
      	}

    	function verifyUser($nick, $id_user){
    		$there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT id_usuario FROM usuario WHERE nickname='".$nick."'");

    		//run the SQL
    		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);


    		}catch(PDOException $e){
    			$rows = 0;
    			echo "<pre>";
    			echo $e;
    			echo "</pre>";
    		}


    		if (count($rows) == 0){
    			$there = true;
    		}else if((count($rows) == 1) && ($id_user == $rows[0]['id_usuario'])) {
          $there = true;
        }
    		return $there;

    	}

      $id_user = $_GET['id_user'];
    	$nick = $_POST['nick_user'];
      $name = $_POST['name_user'];
      $psw = md5($_POST['psw']);


      $sql = "UPDATE usuario SET nickname = '".$nick."', nome = '".$name."', senha = '".$psw."' WHERE  id_usuario = '".$id_user."'";

      try{

        if(verifyUser($nick, $id_user)){
          //define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//Execute the query
      		$pdo->exec($sql);

    			$_GET['message'] = '<font color="green">USUÁRIO EDITADO COM SUCESSO</font><br>';

        }else{
    			$_GET['message'] = '<font color="red">JÁ EXISTE UM USUÁRIO COM ESSE NICK</font><br>';
        }


    	}catch(PDOException $e){
    		$_SESSION['error'] = $e;
    		header('Location:errorPage.php');
    		die();
    	}

      header('Location:../?message='.$_GET['message']);

    ?>

  </body>
</html>
