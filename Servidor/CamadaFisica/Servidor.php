<?php
$ARQUIVO_LOG = "../../log.txt";
$MEU_IP = "127.0.0.1";
$MINHA_PORTA = 8080;
$TAM_MAX_BYTES = '3000000';

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

function obterMenssagemDoQuadro($quadro){
    $preambulo = substr($quadro, 0, 4); //4 bits
    $sfd = substr($quadro, 4, 8); //8 bits
    $mac_org = substr($quadro, 12, 48);
    $mac_dest = substr($quadro, 60, 48);
    $tipo = substr($quadro, 108, 16); //16 bits
    $tam_dado = strlen($quadro) - 156; //tamanho total - cabeçalho - crc
    $data = substr($quadro, 124, $tam_dado);
    $data = binarioParaString($data); //converte o pacote para string
    $crc = substr($quadro, 124+$tam_dado, 32); //crc tem 32 bits
    return $data;
}
function timestamp()
{
    $now = getdate();
    $data = $now['mday'] . ' ' . $now['month'] . ' ' . $now['year'] . ' ' . $now['hours'] . ':' . $now['minutes'] . ':' . $now['seconds'] ." ";
    return $data;
}
function escreveNoLog($mensagem)
{
    file_put_contents ( $GLOBALS['ARQUIVO_LOG'], timestamp() ."[Física: Servidor] " . $mensagem . ". \n", FILE_APPEND | LOCK_EX); //lock_ex lock exclusivo

}

set_time_limit(0); //sem timeout
$socket = socket_create(AF_INET, SOCK_STREAM, 0);
if($socket === FALSE)
{
    escreveNoLog("Socket com a camada física não criado");
}
else
{
    escreveNoLog("Socket com a camada física criado");
}

if(socket_bind($socket, $GLOBALS['MEU_IP'], $GLOBALS['MINHA_PORTA']) === FALSE)
{
    escreveNoLog("Erro ao vincular nome para o socket");
}
else
{
    escreveNoLog("Vinculando um nome para o socket");
}

do
{
    $result = socket_listen($socket);
    if($result === false)
    {
        escreveNoLog("Errro ao ouvir conexão");
    }
    else
    {
        escreveNoLog("Ouvindo a conexão");
    }
    $spawn = socket_accept($socket);
    if($spawn === false){
        escreveNoLog("Conexão não aceita");
    }
    else{
        escreveNoLog("Conexão aceita");
    }
    $quadro = socket_read($spawn, intval($TAM_MAX_BYTES));
    if($quadro === FALSE)
    {
        escreveNoLog("Erro ao receber o quadro");
    }
    else
    {
        escreveNoLog("Quadro recebido");
        $quadro = trim($quadro);
        escreveNoLog("Mensagem {" .obterMenssagemDoQuadro($quadro) ."} recebida");
        socket_write($spawn, $quadro, strlen ($quadro));
    }

}while ($spawn != FALSE);

socket_close($spawn);
escreveNoLog("Conexão encerrada");
?>
