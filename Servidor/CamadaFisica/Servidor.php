<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/26/18
 * Time: 8:09 AM
 */

//===========

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

// set some variables
$host = "127.0.0.1";
$port = 8080;
$TAM_MAX_BYTES = '3000000';
// don't timeout!
set_time_limit(0);
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// bind socket to port
socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
// start listening for connections
socket_listen($socket, 3) or die("Could not set up socket listener\n");
$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
while ($spawn != FALSE)
{
    // read client input
    $quadro = socket_read($spawn, intval($TAM_MAX_BYTES)) or die("Could not read input\n");
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

    $result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
    $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
}

// close sockets
socket_close($spawn);
socket_close($socket);