<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/26/18
 * Time: 8:09 AM
 */

//===========
//Log geral para registro das informacoes
$log_geral = "../log_geral.txt";

$TAM_MAX_BYTES = 3000000;
// set some variables
$host = "127.0.0.1";
$port = 8080;
// don't timeout!
set_time_limit(0);
// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
// bind socket to port
$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
// start listening for connections
$result = socket_listen($socket, 3);// or die("Could not set up socket listener\n");
//Verificacao e log
if($result === false){
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, "Socket_listen --- Abrir escuta para uma conexão no socket OK --- ".$timestamp."\n", FILE_APPEND);
    exit("Could not set up socket listener\n");    
}
else{
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, "Socket_listen --- Abrir escuta para uma conexão no socket OK --- ".$timestamp."\n", FILE_APPEND);
}
$spawn = socket_accept($socket);// or die("Could not accept incoming connection\n");
//Verificacao e log
if($spawn === false){
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, "Socket_accept --- $spawn falhou --- ".$timestamp."\n", FILE_APPEND);
    exit("Could not accept incoming connection\n");    
}
else{
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_geral, "Socket_accept --- $spawn OK --- ".$timestamp."\n", FILE_APPEND);
}

//loop
while ($spawn != FALSE)
{
    // read client input
    $input = socket_read($spawn, 1024) or die("Could not read input\n");
    // clean up input string
    $input = trim($input);
    echo "\nClient Message : ".$input;
    if(strcmp($input, "TAM") == 0)
    {
        socket_write($spawn, $TAM_MAX_BYTES, strlen ($TAM_MAX_BYTES)) or die("Could not write output\n");
    }
    else
    {
        // reverse client input and send back
        $output = strrev($input) . "\n";
        socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
    }

    $result = socket_listen($socket, 3);// or die("Could not set up socket listener\n");
    if($result === false){
        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($log_geral, "Socket_listen --- Abrir escuta para uma conexão no socket OK --- ".$timestamp."\n", FILE_APPEND);
        exit("Could not set up socket listener\n");    
    }
    else{
         $timestamp = date("Y-m-d H:i:s");
         file_put_contents($log_geral, "Socket_listen --- Abrir escuta para uma conexão no socket OK --- ".$timestamp."\n", FILE_APPEND);
    }
    $spawn = socket_accept($socket);// or die("Could not accept incoming connection\n");
        if($spawn === false){
            $timestamp = date("Y-m-d H:i:s");
            file_put_contents($log_geral, "Socket_accept --- $spawn falhou --- ".$timestamp."\n", FILE_APPEND);
            exit("Could not accept incoming connection\n");    
        }
        else{
            $timestamp = date("Y-m-d H:i:s");
            file_put_contents($log_geral, "Socket_accept --- $spawn OK --- ".$timestamp."\n", FILE_APPEND);
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
