<?php
// Adpatado en base a https://github.com/zxsecurity/pfsense-import-certificate

if (empty($argc)) {
	echo "Only accessible from the CLI.\r\n";
	die(1);
}

if ($argc != 2) {
	echo "Usage: php " . $argv[0] . " nombre-de-certificado\r\n";
	die(1);
}

require_once "certs.inc";
require_once "pfsense-utils.inc";
require_once "functions.inc";
require_once "filter.inc";
require_once "shaper.inc";

$name = cert_escape_x509_chars($argv[1]);
$caref = "5e2708ac57caa"; //Remplazar con ID del CA a utilizar

$pconfig['keytype'] = "RSA";
$pconfig['keylen'] = "4096";
$pconfig['ecname'] = "prime256v1";
$pconfig['digest_alg'] = "sha512";
$pconfig['type'] = "user";
$pconfig['lifetime'] = 3650;

$dn = array('commonName' => cert_escape_x509_chars($argv[1]));
$dn['countryName'] = "AR";
$dn['stateOrProvinceName'] = cert_escape_x509_chars("Santa Fe");
$dn['localityName'] = cert_escape_x509_chars("Santa Fe");
$dn['organizationName'] = cert_escape_x509_chars("El Litoral S.R.L.");
$dn['organizationalUnitName'] = cert_escape_x509_chars("Terceros");

$cert = array();
$cert['refid'] = uniqid();
$cert['descr'] = $name;

cert_create($cert, $caref , $pconfig['keylen'], $pconfig['lifetime'], $dn, $pconfig['type'], $pconfig['digest_alg'], $pconfig['keytype'], $pconfig['ecname']);

// Set up the existing certificate store
// Copied from system_certmanager.php
if (!is_array($config['ca'])) {
	$config['ca'] = array();
}

$a_ca =& $config['ca'];

if (!is_array($config['cert'])) {
	$config['cert'] = array();
}

$a_cert =& $config['cert'];

$internal_ca_count = 0;
foreach ($a_ca as $ca) {
	if ($ca['prv']) {
		$internal_ca_count++;
	}
}

// Check if the certificate we just parsed is already imported (we'll check the certificate portion)
foreach ($a_cert as $existing_cert) {
	if ($existing_cert['crt'] === $cert['crt']) {
		echo "Ya existe el certificado $name.\r\n";
		die(); // exit with a valid error code, as this is intended behaviour
	}
}

// Append the final certificate
$a_cert[] = $cert;

// Write out the updated configuration
write_config();

echo "Se genero correctamente el certificado $name.\r\n";
