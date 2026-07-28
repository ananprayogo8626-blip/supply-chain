<?php
// Fast Vendor Extractor Script for FTP Deployments

// Set maximum execution time and memory limit for extraction
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

header('Content-Type: application/json');

// Security key check
$allowedKey = getenv('DEPLOY_EXTRACT_KEY') ?: 'anan_supply_chain_secret_key_2026';
$providedKey = $_GET['key'] ?? $_POST['key'] ?? '';

if (empty($providedKey) || $providedKey !== $allowedKey) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Invalid or missing key.'
    ]);
    exit;
}

$zipPath = __DIR__ . '/../vendor.zip';
$extractPath = __DIR__ . '/../';

if (!file_exists($zipPath)) {
    // Check if vendor directory already exists
    if (is_dir(__DIR__ . '/../vendor')) {
        echo json_encode([
            'status' => 'success',
            'message' => 'vendor.zip not found, but vendor directory already exists.'
        ]);
        exit;
    }

    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'vendor.zip file not found in project root.'
    ]);
    exit;
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'PHP ZipArchive extension is not enabled on this server.'
    ]);
    exit;
}

$zip = new ZipArchive();
$res = $zip->open($zipPath);

if ($res === TRUE) {
    $zip->extractTo($extractPath);
    $zip->close();
    
    // Delete vendor.zip after successful extraction
    @unlink($zipPath);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'vendor.zip successfully extracted and deleted.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to open vendor.zip. Code: ' . $res
    ]);
}
