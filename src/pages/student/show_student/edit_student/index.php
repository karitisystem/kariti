<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
    <title>Editar Aluno</title>
  </head>
  <body>

      <?php
      	if(!isset($_SESSION['name_user'])){
      		$_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
      		header('Location: login.php');
      		die();
      	}

        $id_student_edit = $_GET['id_student'];
        $id_user = $_SESSION['id_user'];

        function getStudent($id_student_edit, $id_user){
      		try{
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');
      			//write SQL
      			$statement = $pdo->query("SELECT * FROM aluno WHERE id_usuario='".$id_user."' AND id_aluno='".$id_student_edit."'");
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
        $rows = getStudent($id_student_edit, $id_user);
      ?>
      <section class="window">
        <div class="container all">
          <form action="settings.php?id_student=<?php echo $id_student_edit; ?>" method="post">
            <div class="inner_container">
            <div class="title">EDITAR ALUNO</div>
            <input type="text" class="input_text" placeholder="Digite o nome do aluno" value="<?php echo $rows[0]['nome_aluno']; ?>" name="name_student" required>
            <input type="email" class="input_text" placeholder="Digite o email do aluno" value="<?php echo $rows[0]['email_aluno']; ?>" name="email_student" required>
            <input type="number" class="input_text" placeholder="Digite o nº de matrícula do aluno" value="<?php echo $rows[0]['matricula_aluno']; ?>"  name="reg_student" readonly>
          </div>
        <button class="button" type="submit">ATUALIZAR</button>
        </form>
        <a href='javascript:history.go(-1)'><img src="../../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
        <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
      </div>
    </section>
  </body>
</html>
