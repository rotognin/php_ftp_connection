<?php
   include ('class/theform.class.php');

   // Aqui ser� montado o formul�rio onde ser�o feitas as configura��es
   // de pastas para subir e baixar.
   // 1 - Ler o arquivo config.xml e carregar as pastas e configura��es
   // 2 - Montar o formul�rio utilizando a classe TheForm
   // A partir da�, o usu�rio ir� alterar ou n�o os dados necess�rios
   //    e gravar as informa��es. Um outro arquivo ir� gravar as
   //    informa��es no arquivo config.xml

   $pagina = '';
   $arquivo = 'index.html';
   $config = 'config.xml';

   if (!$xml_config = simplexml_load_file($config)) {
      echo 'Nao carregou o arquivo de configuracao.' . PHP_EOL;
      exit;
   }

   $form = new TheForm();
   $form->form_before = '<!DOCTYPE html><html><head><title>Configurações' .
                        '</title></head><body><p><h2>Configurações</h2>';
   $form->form_after = '</p></body></html>';
   $form->form_name = 'configuracoes';
   $form->form_action = 'grava_config.php';
   $form->form_method = 'post';
   $pagina .= $form->openForm();

   $form->before_field = '<p><h3>Caminhos para Subir</h3></p><p>';
   $form->label_name = 'Local: ';
   $form->type = 'text';
   $form->id = 'subir_local';
   $form->name = 'subir_local';
   $form->width = '400';
   $form->value = $xml_config->subir->local;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->before_field = '<p>';
   $form->label_name = 'Servidor: ';
   $form->type = 'text';
   $form->id = 'subir_servidor';
   $form->name = 'subir_servidor';
   $form->width = '400';
   $form->value = $xml_config->subir->server;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->before_field = '<p><h3>Caminhos para Baixar</h3></p><p>';
   $form->label_name = 'Servidor: ';
   $form->type = 'text';
   $form->id = 'baixar_servidor';
   $form->name = 'baixar_servidor';
   $form->width = '400';
   $form->value = $xml_config->baixar->server;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->before_field = '<p>';
   $form->label_name = 'Local: ';
   $form->type = 'text';
   $form->id = 'baixar_local';
   $form->name = 'baixar_local';
   $form->width = '400';
   $form->value = $xml_config->baixar->local;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->before_field = '<p><h3>Informações</h3></p><p>';
   $form->label_name = 'Servidor FTP: ';
   $form->type = 'text';
   $form->id = 'servidor_ftp';
   $form->name = 'servidor_ftp';
   $form->width = '400';
   $form->value = $xml_config->acesso->server;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->before_field = '<p>';
   $form->label_name = 'Porta (padrão: 21): ';
   $form->type = 'number';
   $form->id = 'porta_ftp';
   $form->name = 'porta_ftp';
   $form->width = '50';
   $form->value = $xml_config->acesso->porta;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->before_field = '<p>';
   $form->label_name = 'Login: ';
   $form->type = 'text';
   $form->id = 'login_ftp';
   $form->name = 'login_ftp';
   $form->width = '200';
   $form->value = $xml_config->acesso->login;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->before_field = '<p>';
   $form->label_name = 'Senha: ';
   $form->type = 'text';
   $form->id = 'senha_ftp';
   $form->name = 'senha_ftp';
   $form->width = '200';
   $form->value = $xml_config->acesso->password;
   $form->after_field = '</p>';
   $pagina .= $form->getField();

   $form->button_type = 'submit';
   $form->button_value = 'Gravar';
   $pagina .= $form->getButton();

   $pagina .= $form->closeForm();


   echo $pagina;

   /*
   $h_file = fopen($arquivo, 'w');
   fwrite($h_file, $pagina);
   fclose($h_file);
   */
?>