<?php

  session_start();

  if(!isset($_SESSION['name_user'])){
    $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
    header('Location: login.php?message='.$_GET['message']);
    die();
  }

  // Getting settings to send email
  $ini_file = parse_ini_file('settings.ini', true);
  // Getting seting to use python in windows or linux
  $python_w = $ini_file['SYSTEM']['python_windows_path'];
  $python_l = $ini_file['SYSTEM']['python_linux_path'];
  $cut_path_l = $ini_file['SYSTEM']['cut_path_l'];
  $cut_path_w = $ini_file['SYSTEM']['cut_path_w'];

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

  function getTestStudent($id_test, $id_student){
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:database.db');

      //write SQL
      $statement = $pdo->query("SELECT id_prova FROM aluno_prova WHERE id_prova='".$id_test."' AND id_aluno='".$id_student."'");

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

  function addTestStudent($id_test, $ques, $student, $op){
    $sql = "INSERT INTO aluno_prova (id_prova, n_questao, id_aluno, opcao) VALUES ('".$id_test."', '".$ques."', '".$student."', '".$op."')";
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:database.db');

      //Execute the query
      $pdo->exec($sql);

    }catch(PDOException $e){
      $_SESSION['error'] = $e;
      header('Location:errorPage.php');
    }
  }
  //Counters to see how many test are invalid or not
  $x = 0;
  $y = 0;
  $z = 0;

  //Root path till file
  if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
    $uploaddir = $cut_path_w;
  }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
    $uploaddir = $cut_path_l;
  }

  //Run into all tests
  foreach ($_FILES['userfile']['name'] as $key => $value) {
    $uploadfile = $uploaddir . basename($value);
    //It moves this test to our folder
   	if (move_uploaded_file($_FILES['userfile']['tmp_name'][$key], $uploadfile)) {
       echo "Arquivo válido e enviado com sucesso.\n";
   	} else {
    		echo "Possível ataque de upload de arquivo!\n";
   	}

   	$out = array();
     echo '<br>Path: /'.$uploadfile;
     echo '<br>';
     //It corrects the test and return the result
     if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
       $cmd = $python_w.' correctTest.py '.$uploadfile;
       exec($cmd, $out);
       // echo str_replace($cut_path_w, '',$out[0]);
     }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
       $cmd = $python_l.' correctTest.py '.$uploadfile;
       exec($cmd, $out);
       // echo str_replace($cut_path_l, '',$out[0]);
     }

     //It delete this test from our server
    if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
      $cmd = $python_w.' deleteFile.py '.$uploadfile;
      exec($cmd, $out);
    }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
      $cmd = $python_l.' deleteFile.py '.$uploadfile;
      exec($cmd, $out);
    }


   	echo "</br><b>Qtd de itens no Array: ".sizeof($out)." </b></br></br><hr>";
    $test_data = [];
    if ($out[0] != 'THERE ARE MORE OR LESS ALTERNATIVES OR QUESTIONS GOTTEN'){
      // echo 'Prova válida';
      $test_data = [
        'id_test' => str_replace('id_prova:', '', $out[0]),
        'id_student' => str_replace('id_aluno:', '', $out[1]),
        'answers' => explode(',', str_replace('resposta:', '', $out[2]))
      ];
      /*
      echo '<pre>';
      echo var_dump($test_data);
      echo '</pre>';*/

      //If this student don't have a test registered yet
      if(count(getTestStudent($test_data['id_test'], $test_data['id_student'])) == '0'){
        for($i = 0; $i < count($test_data['answers']); $i++){
          $j = $i;
          addTestStudent(
            $test_data['id_test'],
            ++$j,
            $test_data['id_student'],
            $test_data['answers'][$i]
          );
        }
        $x += 1;
      }else {
        $y += 1;
      }
    }else{
      $z += 1;
    }
  }
  $_GET['message'] = '';
  if($x > 0){
    $_GET['message'] .= '<font color="green">'.$x.' PROVA(S) CORRIGIDA(S) COM SUCESSO!</font><br>';
  }
  if($y > 0){
    $_GET['message'] .= '<font color="red">'.$y.' PROVA(S) NÃO CORRIGIDA(S) PORQUE JÁ HAVIA(M) SIDO CORRIGIDA(S) ANTERIORMENTE.</font><br>';
  }
  if($z > 0){
    $_GET['message'] .= '<font color="red">'.$z.' PROVA(S) NÃO CORRIGIDA(S) PORQUE SER(EM) CONSIDERADA(S) INVÁLIDA(S).</font><br>';
  }

  header('Location:searchTest.php?message='.$_GET['message']);
?>
