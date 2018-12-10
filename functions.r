# Funções compartilhadas

# Convert decimal to binary
decToBinary <- function(n) {
  s<-strsplit(n,"\\.")[[1]]
  s[1]
  s[2]
  s[3]
  s[4]
  s1 <- as.numeric(s[1])
  s2 <- as.numeric(s[2])
  s3 <- as.numeric(s[3])
  s4 <- as.numeric(s[4])
  x1 <- intToBits(s1)
  x2 <- intToBits(s2)
  x3 <- intToBits(s3)
  x4 <- intToBits(s4)
  x1 <- rev(x1)
  x2 <- rev(x2)
  x3 <- rev(x3)
  x4 <- rev(x4)
  x1 <- paste(as.integer(x1), collapse = "")
  x2 <- paste(as.integer(x2), collapse = "")
  x3 <- paste(as.integer(x3), collapse = "")
  x4 <- paste(as.integer(x4), collapse = "")
  c1 <- stringr::str_split_fixed(x1,"",25)
  c2 <- stringr::str_split_fixed(x2,"",25)
  c3 <- stringr::str_split_fixed(x3,"",25)
  c4 <- stringr::str_split_fixed(x4,"",25)
  # print(c[25])
  resultado <- paste(c1[25],c2[25],c3[25],c4[25], sep = ".")

  return(resultado)
}

#Convert IP to binary
ipToBinary <- function(ip) {
  r <- strsplit(ip,split="\\.")[[1]]
  bin <- paste(decToBinary(strtoi(r[1], base = 0L)),decToBinary(strtoi(r[2], base = 0L)),
               decToBinary(strtoi(r[3], base = 0L)),decToBinary(strtoi(r[4], base = 0L)),
               collapse=NULL)
}

# Transform to RFC
colocaNoPadraoIP <- function(mensagem, IPd, IPo)
{
  #Coloca no padrao RFC 791 Protocolo IP
  nCarac <- stringr::str_length(mensagem) #conta quantos caracteres tem na mensagem
  totalBytes = nCarac + 20 + 20 #header fixo em 20 bytes
  totalBits <- totalBytes*8
  x <- intToBits(totalBits)
  x <- rev(x)
  x <- paste(as.integer(x), collapse = "")
  c<-stringr::str_split_fixed(x,"",17)
  VersionV <- "0100"
  IHL <- "1111"
  TypeOfService <-"00000000"
  TotalLength <- c[17]

  ID <- "0000000000000001"
  FlagsF <- "010"
  FragmentO <- "0000000000000"
  TTL <- "00000011"
  Protocolo <- "00000110"
  HeaderSoma <- "0000000000000000"
  IPOrigem <- IPo
  IPDestino <- IPd
  Opcao <- "00000000"
  Padding <- "000000000000000000000000"
  msg <- mensagem
  msg1 <- paste(VersionV,IHL,TypeOfService,TotalLength,sep="", collapse=NULL)
  msg2 <- paste(ID,FlagsF,FragmentO,sep="", collapse=NULL)
  msg3 <- paste(TTL,Protocolo,HeaderSoma,sep="", collapse=NULL)
  msg4 <- paste(IPOrigem,sep="", collapse=NULL)
  msg5 <- paste(IPDestino,sep="", collapse=NULL)
  msg6 <- paste(Opcao,Padding,sep="", collapse=NULL)
  msg7 <- paste(msg,sep="", collapse=NULL)
  mensagemNaRFC <- paste(msg1,msg2,msg3,msg4,msg5,msg6,msg7, sep = " ")
  # print(mensagemNaRFC)
  return <- mensagemNaRFC
}

# Pega a mensagem
pegarMsgDoDatagrama <- function(msgDatagrama){
  #Pega mensagem do datagrama
  msgQuebra <- strsplit(msgDatagrama," ")[[1]]
  msgQuebra[7]
  #print(msgQuebra[7])
  return(msgQuebra[7])
}
