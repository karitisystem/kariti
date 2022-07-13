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

    	function verifyClass($id_class, $class_name){
    		$there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

        $id_user = $_SESSION['id_user'];

    		//write SQL
    		$statement = $pdo->query("SELECT nome_turma FROM turma WHERE nome_turma='".$class_name."'AND id_usuario='".$id_user."'");

    		//run the SQL
    		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);


    		}catch(PDOException $e){
    			$rows = 0;
    			echo "<pre>";
    			echo $e;
    			echo "</pre>";
    		}

    		if(count($rows) == 0){
    			$there = true;
    		}else if((count($rows) == 1) && ($class_name == $rows[0]['nome_turma'])) {
          $there = true;
        }else if($class_name != $rows[0]['nome_turma']){
          $there = true;
        }
    		return $there;

    	}

    	function verifyTest($id_student, $id_class){
    		$there = false;

    		try{
    		//define PDO - tell about the database file
    		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

    		//write SQL
    		$statement = $pdo->query("SELECT id_prova FROM aluno_prova WHERE id_aluno='".$id_student."'");

    		//run the SQL
    		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        //write SQL
        $statement = $pdo->query("SELECT id_prova FROM prova WHERE id_turma='".$id_class."'");

        //run the SQL
        $rows_2 = $statement->fetchAll(PDO::FETCH_ASSOC);

    		}catch(PDOException $e){
    			$rows = 0;
    			echo "<pre>";
    			echo $e;
    			echo "</pre>";
    		}

        // Verify is this class is relacionated at a test
    		if(count($rows_2) > 0){
          // Verify is this student is relacionated at a test
          if(count($rows) > 0){
      			$there = true;
      		}else{
            $there = false;
          }
    		}else{
          $there = false;
        }
    		return $there;
    	}

      function getStudentAtClass($id_class){
        try{
          //define PDO - tell about the database file
          $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

          //write SQL
          $statement = $pdo->query("SELECT id_aluno FROM aluno_turma WHERE id_turma='".$id_class."'");
          //run the SQL
          $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        }catch(PDOException $e){
          $rows = 0;
          echo "<pre>";
          echo $e;
          echo "</pre>";
        }

        $students = [];
        for($i = 0; $i < count($rows); $i++ ){
          $students[$i] = $rows[$i]['id_aluno'];
        }

        return $students;
      }

      $class_name = $_POST['class_name'];
    	$course_class = $_POST['course_class'];
      $id_class = $_GET['id_class'];
      isset($_POST['student']) ? $students = $_POST['student'] : $students = [];
      $students_db = getStudentAtClass($id_class);

      try{
        if(verifyClass($id_class, $class_name)){
          $sql = "UPDATE turma SET nome_turma = '".$class_name."', nome_curso = '".$course_class."' WHERE id_turma='".$id_class."'";

          //define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//Execute the query
      		$pdo->exec($sql);

            try{
              //Se foi adicionado no checklist
              for($i=0; $i<count($students); $i++){
                if(!in_array($students[$i], $students_db)){
                  $sql = "INSERT INTO aluno_turma (id_aluno, id_turma) VALUES ('".$students[$i]."', '".$id_class."')";
                  //Execute the query
                  $pdo->exec($sql);
                }
              }
              $c = 0;
              //Se foi retirado do checklist
              for($i=0; $i<count($students_db); $i++){
                if($students_db[$i] != ''){
                  if(!in_array($students_db[$i], $students)){
                    if(!verifyTest($students_db[$i], $id_class)){
                      $sql = "DELETE FROM aluno_turma WHERE id_turma = '".$id_class."' AND id_aluno='".$students_db[$i]."'";
                      //Execute the query
                      $pdo->exec($sql);
                    }else{
                      $c++;
                    }
                  }
                }
              }
            }catch(PDOException $e){
              $_SESSION['error'] = $e;
              header('Location:errorPage.php');
            }

    			$_GET['message'] = '<font color="green">TURMA EDITADA COM SUCESSO</font><br>';
          if ($c > 0){
            $_GET['message'] .= '<font color="red">'.$c.' ALUNO NÃO REMOVIDO(S) POE ESTAR(EM) RELACIONADO(S) A UMA OU MAIS PROVAS NESSA TURMA</font><br>';
          }

        }else{
    			$_GET['message'] = '<font color="red">JÁ EXISTE UMA TURMA COM ESSE NOME</font><br>';
        }


    	}catch(PDOException $e){
    		$_SESSION['error'] = $e;
    		header('Location:errorPage.php');
    		die();
    	}
      header('Location: ./?message='.$_GET['message'].'&id_class='.$id_class);

    ?>

  </body>
</html>
