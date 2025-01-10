# pfsense-import-certs

* No se probo nada, solo esta de ejemplo en base a:
<https://github.com/zxsecurity/pfsense-import-certificate>


ssh root@119.8.74.184 -p5522
mkdir /root/keys && cd /root/keys
fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/importar-certs.php?token=GHSAT0AAAAAACUP4EJK42IB5RT6BDJA5UEIZ4ARQ3A)
fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/importar-certs.sh?token=GHSAT0AAAAAACUP4EJL3PPYN7TXRH6TOLY6Z4ARQ4A
chmod +x importar-certs.sh
./importar-certs.sh
