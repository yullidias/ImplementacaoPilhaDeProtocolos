require 'socket'
require_relative 'functions'

include Socket::Constants

localhost = "127.0.0.1"
port_address = 8090

socket_cliente_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
time_stamp("Camada Aplicacao (CLIENTE): Criado socket com a camada inferior ", "log.txt")

sockaddr = Socket.pack_sockaddr_in( port_address, localhost )
socket_cliente_aplicacao.connect( sockaddr )
time_stamp("Camada Aplicacao (CLIENTE): Socket conectado com host local", "log.txt")

socket_cliente_aplicacao.puts "http://www.google.com/ HTTP/1.1\n\n"
time_stamp("Camada Aplicacao (CLIENTE): Enviada mensagem para camada inferior", "log.txt")

 data = socket_cliente_aplicacao.recvfrom( 10000 )[0].chomp
 puts "Mensagem recebida: '#{data}'"
 time_stamp("Camada Aplicacao (CLIENTE): recebida mensagem de da camadad inferior ", "log.txt")

socket_cliente_aplicacao.close
time_stamp("Camada Aplicacao (CLIENTE): Encerrando conexao " , "log.txt")
puts 'conexão encerrada com sucesso'

