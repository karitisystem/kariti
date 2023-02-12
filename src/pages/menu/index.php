<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="../../styles/styles.css">
    <script>
      function hideOptions(id){
        const items = document.getElementById(id);

        if(items.style.display === 'none'){
          items.style.display = 'block';
        }else{
          items.style.display = 'none';
        }
      }
    </script>
    <title>Menu</title>
  </head>
  <body>
    <?php
    	if(!isset($_SESSION['name_user'])){
    		$_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
    		header('Location: ../login?message='.$_GET['message']);
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
    <div class="container menu_length">

    <div class="inner_container items_align_center">
      <?php
        echo "<div class='user_connected'>Usuário Conectado: " . $_SESSION['name_user']."</div>";
      ?>
    <div class="title">MENU</div>
        <nav class='menu_nav'>
          <ul class="menu" id="menu">
            <li class="li_option"><img src="../../assets/Icons/prova.svg" class="icon_menu"><a id="prova" onclick="hideOptions('prova-items-1')">PROVA</a>
              <ul id="prova-items-1" style="display: none;">
                <li  class="li_sub_option"><a href="../test/add_test">Cadastrar Prova</a></li>
                <li  class="li_sub_option"><a href="../test/correct_test">Corrigir Prova</a></li>
                <li  class="li_sub_option"><a href="../test/search_test">Verificar Prova</a></li>
              </ul>

            </li>
            <li class="li_option"><img src="../../assets/Icons/aluno.svg" class="icon_menu"><a id="aluno" onclick="hideOptions('aluno-items-1')">ALUNO</a>
              <ul id="aluno-items-1" style="display: none;">
                <li  class="li_sub_option"><a href="../student/add_student">Cadastrar Aluno</a></li>
                <li  class="li_sub_option"><a href="../student/show_student">Visualizar Aluno</a></li>
              </ul>
            </li>
            <li class="li_option"><img src="../../assets/Icons/turma.svg" class="icon_menu"><a id="turma" onclick="hideOptions('turma-items-1')">TURMA</a>
              <ul id="turma-items-1" style="display: none;">
                <li  class="li_sub_option"><a href="../class/add_class">Cadastrar Turma</a></li>
                <li  class="li_sub_option"><a href="../class/show_class">Visualizar Turma</a></li>
              </ul>
            </li>
            <?php
              if ($_SESSION['id_user'] == '1'){
                $id_user_ul = "'user-items-1'";
                echo '<li class="li_option"><img src="../../assets/Icons/user.svg" class="icon_menu"><a id="turma" onclick="hideOptions('.$id_user_ul.')">USUÁRIO</a>';
                  echo '<ul id="user-items-1" style="display: none;">';
                  echo '<li  class="li_sub_option"><a href="../user/add_user">Cadastrar Uusário</a></li>';
                  echo '<li  class="li_sub_option"><a href="../user/show_user">Visualizar Usuário</a></li>';
                  echo '</ul>';
                  echo '</li>';
              }
            ?>
          </ul>
        </nav>
      </div>
      <a href='../../services/disconect' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../assets/Icons/fechar.svg" class="icon_fechar"></a>
    </div>
  </section>
  </body>
</html>
