#1/bin/bash
if [ $# -ne 1 ]
then
	echo "Use sudo ./runServidor.sh IpServidorDNS"
	exit -1
fi
ruby Servidor/CamadaAplicacao/Servidor.rb "$1" &
php Servidor/CamadaFisica/Servidor.php "$1" "$1" &
sudo php Cliente/CamadaFisica/Cliente.php "$1" "$1" &
