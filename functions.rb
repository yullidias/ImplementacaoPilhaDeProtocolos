
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
