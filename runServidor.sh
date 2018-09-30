#1/bin/bash
if [ $# -ne 1 ]
then
	echo "Use sudo ./runServidor.sh IpServidorDNS"
	exit -1
fi
ruby2.1 Servidor/CamadaAplicacao/Servidor.rb "$1" &
sleep 0.5
php7.0 Servidor/CamadaFisica/Servidor.php "$1" "$1" &
sleep 0.5
sudo php7.0 Cliente/CamadaFisica/Cliente.php "$1" "$1" &
sleep 0.5
