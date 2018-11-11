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
            Console.WriteLine("monta segmento");
            string s = "";
            string po = portaOrigem.ToString();            
            if(po.Length < 5) { while(po.Length < 5) { po = po.Insert(0, "0"); } };            
            s += po;
            Console.WriteLine("po.len {0}", po.Length);
            Console.WriteLine("{0}", s);
            string pd = portaDestino.ToString(); 
            if(pd.Length < 5) while(pd.Length < 5) pd = pd.Insert(0, "0");           
            s += pd ;
            Console.WriteLine("{0}", s);
            s += seq.ToString();
            Console.WriteLine("{0}", s);
            s += ack.ToString();
            Console.WriteLine("{0}", s);
            s += "0110"; //offset(4 bits) - número de palavras de 32 bits no cabeçalho
            Console.WriteLine("{0}", s);
            s += "000000"; //reserved(6 bits) - valor fixo de 0            
            Console.WriteLine("{0}", s);
            s += getControle(ackControl, synControl, finControl);
            Console.WriteLine("{0}", s);
            string s_rwnd = rwnd.ToString();
            if(s_rwnd.Length < 2) while(s_rwnd.Length < 2) s_rwnd = s_rwnd.Insert(0, "0");           
            s += s_rwnd; //janela(16bits) - nº de octetos que o remetente do segmento está disposto a aceitar
            Console.WriteLine("{0}", s);
            s += "1111000011110000"; //checksum
            s += "0000000000000000"; //Urgent Pointer
            s += "0000000000000000"; //options tamanho variável
            s += "0000000000000000"; //padding - garante o tamanho de 32 bits
            s += dados; //32 bits
            Console.WriteLine("fim monta segmento");
            return Encoding.UTF8.GetBytes(s);
        }
        static void Main(string[] args)
        {
            string ipServidor = "192.168.0.16";
            int minhaPorta = Process.GetCurrentProcess().Id;
            int portaServidor = 11000;
            byte[] respostaServidor = new byte[2048];             
            IPAddress ipAddress = IPAddress.Parse(ipServidor);
            IPEndPoint remoteEP = new IPEndPoint(ipAddress, portaServidor);  
  
            Socket sender = new Socket(ipAddress.AddressFamily, SocketType.Stream, ProtocolType.Tcp );  
            sender.Connect(remoteEP);  

            Console.WriteLine("Socket connected to {0}",  
                sender.RemoteEndPoint.ToString());  

            
            byte[] menssagemRecebidaDaAplicacao = montaSegmentoTCP(minhaPorta, portaServidor, 0, 0, 
                                                                    false, true, false, 0, "");  
            Console.WriteLine(Encoding.ASCII.GetString(menssagemRecebidaDaAplicacao));
            sender.Send(menssagemRecebidaDaAplicacao);  

            int bytesRecebidos = sender.Receive(respostaServidor);
            Console.WriteLine("bytesRecebidos {1} size resposta servidor: {0}", respostaServidor.Length, bytesRecebidos);  
            Console.WriteLine("Echoed test = {0}",  
                Encoding.ASCII.GetString(respostaServidor,0,bytesRecebidos));  

            // Release the socket.  
            sender.Shutdown(SocketShutdown.Both);              
        }
    }
}