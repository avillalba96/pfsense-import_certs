# pfSense OpenVPN Certificates Migration Tool

This tool simplifies the process of **migrating OpenVPN certificates** between pfSense systems. It automates exporting, compressing, transferring, and importing certificates for seamless OpenVPN migrations.

Based on [zxsecurity/pfsense-import-certificate](https://github.com/zxsecurity/pfsense-import-certificate).

---

## 📥 Setup Instructions

### 1. Source pfSense: Export Certificates

1. **Connect to the source pfSense system**:
   ```bash
   ssh root@<source_pfsense_ip> -p22
   ```

2. **Prepare the environment**:
   ```bash
   mkdir -p /root/keys && cd /root/keys
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/export-certs.php
   php export-certs.php /root/keys
   ```

   - This will generate `/root/keys/certs/` containing all exported `.crt` and `.key` files.

3. **Compress the exported certificates**:
   ```bash
   tar -czvf /root/keys/certs.tar.gz -C /root/keys certs
   ```

4. **Transfer the compressed file to the destination pfSense**:
   ```bash
   scp -P22 /root/keys/certs.tar.gz root@<destination_pfsense_ip>:/root/
   cd /root && rm -r keys/
   ```

---

### 2. Destination pfSense: Import Certificates

1. **Connect to the destination pfSense system**:
   ```bash
   ssh root@<destination_pfsense_ip> -p22
   ```

2. **Decompress the certificates**:
   ```bash|
   mkdir /root/keys && cd /root/keys
   mv ../certs.tar.gz . && tar -xzvf /root/keys/certs.tar.gz -C /root/keys
   ```

3. **Download and execute the import script**:
   ```bash
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-cert.php
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-certs.sh
   sh /root/keys/import-certs.sh
   cd /root && rm -r keys/
   ```

---

## 📂 Directory Structure

After setup, your directory structure will look like this:
```
/root/keys/
├── certs/        # Place your Base64-encoded .crt and .key files here
├── decode/       # Decoded files will be generated here (automatically created)
├── import-cert.php
├── import-certs.sh
├── certs.tar.gz  # Compressed certificates for transfer
```

---

## 📄 What the Scripts Do

### Export Script (`export-certs.php`):
- Exports certificates from the pfSense certificate manager.
- Saves `.crt` and `.key` files into `/root/keys/certs/`.

### Import Script (`import-certs.sh`):
1. **Decodes Base64-encoded `.crt` and `.key` files**:
   - Outputs decoded files to `/root/keys/decode/`.

2. **Imports certificates into pfSense**:
   - Registers certificates in **System > Certificate Manager**.

---

## ✅ Features

- **Quick OpenVPN migration**: Transfers user certificates between pfSense systems.
- **Automation**: Decodes Base64 files and imports them into the destination system.
- **Idempotent**: Certificates already imported are skipped.

---

## ℹ️ Reference

This project is based on [zxsecurity/pfsense-import-certificate](https://github.com/zxsecurity/pfsense-import-certificate) and adapted for OpenVPN migrations.
