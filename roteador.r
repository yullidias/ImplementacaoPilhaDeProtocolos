source("../../functions.r")

pegarIPDoDatagrama <- function(msgDatagrama){
  #Pega IP do datagrama
  msgQuebra <- strsplit(msgDatagrama," ")[[1]]
  msgQuebra[5]
  print(msgQuebra[5])
  return <- msgQuebra[5]
}

server <- function(){
  while(TRUE){
    writeLines("Listening...")
    con <- socketConnection(host="localhost", port = 4000, blocking=TRUE,
                            server=TRUE, open="r+")
    datagram <- readLines(con, 1)
    ip <- pegarIPDoDatagrama(datagram)

    # abrir um arquivo txt

    writeLines(response, con)
    close(con)
  }
}

server()
