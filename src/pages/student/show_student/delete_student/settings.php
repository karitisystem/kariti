<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>getImages</title>
  </head>
  <body>
    <?php
      function verifyStudentClass($id_student_del){
        $there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT id_turma FROM aluno_turma WHERE id_aluno='".$id_student_del."'");

    		//run the SQL
    		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);


    		}catch(PDOException $e){
    			echo "<pre>";
    			echo $e;
    			echo "</pre>";
          die();
    		}

        if (count($rows) == 0){
    			$there = true;
    		}

    		return $there;

      }

    	$id_student_del = $_GET['id_student'];
      $id_user = $_SESSION['id_user'];

      $sql = "DELETE FROM aluno WHERE id_usuario = '".$id_user."' AND id_aluno='".$id_student_del."'";

      if(verifyStudentClass($id_student_del)){
      	try{
    			//define PDO - tell about the database file
    			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    			//Execute the query
    			$pdo->exec($sql);
    			$_GET['message'] = '<font color="green">ALUNO DELETADO COM SUCESSO</font><br>';


      	}catch(PDOException $e){
      		$_SESSION['error'] = $e;
      		header('Location:errorPage.php');
      		die();
      	}
      }else{
        $_GET['message'] = '<font color="red">ESSE ALUNO NÃO PODE SER APAGADO PORQUE ESTÁ RELACIONADO A UMA TURMA</font><br>';
      }

      header('Location: ../?message='.$_GET['message']);

    ?>

  </body>
</html>
