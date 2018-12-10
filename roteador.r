source("../../functions.r")

pegarIPDoDatagrama <- function(msgDatagrama){
  #Pega IP do datagrama
  msgQuebra <- strsplit(msgDatagrama," ")[[1]]
  msgQuebra[5]
  print(msgQuebra[5])
  return <- msgQuebra[5]
}

confereTabelaRoteamento <- function(ip) {
  table <- read.table("tabela_roteamento.csv", header = TRUE, sep = ";")
  porta <- table[table$IP == ip,]
  return(porta[2])
}

roteador_parte2 <- function(){
  while(TRUE){
    writeLines("Listening...")
    con <- socketConnection(host="localhost", port = 4000, blocking=TRUE,
                            server=TRUE, open="r+")
    datagram <- readLines(con, 1)
    ip <- pegarIPDoDatagrama(datagram)
    porta <- confereTabelaRoteamento(ip)
    writeLines(porta, con)
    close(con)
  }
}

roteador_parte2()
