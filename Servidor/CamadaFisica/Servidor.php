<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/26/18
 * Time: 8:09 AM
 */

//===========
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
$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
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

    $result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
    $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
}

// close sockets
socket_close($spawn);
socket_close($socket);