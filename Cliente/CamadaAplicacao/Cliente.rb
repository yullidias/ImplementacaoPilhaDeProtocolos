require 'socket'
require_relative 'functions'

include Socket::Constants

localhost = "127.0.1.1"
port_address = 3000

socket_cliente_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
sockaddr = Socket.pack_sockaddr_in( port_address, localhost )
socket_cliente_aplicacao.connect( sockaddr )
socket_cliente_aplicacao.puts "Testando"
socket_cliente_aplicacao.close