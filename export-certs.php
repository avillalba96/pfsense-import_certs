<?php
if (empty($argc)) {
    echo "Only accessible from the CLI.\r\n";
    die(1);
}

if ($argc != 2) {
    echo "Usage: php " . $argv[0] . " /path/to/output_directory\r\n";
    die(1);
}

$outputDir = rtrim($argv[1], '/');
$certsDir = "$outputDir/certs"; // Define the directory for storing certificates

// Create the certs directory if it doesn't exist
if (!is_dir($certsDir)) {
    if (!mkdir($certsDir, 0755, true)) {
        echo "ERROR: Failed to create directory $certsDir.\r\n";
        die(1);
    }
}

require_once "certs.inc";
require_once "pfsense-utils.inc";
require_once "functions.inc";

global $config;

// Ensure the certificates configuration exists
if (!isset($config['cert']) || !is_array($config['cert'])) {
    echo "No certificates found in the configuration.\r\n";
    die(1);
}

$certs = $config['cert'];
$count = 0;

foreach ($certs as $cert) {
    $name = isset($cert['descr']) ? $cert['descr'] : "cert_" . $count;

    // Prepare safe file names
    $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);

    // Write certificate
    if (!empty($cert['crt'])) {
        file_put_contents("$certsDir/{$safeName}.crt", $cert['crt']);
    }

    // Write private key
    if (!empty($cert['prv'])) {
        file_put_contents("$certsDir/{$safeName}.key", $cert['prv']);
    }

    $count++;
}

echo "Exported $count certificates to $certsDir.\r\n";
