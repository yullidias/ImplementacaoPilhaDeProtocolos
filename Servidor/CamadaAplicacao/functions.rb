
def time_stamp(str, file_name)
  now = Time.new.strftime("%d-%m-%y %H:%M")
  log = File.open("../../" + file_name , "a")
  log.puts(str + now.to_str + "\n")
  log.close
end

def write_to_file(file_name, content)
  fd = File.open("../../" + file_name, "w")
  fd.write(content)
  fd.close
end
