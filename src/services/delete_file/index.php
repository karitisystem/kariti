<?php

  session_start();

  // Getting settings to send email
  $ini_file = parse_ini_file('../../settings.ini', true);
  // Getting seting to use python in windows or linux
  $python_w = $_SESSION['absolute_path_base'] . $ini_file['SYSTEM']['python_windows_path'];
  $python_l = $_SESSION['absolute_path_base'] . $ini_file['SYSTEM']['python_linux_path'];

  $path = '../../../'.$_GET['path'];

  $python_function = ' ' . $_SESSION['absolute_path_base'] . 'src/test_core/deleteFile.py ';

  echo 'batata';

  if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
    $cmd = $python_w . $python_function . $path;
  }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
    $cmd = $python_l . $python_function . $path;
  }

  sleep(8);
  exec($cmd, $out);
  echo 'prova deletada com sucesso . Path:'. $path;

 ?>
