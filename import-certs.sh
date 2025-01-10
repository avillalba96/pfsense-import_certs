#!/bin/bash
#Entra al directorio donde se copiaron los certificados
# e importa todos los archivos que tengan el par .crt y .key
cd /root/keys || exit
FILES="*.crt"
for f in $FILES
do
  BASE=$(basename "$f")
  BASE=${BASE%.*}
  #echo $BASE
  php /root/keys/importar-certs.php "$BASE".crt "$BASE".key
done

