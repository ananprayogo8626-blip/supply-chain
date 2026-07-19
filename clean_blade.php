<?php
$dir = 'c:/xampp/htdocs/supply-chain/resources/views';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($it as $file) {
    if ($file->isFile() && preg_match('/\.blade\.php$/', $file->getFilename())) {
        $content = file_get_contents($file->getPathname());
        $clean = preg_replace('/^\d+:\s*/m', '', $content);
        file_put_contents($file->getPathname(), $clean);
    }
}
?>
