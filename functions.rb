
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
  arq = File.open("DNS.txt", "r")
  while line = arq.gets
    resultBusca = line
    split = resultBusca.split
    if (split[1].strip === enderecoIP.strip)
      arq.close
      return split[0]
    end
  end
  arq.close
  return -1
end

def retornaIP(dominioPerguntado)
  dominio = dominioPerguntado
  arq = File.open("DNS.txt")
  while line = arq.gets
    resultBusca = line
    split = resultBusca.split
    if (split[0].strip === dominio.strip)
      arq.close
      return split[1]
    end
  end
  arq.close
  return -1
end
