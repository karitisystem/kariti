<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="../../../styles/styles.css">
    <script>

      function changeLegendFile(){
        let legend = document.getElementById('file_legend');
        let input = document.getElementById('files');
        if (input.value != ''){
          legend.innerHTML = 'Arquivo selecionado.';
        }else{
          legend.innerHTML = 'Clique aqui para selecionar o arquivo CSV contendo os alunos.';
        }
      }

      function callLoading(txt){
        let legend = document.getElementById('file_legend').textContent;
        const loading_html = document.getElementById('loading');
        if(legend == 'Arquivo selecionado.'){
          if(loading_html.style.display === 'none'){
            loading_html.innerHTML = '<img class="loading_gif" src="../../../assets/Icons/addStudents.gif">' + txt;
            loading_html.style.display = 'flex';
          }else{
            loading_html.style.display = 'none';
          }
        }
      }
    </script>
    <title>Cadastro de Aluno</title>
  </head>
  <body>
    <div style="display: none;" class="loading" id="loading">
    </div>
    <?php

      if(!isset($_SESSION['name_user'])){
      $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
      header('Location: login.php');
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
        <form enctype="multipart/form-data" action="add_student_CSV.php" method="POST">
          <div class="title">CADASTRO DE ALUNO POR CSV</div>
          <div class="inner_container contour">
            <div class="div_file">
              <!-- MAX_FILE_SIZE deve preceder o campo input -->
              <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
              <!-- O Nome do elemento input determina o nome da array $_FILES -->
              <input class="button" onchange="changeLegendFile()" id="files" name="userfile" type="file"/>
              <label for="files"><span class="button button_file">Procurar Arquivo</span></label>
              <span class="file_legend" id="file_legend">Clique aqui para selecionar o arquivo CSV contendo os alunos.</span>
            </div>

            <a href="../../../assets/docs/students_list_one.csv" download="Modelo Estudantes CSV(não modifica a formatação).csv"><input class="button button_file" type="button" value="Baixar Modelo CSV" /></a>
            <input class="button" onclick="callLoading('CADASTRANDO ALUNOS')" type="submit" value="CADASTRAR CSV" />
          </div>
        </form>
          <form action="settings.php" method="post">
            <div class="title">CADASTRO DE ALUNO</div>
              <div class="inner_container">
                <input type="text" class="input_text" placeholder="Nome do aluno" name="name_student" required>

                <input type="email" class="input_text" placeholder="Email do aluno" name="email_student" required>

                <input type="number" class="input_text" placeholder="Nº de matrícula do aluno" name="reg_student" required>

            </div>
            <button class="button" type="submit">CADASTRAR ALUNO</button>
        </form>
        <a href='../../menu'><img src="../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
        <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
      </div>
    </section>
  </body>
</html>
