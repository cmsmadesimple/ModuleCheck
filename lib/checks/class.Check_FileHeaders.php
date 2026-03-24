<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_FileHeaders extends AbstractCheck
{
    const ACCESS_GUARD_PATTERNS = [
        '/if\s*\(\s*!\s*isset\s*\(\s*\$gCms\s*\)\s*\)\s*(exit|die)\s*;/i',
        '/if\s*\(\s*!\s*defined\s*\(\s*[\'"]CMS_VERSION[\'"]\s*\)\s*\)\s*(exit|die)\s*;/i',
        '/if\s*\(\s*!\s*isset\s*\(\s*\$gCms\s*\)\s*\)\s*\{?\s*(exit|die)/i',
        '/defined\s*\(\s*[\'"]CMS_VERSION[\'"]\s*\)\s*(\|\||or)\s*(exit|die)/i',
    ];

    public function getCategories(): array { return ['security']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $php_files = $this->getPhpFiles($module_path);

        foreach ($php_files as $file) {
            $relative = str_replace($module_path . DIRECTORY_SEPARATOR, '', $file);
            $contents = $this->getFileContents($file);
            if (!$contents) continue;

            // Skip lib/ class files — they typically don't need access guards
            if ($this->isClassOnlyFile($contents)) continue;

            // Check direct access guard on action/method files
            if ($this->needsAccessGuard($relative)) {
                if (!$this->hasAccessGuard($contents)) {
                    $results[] = $this->error('missing_access_guard',
                        $this->mod->Lang('error_missing_access_guard', $relative), 7, $file);
                }
            }
        }

        // Check license header on main module file
        $main_file = $module_path . DIRECTORY_SEPARATOR . $module_name . '.module.php';
        if (is_file($main_file)) {
            $contents = $this->getFileContents($main_file);
            if ($contents && !$this->hasLicenseHeader($contents)) {
                $results[] = $this->warning('missing_license_header',
                    $this->mod->Lang('warning_missing_license_header', $module_name . '.module.php'), 4, $main_file);
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

    private function needsAccessGuard(string $relative): bool
    {
        // Action files and method files need guards
        if (preg_match('/^action\..+\.php$/', $relative)) return true;
        if (preg_match('/^method\..+\.php$/', $relative)) return true;
        if (preg_match('/^function\..+\.php$/', $relative)) return true;
        // Main module file is a class — does NOT need an access guard
        return false;
    }

    private function hasAccessGuard(string $contents): bool
    {
        // Check first 30 lines
        $lines = explode("\n", $contents);
        $beginning = implode("\n", array_slice($lines, 0, 30));

        foreach (self::ACCESS_GUARD_PATTERNS as $pattern) {
            if (preg_match($pattern, $beginning)) return true;
        }
        return false;
    }

    private function isClassOnlyFile(string $contents): bool
    {
        // Remove comments
        $cleaned = preg_replace('#/\*.*?\*/#s', '', $contents);
        $cleaned = preg_replace('#//.*$#m', '', $cleaned ?? '');
        $cleaned = preg_replace('/\#.*$/m', '', $cleaned ?? '');

        return (bool) preg_match('/^\s*<\?php\s*(namespace\s+[^;]+;\s*)?(use\s+[^;]+;\s*)*\s*(\/\*.*?\*\/\s*)?(abstract\s+|final\s+)?(class|interface|trait)\s+\w+/is', $cleaned ?? '');
    }

    private function hasLicenseHeader(string $contents): bool
    {
        $first_500 = substr($contents, 0, 500);
        $patterns = [
            '/licen[cs]e/i',
            '/copyright/i',
            '/GNU General Public License/i',
            '/GPL/i',
            '/See LICENSE/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $first_500)) return true;
        }
        return false;
    }
}
