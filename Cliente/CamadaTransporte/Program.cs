using System;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading.Tasks;

namespace Cliente
{
    internal class Program
    {
        
        static void Main(string[] args)
        {
            string ipServidor = "192.168.0.16";
            int portaServidor = 11000;
            byte[] respostaServidor = new byte[1024]; 
            // Establish the remote endpoint for the socket.  
            // This example uses port 11000 on the local computer.  
            IPAddress ipAddress = IPAddress.Parse(ipServidor);
            IPEndPoint remoteEP = new IPEndPoint(ipAddress, portaServidor);  
  
            // Create a TCP/IP  socket.  
            Socket sender = new Socket(ipAddress.AddressFamily, SocketType.Stream, ProtocolType.Tcp );  
  
            sender.Connect(remoteEP);  

            Console.WriteLine("Socket connected to {0}",  
                sender.RemoteEndPoint.ToString());  

            byte[] msg = Encoding.ASCII.GetBytes("This is a test<EOF>");  

            sender.Send(msg);  

            int bytesRecebidos = sender.Receive(respostaServidor);  
            Console.WriteLine("Echoed test = {0}",  
                Encoding.ASCII.GetString(respostaServidor,0,bytesRecebidos));  

            // Release the socket.  
            sender.Shutdown(SocketShutdown.Both);              
        }
    }
}