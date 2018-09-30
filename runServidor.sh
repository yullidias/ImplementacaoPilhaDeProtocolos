#!/bin/bash
if [ $# -ne 1 ]
then
	echo "Use sudo ./runServidor.sh IpServidorDNS"
	exit -1
fi
RED='\033[0;31m'
NC='\033[0m' # No Color
echo -e "${RED}Caso mostre Warnings, use lsof -wni tcp:8070 && lsof -wni tcp:8080 && lsof -wni tcp:8090 e mate os processos! ${NC}"
ruby Servidor/CamadaAplicacao/Servidor.rb "$1" &
php Servidor/CamadaFisica/Servidor.php "$1" "$1" &
sudo php Cliente/CamadaFisica/Cliente.php "$1" "$1" &
