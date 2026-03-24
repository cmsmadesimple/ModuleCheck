<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_DeprecatedPHP extends AbstractCheck
{
    const DEPRECATED_PATTERNS = [
        // Deprecated PHP functions
        '/\bsplit\s*\(/'          => ['warning_deprecated_php_split', 5],
        '/\bereg\s*\(/'           => ['warning_deprecated_php_ereg', 5],
        '/\beregi\s*\(/'          => ['warning_deprecated_php_ereg', 5],
        '/\bereg_replace\s*\(/'   => ['warning_deprecated_php_ereg', 5],
        '/\beregi_replace\s*\(/'  => ['warning_deprecated_php_ereg', 5],
        '/\bmysql_\w+\s*\(/'      => ['warning_deprecated_php_mysql', 7],
        '/\beach\s*\(/'           => ['warning_deprecated_php_each', 5],

        // Deprecated CMSMS APIs
        '/\bCreateURL\s*\(/'       => ['warning_deprecated_create_url', 4],
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

            foreach (self::DEPRECATED_PATTERNS as $pattern => [$lang_key, $severity]) {
                if (preg_match($pattern, $contents)) {
                    $results[] = $this->warning($lang_key,
                        $this->mod->Lang($lang_key, $relative), $severity, $file);
                }
            }

            // {} string offset syntax (deprecated in PHP 7.4+)
            if (preg_match('/\$\w+\s*\{[^}]*\}/', $contents)) {
                // Exclude array declarations and Smarty-like patterns
                $lines = explode("\n", $contents);
                foreach ($lines as $line) {
                    if (preg_match('/\$\w+\{(\d+|[\$]\w+)\}/', $line) && strpos($line, '=>') === false) {
                        $results[] = $this->warning('deprecated_curly_string_offset',
                            $this->mod->Lang('warning_deprecated_curly_offset', $relative), 5, $file);
                        break;
                    }
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
