<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/25/18
 * Time: 9:37 PM
 */

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
$bin = macParaBinario(getMAC());
binarioParaMac($bin);