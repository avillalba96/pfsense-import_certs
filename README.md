# pfsense-import-certs

* No se probo nada, solo esta de ejemplo en base a:
<https://github.com/zxsecurity/pfsense-import-certificate>
* Esto es para poder migrar Certificados de forma sencilla, usado por ejemplo para la migracion del servicio OpenVPN, y solo se migran los certificados de los usuarios, **NO DEL SERVIDIOR OPENVPN**, ese se puede hacer de forma manual ya que necesitas el CA y el CERTIFICADO y reconfigurar el Servicio asociandolos
* Primero bajar todos los certificados del pfsense-origen y comprimirlos para luego moverlos al pfsense-destino
```bash
ssh root@119.8.74.184 -p5522
mkdir /root/keys && cd /root/keys
fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-cert.php?token=GHSAT0AAAAAACUP4EJK42IB5RT6BDJA5UEIZ4ARQ3A)
fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-certs.sh?token=GHSAT0AAAAAACUP4EJL3PPYN7TXRH6TOLY6Z4ARQ4A
chmod +x import-certs.sh
mkdir certs
sh import-certs.sh
```
