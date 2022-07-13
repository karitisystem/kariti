<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>CADASTRO DE prova</title>
  </head>
  <body>
    <?php
      if(!isset($_SESSION['name_user'])){
        $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
        header('Location: login.php');
        die();
      }

      function getIdTest($test_name, $id_class){
      	try{
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query("SELECT id_prova FROM prova WHERE id_turma='".$id_class."' AND nome_prova='".$test_name."'");

      		//run the SQL
      		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);


      	}catch(PDOException $e){
      		$rows = 0;
      		echo "<pre>";
      		echo $e;
      		echo "</pre>";
      	}


      	return $rows[0]['id_prova'];
      }

      $test_name = $_POST['test_name'];
      $id_class = $_POST['id_class'];
      $a_ques = $_POST['a_ques'];
      $a_alt = $_POST['a_alt'];
      $date_test = $_POST['date_test'];
      echo $date_test;

      //Cadastrando Prova
      try{

        $sql = "INSERT INTO prova (nome_prova, data, n_questoes, n_alternativas, id_turma) VALUES ('".$test_name."', '".$date_test."', '".$a_ques."', '".$a_alt."', '".$id_class."')";

  			//define PDO - tell about the database file
  			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

  			//Execute the query
  			$pdo->exec($sql);

  			$_GET['message'] = '<font color="green">PROVA CRIADA COM SUCESSO</font><br>';

    	}catch(PDOException $e){
    		$_SESSION['error'] = $e;
    		header('Location:errorPage.php');
    	}
      //Cadastrando Gabarito
      $questions = [];
      $weight = [];

      for ($i = 1; $i <= $a_ques; $i++){
        array_push($questions, $_POST['q'.$i]);
        array_push($weight, $_POST['w'.$i]);
      }

      try{
        $id_test = getIdTest($test_name, $id_class);
        for($i = 0; $i < $a_ques; $i++){
          $q = $i + 1;
          $a = $questions[$i];
          $w = $weight[$i];


          $sql = "INSERT INTO gabarito (id_prova, n_questao, opcao, peso) VALUES ('".$id_test."','".$q."', '".$a."', '".$w."')";

    			//define PDO - tell about the database file
    			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    			//Execute the query
    			$pdo->exec($sql);

    			//$_GET['message'] .= '<font color="green">GABARITO CRIADO COM SUCESSO</font><br>';
        }
    	}catch(PDOException $e){
    		$_SESSION['error'] = $e;
    		header('Location:errorPage.php');
    	}
      header('Location:../search_test?message='.$_GET['message']);
     ?>


  </body>
</html>
