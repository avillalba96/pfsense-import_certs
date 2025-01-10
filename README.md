# pfSense OpenVPN Certificates Import Tool

This tool simplifies the process of **migrating OpenVPN certificates** to a pfSense system. It automates the decoding of Base64-encoded `.crt` and `.key` files, followed by their importation into the pfSense certificate manager. This is particularly useful for quick and seamless OpenVPN migrations.

Based on [zxsecurity/pfsense-import-certificate](https://github.com/zxsecurity/pfsense-import-certificate).

---

## 📥 Setup Instructions

### 1. Connect to your pfSense system via SSH
Replace `x.x.x.x` with the IP address of your pfSense system:
```bash
ssh root@x.x.x.x -p22
```

### 2. Prepare the working directories
Create the main directory for the migration and a subdirectory for the OpenVPN certificates:
```bash
mkdir -p /root/keys/certs && cd /root/keys
```

### 3. Download the required scripts
Fetch the necessary scripts directly from the GitHub repository:
```bash
fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-cert.php
fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-certs.sh
```

### 4. Upload your OpenVPN certificates
Copy your Base64-encoded `.crt` and `.key` files for OpenVPN users into the `/root/keys/certs` directory.

### 5. Run the import script
Execute the shell script to decode and import the OpenVPN certificates:
```bash
sh import-certs.sh
```

---

## 📂 Directory Structure

After setup, your directory structure should look like this:
```
/root/keys/
├── certs/        # Place your Base64-encoded .crt and .key files here
├── decode/       # Decoded files will be generated here (automatically created)
├── import-cert.php
├── import-certs.sh
```

---

## 📄 What the Script Does

1. **Decode Base64-encoded certificates**:
   - `.crt` and `.key` files from `/root/keys/certs/` are decoded into the `/root/keys/decode/` directory.

2. **Import certificates into pfSense**:
   - The script imports the certificates into the pfSense **System > Certificate Manager**.

3. **Logs the process**:
   - A detailed log is created at `/root/keys/import.log`, including:
     - Certificates successfully imported.
     - Certificates already present in pfSense.
     - Errors encountered during the import process.

---

## 📝 Logs

To view the log file:
```bash
cat /root/keys/import.log
```

The log includes:
- Certificates successfully imported into pfSense.
- Certificates that were already imported.
- Errors encountered, such as missing `.crt` or `.key` files.

---

## ✅ Features

- **Quick OpenVPN migration**: Simplifies the process of transferring user certificates to pfSense.
- **Automation**: Decodes Base64 files and imports them with minimal manual intervention.
- **Comprehensive logs**: Tracks every certificate's status during the migration.
- **Idempotent**: Skips certificates that are already imported.

---

## 🚀 Example Workflow

1. **Connect to pfSense**:
    ```bash
    ssh root@192.168.1.1 -p22
    ```

2. **Set up the environment**:
    ```bash
    mkdir -p /root/keys/certs && cd /root/keys
    fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-cert.php
    fetch https://raw.githubusercontent.com/avillalba96/pfsense-import_certs/refs/heads/master/import-certs.sh
    ```

3. **Upload OpenVPN certificates**:
    - Add `.crt` and `.key` files (Base64-encoded) for OpenVPN users to `/root/keys/certs`.

4. **Run the script**:
    ```bash
    sh import-certs.sh
    ```

5. **Check the log for results**:
    ```bash
    cat /root/keys/import.log
    ```

---

## ℹ️ Notes

- This tool is specifically designed for **migrating OpenVPN certificates** during pfSense deployments or upgrades.
- Ensure that your `.crt` and `.key` files are encoded in **Base64** before placing them in `/root/keys/certs`.

---

## 📌 Reference

This project is adapted from [zxsecurity/pfsense-import-certificate](https://github.com/zxsecurity/pfsense-import-certificate).
```

---

### **Mejoras Implementadas**
1. **Claridad en el objetivo del proyecto**:
   - Se especifica que el propósito principal es la **migración rápida de certificados OpenVPN**.

2. **Directorio y flujo de trabajo orientado a OpenVPN**:
   - Se detalla dónde deben colocarse los certificados de OpenVPN y cómo procesarlos.

3. **Logs mejorados**:
   - Se resalta la importancia de los logs y cómo revisarlos.

4. **Idempotencia**:
   - Se enfatiza que los certificados ya presentes no se duplicarán.
