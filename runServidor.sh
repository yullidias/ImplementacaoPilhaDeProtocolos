#!/bin/bash
if [ $# -ne 1 ]
then
	echo "Use sudo ./runServidor.sh IpServidorDNS"
	exit -1
fi

ps -aux | grep "ruby Servidor/CamadaAplicacao/Servidor.rb" | cut -d" " -f6 | xargs sudo kill -9
ps -aux | grep "php Servidor/CamadaFisica/Servidor.php" | cut -d" " -f6 | xargs sudo kill -9
ps -aux | grep "sudo php Cliente/CamadaFisica/Cliente.php" | cut -d" " -f6 | xargs sudo kill -9
ps -aux | grep "php Cliente/CamadaFisica/Cliente.php" | cut -d" " -f6 | xargs sudo kill -9

RED='\033[0;31m'
NC='\033[0m' # No Color
echo -e "${RED}Caso mostre Warnings, use lsof -wni tcp:8070 && lsof -wni tcp:8080 && lsof -wni tcp:8090 e mate os processos! ${NC}"
echo "Executar esse script na raiz do repositorio"
ruby Servidor/CamadaAplicacao/Servidor.rb "$1" &
pidSR=$!
echo $pidSR
php Servidor/CamadaFisica/Servidor.php "$1" "$1" &
pidSP=$!
echo $pidSP
sudo php Cliente/CamadaFisica/Cliente.php "$1" "$1" &
pidCP=$!
echo $pidCP
