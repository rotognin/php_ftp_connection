<?php
   define ('DS',DIRECTORY_SEPARATOR);
   
   function barra($caminho) {
      echo $caminho . PHP_EOL;
      $retorno = preg_replace('/[\\\\]/',DS, $caminho);
      echo $retorno . PHP_EOL;
      return $retorno;
   }
   

   $subir_local = barra($_POST['subir_local']);
   $subir_servidor = barra($_POST['subir_servidor']);
   $baixar_servidor = barra($_POST['baixar_servidor']);
   $baixar_local = barra($_POST['baixar_local']);
   $servidor_ftp = $_POST['servidor_ftp'];
   $porta_ftp = $_POST['porta_ftp'];
   $login_ftp = $_POST['login_ftp'];
   $senha_ftp = $_POST['senha_ftp'];

   // Abrir o arquivo XML e gravar as informa��es nele
   $xml = new SimpleXMLElement('<?xml version="1.0" ?><movimento />');
   $xml_subir = $xml->addChild('subir');
   $xml_subir->addChild('local', $subir_local);
   $xml_subir->addChild('server', $subir_servidor);
   $xml_baixar = $xml->addChild('baixar');
   $xml_baixar->addChild('server', $baixar_servidor);
   $xml_baixar->addChild('local', $baixar_local);
   $xml_acesso = $xml->addChild('acesso');
   $xml_acesso->addChild('server', $servidor_ftp);
   $xml_acesso->addChild('porta', $porta_ftp);
   $xml_acesso->addChild('login', $login_ftp);
   $xml_acesso->addChild('password', $senha_ftp);

   $xml->saveXML('config.xml');
   
   header('Location: index.php');
?>