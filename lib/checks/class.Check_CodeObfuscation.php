<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_CodeObfuscation extends AbstractCheck
{
    const OBFUSCATION_PATTERNS = [
        'Zend Guard'      => '/(\<\?php \@Zend;)|(This file was encoded by)/i',
        'SourceGuardian'  => '/(sourceguardian\.com)|(function_exists\(\'sg_load\'\))|(\$__x=)/i',
        'ionCube'         => '/ionCube/i',
    ];

    public function getCategories(): array { return ['security']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $php_files = $this->getPhpFiles($module_path);

        foreach ($php_files as $file) {
            $contents = $this->getFileContents($file);
            if (!$contents) continue;
            $relative = str_replace($module_path . DIRECTORY_SEPARATOR, '', $file);

            foreach (self::OBFUSCATION_PATTERNS as $tool => $pattern) {
                if (preg_match($pattern, $contents)) {
                    $results[] = $this->error('obfuscated_code',
                        $this->mod->Lang('error_obfuscated_code', $tool, $relative), 9, $file);
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
