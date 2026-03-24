<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_Localhost extends AbstractCheck
{
    const LOCALHOST_PATTERNS = [
        '/https?:\/\/localhost\b/i',
        '/https?:\/\/127\.0\.0\.1\b/',
        '/https?:\/\/0\.0\.0\.0\b/',
        '/https?:\/\/\[::1\]/',
    ];

    public function getCategories(): array { return ['best_practices']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $php_files = $this->getPhpFiles($module_path);

        foreach ($php_files as $file) {
            $contents = $this->getFileContents($file);
            if (!$contents) continue;
            $relative = str_replace($module_path . DIRECTORY_SEPARATOR, '', $file);

            foreach (self::LOCALHOST_PATTERNS as $pattern) {
                if (preg_match($pattern, $contents)) {
                    $results[] = $this->warning('localhost_reference',
                        $this->mod->Lang('warning_localhost_reference', $relative), 7, $file);
                    break;
                }
            }
        }

        return $results;
    }

    private function getPhpFiles(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}
