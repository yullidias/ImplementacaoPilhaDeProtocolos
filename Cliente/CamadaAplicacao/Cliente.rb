require 'socket'
require_relative '../../functions'
include Socket::Constants

ArgumentoslinhaDeComando = ARGV
if ArgumentoslinhaDeComando.length != 2
  puts "Use: ruby2.1 Cliente.rb IpServidorDNS MensagemParaOServidorDNS"
  exit(-1)
end

SERVIDOR = ArgumentoslinhaDeComando[0]
PORTA_SERVIDOR = 8090
MENSAGEM = ArgumentoslinhaDeComando[1]
MAQUINA = "Cliente"

socket_cliente_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
time_stamp(MAQUINA,"Criado socket com a camada inferior", "log.txt")

sockaddr = Socket.pack_sockaddr_in( PORTA_SERVIDOR, SERVIDOR )
socket_cliente_aplicacao.connect( sockaddr )
time_stamp(MAQUINA, "Socket conectado com host local", "log.txt")

colocaNaMensagem = montagemRequisicao(MENSAGEM) #coloca no padrao da rfc1035

socket_cliente_aplicacao.puts colocaNaMensagem #envia padrao para socket ao inves da MENSAGEM
time_stamp(MAQUINA, "Enviada mensagem para camada inferior", "log.txt")

 mensagem1 = socket_cliente_aplicacao.recvfrom( 10000 )[0].chomp #alteracao da variavel data por mensagem 1
 data = leituraDaResposta(mensagem1) #add
 puts "Resposta DNS: '#{data}' \n\n"
 time_stamp(MAQUINA, "Resposta DNS {" + data + "}", "log.txt")

socket_cliente_aplicacao.close
time_stamp(MAQUINA, "Conexao encerrada com sucesso" , "log.txt")


