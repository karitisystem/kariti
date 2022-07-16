<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>getImages</title>
  </head>
  <body>
    <?php
      function verifyClassStudent($id_class_del){
        $there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT id_aluno FROM aluno_turma WHERE id_turma='".$id_class_del."'");

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

      function verifyClassTest($id_class_del){
        $there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT id_prova FROM prova WHERE id_turma='".$id_class_del."'");

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

    	$id_class_del = $_GET['id_class'];
      $id_user = $_SESSION['id_user'];

      $sql = "DELETE FROM turma WHERE id_usuario = '".$id_user."' AND id_turma='".$id_class_del."'";

      if(verifyClassTest($id_class_del)){
        if(verifyClassStudent($id_class_del)){
        	try{
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      			//Execute the query
      			$pdo->exec($sql);
      			$_GET['message'] = '<font color="green">TURMA DELETADA COM SUCESSO</font><br>';


        	}catch(PDOException $e){
        		$_SESSION['error'] = $e;
        		header('Location:errorPage.php');
        		die();
        	}
        }else{
          $_GET['message'] = '<font color="red">ESSA TURMA NÃO PODE SER APAGADA PORQUE ESTÁ RELACIONADO A UM OU MAIS ALUNOS</font><br>';
        }
      }else{
        $_GET['message'] = '<font color="red">ESSA TURMA NÃO PODE SER APAGADA PORQUE ESTÁ RELACIONADO A UM OU MAIS PROVAS</font><br>';
      }

      header('Location: ../?message='.$_GET['message']);

    ?>

  </body>
</html>
