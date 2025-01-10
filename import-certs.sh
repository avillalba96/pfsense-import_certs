#!/bin/sh
# Directory where the Base64 .crt and .key files are located
CERT_DIR="/root/keys"
DECODE_DIR="$CERT_DIR/decode"

# Create the decode directory only if it doesn't already exist
[ ! -d "$DECODE_DIR" ] && mkdir "$DECODE_DIR"

cd "$CERT_DIR" || { echo "ERROR: Cannot access the directory $CERT_DIR"; exit 1; }

# Iterate over the .crt files
FILES="*.crt"
for f in $FILES; do
  BASE=$(basename "$f")
  BASE=${BASE%.*}

  # Decode the .crt file (certificate)
  if [ -f "$BASE.crt" ]; then
    base64 -d "$BASE.crt" > "$DECODE_DIR/$BASE"_decode.crt
  else
    echo "ERROR: The file $BASE.crt was not found"
    continue
  fi

  # Decode the .key file (private key)
  if [ -f "$BASE.key" ]; then
    base64 -d "$BASE.key" > "$DECODE_DIR/$BASE"_decode.key
  else
    echo "ERROR: The file $BASE.key was not found"
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

echo "All certificates have been processed."
