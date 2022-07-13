<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../styles/styles.css">
    <title>Cadastro de Prova</title>
    <script>
      function changeArrow(id){
        let arrow = document.getElementById(id);
        // A função indexOf retorna a posição de uma string em outra string ou -1 se não encontrar.
        if(arrow.src.indexOf('arrow_up') != -1){
          let input = document.getElementById('input_number_'+id.replace('arrow_up_',''))
          arrow.src = '../../../assets/Icons/arrow_up.svg';
          input.value = parseInt(input.value) + 1;
          setTimeout(function(){
            arrow.src = '../../../assets/Icons/arrow_up_c.svg';
          },150);
        }else if(arrow.src.indexOf('arrow_down') != -1){
          let input = document.getElementById('input_number_'+id.replace('arrow_down_',''))
          arrow.src = '../../../assets/Icons/arrow_down.svg';
          if(parseInt(input.value) > 1){
            input.value = parseInt(input.value) - 1;
          }
          setTimeout(function(){
            arrow.src = '../../../assets/Icons/arrow_down_c.svg';
          },150);
        }
      }
    </script>
  </head>
  <body>
    <?php
      if(!isset($_SESSION['name_user'])){
      $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
      header('Location: login.php');
      die();
      }

      function getClass($id_user){
      	try{
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query("SELECT id_turma, nome_turma FROM turma WHERE id_usuario='".$id_user."' ORDER BY nome_turma");

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

     ?>
     <section class="window">
       <div class="container all">
        <form action="add_template.php" method="post">
          <div class="title">CADASTRO DE PROVA</div>
            <div class="inner_container">
              <input type="text" placeholder="Digite o nome da Prova" class="input_text" name="test_name" required>

              <select placeholder="Selecione a turma" class="input_text" name="class_name" required>
                <?php
                $classes = getClass($_SESSION['id_user']);
                foreach ($classes as $k) {
                  echo '<option value="'.$k['id_turma'].';'.$k['nome_turma'].'">'.$k['nome_turma'].'</option>';
                }
                 ?>
              </select>
              <span class="span_input_number_text">
              Quantidade de questões
              <span class="span_input_number">
              <input type="number" id="input_number_1" class="input_number" value="1" min="1" max="20" name="a_ques" required>
              <span class="arrows">
                <img id="arrow_up_1" onclick="changeArrow('arrow_up_1')" class="arrow_svg" src="../../../assets/Icons/arrow_up_c.svg">
                <img id="arrow_down_1" onclick="changeArrow('arrow_down_1')" class="arrow_svg" src="../../../assets/Icons/arrow_down_c.svg">
              </span>
              </span>
            </span>

            <span class="span_input_number_text">
              Quantidade de alternativas
              <span class="span_input_number">
              <input type="number" id="input_number_2" class="input_number" value="1" min="1" max="7"  name="a_alt" required>
              <span class="arrows">
                <img id="arrow_up_2" onclick="changeArrow('arrow_up_2')" class="arrow_svg" src="../../../assets/Icons/arrow_up_c.svg">
                <img id="arrow_down_2" onclick="changeArrow('arrow_down_2')" class="arrow_svg" src="../../../assets/Icons/arrow_down_c.svg">
              </span>
              </span>
              </span>
            </div>
              <button type="submit" class="button">GERAR PROVA</button>
      </form>
      <a href='javascript:history.go(-1)'><img src="../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
      <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
    </div>
  </section>

  </body>
</html>
