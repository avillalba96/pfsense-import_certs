<?php
// Check if the script is being run from CLI
if (php_sapi_name() !== 'cli') {
    echo "This script can only be run from the CLI.\r\n";
    die(1);
}

require_once "certs.inc";
require_once "pfsense-utils.inc";
require_once "functions.inc";

global $config;

// Ensure the CA configuration exists
if (!isset($config['ca']) || !is_array($config['ca'])) {
    echo "No Certificate Authorities (CA) found in the configuration.\r\n";
    die(1);
}

// Find the refid of the CA named "OpenVPN_CA"
$ca_refid = null;
foreach ($config['ca'] as $ca) {
    if (isset($ca['descr']) && $ca['descr'] === "OpenVPN_CA") {
        $ca_refid = $ca['refid'];
        break;
    }
}

if ($ca_refid === null) {
    echo "No CA named 'OpenVPN_CA' found.\r\n";
    die(1);
}

echo "Found CA 'OpenVPN_CA' with refid: $ca_refid\r\n";

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
    write_config("Deleted $deleted_count certificates issued by OpenVPN_CA.");
    echo "Deleted $deleted_count certificates issued by OpenVPN_CA.\r\n";
} else {
    echo "No certificates issued by OpenVPN_CA were found.\r\n";
}
