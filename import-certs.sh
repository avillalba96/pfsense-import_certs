#!/bin/sh
# Base directory where the Base64 .crt and .key files are located
BASE_DIR="/root/keys"
CERT_DIR="$BASE_DIR/certs"
DECODE_DIR="$BASE_DIR/decode"

# Create the decode directory only if it doesn't already exist
[ ! -d "$DECODE_DIR" ] && mkdir "$DECODE_DIR"

# Ensure the CERT_DIR exists
cd "$CERT_DIR" || { echo "ERROR: Cannot access the directory $CERT_DIR"; exit 1; }

# Iterate over the .crt files in the certs directory
FILES="*.crt"
for f in $FILES; do
  # Check if there are matching files
  [ ! -f "$f" ] && continue

  BASE=$(basename "$f")
  BASE=${BASE%.*}

  # Decode the .crt file (certificate)
  if [ -f "$CERT_DIR/$BASE.crt" ]; then
    base64 -d "$CERT_DIR/$BASE.crt" > "$DECODE_DIR/$BASE"_decode.crt
  else
    echo "ERROR: The file $CERT_DIR/$BASE.crt was not found"
    continue
  fi

  # Decode the .key file (private key)
  if [ -f "$CERT_DIR/$BASE.key" ]; then
    base64 -d "$CERT_DIR/$BASE.key" > "$DECODE_DIR/$BASE"_decode.key
  else
    echo "ERROR: The file $CERT_DIR/$BASE.key was not found"
    continue
  fi

  # Import the decoded certificate and private key using PHP
  if [ -f "$DECODE_DIR/$BASE"_decode.crt ] && [ -f "$DECODE_DIR/$BASE"_decode.key ]; then
    php /root/keys/import-cert.php "$DECODE_DIR/$BASE"_decode.crt "$DECODE_DIR/$BASE"_decode.key
  else
    echo "ERROR: Could not correctly decode $BASE.crt and/or $BASE.key"
    continue
  fi
done

echo "All certificates from $CERT_DIR have been processed."
