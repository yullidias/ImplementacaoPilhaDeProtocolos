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
        public static void Main(string[] args)
        {
            string datagrama = null;  
            byte[] bytes = new Byte[1024];  
            int minhaPorta = 11000;
            string meuIP = "192.168.0.16";
            IPAddress ipAddress = IPAddress.Parse(meuIP); 
            IPEndPoint localEndPoint = new IPEndPoint(ipAddress, minhaPorta);  
          
            Socket listener = new Socket(ipAddress.AddressFamily, SocketType.Stream, ProtocolType.Tcp );  
         
            listener.Bind(localEndPoint);  
            listener.Listen(10);  
  
            while (true) {  
                Socket MeuSocket = listener.Accept();  
                datagrama = null;  
  
                // An incoming connection needs to be processed.  
                while (true) {  
                    int bytesRec = MeuSocket.Receive(bytes);  
                    datagrama += Encoding.ASCII.GetString(bytes,0,bytesRec);  
                    if (datagrama.IndexOf("<EOF>") > -1) {  
                        break;  
                    }  
                }  
                // Show the data on the console.  
                Console.WriteLine( "Text received : {0}", datagrama);  
  
                // Echo the data back to the client.  
                byte[] msg = Encoding.ASCII.GetBytes(datagrama);  
  
                MeuSocket.Send(msg);  
                MeuSocket.Shutdown(SocketShutdown.Both);  
                //MeuSocket.close();      
            }  
            Console.WriteLine("\nPress ENTER to continue...");  
        }
    }  
}