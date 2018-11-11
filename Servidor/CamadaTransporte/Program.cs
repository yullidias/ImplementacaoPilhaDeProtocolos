using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading;
using System.Threading.Tasks;

namespace Server
{
    internal class Program
    {
        public enum bitControle { URG, ACK, EOL, RST, SYN, FIN};

        public static string getControle(bool ack, bool syn, bool fin)
        {
            string controle = "0"; //URG, ACK, EOL, RST, SYN, FIN
            if(ack) controle += "1"; else controle += "0";
            controle += "00";                
            if(syn) controle += "1"; else controle += "0";
            if(fin) controle += "1"; else controle += "0";
            return controle;
        }
        
        public static byte[] montaSegmentoTCP(int portaOrigem, string portaDestino, int seq, int ack,
                                    bool ackControl, bool synControl, bool finControl, int rwnd, 
                                    string dados)
        {
            Console.WriteLine("monta segmento");
            string s = "";
            string po = portaOrigem.ToString();            
            if(po.Length < 5) { while(po.Length < 5) { po = po.Insert(0, "0"); } };            
            s += po;
            string pd = portaDestino; 
            if(pd.Length < 5) while(pd.Length < 5) pd = pd.Insert(0, "0");           
            s += pd ;
            s += seq.ToString();
            s += ack.ToString();
            s += "0110"; //offset(4 bits) - número de palavras de 32 bits no cabeçalho
            s += "000000"; //reserved(6 bits) - valor fixo de 0            
            s += getControle(ackControl, synControl, finControl);
            string s_rwnd = rwnd.ToString();
            if(s_rwnd.Length < 2) while(s_rwnd.Length < 2) s_rwnd = s_rwnd.Insert(0, "0");           
            s += s_rwnd; //janela(16bits) - nº de octetos que o remetente do segmento está disposto a aceitar
            s += "1111000011110000"; //checksum
            s += "0000000000000000"; //Urgent Pointer
            s += "0000000000000000"; //options tamanho variável
            s += "0000000000000000"; //padding - garante o tamanho de 32 bits
            s += dados; //32 bits
            return Encoding.UTF8.GetBytes(s);
        }
        public static void decodificaSegmentoTCP(string pacote, out string portaOrigem, out string portaDestino, 
                                                    out string seq, out string ack, out string offset, out string reserved,
                                                    out string ackControl, out string synControl, out string finControl, out string rwnd)
        {
            int index = 0;
            Console.WriteLine("pacote: {0}", pacote);
            portaOrigem = pacote.Substring(0, 5); index += 5;            
            portaDestino = pacote.Substring(index, 5); index += 5;
            seq = pacote.Substring(index, 1); index += 1;
            ack = pacote.Substring(index, 1); index += 1;
            offset = pacote.Substring(index, 4); index += 4;//offset(4 bits) - número de palavras de 32 bits no cabeçalho            
            reserved = pacote.Substring(index, 6); index += 6; //reserved(6 bits) - valor fixo de 0            
            index += 1; //pula URG
            ackControl = pacote.Substring(index, 1); index += 1;
            index += 2; //pula EOL, RST
            synControl = pacote.Substring(index, 1); index += 1;
            finControl = pacote.Substring(index, 1); index += 1;
            rwnd = pacote.Substring(index, 2); index += 2; //janela(16bits) - nº de octetos que o remetente do segmento está disposto a aceitar            
            Console.WriteLine("PO {0} PD {1} SEQ {2} ACK {3} OFFSET {4} RESERVED {5} ACKC{6} SYNC{7} FINC{8} RWND{9} ",portaOrigem, portaDestino, seq, ack, offset, reserved, ackControl, synControl, finControl, rwnd);
        }

        public static void Main(string[] args)
        {
            bool fimConexaoTCP = false;
            string datagrama = null;  
            byte[] segmento;
            byte[] bytes = new Byte[1024];  
            int minhaPorta = 11000;
            string meuIP = "192.168.0.16";
            IPAddress ipAddress = IPAddress.Parse(meuIP); 
            IPEndPoint localEndPoint = new IPEndPoint(ipAddress, minhaPorta); 

            string portaOrigem, portaDestino, seq, ack, offset, reserved, ackControl, synControl, finControl, rwnd;
          
            Socket listener = new Socket(ipAddress.AddressFamily, SocketType.Stream, ProtocolType.Tcp);  
         
            listener.Bind(localEndPoint);  
            listener.Listen(10);  
  
            while (!fimConexaoTCP) {  
                Socket MeuSocket = listener.Accept();  
                datagrama = null;  
                segmento = null;
                MeuSocket.Receive(bytes, SocketFlags.None);  
                datagrama = Encoding.UTF8.GetString(bytes);  
                
                
                decodificaSegmentoTCP(datagrama, out portaOrigem, out portaDestino, out seq, out ack, out offset, out reserved, out ackControl, out synControl, out finControl, out rwnd);
                if(synControl == "1")
                    segmento = montaSegmentoTCP(minhaPorta, portaOrigem, 0, 1, true, true,  false, 10, "");
                else if(ackControl == "1")
                {
                    segmento = montaSegmentoTCP(minhaPorta, portaOrigem, 0, 1, false, false,  true, 10, "");
                    fimConexaoTCP = true;
                }
                else
                {
                    segmento = montaSegmentoTCP(minhaPorta, portaOrigem, 0, 1, false, false,  false, 10, "Sem conexao");
                }
                Console.WriteLine( "Text received : {0}", datagrama);  
                byte[] resposta = segmento;
                Console.WriteLine("Resposta {0}", Encoding.UTF8.GetString(resposta));
                MeuSocket.Send(resposta); //responde com o que recebeu  
                MeuSocket.Shutdown(SocketShutdown.Both);  
            }  
            Console.WriteLine("Conexão UDP");
        }
    }  
}