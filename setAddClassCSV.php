<?php

  session_start();

  if(!isset($_SESSION['name_user'])){
    header('Location: login.php');
    die();
  }


  function getStudent($key, $id_user){
  	try{
      //echo $key;
      //Se for e email
      $sql = "SELECT id_aluno FROM aluno WHERE id_usuario='".$id_user."' AND (email_aluno='".$key."' OR nome_aluno='".$key."' OR matricula_aluno='".$key."')";

  		//define PDO - tell about the database file
  		$pdo = new PDO('sqlite:database.db');

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
    if (count($rows) != 1){
      $rows = 0;
    }

  	return $rows;
  }

  $id_user = $_SESSION['id_user'];

  $keys_ec = $_GET['keys'];
  $keys[] = explode('schlussel', $keys_ec);

  foreach($keys[0] as $k){
    $csv[] = $k;
  }



	try{

    $id_students = [];
    for($i = 1; $i < count($csv); $i++){
      //trim é uma função santa!
      $id = getStudent(trim($csv[$i]), $id_user)[0]['id_aluno'];
      array_push($id_students, $id);
    }
	}catch(PDOException $e){
		$error = $e;
	}


  $str = '';
  for($i = 0; $i < count($id_students); $i++){
    if($i != 0){
      $str .= 'schlussel'.$id_students[$i];
    }else{
      $str .= $id_students[$i];
    }
  }
    echo $str;

  /*
  if(isset($_GET['id_class'])){
    $id_class = $_GET['id_class'];
    $url = 'Location: editClass.php?id_class='.$id_class.'&';
  }else{
    $url = 'Location: addClass.php?';
  }

  for($i = 0; $i < count($id_students); $i++){
    $url .= 'id_students[]='.$id_students[$i];
    if(!($i == count($id_students)-1)){
      $url .= '&';
    }
  }*/

?>
