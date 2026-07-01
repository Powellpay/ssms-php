<?php

$providers = ['App\Providers\AppServiceProvider'];

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Domain'));
foreach ($it as $f) {
    if ($f->isFile() && $f->getExtension() === 'php' && str_contains($f->getFilename(), 'ServiceProvider')) {
        $path = str_replace(['/', '\\'], '/', $f->getPathname());
        $rel = substr($path, 4, -4); // strip 'app/' and '.php'
        $rel = str_replace('/', '\\', $rel);
        $providers[] = 'App\\' . $rel;
    }
}

$out = "<?php\n\nreturn [\n";
foreach ($providers as $p) {
    $out .= "    {$p}::class,\n";
}
$out .= "];\n";

file_put_contents('bootstrap/providers.php', $out);
echo "Generated bootstrap/providers.php with " . count($providers) . " providers.\n";
