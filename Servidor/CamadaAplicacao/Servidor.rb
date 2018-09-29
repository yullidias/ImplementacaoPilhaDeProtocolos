require 'socket'
require_relative 'functions'

include Socket::Constants

localhost = "127.0.1.1"
port_address = 3000

socket_servidor_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
time_stamp("Camada Aplicacao: Criado socket com a camada inferior ", "../../log.txt")

sockaddr = Socket.pack_sockaddr_in( port_address, localhost )
socket_servidor_aplicacao.bind( sockaddr )
time_stamp("Camada Aplicacao: Bind do socket com a camada inferior ", "../../log.txt")

socket_servidor_aplicacao.listen( 1 )
time_stamp("Camada Aplicacao: Servidor escutando...", "../../log.txt")

client, client_addrinfo = socket_servidor_aplicacao.accept
# puts client_addrinfo

write_to_file("ip.txt", client_addrinfo)

data = client.recvfrom( 10000 )[0].chomp
puts "I only received 20 bytes '#{data}'"
sleep 1

socket_servidor_aplicacao.close
time_stamp("Camada Aplicacao: Encerrando conexao " , "../../log.txt")
