<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8"/>
    <title>Visualização de Turma</title>
    <link rel="stylesheet" type="text/css" href="../../../styles/styles.css">
    <script>
      function search(ids){
        let search_bar = document.getElementById('search').value.toLowerCase();
        // /\s/g is a regex for space :D
        search_bar = search_bar.replace(/\s/g, '');
        // let tr = document.getElementById(search_bar)
        for(let i=0; i < ids.length; i++){
          if(search_bar.length > 0){
            if (!(ids[i].indexOf(search_bar) != -1)){
              document.getElementById(ids[i]).style.display = 'none'
            }else{
              document.getElementById(ids[i]).style.display = 'block'
            }
          }else{
            document.getElementById(ids[i]).style.display = 'block'
          }
        }
      }
    </script>
  </head>
  <body>
    <?php
      function getClass($id_user){
      	try{
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query("SELECT * FROM turma WHERE id_usuario='".$id_user."' ORDER BY nome_turma");

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

      if(!isset($_SESSION['name_user'])){
      	header('Location: login.php');
      	die();
      }

      $rows = getClass($_SESSION['id_user']);
      if(count($rows) == 0){
        $_GET['message'] = '<font color="red">VOCÊ NÃO POSSUI NENHUMA TURMA CADASTRADO AINDA</font><br>';
        header('Location: menu.php?message='.$_GET['message']);
        die();
      }


      ?>
    <section class="window">
      <?php
        if(isset($_GET['message'])){
          if ($_GET['message'] != ''){
            echo '<div class="message_container">';
            echo '<div class="message">'.$_GET['message'].'</div>';
            echo '</div>';
            $_GET['message'] = '';
          }
        }
       ?>
      <div class="container all">
        <div class="title">VISUALIZAÇÃO DE TURMA</div>
        <div class="inner_container">
          <?php
            for ($i = 0; $i < count($rows); $i++) {
              $tr_id[] = "'".strtolower(str_replace(' ','',$rows[$i]['nome_turma']))."'";
            }
           ?>
          <div class="span_input_search">
            <input placeholder="Pesquise pelo nome da turma" type="text" class="input_number inner align_left" id="search" onkeyup="search([<?php echo implode(',', $tr_id); ?>])">
            <label class="search_label" for="search" class="">
            <img class="search_svg" src="../../../assets/Icons/pesquisar.svg">
          </label>
         </div>
          <?php
            echo '<div class="divList">';
            echo '<table class="view_table">';
            echo '<tbody>';
          	for ($i = 0; $i < count($rows); $i++) {
            echo '<tr id="'.str_replace("'", "",$tr_id[$i]).'">';

            echo '<td>';
            echo '<a onclick="return confirm(\'Deseja realmente apagar '. $rows[$i]["nome_turma"].'?\');" href="delete_class/settings.php?id_class='.$rows[$i]['id_turma'].'"><img src="../../../assets/Icons/excluir.svg" height=20></a>';
            echo '</td>';

            echo '<td>';
          	echo '<a href="edit_class?id_class='.$rows[$i]['id_turma'].'"><img src="../../../assets/Icons/editar.svg" height=20></a>';
            echo '</td>';

            echo '<td>';
          	echo $rows[$i]['nome_turma'];
            echo '</td>';
            echo '</tr>';
          	}
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
          ?>
        </div>
        <a href='../../menu'><img src="../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
        <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
      </div>
    </section>
  </body>
</html>
