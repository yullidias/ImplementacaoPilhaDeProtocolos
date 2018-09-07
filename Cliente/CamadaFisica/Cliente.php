<?php

$IP_ORIGEM = "127.0.0.1";
$IP_DESTINO = "127.0.0.1";
$PORTA_SERVIDOR_FISICA = 8080;
$ARQUIVO_LOG = "../../log.txt";
$LIMITE_MAXIMO_MENSAGEM = '1024';
$MAC_from_IP = array( "127.0.0.1" => "d0:df:9a:c4:07:ab");

function getMAC($ip, &$macIp)
{
    if(array_key_exists($ip,$macIp))
    {
        $mac = $macIp[$ip];
    }
    else
    {
        do
        {
            $arp_scan = shell_exec("arp-scan " . $ip); //necessario executar como root
            $linhas = explode("\n", $arp_scan);
            $array = str_split($linhas[2]);
            $mac = '';
            $i = 13;
            while($i < strlen($linhas[2]) && $i <=29)
            {
                $mac = $mac . $array[$i];
                $i++;
            }
        }while(strlen($mac) < 17);
        $macIp[$ip] = $mac;
    }
    EscreveNoLog("Protocolo ARP o ip " . $ip . " pertence ao MAC " . $mac);
    return $mac;
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

    if (socket_write($socket, $mensagem, strlen($mensagem)) === FALSE) //retorna 0 quando os bits são escritos o operador === é usando para garantir que retornou falso e não 0
    {
        EscreveNoLog("Mensagem não enviada");
    }
    else
    {
        EscreveNoLog("Mensagem enviada");
    }
}

function receberRespostaServidor($socket, $limiteMensagem)
{
    $resposta = socket_read ($socket, intval($limiteMensagem));
    if( $resposta === FALSE)
    {
        EscreveNoLog("Resposta não recebida");
        return null;
    }
    else
    {
        EscreveNoLog("Resposta recebida");
        return $resposta;
    }
}
function timestamp()
{
    $now = getdate();
    $data = $now['mday'] . ' ' . $now['month'] . ' ' . $now['year'] . ' ' . $now['hours'] . ':' . $now['minutes'] . ':' . $now['seconds'] ." ";
    return $data;
}
function EscreveNoLog($mensagem)
{
    file_put_contents ( $GLOBALS['ARQUIVO_LOG'], timestamp() ."[Física: Cliente] " . $mensagem . ". \n", FILE_APPEND | LOCK_EX); //lock_ex lock exclusivo

}
function EnviarMensagemEObterRespostaDoServidor($mensagem, $limite)
{
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    if ($socket === FALSE){
        EscreveNoLog("Socket com a camada física não criado");
    }
    else
    {
        EscreveNoLog("Socket com a camada física criado");
    }
    $result = socket_connect($socket, $GLOBALS['IP_DESTINO'], $GLOBALS['PORTA_SERVIDOR_FISICA']);
    if($result === FALSE)
    {
        EscreveNoLog("Conexão criada com a camada física");
    }
    enviarMessagemServidor($socket, $mensagem);
    $resposta = receberRespostaServidor($socket, $limite);
    socket_close($socket);
    return $resposta;
}
function string_to_bin($string){
    $stringEmBinario = '';
    $arrayDeCaracter = str_split($string);
    foreach($arrayDeCaracter as $caracter){
        $caracterEmHexadecimal = unpack('H*', $caracter);
        $caracterEmBinario = base_convert($caracterEmHexadecimal[1], 16, 2);
        while(strlen($caracterEmBinario)<8){ $caracterEmBinario = '0'.$caracterEmBinario; } //garante que tem 8 bits
        $stringEmBinario .= $caracterEmBinario;
    }
    return $stringEmBinario;
}
function bin_to_string($sequenciaDeBits){
    $string = '';
    for($i=0; $i<(strlen($sequenciaDeBits)-1); $i+=8){
        $hex = base_convert(substr($sequenciaDeBits, $i, 8), 2, 16);
        while(strlen($hex)<2)
        {
            $hex = '0'.$hex;
        }
        $caracter = pack('H*', $hex);
        $string .= $caracter;
    }
    return $string;
}

function getMensagemPacote()
{
    $conteudo = file('../pacote.txt');
    $split = explode(' ', $conteudo[0]);
    return $split[1];
}
function getIpPacote()
{
    $conteudo = file('../pacote.txt');
    $split = explode(' ', $conteudo[0]);
    return $split[0];
}
function MontaQuadro(&$macIp)
{
    $ipDestino = getIpPacote();
    $mensagem = getMensagemPacote();
    $preambulo = '0101';
    $sfd = '10101011'; // Delimitador de início de quadro
    $macOrigem = macParaBinario(getMAC($GLOBALS['IP_ORIGEM'], $macIp));
    $macDestino = macParaBinario(getMAC($ipDestino, $macIp));
    $tipo = '0100100101010000';//IP
    $data = string_to_bin($mensagem);
    $crc = '01000101010100100101001001001111'; //string ERRO
    return $preambulo.$sfd.$macOrigem.$macDestino.$tipo.$data.$crc;
}

$quadro = MontaQuadro($MAC_from_IP);

$tamMensagemEmBinario = EnviarMensagemEObterRespostaDoServidor(string_to_bin("TAM"), $GLOBALS['LIMITE_MAXIMO_MENSAGEM']);
$GLOBALS['LIMITE_MAXIMO_MENSAGEM'] = bin_to_string($tamMensagemEmBinario);
print "limite " . $GLOBALS['LIMITE_MAXIMO_MENSAGEM'];
$N_maxTentativas = 10;
$tentativa = 0;
while($tentativa < $N_maxTentativas)
{
    if(rand(0,100) > 30)
    {
        $tentativa += 1;
        EscreveNoLog("Colisão! Tentativa " . $tentativa);
        sleep(rand(0,3));
    }
    else
    {
        $tentativa = 0;
        $mensagem = MontaQuadro($MAC_from_IP);
        $resposta = EnviarMensagemEObterRespostaDoServidor($mensagem, $GLOBALS['LIMITE_MAXIMO_MENSAGEM']);
        break;
    }
    sleep(1);
}

if($tentativa == $N_maxTentativas)
{
    EscreveNoLog("Número máximo de tentativas para enviar o pacote foi atingido");
}

if(strcmp($resposta, $mensagem) == 0)
{
    print "\n\nPacote recebido com sucesso!";
}