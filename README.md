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
   mkdir -p /root/keys
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
   scp -P 22 /root/keys/certs.tar.gz root@<destination_pfsense_ip>:/root/keys/
   ```

---

### 2. Destination pfSense: Import Certificates

1. **Connect to the destination pfSense system**:
   ```bash
   ssh root@<destination_pfsense_ip> -p22
   ```

2. **Decompress the certificates**:
   ```bash
   tar -xzvf /root/keys/certs.tar.gz -C /root/keys
   ```

3. **Download and execute the import script**:
   ```bash
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-cert.php
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-certs.sh
   sh /root/keys/import-certs.sh
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

3. **Logs all actions**:
   - Creates `/root/keys/import.log` with details of success, failures, and duplicates.

---

## 📑 Logs

To view the log file:
```bash
cat /root/keys/import.log
```

The log includes:
- Certificates successfully imported.
- Certificates already present.
- Errors, such as missing `.crt` or `.key` files.

---

## ✅ Features

- **Quick OpenVPN migration**: Transfers user certificates between pfSense systems.
- **Automation**: Decodes Base64 files and imports them into the destination system.
- **Comprehensive logs**: Tracks every certificate's status during migration.
- **Idempotent**: Certificates already imported are skipped.

---

## 🚀 Example Workflow

1. **On the source pfSense**:
   ```bash
   ssh root@<source_pfsense_ip> -p22
   mkdir -p /root/keys
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/export-certs.php
   php export-certs.php /root/keys
   tar -czvf /root/keys/certs.tar.gz -C /root/keys certs
   scp -P 22 /root/keys/certs.tar.gz root@<destination_pfsense_ip>:/root/keys/
   ```

2. **On the destination pfSense**:
   ```bash
   ssh root@<destination_pfsense_ip> -p22
   tar -xzvf /root/keys/certs.tar.gz -C /root/keys
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-cert.php
   fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-certs.sh
   sh /root/keys/import-certs.sh
   ```

3. **Verify logs**:
   ```bash
   cat /root/keys/import.log
   ```

---

## ℹ️ Reference

This project is based on [zxsecurity/pfsense-import-certificate](https://github.com/zxsecurity/pfsense-import-certificate) and adapted for OpenVPN migrations.
