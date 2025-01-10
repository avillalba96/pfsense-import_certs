<?php
// Adapted from https://github.com/zxsecurity/pfsense-import-certificate

if (php_sapi_name() !== 'cli') {
    echo "This script can only be run from the CLI.\r\n";
    die(1);
}

if ($argc < 2) {
    echo "Usage: php " . $argv[0] . " certificate-name [CA-ref-id]\r\n";
    die(1);
}

require_once "certs.inc";
require_once "pfsense-utils.inc";
require_once "functions.inc";
require_once "filter.inc";
require_once "shaper.inc";

// Certificate name and CA reference ID
$name = cert_escape_x509_chars($argv[1]);
$caref = isset($argv[2]) ? $argv[2] : null;

if (!$caref) {
    echo "Enter the CA Reference ID: ";
    $caref = trim(fgets(STDIN));
    if (empty($caref)) {
        echo "CA Reference ID is required.\r\n";
        die(1);
    }
}

// Request additional certificate parameters
echo "Enter the key type (default: RSA): ";
$keytype = trim(fgets(STDIN)) ?: "RSA";

echo "Enter the key length (default: 4096): ";
$keylen = trim(fgets(STDIN)) ?: "4096";

echo "Enter the digest algorithm (default: sha512): ";
$digest_alg = trim(fgets(STDIN)) ?: "sha512";

echo "Enter the certificate lifetime in days (default: 3650): ";
$lifetime = trim(fgets(STDIN)) ?: 3650;

echo "Enter the country name (default: AR): ";
$countryName = trim(fgets(STDIN)) ?: "AR";

echo "Enter the state/province name (default: Santa Fe): ";
$stateOrProvinceName = trim(fgets(STDIN)) ?: "Santa Fe";

echo "Enter the locality name (default: Santa Fe): ";
$localityName = trim(fgets(STDIN)) ?: "Santa Fe";

echo "Enter the organization name (default: El Litoral S.R.L.): ";
$organizationName = trim(fgets(STDIN)) ?: "El Litoral S.R.L.";

echo "Enter the organizational unit name (default: Terceros): ";
$organizationalUnitName = trim(fgets(STDIN)) ?: "Terceros";

// Certificate DN
$dn = array(
    'commonName' => $name,
    'countryName' => $countryName,
    'stateOrProvinceName' => $stateOrProvinceName,
    'localityName' => $localityName,
    'organizationName' => $organizationName,
    'organizationalUnitName' => $organizationalUnitName
);

// Prepare certificate configuration
$pconfig = array(
    'keytype' => $keytype,
    'keylen' => $keylen,
    'digest_alg' => $digest_alg,
    'type' => "user",
    'lifetime' => $lifetime,
);

// Create the certificate
$cert = array();
$cert['refid'] = uniqid();
$cert['descr'] = $name;

cert_create($cert, $caref, $pconfig['keylen'], $pconfig['lifetime'], $dn, $pconfig['type'], $pconfig['digest_alg'], $pconfig['keytype'], $pconfig['ecname']);

// Ensure certificate arrays exist in configuration
if (!is_array($config['ca'])) {
    $config['ca'] = array();
}

$a_ca = &$config['ca'];

if (!is_array($config['cert'])) {
    $config['cert'] = array();
}

$a_cert = &$config['cert'];

// Check if the certificate already exists
foreach ($a_cert as $existing_cert) {
    if ($existing_cert['crt'] === $cert['crt']) {
        echo "The certificate $name already exists.\r\n";
        die(0); // Exit gracefully
    }
}

// Append the new certificate
$a_cert[] = $cert;

// Save the updated configuration
write_config();

echo "Certificate $name was successfully created.\r\n";
