require 'resolv'
class Teste
  puts Resolv.getaddresses 'vm-opencms-01.ditic.sgi.cefetmg.br'
end