
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

def montaHeader
  #montaCabecalho
  #Os campos sao definidos de maneira fixada para facilitar a montagem
  # ID - identificador de 16 bits fixado pelo  cliente
  # 1 byte = 8 bits  - 1 char = 1 byte
  @ID = '0000000000000000'
  #Segunda Linha do Header
  @QR = '1' #1 bit - 0 query 1 response
  @OpCode = '0000' #4 bits - especifica o tipo de requisicao da mensagem. Gerado pelo Cliente. 0-standard query (QUERY) 1-inverse query (IQUERY)
  @AA = '0' #1 bit - Authoritative Answer
  @TC = '0' # TrunCation
  @RD = '0' #1 bit - 1 em questoes
  @RA = '1' #i bit
  @z = '000' #sempre
  @RCODE = '0000' #Response code - 4 bit - part of responses. 0-No error condition  1-Format error-server was unable to interpret the query.  2-Server failure-server was unable to process this query due to a problem with the name server.   3-Name Error - the domain name referenced in the query does not exist.    4-Not Implemented - The name server does not support the requested kind of query.   5-Refused - The name server refuses to perform the specified operation for  policy reasons.
  #Terceira Linha
  @QDcount  = '0000000000000001'#16 bit integer specifying the number of entries in the question section.
  # Quarta Linha
  @ANcount = '0000000000000001'#16 bit integer specifying the number of resource records in the answer section.
  # Quinta Linha
  @NScount = '0000000000000000' #16 bit integer specifying the number of name server resource records in the authority records section.
  # Sexta Linha
  @ARcount = '0000000000000000' #16 bit integer specifying the number of resource records in the additional records section.
  resposta = @ID+"\n"+@QR+@OpCode+@AA+@TC+@RD+@RA+@z+@RCODE+"\n"+@QDcount+"\n"+@ANcount+"\n"+@NScount+"\n"+@ARcount
  return resposta
end

def montaQuestion (data)
  @Qname = data
  @Qtype = '0000000000010000'#2 octet specifies the type of the query. A=1 host address PTR = 12 domain name pointer TXT= 16 text strigs
  @Qclass ='0000000000000001' #a two octet code that specifies the class of the query. IN = value 1
  resposta = @Qname+"\n"+@Qtype+"\n"+@Qclass
  #  puts resposta
  return resposta
end

def montaResposta (resposta)
  @nome = resposta #a domain name to which this resource record pertains.
  @tipo = '0000000000010000' #2 octets containing one of the RR type codes.
  @classe = '0000000000000001' #2 octets which specify the class of the data in the RDATA field.
  @ttl = '0010000011010000'# 32 bit integer that specifies the time interval (in seconds) that the resource record may be cached before it should be discarded.
  @rdlength = '0000000010101000'#16 bit integer that specifies the length in octets of the RDATA field.
  @Rdata =  'descricao do registro'#a variable length string of octets that describes the resource.
  resposta = @nome+"\n"+@tipo+"\n"+@classe+"\n"+@ttl+"\n"+@rdlength+"\n"+@Rdata
  return resposta
end

def montaAuthority ()
  @nome = 'escreva algo aqui' #a domain name to which this resource record pertains.
  @tipo = '0000000000010000' #2 octets containing one of the RR type codes.
  @classe = '0000000000000001' #2 octets which specify the class of the data in the RDATA field.
  @ttl = '0010000011010000'# 32 bit integer that specifies the time interval (in seconds) that the resource record may be cached before it should be discarded.
  @rdlength = '0000000010101000'#16 bit integer that specifies the length in octets of the RDATA field.
  @Rdata =  'descricao do registro'#a variable length string of octets that describes the resource.
  resposta = @nome+"\n"+@tipo+"\n"+@classe+"\n"+@ttl+"\n"+@rdlength+"\n"+@Rdata
  return resposta
end

def montagemAnswer(datan,resp)
  #montagem das coisas
  head = montaHeader()
  questao = montaQuestion(datan)
  answer = montaResposta(resp)
  autor = montaAuthority
  resposta = head+"\n"+questao+"\n"+answer+"\n"+autor
  return resposta
end

def montaHeaderRequisicao
  #montaCabecalho
  #Os campos sao definidos de maneira fixada para facilitar a montagem
  # ID - identificador de 16 bits fixado pelo  cliente
  # 1 byte = 8 bits  - 1 char = 1 byte
  @ID = '0000000000000000'
  #Segunda Linha do Header
  @QR = '0' #1 bit - 0 query 1 response
  @OpCode = '0000' #4 bits - especifica o tipo de requisicao da mensagem. Gerado pelo Cliente. 0-standard query (QUERY) 1-inverse query (IQUERY)
  @AA = '0' #1 bit - Authoritative Answer
  @TC = '0' # TrunCation
  @RD = '1' #1 bit - 1 em questoes
  @RA = '0' #i bit
  @z = '000' #sempre
  @RCODE = '0000' #Response code - 4 bit - part of responses. 0-No error condition  1-Format error-server was unable to interpret the query.  2-Server failure-server was unable to process this query due to a problem with the name server.   3-Name Error - the domain name referenced in the query does not exist.    4-Not Implemented - The name server does not support the requested kind of query.   5-Refused - The name server refuses to perform the specified operation for  policy reasons.
  #Terceira Linha
  @QDcount  = '0000000000000001'#16 bit integer specifying the number of entries in the question section.
  # Quarta Linha
  @ANcount = '0000000000000000'#16 bit integer specifying the number of resource records in the answer section.
  # Quinta Linha
  @NScount = '0000000000000000' #16 bit integer specifying the number of name server resource records in the authority records section.
  # Sexta Linha
  @ARcount = '0000000000000000' #16 bit integer specifying the number of resource records in the additional records section.
  resposta = @ID+"\n"+@QR+@OpCode+@AA+@TC+@RD+@RA+@z+@RCODE+"\n"+@QDcount+"\n"+@ANcount+"\n"+@NScount+"\n"+@ARcount
  return resposta
end

def montagemRequisicao(datan)
  head = montaHeaderRequisicao
  questao = montaQuestion(datan)
  autor = montaAuthority
  resposta = head+"\n"+questao+"\n"+autor
  return resposta
end

def leituraDaQuestao(msg)
  split = msg.split
  return "#{split[6]}"
end

def leituraDaResposta(msg)
  split = msg.split
  return "#{split[9]}"
end
