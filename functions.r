# Funções compartilhadas

# Convert decimal to binary
decToBinary <- function(n) {
  if(n > 1) {
    decToBinary(as.integer(n/2))
  }
  cat(n %% 2)
}

#Convert IP to binary
ipToBinary <- function(ip) {
  r <- strsplit(ip,split="\\.")[[1]]
  bin <- paste(decToBinary(strtoi(r[1], base = 0L)),decToBinary(strtoi(r[2], base = 0L)),
               decToBinary(strtoi(r[3], base = 0L)),decToBinary(strtoi(r[4], base = 0L)),
               collapse=NULL)
}

# Transform to RFC
colocaNaRFC <- function(mensagem, IPd, IPo)
{
  #Coloca no padrao RFC 791 Protocolo IP
  nCarac <- stringr::str_length(msg) #conta quantos caracteres tem na mensagem
  totalBytes = nCarac + 20 + 20 #header fixo em 20 bytes
  totalBits <- totalBytes*8
  x <- intToBits(totalBits)
  x <- rev(x)
  x <- paste(as.integer(x), collapse = "")
  c<-str_split_fixed(x,"",17)
  VersionV <- "0100"
  IHL <- "1111"
  TypeOfService <-"00000000"
  TotalLength <- c[17]
  print(TotalLength)
  ID <- "0000000000000001"
  FlagsF <- "010"
  FragmentO <- "0000000000000"
  TTL <- "00000011"
  Protocolo <- "000000110"
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

  return(mensagemNaRFC)
}
