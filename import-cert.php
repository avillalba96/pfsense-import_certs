<?php
// Adpatado en base a https://github.com/zxsecurity/pfsense-import-certificate

if (empty($argc)) {
	echo "Only accessible from the CLI.\r\n";
	die(1);
}

if ($argc != 3) {
	echo "Usage: php " . $argv[0] . " /path/to/certificate.crt /path/to/private/certificate.key\r\n";
	die(1);
}

require_once "certs.inc";
require_once "pfsense-utils.inc";
require_once "functions.inc";
require_once "filter.inc";
require_once "shaper.inc";

$certificate = trim(file_get_contents($argv[1]));
$key = trim(file_get_contents($argv[2]));
$name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $argv[1]);

// Do some quick verification of the certificate, similar to what the GUI does
if (empty($certificate)) {
	echo "The certificate is empty.\r\n";
	die(1);
}
if (!strstr($certificate, "BEGIN CERTIFICATE") || !strstr($certificate, "END CERTIFICATE")) {
	echo "This certificate does not appear to be valid.\r\n";
	die(1);
}

// Verification that the certificate matches the key
if (empty($key)) {
	echo "The key is empty.\r\n";
	die(1);
}
if (cert_get_publickey($certificate, false) != cert_get_publickey($key, false, 'prv')) {
	echo "The private key does not match the certificate.\r\n";
	die(1);
}

$cert = array();
$cert['refid'] = uniqid();
$cert['descr'] = $name;

cert_import($cert, $certificate, $key);

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
		echo "The certificate is already imported.\r\n";
		die(); // exit with a valid error code, as this is intended behaviour
	}
}

// Append the final certificate
$a_cert[] = $cert;

// Write out the updated configuration
write_config();

echo "Completed! New certificate $name installed.\r\n";
