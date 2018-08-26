<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/25/18
 * Time: 9:37 PM
 */
//Log geral para registro das informacoes
$log_geral = "../log_geral.txt";

/*
function getMAC($interface)
{
    $arp_scan = shell_exec("arp-scan --interface=" . $interface . " --localnet"); //necessario executar como root
    print $arp_scan;
    $linhas = explode("\n", $arp_scan);
    print "===\n";
    print $linhas[3];
    $ipAndMAC = explode("	", $linhas[3]);
    $IP = $ipAndMAC[0];
    $MAC = $ipAndMAC[1];
    print $IP. $MAC;
}*/

function getMAC()
{
    return "d0:df:9a:c4:07:ab";
}

function macParaBinario($mac)
{
    $binario = '';
    $macArray = explode(':', $mac);
    foreach ($macArray as $hexaComDoisDigitos)
    {
        $bin =  base_convert($hexaComDoisDigitos, 16, 2);
        while( strlen($bin) < 8)
        {
            $bin = '0'. $bin;
        }
        $binario = $binario . $bin;
    }
    return $binario;
}

function binarioParaMac($binario)
{
    $macDesformatado =  base_convert($binario, 2, 16);
    $mac = substr($macDesformatado, 0, 2);
    for ($i = 2; $i < strlen($macDesformatado); $i += 2)
    {
        $mac = $mac . ":" . substr($macDesformatado, $i, 2);
    }
    return $mac;
}

function enviarMessagemServidor($socket, $mensagem)
{
    echo "Message To server :".$mensagem;
// send string to server
    socket_write($socket, $mensagem, strlen($mensagem)) or die("Could not send data to server\n");
}

function receberRespostaServidor($socket, $limiteMensagem)
{
    $result = socket_read ($socket, $limiteMensagem) or die("Could not read server response\n");
    echo " Reply From Server  :".$result . "\n";
    return $result;
}

function conectarAoServidor($host, $port, $mensagem, $limite)
{
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n"); //SOL_TCP
    /*AF_INET é um parametro domain IPv4 baseado nos protocolos de Internet. TCP é protocolo comum dessa família de protocolos.*/
    /* SOCK_STREAM éFornece sequencial, seguro, e em ambos os sentidos, conexões baseadas em "byte streams". Dados "out-of-band" do
    mecanismo de transmissão devem ser suportados. O protocolo TCP é baseado neste tipo de socket*/
    //Verificar se a criacao do socket foi ok
    if ($socket === false){
        echo "/n------\nErro na criacao do socket: ".socket_strerror(socket_last_error())."\n------\n";
        //chama a função socket_strerror() e pega o código de erro com a função socket_last_error().
        //Retorna uma string descrevendo o erro.
        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($log_geral, "Criacao de socket --- Erro na criacao do socket do cliente --- ".$timestamp."\n", FILE_APPEND);
	    //FILE_APPEND - Se o arquivo filename já existir, acrescenta os dados ao arquivo ao invés de sobrescrevê-lo.
    }
    else{
	    echo "Socket criado com sucesso!\n"; //Exibe uma string avisando que a criacao ocorreu bem
	    $timestamp = date("Y-m-d H:i:s");
	    file_put_contents($log_geral, "Criacao de socket --- Sucesso na criacao do socket do cliente --- ".$timestamp."\n", FILE_APPEND);
    }
    $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
    enviarMessagemServidor($socket, $mensagem);
    $resposta = receberRespostaServidor($socket, $limite);
    socket_close($socket);
    return $resposta;
}
$bin = macParaBinario(getMAC());
binarioParaMac($bin);


//=========================
$host    = "127.0.0.1";
$port    = 8080;

$tamMensagem = conectarAoServidor($host,$port,"TAM", 1024);
conectarAoServidor($host,$port, "Teste", $tamMensagem);
