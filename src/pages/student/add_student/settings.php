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


    	function verifyUser($registration, $id_user){
    		$there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT id_aluno FROM aluno WHERE matricula_aluno='".$registration."' AND id_usuario='".$id_user."'");

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
    	$name = $_POST['name_student'];
      $email = $_POST['email_student'];
      $registration = $_POST['reg_student'];
      $id_user = $_SESSION['id_user'];

    	$sql = "INSERT INTO aluno (nome_aluno, email_aluno, matricula_aluno, id_usuario) VALUES ('".$name."', '".$email."', '".$registration."', '".$id_user."')";

    	try{
    		if(verifyUser($registration, $id_user)){
    			//define PDO - tell about the database file
    			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    			//Execute the query
    			$pdo->exec($sql);

    			$_GET['message'] = '<font color="green">ALUNO CRIADO COM SUCESSO</font><br>';

    		}else{
    			$_GET['message'] = '<font color="red">JÁ EXISTE UM ALUNO COM ESSE Nº DE MATRÍCULA</font><br>';
    		}

    	}catch(PDOException $e){
    		$_SESSION['error'] = $e;
    		header('Location:errorPage.php');
    	}

    	header('Location: ./?message='.$_GET['message']);

    ?>

  </body>
</html>
