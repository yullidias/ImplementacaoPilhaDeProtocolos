require 'socket'
require_relative '../../functions'
include Socket::Constants

ArgumentoslinhaDeComando = ARGV
if ArgumentoslinhaDeComando.length != 2
  puts "Use: ruby2.1 Cliente.rb IpServidor PortaServidor"
  exit(-1)
end

SERVIDOR = ArgumentoslinhaDeComando[0]
PORTA_SERVIDOR = ArgumentoslinhaDeComando[1]
MAQUINA = "Cliente"

socket_cliente_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
time_stamp(MAQUINA,"Criado socket com a camada inferior", "log.txt")

sockaddr = Socket.pack_sockaddr_in( PORTA_SERVIDOR, SERVIDOR )
socket_cliente_aplicacao.connect( sockaddr )
time_stamp(MAQUINA, "Socket conectado com host local", "log.txt")

socket_cliente_aplicacao.puts "http://www.google.com/ HTTP/1.1"
time_stamp(MAQUINA, "Enviada mensagem para camada inferior", "log.txt")

 data = socket_cliente_aplicacao.recvfrom( 10000 )[0].chomp
 puts "Mensagem recebida: '#{data}'"
 time_stamp(MAQUINA, "Recebida mensagem de da camadad inferior ", "log.txt")

socket_cliente_aplicacao.close
time_stamp(MAQUINA, "Encerrando conexao " , "log.txt")
puts 'conexão encerrada com sucesso'

