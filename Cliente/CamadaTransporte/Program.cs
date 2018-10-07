using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading.Tasks;

namespace TCPUDPClient
{
    class Program
    {
        // Teste de envio de dados para o servidor TCP e UDP
        static void Main(string[] args)
        {
            UdpClient udpClient = null;
            TcpClient tcpClient = null;
            NetworkStream tcpStream = null;
            
            int porta = 59567;

            ConsoleKeyInfo key;
            bool executar = true;
            byte[] buffer;

            Console.WriteLine(string.Format("Inicializa uma conexão cliente com os servidor TCP e UDP inicializados na porta {0}...", porta));

            try
            {
                udpClient = new UdpClient();

                // Cria uma instância do cliente para o endpoint do servidor UDP em execução.
                udpClient.Connect(IPAddress.Loopback, porta);

                // Cria uma instância do cliente para o endpoint do servidor TCP em execução.
                tcpClient = new TcpClient();
                tcpClient.Connect(IPAddress.Loopback, porta);

                while (executar)
                {
                    Console.WriteLine("Pressione 'T' para testar o envido de dados TCP, 'U' para testar o envio de dados via UDP ou 'X' para sair.");
                    key = Console.ReadKey(true);

                    switch (key.Key)
                    {
                        case ConsoleKey.X:
                            executar = false;
                            break;

                        case ConsoleKey.U:
                            // Envia a hora atual usando o UDP
                            buffer = Encoding.ASCII.GetBytes(DateTime.Now.ToString("HH:mm:ss.fff"));
                            udpClient.Send(buffer, buffer.Length);
                            break;

                        case ConsoleKey.T:
                            // Envia a hora atual usando o TCP
                            buffer = Encoding.ASCII.GetBytes(DateTime.Now.ToString("HH:mm:ss.fff"));

                            if (tcpStream == null)
                                tcpStream = tcpClient.GetStream();

                            tcpStream.Write(buffer, 0, buffer.Length);
                            break;
                    }
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("ERRO: -> " + ex);
            }
            finally
            {
                if (udpClient != null)
                    udpClient.Close();

                if (tcpStream != null)
                    tcpStream.Close();

                if (tcpClient != null)
                    tcpClient.Close();
            }

            Console.WriteLine("Pressione <ENTER> para sair.");
            Console.ReadLine();
        }
    }
}