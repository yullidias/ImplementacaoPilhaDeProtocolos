
def time_stamp(maquina, mensagem, file_name)
  now = Time.new.strftime("%d %B %Y %H:%M:%S")
  log = File.open("../../" + file_name , "a")
  log.puts(now.to_str  + " [Aplicação: " + maquina + "] " + mensagem + "\n")
  log.close
end

def write_to_file(file_name, content)
  fd = File.open("../../" + file_name, "w")
  fd.write(content)
  fd.close
end

def descobreIP(enderecoIPperguntado)
  enderecoIP = enderecoIPperguntado
  arq = File.open("DNS.txt")
  while line = arq.gets
    #puts line
    resultBusca = line
    split = resultBusca.split
    #puts "#{split[0]}"
    if (split[1] == enderecoIP)
     # puts "Opa!Conheco esse endereco!\n"
      arq.close
      return split[0]
    end
  end
  arq.close
  return -1
end

def retornaIP(dominioPerguntado)
  #variaveis Globais
  $dominio = dominioPerguntado

  arq = File.open("dns.txt")
  while line = arq.gets
    puts line
    $resultBusca = line
    $split = $resultBusca.split
    puts "#{$split[1]}"
    if ($split[0] === $dominio)
      puts "Opa!Conheco esse endereco!\n"
      $enderecoIPdominio = $split[1]
      break
    elsif ($split[0] != $dominio)
      $enderecoIPdominio = "Desconhecido por mim\n"
    end
  end
  if($enderecoIPdominio == "Desconhecido por mim\n")
    puts "Nao conheco o endereco desse dominio! Desculpe!\n"
  end
  puts "#{$enderecoIPdominio}"
  arq.close
  return enderecoIPdominio
end

dominio = descobreIP("172.217.0.35")
puts dominio
