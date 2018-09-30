require 'socket'
require_relative 'functions'

include Socket::Constants

localhost = "127.0.1.1"
port_address = 7000

socket_servidor_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
time_stamp("Camada Aplicacao (SERVIDOR): Criado socket com a camada inferior ", "log.txt")

sockaddr = Socket.pack_sockaddr_in( port_address, localhost )
socket_servidor_aplicacao.bind( sockaddr )
time_stamp("Camada Aplicacao (SERVIDOR): Bind do socket com a camada inferior ", "log.txt")

socket_servidor_aplicacao.listen( 5 )
time_stamp("Camada Aplicacao (SERVIDOR): Servidor escutando...", "log.txt")

loop do
  client, client_addrinfo = socket_servidor_aplicacao.accept

  write_to_file("ip.txt", client_addrinfo)

  data = client.recvfrom( 10000 )[0].chomp
  if data
    puts "Mensagem recebida: '#{data}'"
    time_stamp("Camada Aplicacao (SERVIDOR): recebida mensagem da camada inferior ", "log.txt")

    sleep 1
    # return "200"
  else
    sleep 1
    # return "400"
  end

end

socket_servidor_aplicacao.close
time_stamp("Camada Aplicacao (SERVIDOR): Encerrando conexao " , "log.txt")
