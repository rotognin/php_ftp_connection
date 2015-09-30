<?php
    // Carregar o arquivo XML de configurações
    $config = 'config.xml';
    if (!is_file($config)) {
       echo 'Arquivo config nao encontrado.' . PHP_EOL;
       exit;
    }

    if (!$xml_config = simplexml_load_file($config)){
        echo 'Arquivo xml de configuracao nao carregado.';
        exit;
    }

    $configs = array('subir_local' => $xml_config->subir->local,
                     'subir_server' => $xml_config->subir->server,
                     'baixar_server' => $xml_config->baixar->server,
                     'baixar_local' => $xml_config->baixar->local,
                     'acesso_server' => $xml_config->acesso->server,
                     'acesso_porta' => (string)$xml_config->acesso->porta,
                     'acesso_login' => $xml_config->acesso->login,
                     'acesso_password' => $xml_config->acesso->password);

    foreach ($configs as $chave => $valor) {
        if ($valor == '') {
            $valor_invalido = str_replace('_', ' ', $chave);
            echo 'O valor "' . $valor_invalido . '" nao foi informado.' . PHP_EOL;
            exit;
        }
    }

    // Ver se a pasta de subir e baixar são válidas e existem
    // Se não existirem, criá-las
    if (!is_dir($configs['subir_local'])) {
        if (!mkdir($configs['subir_local'], 0777, true)){
            echo 'Nao foi possivel criar a pasta subir local: ' . $configs['subir_local'] . PHP_EOL;
            exit;
        }
    }

    if (!is_dir($configs['baixar_local'])) {
        if (!mkdir($configs['baixar_local'], 0777, true)){
            echo 'Nao foi possivel criar a pasta baixar local: ' . $configs['baixar_local'] . PHP_EOL;
            exit;
        }
    }

    // Conectar no ftp.
    echo 'Conectando ao servidor...' . PHP_EOL;

    if (!$ftp_conn = ftp_connect($configs['acesso_server'])) {
       echo 'Nao foi possivel conectar a ' . $configs['acesso_server'] . PHP_EOL;
       exit;
    }

    echo 'Conectado. Efetuando login...' . PHP_EOL;

    if (!ftp_login($ftp_conn, $configs['acesso_login'], $configs['acesso_password'])) {
       ftp_close($ftp_conn);
       echo 'Login invalido.' . PHP_EOL;
       exit;
    }

    echo 'Login OK.' . PHP_EOL;
    echo 'Entrando em modo passivo...' . PHP_EOL;

    if (ftp_pasv($ftp_conn, true)) {
       echo 'Modo passivo OK.' . PHP_EOL;
    } else {
       echo 'Falha ao entrar em modo passivo.' . PHP_EOL;
    }

    echo 'Pegando o caminho atual...' . PHP_EOL;
    $caminho = ftp_pwd($ftp_conn);

    echo 'Caminho atual: ' . $caminho . PHP_EOL;

    // Checar as pastas no servidor se são válidas.
    // Se não existirem, criá-las.
    $pasta_subir = $caminho . $configs['subir_server'];

    // Checar se a pasta do servidor existe
    if (!@ftp_chdir($ftp_conn, $pasta_subir)) {
       // Se não existe, criá-la
       if (!ftp_mkdir($ftp_conn, $pasta_subir)) {
           echo 'Pasta para subir nao criada: ' . $pasta_subir;
           ftp_close($ftp_conn);
           exit;
       }
    }

    echo 'Ver se existem arquivos para subir' . PHP_EOL;
    $ver_arq = @scandir($configs['subir_local']);
    $arquivos = array_diff($ver_arq, array('.','..'));

    if (is_array($arquivos) && count($arquivos) > 0) {
       echo 'Existem ' . (int)count($arquivos) . ' a serem enviados' . PHP_EOL;
       // Quer dizer que foram encontrados arquivos para subir
       foreach ($arquivos as $arquivo) {
          // Subir para o FTP
          if (is_file($configs['subir_local'] . $arquivo)) {
              // Subir o arquivo. O mesmo será substituído caso exista no destino
              if (!ftp_put($ftp_conn, $pasta_subir . $arquivo, $configs['subir_local'] . $arquivo, FTP_BINARY)) {
                  echo 'O arquivo ' . $arquivo . ' nao subiu.' . PHP_EOL;
                  echo 'Subir local: ' . $configs['subir_local'] . $arquivo . PHP_EOL;
                  echo 'Subir server: ' . $pasta_subir . $arquivo . PHP_EOL;
              } else {
                  echo ' -> Arquivo ' . $arquivo . ' enviado.' . PHP_EOL;
              }
          }
       }
    } else {
       echo 'Sem arquivos para subir.' . PHP_EOL;
    }

    echo 'Ver se existem arquivos para baixar... ' . PHP_EOL;
    $pasta_baixar = $caminho . $configs['baixar_server'];
    if (!@ftp_chdir($ftp_conn, $pasta_baixar)) {
       echo 'Nao foi possivel alterar para a pasta: ' . $pasta_baixar . PHP_EOL;
       ftp_close($ftp_conn);
       exit;
    }

    echo 'Checar arquivos na pasta ' . $pasta_baixar . PHP_EOL;

    $ver_arq = ftp_nlist($ftp_conn, $pasta_baixar);

    $arquivos_baixar = array_diff($ver_arq, array('.','..'));

    if (is_array($arquivos_baixar) && count($arquivos_baixar) > 0) {
       echo 'Existem ' . (int)count($arquivos_baixar) . ' arquivos a serem baixados. ' . PHP_EOL;
       foreach ($arquivos_baixar as $arquivo) {
          if (!ftp_get($ftp_conn, $configs['baixar_local'] . $arquivo, $pasta_baixar . $arquivo, FTP_BINARY)) {
              echo 'Nao baixou o arquivo ' . $arquivo . PHP_EOL;
          } else {
              echo ' -> Baixou o arquivo ' . $arquivo . PHP_EOL;
          }
       }
    } else {
       echo 'Sem arquivos para baixar.' . PHP_EOL;
    }

    // Fechar a conexão e encerrar o programa.
    ftp_close($ftp_conn);

    echo 'Conexao finalizada, processo finalizado.' . PHP_EOL;

?>