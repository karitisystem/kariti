<?php

  // Getting settings to send email
  $ini_file = parse_ini_file('settings.ini', true);
  // Getting seting to use python in windows or linux
  $python_w = $ini_file['SYSTEM']['python_windows_path'];
  $python_l = $ini_file['SYSTEM']['python_linux_path'];
  $cut_path_l = $ini_file['SYSTEM']['cut_path_l'];
  $cut_path_w = $ini_file['SYSTEM']['cut_path_w'];

  $id_test = $_GET['id_test'];
  $id_class = $_GET['id_class'];
  $id_student = $_GET['id_student'];

  $out = array();
  $cmd = '';
  if($id_student != '0'){
    if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
      $cmd = $python_w.' funcaoProva.py '.$id_test.' '. $id_student;
      exec($cmd, $out);
      echo str_replace($cut_path_l, '',$out[0]);
    }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
      $cmd = $python_l.' funcaoProva.py '.$id_test.' '. $id_student;
      exec($cmd, $out);
      echo str_replace($cut_path_w, '',$out[0]);
    }
  }else{
    if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
      $cmd = $python_w.' funcaoProva.py '.$id_test.' 0';
      exec($cmd, $out);
      echo str_replace($cut_path_w, '',$out[0]);
    }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
      $cmd = $python_l.' funcaoProva.py '.$id_test.' 0';
      exec($cmd, $out);
      echo str_replace($cut_path_l, '',$out[0]);
    }
  }



 ?>
