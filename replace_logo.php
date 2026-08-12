<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        
        $replaced = str_replace(
            "asset('manager_asset/images/salespilot logo1.png')", 
            "app_logo()", 
            $content
        );
        
        // Also catch double quotes if any
        $replaced = str_replace(
            'asset("manager_asset/images/salespilot logo1.png")', 
            "app_logo()", 
            $replaced
        );

        if ($content !== $replaced) {
            file_put_contents($file->getRealPath(), $replaced);
            echo "Updated: " . $file->getRealPath() . "\n";
        }
    }
}
echo "Done.\n";
