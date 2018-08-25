/*Deverá ser usado o TCP em sua implementação com um código cliente-servidor para fazer a transferência entre os dois hosts. O Quadro 
Ethernet a ser enviado deverá estar dentro de um arquivo txt, cujo conteúdo serão os bits que o formam seguindo a definição da RFC
(https://tools.ietf.org/html/rfc895). Neste caso teremos duas PDUs a serem apresentadas por esta camada, a PDU original, proveniente da 
camada superior e a PDU convertida para bits. Não será feita verificação de colisão, apenas a entrega do quadro ao host de destino. Camada 
física recebe da camada superior a mensagem a ser trocada e o endereço (IP) do destinatário. Assim, deverá descobrir o MAC Address para 
preencher o quadro com esta informação, para isso, fará uso do protocolo ARP (ou comando ARP na linha de comando). Deverá ser implementada
a probabilidade de uma colisão, ou seja, a cada envio de PDU de um lado para outro, deverá ser gerado um número aleatório que, se dentro de
uma faixa de valores, considera-se que houve colisão para se esperar um tempo aleatório e depois reenviar o quadro.
Destinatário responde com o valor em bytes. Remetente verifica se há colisão (probabilidade). Se sim, aguarda tempo aleatório, senão envia.
Remetente envia quadro (fragmentação, se necessária, será realizada posteriormente pela camada de rede. Destinatário recebe quadro e
encaminha para a camada superior*/

<?php
//Criar socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
/*AF_INET é um parametro domain IPv4 baseado nos protocolos de Internet. TCP é protocolo comum dessa família de protocolos.*/
/* SOCK_STREAM éFornece sequencial, seguro, e em ambos os sentidos, conexões baseadas em "byte streams". Dados "out-of-band" do
mecanismo de transmissão devem ser suportados. O protocolo TCP é baseado neste tipo de socket*/

//Verificar se a criacao do socket foi ok
if ($socket === false){
  echo "/n------\nErro na criacao do socket: ".socket_strerror(socket_last_error())."\n------\n";
  //chama a função socket_strerror() e pega o código de erro com a função socket_last_error().
  //Retorna uma string descrevendo o erro.
}
else{
  echo "Socket criado com sucesso!\n"; //Exibe uma string avisando que a criacao ocorreu bem
}
//Conexao com o servidor

//Probabilidade de colisão durante envio dos pacotes

//O programa deve estar continuamente em execucao: receber a quantidade de bits e enviar em pacotes
while(true){
  
}
//Encerra a conexão
socket_close($socket);
echo "Conexao encerrada!\n";
?>

