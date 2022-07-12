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

    	function verifyUser($nick){
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
    		}
    		return $there;

    	}
    	$nick = $_POST['nick_user'];
      $name = $_POST['name_user'];
      $psw = md5($_POST['psw']);

    	$ids = verifyUser($nick);

    	$sql = "INSERT INTO usuario (nickname, nome, senha) VALUES ('".$nick."', '".$name."', '".$psw."')";

    	try{
    		if(verifyUser($nick)){
    			//define PDO - tell about the database file
    			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    			//Execute the query
    			$pdo->exec($sql);

    			$_GET['message'] = '<font color="green">USUÁRIO CRIADO COM SUCESSO</font><br>';

    		}else{
    			$_GET['message'] = '<font color="red">JÁ EXISTE UM USUÁRIO COM ESSE NICK</font><br>';
    		}

    	}catch(PDOException $e){
    		$_SESSION['error'] = $e;
    		header('Location:errorPage.php');
    	}

    	header('Location: ./?message='.$_GET['message']);

    ?>

  </body>
</html>
