<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_Security extends AbstractCheck
{
    public function getCategories(): array { return ['security']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $php_files = $this->getPhpFiles($module_path);

        foreach ($php_files as $file) {
            $contents = $this->getFileContents($file);
            if (!$contents) continue;
            $relative = str_replace($module_path . DIRECTORY_SEPARATOR, '', $file);

            $this->checkGlobalGCms($results, $contents, $relative, $file);
            $this->checkRawSuperglobals($results, $contents, $relative, $file);
            $this->checkSqlInjection($results, $contents, $relative, $file);
            $this->checkDeprecatedApis($results, $contents, $relative, $file);
        }

        return $results;
    }

    private function checkGlobalGCms(array &$results, string $contents, string $relative, string $file): void
    {
        if (preg_match('/\bglobal\s+\$gCms\b/', $contents)) {
            $results[] = $this->error('deprecated_global_gcms',
                $this->mod->Lang('error_deprecated_global_gcms', $relative), 7, $file);
        }
    }

    private function checkRawSuperglobals(array &$results, string $contents, string $relative, string $file): void
    {
        // Skip lib/ class files
        if (preg_match('#^lib[/\\\\]#', $relative)) return;

        if (preg_match('/\$_(POST|GET|REQUEST)\s*\[/', $contents)) {
            $results[] = $this->warning('raw_superglobal_access',
                $this->mod->Lang('warning_raw_superglobal', $relative), 6, $file);
        }
    }

    private function checkSqlInjection(array &$results, string $contents, string $relative, string $file): void
    {
        $sql_keywords = 'SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE';

        $lines = explode("\n", $contents);
        foreach ($lines as $num => $line) {
            if (preg_match('/["\'](' . $sql_keywords . ')\b/i', $line) &&
                preg_match('/\.\s*\$/', $line) &&
                strpos($line, '?') === false &&
                !preg_match('/\.\s*CMS_DB_PREFIX/', $line) &&
                !preg_match('/\.\s*cms_db_prefix\s*\(/', $line)) {
                $results[] = $this->error('possible_sql_injection',
                    $this->mod->Lang('error_possible_sql_injection', $relative, $num + 1), 9, $file);
                break;
            }
        }
    }

    private function checkDeprecatedApis(array &$results, string $contents, string $relative, string $file): void
    {
        $deprecated = [
            // pattern => [lang_key, severity]
            '/echo\s+\$this\s*->\s*ProcessTemplate\s*\(/'
                => ['warning_deprecated_process_template', 6],
            '/\$gCms\s*->\s*GetDb\s*\(/'
                => ['warning_deprecated_gcms_getdb', 6],
            '/\bcreate_event\s*\(/'
                => ['warning_deprecated_create_event', 5],
        ];

        foreach ($deprecated as $pattern => [$lang_key, $severity]) {
            if (preg_match($pattern, $contents)) {
                $results[] = $this->warning($lang_key,
                    $this->mod->Lang($lang_key, $relative), $severity, $file);
            }
        }
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
