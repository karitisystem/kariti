<?php

  session_start();

  function getIdClass($id_test){
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      //write SQL
      $statement = $pdo->query("SELECT id_turma FROM prova WHERE id_prova='".$id_test."'");

      //run the SQL
      $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


    }catch(PDOException $e){
      $rows = 0;
      echo "<pre>";
      echo $e;
      echo "</pre>";
    }

    return $rows[0]['id_turma'];
  }

  function getIdStudent($id_class){
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      $sql = "SELECT id_aluno FROM aluno_turma WHERE id_turma='".$id_class."'";

      //write SQL
      $statement = $pdo->query($sql);

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

  function getStudents($id_student){
    try{
      $sql = "SELECT id_aluno, nome_aluno FROM aluno WHERE";
      for($i = 0; $i < count($id_student); $i++ ){
        if($i < count($id_student)-1){
          $sql .= ' id_aluno = '.$id_student[$i]["id_aluno"].' OR ';
        }else{
          $sql .= ' id_aluno = '.$id_student[$i]["id_aluno"];
        }
      }

      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      //write SQL
      $statement = $pdo->query($sql. ' ORDER BY nome_aluno');

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

  //Passando qual sigla foi solicitada
  //Trocar estado por id_test
	$id_test = $_GET["sigla"];

  $id_class = getIdClass($id_test);
  $id_student = getIdStudent($id_class);
  $students = getStudents($id_student);

  $name_students = [];
  $id_students = [];
  for($i = 0; $i < count($students); $i++){
    //Alocando apenas as cidades correspondentes a sigla solicitada
    $name_students[] = $students[$i]['nome_aluno'];
    $id_students[] = $students[$i]['id_aluno'];
  }

	$saida = "";
  //Criando a String com as cidade solicitadas, no tipo: city, city1,..., cityn
	for ($i = 0; $i < count($name_students); $i++) {
		if (strlen($saida) > 0){
			$saida = $saida.",";
		}
		$saida = $saida.$name_students[$i].';'.$id_students[$i];
	}
	echo $saida;
?>
