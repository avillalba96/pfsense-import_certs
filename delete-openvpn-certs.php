<?php
// Check if the script is being run from CLI
if (php_sapi_name() !== 'cli') {
    echo "This script can only be run from the CLI.\r\n";
    die(1);
}

// Check if the CA name is provided as an argument
if ($argc !== 2) {
    echo "Usage: php " . $argv[0] . " <CA_Name>\r\n";
    die(1);
}

$ca_name = $argv[1]; // Get the CA name from the command line argument

require_once "certs.inc";
require_once "pfsense-utils.inc";
require_once "functions.inc";

global $config;

// Ensure the CA configuration exists
if (!isset($config['ca']) || !is_array($config['ca'])) {
    echo "No Certificate Authorities (CA) found in the configuration.\r\n";
    die(1);
}

// Find the refid of the CA with the given name
$ca_refid = null;
foreach ($config['ca'] as $ca) {
    if (isset($ca['descr']) && $ca['descr'] === $ca_name) {
        $ca_refid = $ca['refid'];
        break;
    }
}

if ($ca_refid === null) {
    echo "No CA named '$ca_name' found.\r\n";
    die(1);
}

echo "Found CA '$ca_name' with refid: $ca_refid\r\n";

// Ensure the certificates configuration exists
if (!isset($config['cert']) || !is_array($config['cert'])) {
    echo "No certificates found in the configuration.\r\n";
    die(1);
}

$certs = &$config['cert']; // Reference to the certificates array
$deleted_count = 0;

// Iterate through certificates and delete those with matching caref
foreach ($certs as $index => $cert) {
    if (isset($cert['caref']) && $cert['caref'] === $ca_refid) {
        echo "Deleting certificate: " . ($cert['descr'] ?? "Unnamed Certificate") . "\r\n";

        // Remove the certificate
        unset($certs[$index]);
        $deleted_count++;
    }
}

// Save the updated configuration
if ($deleted_count > 0) {
    write_config("Deleted $deleted_count certificates issued by '$ca_name'.");
    echo "Deleted $deleted_count certificates issued by '$ca_name'.\r\n";
} else {
    echo "No certificates issued by '$ca_name' were found.\r\n";
}
