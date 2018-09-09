<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/26/18
 * Time: 8:09 AM
 */

function bin_to_string($bin){ //converte a sequencia binaria para uma string//
    $string = '';
    for($i=0; $i<(strlen($bin)-1); $i+=8){//para cada caractere em binário//
        $hex = base_convert(substr($bin, $i, 8), 2, 16);//converte de binário para hexadecimal//
        while(strlen($hex)<2){ $hex = '0'.$hex; }
        $c = pack('H*', $hex);//passa o hexadecimal para caractere, concatena na string//
        $string .= $c;
    } //echo "Binario: ".$bin."<</br>>String: ".$string."<</br>>";//debug//
    return $string;
}
function string_to_bin($string){ //converte a string em uma sequencia binaria//
    $bin = '';
    $chars = str_split($string); //separa a string em um array de caracteres//
    foreach($chars as $c){//para cada caractere na string//
        $hex = unpack('H*', $c);//passa o caractere para hexadecimal//
        $b = base_convert($hex[1], 16, 2); //passa o hexadecimal para binario//
        while(strlen($b)<8){ $b = '0'.$b; } //garante que tem 8 bits//
        $bin .= $b; //concatena//
    } //echo "String: ".$string."</br>Binario: ".$bin."</br>";//debug//
    return $bin;
}
function MontaPacote($quadro){
    $preambulo = substr($quadro, 0, 4); //4 bits//
    $sfd = substr($quadro, 4, 8); //8 bits//
    $mac_org = substr($quadro, 12, 48); //echo bin_to_mac($mac_org)."</br>";//
    $mac_dest = substr($quadro, 60, 48);  //echo bin_to_mac($mac_dest)."</br>";//
    $tipo = substr($quadro, 108, 16); //16 bits//IP//
    $tam_dado = strlen($quadro) - 156; //tamanho total - cabeçalho - crc//
    $data = substr($quadro, 124, $tam_dado);
    $data = bin_to_string($data); //converte o pacote para string//
    $crc = substr($quadro, 124+$tam_dado, 32); //crc tem 32 bits//string ERRO//
    return $data;
}

//Log geral para registro das informacoes
$log_geral = "../log_geral.txt";

$TAM_MAX_BYTES = 3000000;

// set some variables
$host = "127.0.0.1";
$port = 8080;
$TAM_MAX_BYTES = '3000000';
// don't timeout!
set_time_limit(0);
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0);
if(!$socket) {
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_create --- erro ao criar socket --- \n", FILE_APPEND);
    exit("Could not set up socket listener\n");
}else {
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_create --- socket criado com sucesso --- \n", FILE_APPEND);
}
// bind socket to port
if(!socket_bind($socket, $host, $port)){
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_bind --- socket_bind error --- \n", FILE_APPEND);
    exit("Could not set up socket bind\n");
} else {
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_bind --- socket_bind ok --- \n", FILE_APPEND);
}
// start listening for connections

$result = socket_listen($socket, 3);// or die("Could not set up socket listener\n");
//Verificacao e log
if($result === false){
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_listen --- Abrir escuta para uma conexão no socket error --- \n", FILE_APPEND);
    exit("Could not set up socket listener\n");    
}
else{
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_listen --- Abrir escuta para uma conexão no socket OK --- \n", FILE_APPEND);
}
$spawn = socket_accept($socket);// or die("Could not accept incoming connection\n");
//Verificacao e log
if($spawn === false){
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_accept --- $spawn falhou --- \n", FILE_APPEND);
    exit("Could not accept incoming connection\n");    
}
else{
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, $timestamp." Socket_accept --- $spawn OK --- \n", FILE_APPEND);
}

while ($spawn != FALSE)
{
    // read client input
    $quadro = socket_read($spawn, intval($TAM_MAX_BYTES));// or die("Could not read input\n");
    if($quadro === false){
        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($log_geral, $timestamp." Socket_read --- Could not read input --- \n", FILE_APPEND);
        exit("Could not read input\n");
    }
    else{
        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($log_geral, $timestamp." Socket_read ---  OK --- \n", FILE_APPEND);
    }
    // clean up input string
    $quadro = trim($quadro);
    print "quadro " . $quadro . "\n";
    $mensagem = bin_to_string($quadro);
    if(strcmp($mensagem, "TAM") == 0)
    {
        $resposta = string_to_bin($TAM_MAX_BYTES);
        socket_write($spawn, $resposta, strlen ($resposta)) or die("Could not write output\n");
    }
    else
    {
        print "MP ". MontaPacote($quadro) . "\n\n";
        socket_write($spawn, $quadro, strlen ($quadro)) or die("Could not write output\n");
    }

    $result = socket_listen($socket, 3);// or die("Could not set up socket listener\n");
    if($result === false){
        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($log_geral, $timestamp." Socket_listen --- Abrir escuta para uma conexão no socket error --- \n", FILE_APPEND);
        exit("Could not set up socket listener\n");    
    }
    else{
         $timestamp = date("Y-m-d H:i:s");
         file_put_contents($log_geral, $timestamp." Socket_listen --- Abrir escuta para uma conexão no socket OK --- \n", FILE_APPEND);
    }
    $spawn = socket_accept($socket);// or die("Could not accept incoming connection\n");
    if($spawn === false){
            $timestamp = date("Y-m-d H:i:s");
            file_put_contents($log_geral, $timestamp." Socket_accept --- $spawn falhou --- \n", FILE_APPEND);
            exit("Could not accept incoming connection\n");    
    }
    else{
            $timestamp = date("Y-m-d H:i:s");
            file_put_contents($log_geral, $timestamp." Socket_accept --- $spawn OK --- \n", FILE_APPEND);
    }
}

// close sockets
socket_close($spawn);
$timestamp = date("Y-m-d H:i:s");
file_put_contents($log_geral, "Socket_close($spawn) --- Conexao encerrada --- ".$timestamp."\n", FILE_APPEND);
socket_close($socket);
$timestamp = date("Y-m-d H:i:s");
file_put_contents($log_geral, "Socket_close($socket) --- Conexao encerrada --- ".$timestamp."\n", FILE_APPEND);
echo "Conexao encerrada!\n";
?>
