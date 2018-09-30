<?php

$MEU_IP = "127.0.0.1";
$IP_SERVIDOR = "127.0.0.1";
$PORTA_SERVIDOR_FISICA = 8080;
$MINHA_PORTA_CAMADA_SUPERIOR = 8090;
$ARQUIVO_LOG = "../../log.txt";
$LIMITE_MAXIMO_MENSAGEM = '3000000';
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
    $result = socket_connect($socket, $GLOBALS['IP_SERVIDOR'], $GLOBALS['PORTA_SERVIDOR_FISICA']);
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
function montaQuadro(&$macIp, $data = null)
{
    $preambulo = '0101';
    $sfd = '10101011'; // Delimitador de início de quadro
    $mensagem = $data == null ? getMensagemPacote() : $data;
    $macOrigem = macParaBinario(getMAC($GLOBALS['MEU_IP'], $macIp));
    $macDestino = macParaBinario(getMAC($GLOBALS['IP_SERVIDOR'], $macIp));//Troquei para pegar a variave global tb
    $tipo = '0100100101010000';//IP
    $data = stringParaBinario($mensagem);
    $crc = '01000101010100100101001001001111'; //string ERRO
    return $preambulo.$sfd.$macOrigem.$macDestino.$tipo.$data.$crc;
}

function criaServidorSocketCamadaSuperior()
{
    set_time_limit(0); //sem timeout
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    if($socket === FALSE)
    {
        escreveNoLog("Socket com a camada superior não criado");
    }
    else
    {
        escreveNoLog("Socket com a camada superior criado");
    }

    if(socket_bind($socket, $GLOBALS['MEU_IP'], $GLOBALS['MINHA_PORTA_CAMADA_SUPERIOR']) === FALSE)
    {
        escreveNoLog("Erro ao vincular nome para o socket");
    }
    return $socket;
}

function comunicacaoComOServidorCamadaFisica($MAC_from_IP, $data = null)
{
    $N_maxTentativas = 10;
    $tentativa = 0;
    while($tentativa < $N_maxTentativas) {
        if(rand(0,100) > 40)
        {
            $tentativa += 1;
            EscreveNoLog("Colisão! Tentativa " . $tentativa);
            sleep(rand(0,3));
        }
        else
        {
            $tentativa = 0;
            $mensagem = montaQuadro($MAC_from_IP, $data);
            $resposta = enviarMensagemEObterRespostaDoServidor($mensagem, $GLOBALS['LIMITE_MAXIMO_MENSAGEM']);
            return $resposta;
        }
        sleep(1);
    }

    if($tentativa == $N_maxTentativas)
    {
        escreveNoLog("Número máximo de tentativas para enviar o pacote foi atingido");
        return -1;
    }
}

$socketCamadaSuperior = criaServidorSocketCamadaSuperior();
do
{
    $result = socket_listen($socketCamadaSuperior);
    if($result === false)
    {
        escreveNoLog("Erro ao ouvir conexão");
    }
    else
    {
        escreveNoLog("Ouvindo a conexão");
    }
    print("Listening ...\n");
    $IsConexaoAceita = socket_accept($socketCamadaSuperior);
    print("conexao aceita\n");
    if($IsConexaoAceita === false){
        escreveNoLog("Conexão não aceita");
    }
    else{
        $pacote = socket_read($IsConexaoAceita, intval($GLOBALS['LIMITE_MAXIMO_MENSAGEM']));
        if($pacote === FALSE)
        {
            escreveNoLog("Erro ao receber o pacote");
        }
        else
        {
            print("pacote: " . $pacote);
            escreveNoLog("Pacote recebido");
        }
        escreveNoLog("Mensagem {" . $pacote ."} recebida da camada superior");
        do {
            $respostaCamadaFisica = comunicacaoComOServidorCamadaFisica($MAC_from_IP, $pacote);
            escreveNoLog("Reenviando quadro");
        }while($respostaCamadaFisica == -1);
        socket_write($IsConexaoAceita, $respostaCamadaFisica, strlen ($respostaCamadaFisica));
    }
}while ($IsConexaoAceita != FALSE);
socket_close($IsConexaoAceita);
escreveNoLog("Conexão encerrada");
