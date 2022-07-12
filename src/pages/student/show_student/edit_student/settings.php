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

    	function verifyStudent($registration, $id_user){
    		$there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT matricula_aluno FROM aluno WHERE matricula_aluno='".$registration."' AND id_usuario='".$id_user."'");

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
    		}else if((count($rows) == 1) && ($registration == $rows[0]['matricula_aluno'])) {
          $there = true;
        }
    		return $there;

    	}
      $id_student_edit = $_GET['id_student'];
      $id_user = $_SESSION['id_user'];
    	$name = $_POST['name_student'];
      $email = $_POST['email_student'];
      $registration = $_POST['reg_student'];


      $sql = "UPDATE aluno SET nome_aluno = '".$name."', email_aluno = '".$email."', matricula_aluno = '".$registration."' WHERE  id_usuario = '".$id_user."' AND id_aluno='".$id_student_edit."'";
      echo $sql;
      try{
        if(verifyStudent($registration, $id_user)){
          //define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//Execute the query
      		$pdo->exec($sql);

    			$_GET['message'] = '<font color="green">ALUNO EDITADO COM SUCESSO</font><br>';

        }else{
    			$_GET['message'] = '<font color="red">JÁ EXISTE UM ALUNO COM ESSE Nº DE MATRÍCULA</font><br>';
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
