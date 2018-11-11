using System;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading.Tasks;
using System.Diagnostics;
using System.Collections.Generic;

namespace Cliente
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
        
        public static byte[] montaSegmentoTCP(int portaOrigem, int portaDestino, int seq, int ack,
                                    bool ackControl, bool synControl, bool finControl, int rwnd, 
                                    string dados)
        {
            string s = "";
            string po = portaOrigem.ToString();            
            if(po.Length < 5) { while(po.Length < 5) { po = po.Insert(0, "0"); } };            
            s += po;
            string pd = portaDestino.ToString(); 
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
            Console.WriteLine("{0}", s);
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
        public static byte[] enviaSegmentoEObtemResposta(IPAddress ipServidor, IPEndPoint remoteEP,
                                                        int portaOrigem, int portaDestino, int seq, int ack,
                                                        bool ackControl, bool synControl, bool finControl, int rwnd, 
                                                        string dados)
        {
            byte[] respostaServidor = new byte[1024];    
            Socket sender = new Socket(ipServidor.AddressFamily, SocketType.Stream, ProtocolType.Tcp );  
            sender.Connect(remoteEP);  

            Console.WriteLine("Socket connected to {0}",  
                sender.RemoteEndPoint.ToString());  

            
            byte[] menssagemRecebidaDaAplicacao = montaSegmentoTCP(portaOrigem, portaDestino, seq, ack, 
                                                                    ackControl, synControl, finControl, rwnd, dados);  
            Console.WriteLine(Encoding.ASCII.GetString(menssagemRecebidaDaAplicacao));
            sender.Send(menssagemRecebidaDaAplicacao);  

            int bytesRecebidos = sender.Receive(respostaServidor);
            Console.WriteLine("bytesRecebidos {1} size resposta servidor: {0}", respostaServidor.Length, bytesRecebidos);  

            // Release the socket.  
            sender.Shutdown(SocketShutdown.Both);              
            return respostaServidor;
        }
        static void Main(string[] args)
        {
            string ipServidor = "192.168.0.16";
            int minhaPorta = Process.GetCurrentProcess().Id;
            int portaServidor = 11000;
            int portaServidorUDP = 9999;
            byte[] respostaServidor = new byte[1024];    

            string portaOrigem, portaDestino, seq, ack, offset, reserved, ackControl, synControl, finControl, rwnd;
            
            IPAddress ipAddress = IPAddress.Parse(ipServidor);
            IPEndPoint remoteEP = new IPEndPoint(ipAddress, portaServidor);  
  
            respostaServidor = enviaSegmentoEObtemResposta(ipAddress, remoteEP, minhaPorta, portaServidor, 0, 0, 
                                                                    false, true, false, 00, "");  
                decodificaSegmentoTCP(Encoding.ASCII.GetString(respostaServidor), out portaOrigem, out portaDestino, out seq, out ack, out offset, out reserved, out ackControl, out synControl, out finControl, out rwnd);
                Console.WriteLine("ACK {0}, SYNC {1}", ackControl, synControl);
            if(ackControl == "1" || synControl == "1")
            {
                respostaServidor = enviaSegmentoEObtemResposta(ipAddress, remoteEP, minhaPorta, portaServidor, 0, 0, 
                                                                    true, false, false, 00, "");  
                decodificaSegmentoTCP(Encoding.ASCII.GetString(respostaServidor), out portaOrigem, out portaDestino, out seq, out ack, out offset, out reserved, out ackControl, out synControl, out finControl, out rwnd);
                Console.WriteLine("ACK {0}, SYNC {1}, FINC {2}", ackControl, synControl, finControl);
            }
            if(finControl == "1")
            {
                
                Socket s = new Socket(AddressFamily.InterNetwork, SocketType.Dgram, ProtocolType.Udp);  
        
                byte[] sendbuf = Encoding.ASCII.GetBytes("TESTE UDP");  
                IPEndPoint ep = new IPEndPoint(ipAddress, portaServidorUDP);  
        
                s.SendTo(sendbuf, ep);  
        
                Console.WriteLine("Message sent to UDP");    
         }
                 
        }
    }
}