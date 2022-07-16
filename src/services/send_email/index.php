<?php
  session_start();

  // PHPMailer dependencies
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;

  require '../composer_sendemail/vendor/autoload.php';

  // Getting settings to send email
  $ini_file = parse_ini_file('../../settings.ini', true);
  $user_mail = $ini_file['MAIL']['user'];
  $user_password = $ini_file['MAIL']['password'];
  $name_mail = $ini_file['MAIL']['name'];

  $cut_path_l = $ini_file['SYSTEM']['cut_path_l'];
  $cut_path_w = $ini_file['SYSTEM']['cut_path_w'];

  // Getting seting to use python in windows or linux
  $python_w = $ini_file['SYSTEM']['python_windows_path'];
  $python_l = $ini_file['SYSTEM']['python_linux_path'];

  function getTest($id_test){
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      //write SQL
      $statement = $pdo->query("SELECT nome_prova FROM prova WHERE id_prova='".$id_test."'");

      //run the SQL
      $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


    }catch(PDOException $e){
      $rows = 0;
      echo "<pre>";
      echo $e;
      echo "</pre>";
    }
    return $rows[0]['nome_prova'];
  }

  function getStudent($id_student){
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      //write SQL
      $statement = $pdo->query("SELECT nome_aluno, email_aluno FROM aluno WHERE id_aluno='".$id_student."'");

      //run the SQL
      $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


    }catch(PDOException $e){
      $rows = 0;
      echo "<pre>";
      echo $e;
      echo "</pre>";
    }
    return $rows[0];
  }

  function getUser($id_user){
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      //write SQL
      $statement = $pdo->query("SELECT nome FROM usuario WHERE id_usuario='".$id_user."'");

      //run the SQL
      $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


    }catch(PDOException $e){
      $rows = 0;
      echo "<pre>";
      echo $e;
      echo "</pre>";
    }
    return $rows[0];
  }

  $id_test = $_GET['id_test'];
  $student_data = $_GET['student_data'];
  $professor_name = getUser($_SESSION['id_user'])['nome'];
  $test_name = getTest($id_test);

  //Each element from $student_d represents a student with his/her id, hit and grade separated by schlussel
  $student_d = explode('ergebnis', $student_data);

  $test_name = getTest($id_test);

  $user_mail = $ini_file['MAIL']['user'];
  $user_password = $ini_file['MAIL']['password'];
  $name_mail = $ini_file['MAIL']['name'];

  if(!(($user_mail == '')||($user_password == '')||($name_mail == ''))){
    //Send an email for each student
    foreach ($student_d as $key) {
      $vector = explode('schlussel', $key);
      $id_student = $vector[0];
      $hit = $vector[1];
      $grade = $vector[2];
      $student = getStudent($id_student);
      $student_name = $student['nome_aluno'];
      $student_email = $student['email_aluno'];

      $msg =  '
      <body style="font-family: Calibri;font-size: 1em;background-color: #f7f7f7;width: 100%;height: max-content;color: #13D4E2;display: flex;align-items: center;">
        <section style="font-family: Calibri;font-size: 1em;background-color: #fff;width: 900px;height: max-content;display: block;border: solid 2px #13D4E2;border-radius: 0.5em;margin-left: auto;margin-right: auto;">
          <div class="title_box" style="font-family: Calibri;font-size: 1em;position: relative;text-align: center;background-color: #13D4E2;top: 50px;padding-top: 50px;padding-bottom: 50px;margin-bottom: 10px;">
            <div class="title" style="font-family: Calibri;font-size: 3em;color: #fff;">
              RESUTADO DE PROVA
            </div>
          </div>
          <div class="body_message" style="font-family: Calibri;font-size: 2em;padding: 0px 40px 0px 40px;">
              Caro aluno '.$student_name.',
              <br style="font-family: Calibri;font-size: 1em;"><br style="font-family: Calibri;font-size: 1em;">
              Este email serve para informar sua nota.<br style="font-family: Calibri;font-size: 1em;">
              <b style="font-family: Calibri;font-size: 1em;">Prova: '.$test_name.'</b><br style="font-family: Calibri;font-size: 1em;">
              <b style="font-family: Calibri;font-size: 1em;">Professor: '.$professor_name.'</b><br style="font-family: Calibri;font-size: 1em;">
              <br style="font-family: Calibri;font-size: 1em;">
              <table class="table_grades" style="font-family: Calibri;font-size: 1em;border-collapse: collapse;margin: auto;">
                <tr style="font-family: Calibri;font-size: 1em;text-align: center;border-top: 2px solid #13D4E2;border-bottom: 2px solid #13D4E2;"><td style="font-family: Calibri;font-size: 1em;padding: 5px 15px 5px 15px;">Nº de acertos</td><td style="font-family: Calibri;font-size: 1em;padding: 5px 15px 5px 15px;">Nota Final</td></tr>
                <tr style="font-family: Calibri;font-size: 1em;text-align: center;border-top: 2px solid #13D4E2;border-bottom: 2px solid #13D4E2;"><td style="font-family: Calibri;font-size: 1em;padding: 5px 15px 5px 15px;">'.$hit.'</td><td style="font-family: Calibri;font-size: 1em;padding: 5px 15px 5px 15px;">'.$grade.'</td></tr>
              </table>
              <br style="font-family: Calibri;font-size: 1em;">
              <div class="more_info" style="font-family: Calibri;font-size: 1em;text-align: center;color: #BAE6E9;margin-bottom: 20px;">
                Para mais informações entre em contato com o responsável da sua prova.
              </div>
            </div>
        </section>
      </body>
      ';

      // Instantiation and passing `true` enables exceptions
      $mail = new PHPMailer(true);

      try {

        //Server settings
        //I did comment this command for not show a big text as return
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                       //Enable verbose debug output
        $mail->isSMTP();                                                //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                           //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                       //Enable SMTP authentication
        $mail->Username   = $user_mail;                                 //SMTP username
        $mail->Password   = $user_password;                             //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;             //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                        //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above


        //Recipients
        $mail->setFrom($user_mail, $name_mail);
        $mail->addAddress($student_email, $student_name);     //Add a recipient

        //Gerating test
        $python_function = ' ' . $_SESSION['absolute_path_base'] . 'src/test_core/baixaProvasFeitas.py ';
        if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
          $cmd = $python_w. $python_function .$id_test.' '. $id_student;
          exec($cmd, $out);
          $pdf_path = $out[0];
        }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
          $cmd = $python_l. $python_function .$id_test.' '. $id_student;
          exec($cmd, $out);
          $pdf_path = str_replace($cut_path_l, '',$out[0]);
        }

        //Attachments
        $mail->addAttachment($pdf_path, $test_name.' - '.$student_name.'.pdf');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $test_name.' - Resultado';
        $mail->Body    = $msg;
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';


        if($mail->send()){
          echo '<font color="green">EMAIL PARA '.$student_name.' ENVIADO COM SUCESSO!</font><br>';
        }else{
          echo '<font color="red">EMAIL PARA '.$student_name.' NÃO ENVIADO COM SUCESSO!</font><br>';
        }

        // Delete test in pdf
        $python_function = ' ' . $_SESSION['absolute_path_base'] . 'src/test_core/deleteFile.py ';
        if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
          $cmd = $python_w . $python_function . $pdf_path;
        }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
          $cmd = $python_l . $python_function . $pdf_path;
        }
        exec($cmd, $out);


      } catch (Exception $e) {

        echo '<font color="red">EMAIL PARA '.$student_name.' NÃO ENVIADO COM SUCESSO!</font><br>';

      }
    }
  }else{
    echo '<font color="red">A configurações email não estão corretas. Consulte o administrador do sistema.</font><br>';
  }


 ?>
