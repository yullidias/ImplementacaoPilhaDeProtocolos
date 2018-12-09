client <- function(){
  while(TRUE){
    con <- socketConnection(host="localhost", port = 6011, blocking=TRUE,
                            server=FALSE, open="r+")
    f <- file("stdin")
    open(f)
    print("Enter text to be upper-cased, q to quit")
    sendme <- readLines(f, n=1)
    if(tolower(sendme)=="q"){
      break
    }
    write_resp <- writeLines(sendme, con)
    server_resp <- readLines(con, 1)
    print(paste("Your upper cased text:  ", server_resp))
    close(con)
  }
}
client()


############################
library(stringr)
msg <- "sao paulo"


colocaNaRFC <- function(mensagem, IPd, IPo)
{
  #Coloca no padrao RFC 791 Protocolo IP
  nCarac <- stringr::str_length(msg) #conta quantos caracteres tem na mensagem
  totalBytes = nCarac + 20 + 20 #header fixo em 20 bytes
  totalBits <- totalBytes*8
  x <- intToBits(totalBits)
  x <- rev(x)
  x <- paste(as.integer(x), collapse = "")
  #print(x)
  c<-str_split_fixed(x,"",17)
  #print(c[17])
  VersionV <- "0100"
  IHL <- "1111"
  TypeOfService <-"00000000"
  TotalLength <- c[17]
  print(TotalLength)
  ID <- "00000000 00000001"
  FlagsF <- "010"
  FragmentO <- "0000000000000"
  TTL <- "00000011"
  Protocolo <- "000000110"
  HeaderSoma <- "0000000000000000"
  IPOrigem <- IPo
  IPDestino <- IPd
  Opcao <- "00000000"
  Padding <- "000000000000000000000000"
  cat('', sep="\n")
  msg <- mensagem
  mensagemRFC = cat(VersionV,IHL,TypeOfService,TotalLength, sep="\n")
  print(mensagemRFC)

}

colocaNaRFC("string da mensage", "IPD", "IPO")
