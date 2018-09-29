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
    escreveNoLog("Protocolo ARP o ip " . $ip . " pertence ao MAC " . $mac);
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
        escreveNoLog("Mensagem não enviada");
    }
    else
    {
        escreveNoLog("Mensagem enviada");
    }
}

function receberRespostaServidor($socket, $limiteMensagem)
{
    $resposta = socket_read ($socket, intval($limiteMensagem));
    if( $resposta === FALSE)
    {
        escreveNoLog("Resposta não recebida");
        return null;
    }
    else
    {
        escreveNoLog("Resposta recebida");
        return $resposta;
    }
}
function timestamp()
{
    $now = getdate();
    $data = $now['mday'] . ' ' . $now['month'] . ' ' . $now['year'] . ' ' . $now['hours'] . ':' . $now['minutes'] . ':' . $now['seconds'] ." ";
    return $data;
}
function escreveNoLog($mensagem)
{
    file_put_contents ( $GLOBALS['ARQUIVO_LOG'], timestamp() ."[Física: Cliente] " . $mensagem . ". \n", FILE_APPEND | LOCK_EX); //lock_ex lock exclusivo

}
function enviarMensagemEObterRespostaDoServidor($mensagem, $limite)
{
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    if ($socket === FALSE){
        escreveNoLog("Socket com a camada física não criado");
    }
    else
    {
        escreveNoLog("Socket com a camada física criado");
    }
    $result = socket_connect($socket, $GLOBALS['IP_DESTINO'], $GLOBALS['PORTA_SERVIDOR_FISICA']);
    if($result === FALSE)
    {
        escreveNoLog("Conexão criada com a camada física");
    }
    enviarMessagemServidor($socket, $mensagem);
    $resposta = receberRespostaServidor($socket, $limite);
    socket_close($socket);
    return $resposta;
}
function stringParaBinario($string){
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
function binarioParaString($sequenciaDeBits){
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
    return $split[0];//No momento , o pacote possui apenas o dominio a ser enviado ao servidor dns - troquei a posicao para 0
}
function getIpPacote()//Funcao nao utilizada no momento
{
    $conteudo = file('../pacote.txt');
    $split = explode(' ', $conteudo[0]);
    return $split[0];
}
function montaQuadro(&$macIp)
{
    //$ipDestino = getIpPacote();
    $mensagem = getMensagemPacote();
    $preambulo = '0101';
    $sfd = '10101011'; // Delimitador de início de quadro
    $macOrigem = macParaBinario(getMAC($GLOBALS['IP_ORIGEM'], $macIp));
    $macDestino = macParaBinario(getMAC($GLOBALS['IP_DESTINO'], $macIp));//Troquei para pegar a variave global tb
    $tipo = '0100100101010000';//IP
    $data = stringParaBinario($mensagem);
    $crc = '01000101010100100101001001001111'; //string ERRO
    return $preambulo.$sfd.$macOrigem.$macDestino.$tipo.$data.$crc;
}

$quadro = montaQuadro($MAC_from_IP);

$tamMensagemEmBinario = enviarMensagemEObterRespostaDoServidor(stringParaBinario("TAM"), $GLOBALS['LIMITE_MAXIMO_MENSAGEM']);
$GLOBALS['LIMITE_MAXIMO_MENSAGEM'] = binarioParaString($tamMensagemEmBinario);
print "\n\nlimite " . $GLOBALS['LIMITE_MAXIMO_MENSAGEM'] . "\n\n";
$N_maxTentativas = 10;
$tentativa = 0;
$a = array_fill(0, 10, 'null');
//print_r($a);
$probcolisao = (20*10)/100;//probabilidade de 20%
$minrange = 0;
$maxrange = 1;
for($w = 0 ; $w < 10; $w ++) {
    $contador = 0;
    for($j=0; $j <= $w; $j ++){
        if($a[$j] === 1){
            $contador ++;
        }
    }
    if($contador < $probcolisao) {
        $a[$w] = random_int($minrange, $maxrange);
    }
    else{
        $a[$w] = 0;
    }
}
//print_r($a);
$conta = 2;
while($tentativa < $N_maxTentativas) {
    $sorteio = random_int(0, 9);
    //print_r($sorteio);
    if($a[$sorteio] === 1) {
        $tentativa += 1;
        echo "\nCOLISAO! --- Contagem aleatoria de tempo para tentar outra vez... \n";
        escreveNoLog("Colisão! Tentativa " . $tentativa);
        //sleep(rand(0,3));
        sleep($conta);
        $conta = $conta + 2;//incrementa a contagem dos segudos ate tentar reenviar
    }
    else
    {
        $tentativa = 0;
        $mensagem = montaQuadro($MAC_from_IP);
        $resposta = enviarMensagemEObterRespostaDoServidor($mensagem, $GLOBALS['LIMITE_MAXIMO_MENSAGEM']);
        if(strcmp($resposta, $mensagem) == 0)
        {
            print "\n\nPacote recebido com sucesso!\n\n";
        }
        break;
    }
    sleep(1);
}

if($tentativa == $N_maxTentativas)
{
    escreveNoLog("Número máximo de tentativas para enviar o pacote foi atingido");
}
