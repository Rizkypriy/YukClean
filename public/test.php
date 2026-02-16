<?php
$tmpPath = 'C:\xampp\tmp';
echo '<h3>Session Path Test</h3>';
echo 'Session save path: ' . session_save_path() . '<br>';
echo 'Folder exists: ' . (file_exists($tmpPath) ? 'Yes' : 'No') . '<br>';
echo 'Is writable: ' . (is_writable($tmpPath) ? 'Yes' : 'No') . '<br>';

// Test write file
$testFile = $tmpPath . '\test_write_' . time() . '.txt';
$writeTest = file_put_contents($testFile, 'Test write permission');
echo 'Write test: ' . ($writeTest !== false ? 'Success' : 'Failed') . '<br>';

if ($writeTest !== false) {
    unlink($testFile); // Hapus file test
    echo 'Test file deleted<br>';
}

// Test session write
session_start();
$_SESSION['test'] = 'Session works at ' . date('Y-m-d H:i:s');
session_write_close();
echo 'Session test: ' . (isset($_SESSION['test']) ? 'Success' : 'Failed') . '<br>';