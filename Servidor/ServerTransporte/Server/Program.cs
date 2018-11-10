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

          TcpListener servidorTCP = null;
          UdpClient   servidorUDP = null;
          int         porta      = 59567;
    
          Console.WriteLine(string.Format("Iniciando conexão do servidor TCP e UDP na porta {0}...", porta));
    
          try
          {
            // Carrega um servidor UDP e TCP em cada thread.
            servidorUDP = new UdpClient(porta);
            servidorTCP = new TcpListener(IPAddress.Any, porta);
    
            var udpThread = new Thread(new ParameterizedThreadStart(UDPServerProc));
            udpThread.IsBackground = true;
            udpThread.Name = "Thread do servidor UDP";
            udpThread.Start(servidorUDP);
    
            var tcpThread = new Thread(new ParameterizedThreadStart(TCPServerProc));
            tcpThread.IsBackground = true;
            tcpThread.Name = "Thread do servidor TCP";
            tcpThread.Start(servidorTCP);
    
            Console.WriteLine("Pressionar <ENTER> para finalizar os servidores.");
            Console.ReadLine();
          }
          catch (Exception ex)
          {
            Console.WriteLine("ERROR: -> " + ex);
          }
          finally
          {
            if (servidorUDP != null)
              servidorUDP.Close();
    
            if (servidorTCP != null)
              servidorTCP.Stop();
          }
    
          Console.WriteLine("Pressionar <ENTER> para sair.");
          Console.ReadLine();
        }
    
        private static void UDPServerProc(object arg)
        {
          Console.WriteLine("Thread do servidor UDP inicializada.");
    
          try
          {
            // Recebe a instância do servidor UDP como argumento para ser executado na thread.
            UdpClient server = (UdpClient) arg;
            IPEndPoint remoteEP;
            byte[] buffer;
    
            // Thread fica esperando pelos dados enviados por algum cliente conectado.
            // Os bytes que chegam são impressos no console.
            for(;;)
            {
              remoteEP = null;
              buffer   = server.Receive(ref remoteEP);
    
              if (buffer != null && buffer.Length > 0)
              {
                // TODO: Aqui temos que entregar os dados para a camada de aplicação.
                Console.WriteLine("UDP: " + Encoding.ASCII.GetString(buffer));
              }
            }
          }
          catch (SocketException ex)
          {
            if(ex.ErrorCode != 10004) // unexpected
              Console.WriteLine("Erro no UDPServerProc: " + ex);
          }
          catch (Exception ex)
          {
            Console.WriteLine("Erro no UDPServerProc: " + ex);
          }
    
          Console.WriteLine("Thread do servidor UDP finalizada.");
        }
    
        private static void TCPServerProc(object arg)
        {
          Console.WriteLine("Thread do servidor TCP inicializada.");
    
          try
          {
            // Recebe a instância do servidor TCP como argumento e chama o método start() para inicializar.
            TcpListener server = (TcpListener)arg;
            byte[]      buffer = new byte[2048];
            int         count; 
    
            server.Start();
    
            // Thread fica esperando pelos dados enviados por algum cliente conectado.
            // Os bytes que chegam são impressos no console.
            for(;;)
            {
              TcpClient client = server.AcceptTcpClient();
    
              using (var stream = client.GetStream())
              {
                while ((count = stream.Read(buffer, 0, buffer.Length)) != 0)
                {
                  // TODO: Aqui temos que entregar os dados para a camada de aplicação.
                  Console.WriteLine("TCP: " + Encoding.ASCII.GetString(buffer, 0, count));
                }
              }
              client.Close();
            }
          }
          catch (SocketException ex)
          {
            if (ex.ErrorCode != 10004) // unexpected
              Console.WriteLine("Erro no TCPServerProc: " + ex);
          }
          catch (Exception ex)
          {
            Console.WriteLine("Erro no TCPServerProc: " + ex);
          }
    
          Console.WriteLine("Thread do servidor TCP finalizada.");
        }
    }
}