<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>getImages</title>
  </head>
  <body>
    <?php
      function verifyUserClass($id_user_del){
        $there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT id_turma FROM turma WHERE id_usuario='".$id_user_del."'");

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

    	$id_user_del = $_GET['id_user'];

      $sql = "DELETE FROM usuario WHERE id_usuario = '".$id_user_del."'";

    	if(verifyUserClass($id_user_del)){
        try{
      		if($id_user_del != '1'){
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      			//Execute the query
      			$pdo->exec($sql);
      			$_GET['message'] = '<font color="green">USUÁRIO DELETADO COM SUCESSO</font><br>';
      		}else{
            $_GET['message'] = '<font color="red">ESSE USUÁRIO NÃO PODE SER APAGADO</font><br>';
          }

      	}catch(PDOException $e){
      		$_SESSION['error'] = $e;
      		header('Location:errorPage.php');
      		die();
      	}
      }else{
        $_GET['message'] = '<font color="red">ESSE USUÁRIO NÃO PODE SER APAGADO PORQUE ESTÁ RELACIONADO A UMA TURMA</font><br>';
      }


      header('Location: ../?message='.$_GET['message']);

    ?>

  </body>
</html>
