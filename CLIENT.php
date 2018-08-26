/*Deverá ser usado o TCP em sua implementação com um código cliente-servidor para fazer a transferência entre os dois hosts. O Quadro 
Ethernet a ser enviado deverá estar dentro de um arquivo txt, cujo conteúdo serão os bits que o formam seguindo a definição da RFC
(https://tools.ietf.org/html/rfc895). Neste caso teremos duas PDUs a serem apresentadas por esta camada, a PDU original, proveniente da 
camada superior e a PDU convertida para bits. Não será feita verificação de colisão, apenas a entrega do quadro ao host de destino. Camada 
física recebe da camada superior a mensagem a ser trocada e o endereço (IP) do destinatário. Assim, deverá descobrir o MAC Address para 
preencher o quadro com esta informação, para isso, fará uso do protocolo ARP (ou comando ARP na linha de comando). Deverá ser 
implementada a probabilidade de uma colisão, ou seja, a cada envio de PDU de um lado para outro, deverá ser gerado um número aleatório 
que, se dentro de uma faixa de valores, considera-se que houve colisão para se esperar um tempo aleatório e depois reenviar o quadro.
Destinatário responde com o valor em bytes. Remetente verifica se há colisão (probabilidade). Se sim, aguarda tempo aleatório, senão
envia.
Remetente envia quadro (fragmentação, se necessária, será realizada posteriormente pela camada de rede. Destinatário recebe quadro e
encaminha para a camada superior.
Toda atividade executada deve ser registrada num log geral, ou seja, comunicação com camada superior ou inferior, cálculo do MAC Address,
envio de solicitação de conexão, colisão, troca de valor de TMQ, envio de quadro, dentre outras. Cada registro deve ser precedido por um
timestamp (hora da máquina com data)*/

<?php
//Definicao da porta e do endereco
$endereco_server = '192.168.1.53';
$porta_server = 10000;

//Criar socket
$socket_cliente = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
/*AF_INET é um parametro domain IPv4 baseado nos protocolos de Internet. TCP é protocolo comum dessa família de protocolos.*/
/* SOCK_STREAM éFornece sequencial, seguro, e em ambos os sentidos, conexões baseadas em "byte streams". Dados "out-of-band" do
mecanismo de transmissão devem ser suportados. O protocolo TCP é baseado neste tipo de socket*/

//Verificar se a criacao do socket foi ok
if ($socket_cliente === false){
  echo "/n------\nErro na criacao do socket: ".socket_strerror(socket_last_error())."\n------\n";
  //chama a função socket_strerror() e pega o código de erro com a função socket_last_error().
  //Retorna uma string descrevendo o erro.
}
else{
	echo "Socket criado com sucesso!\n"; //Exibe uma string avisando que a criacao ocorreu bem
}

$contador=0;
//Ocorrerao apenas 5 tentativas de conexao com o server
while($contador<=5){
	$contador=$contador+1;
	//Conexao com o servidor - estabelecer uma conexão TCP entre cliente e servidor
  echo "Tentando conectar ao '$endereco_server' pela porta '$porta_server' ... \n";
  $conexao = socket_connect (resource $socket_cliente , string $endereco_server , int $porta_server);
  if ($conexao === false){
		if($contador>5){
			echo "/n------\nErro no estabelecimento da conexao com o socket: ".socket_strerror(socket_last_error())."\n------\n";
			//chama a função socket_strerror() e pega o código de erro com a função socket_last_error().
			//Retorna uma string descrevendo o erro.
		}
		else{
			echo "Tentando novamente...\n";
		}
  }
  else{
  	echo "Conexao estabelecida com sucesso!\n"; //Exibe uma string avisando que a conexao ocorreu bem
		break;
	}
}
//Atribui um nome ao socket
socket_bind ($socket_cliente , $endereco_server, $porta_server);
//Abre escuta para uma conexão no socket
socket_listen ($socket_cliente, SOMAXCONN); //SOMAXCONN - valor máximo passado para o parâmetro backlog
//Após o socket socket ter sido criado, passar um nome, e dizer para listar conexões, essa função irá aceitar conexões neste socket.
//Uma vez que uma conexão com sucesso é feita, um novo "resource" do socket é retornado, que deve ser usado para comunicação. 
socket_accept ( $socket_cliente);
//Camada física recebe da camada superior a mensagem a ser trocada e o endereço (IP) do destinatário.

//Probabilidade de colisão durante envio dos pacotes
//Encerra a conexão
socket_close($socket_cliente);
echo "Conexao encerrada!\n";
?>

