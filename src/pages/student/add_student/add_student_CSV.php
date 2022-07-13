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

      // Getting settings to send email
      $ini_file = parse_ini_file('../../../settings.ini', true);
      $cut_path_l = $ini_file['SYSTEM']['cut_path_l'];
      $cut_path_w = $ini_file['SYSTEM']['cut_path_w'];

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

      if(basename($_FILES['userfile']['name']) == ''){
        $_GET['message'] = '<font color="red">ARQUIVO CSV INVÁLIDO</font><br>';
        header('Location: addStudent.php?message='.$_GET['message']);
        die();
      };

      //Root path till file
      if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
        $uploaddir = $cut_path_w;
      }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
        $uploaddir = $cut_path_l;
      }

    	$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

    	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        echo "Arquivo válido e enviado com sucesso.\n";
    	} else {
     		echo "Possível ataque de upload de arquivo!\n";
    	}

      $file = file($uploadfile);

      unlink($uploadfile);

      foreach($file as $k){
        if(mb_strpos($k, ';') !== false){
          $csv[] = explode(';', $k);
        }else if(mb_strpos($k, ',') !== false){
          $csv[] = explode(',', $k);
        }else{
          $_GET['message'] = '<font color="red">ARQUIVO CSV INVÁLIDO</font><br>';
          header('Location: viewStudent.php?message='.$_GET['message']);
          die();
        }
      }

      for($i = 1; $i < count($csv); $i++){

      	$name = trim($csv[$i][0]);
        $email = trim($csv[$i][1]);
        $registration = trim($csv[$i][2]);
        $id_user = $_SESSION['id_user'];


      	$sql = "INSERT INTO aluno (nome_aluno, email_aluno, matricula_aluno, id_usuario) VALUES ('".$name."', '".$email."', '".$registration."', '".$id_user."')";

      	try{
      		if(verifyUser($registration, $id_user)){
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      			//Execute the query
      			$pdo->exec($sql);

      			$_GET['message'] .= '<font color="green">ALUNO COM O Nº DE MATRÍCULA '.$registration.' ADICIONADO COM SUCESSO</font><br>';

      		}else{
      			$_GET['message'] .= '<font color="red">JÁ EXISTE UM ALUNO COM O Nº DE MATRÍCULA '.$registration.'</font><br>';

      		}

      	}catch(PDOException $e){
      		$_SESSION['error'] = $e;
      	}

      }
      header('Location: ../show_student?message='.$_GET['message']);
    ?>

  </body>
</html>
