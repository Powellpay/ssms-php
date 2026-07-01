<?php

declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

$modules = ['Auth', 'Academic', 'Staff', 'Students', 'Curriculum', 'Assessment', 'Reports', 'Attendance', 'Timetable', 'Finance', 'Discipline', 'Library', 'Announcements', 'Shared'];

foreach ($modules as $module) {
    $dir = "app/Domain/{$module}";
    if (!is_dir($dir)) continue;
    
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') continue;
        
        $content = file_get_contents($file->getPathname());
        // Fix: App\Domain\{Module}\{Module}\ → App\Domain\{Module}\
        $wrong = "App\\Domain\\{$module}\\{$module}\\";
        $right = "App\\Domain\\{$module}\\";
        if (str_contains($content, $wrong)) {
            $content = str_replace($wrong, $right, $content);
            file_put_contents($file->getPathname(), $content);
            echo "Fixed: {$file->getPathname()}\n";
        }
    }
}

echo "Done fixing namespaces.\n";
