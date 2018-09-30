require 'socket'
require_relative '../../functions'

include Socket::Constants

maquina = "Servidor"

localhost = "127.0.0.1"
port_address = 8070

socket_servidor_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
time_stamp(maquina, "Criado socket com a camada inferior", "log.txt")

sockaddr = Socket.pack_sockaddr_in( port_address, localhost )
socket_servidor_aplicacao.bind( sockaddr )
time_stamp(maquina, "Bind do socket com a camada inferior", "log.txt")

socket_servidor_aplicacao.listen( 5 )
time_stamp(maquina, "Servidor escutando...", "log.txt")

loop do
  client, client_addrinfo = socket_servidor_aplicacao.accept

  write_to_file("ip.txt", client_addrinfo)

  data = client.recvfrom( 10000 )[0].chomp
  if data
    if (data =~ /[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}/)
      dominio = "www.facebook.com"
      time_stamp(maquina, "IP " + data + "pertence ao dominio " + dominio, "log.txt")
      client.puts dominio
    else
      ip = "198.168.0.20"
      time_stamp(maquina, "O dominio " + data + "pertence ao IP " + ip, "log.txt")
      client.puts ip
    end
  end
end
socket_servidor_aplicacao.close
time_stamp(maquina, "Encerrando conexao" , "log.txt")