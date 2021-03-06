require 'socket'
#require 'resolv'
require_relative '../../functions'
include Socket::Constants

ArgumentoslinhaDeComando = ARGV
if ArgumentoslinhaDeComando.length != 1
  puts "Use: ruby2.1 Servidor.rb IpServidorDNS"
  exit(-1)
end

maquina = "Servidor"

meuIp = ArgumentoslinhaDeComando[0]
port_address = 8070

socket_servidor_aplicacao = Socket.new( AF_INET, SOCK_STREAM, 0 )
time_stamp(maquina, "Criado socket com a camada inferior", "log.txt")

sockaddr = Socket.pack_sockaddr_in( port_address, meuIp )
socket_servidor_aplicacao.bind( sockaddr )
time_stamp(maquina, "Bind do socket com a camada inferior", "log.txt")

socket_servidor_aplicacao.listen( 5 )
time_stamp(maquina, "Servidor escutando...", "log.txt")

loop do
  client, client_addrinfo = socket_servidor_aplicacao.accept
  mensagem2 = client.recvfrom( 10000 )[0].chomp #mudanca da variavel data por mensagem2
  data = leituraDaQuestao(mensagem2) #add
  if data
    if (data =~ /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/)
      dominio = descobreIP(data)
      #dominio = "#{Resolv.getname (data)}"
      if(dominio == nil )
        dominio = ' '
      end
      resp = "#{montagemAnswer(data,dominio)}" #add
      
      if(dominio != -1)
        time_stamp(maquina, "O IP " + data + " pertence ao dominio " + dominio, "log.txt")
        client.puts resp #alterado de dominio para resp
      else
        time_stamp(maquina, "IP " + data + " não cadastrado", "log.txt")
        client.puts "IP " + data + " nao cadastrado"
      end
    else
      ip = "#{retornaIP(data)}"
      #ip = "#{Resolv.getaddresses (data)}"
      if(ip == nil )
        ip = ' '
      end
      resp1 = montagemAnswer(data,ip) #add
      if(ip != -1)
        time_stamp(maquina, "O dominio " + data + " pertence ao IP " + ip, "log.txt")
        client.puts resp1 #alteraddo de ip para resp1
      else
        time_stamp(maquina, "Dominio " + data + " não cadastado", "log.txt")
        client.puts "Dominio " + data + " nao cadastado"
      end
    end
  end
end
socket_servidor_aplicacao.close
time_stamp(maquina, "Encerrando conexao" , "log.txt")
