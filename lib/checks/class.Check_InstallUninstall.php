<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_InstallUninstall extends AbstractCheck
{
    public function getCategories(): array { return ['general']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $ds = DIRECTORY_SEPARATOR;

        // Check method.install.php
        $install_file = $module_path . $ds . 'method.install.php';
        if (is_file($install_file)) {
            $contents = $this->getFileContents($install_file);

            // Check for CreatePermission
            if (!preg_match('/CreatePermission\s*\(/', $contents)) {
                $results[] = $this->warning('install_no_permissions',
                    $this->mod->Lang('warning_install_no_permissions'), 4, $install_file);
            }

            // Check for access guard
            if (!$this->hasAccessGuard($contents)) {
                $results[] = $this->error('install_no_access_guard',
                    $this->mod->Lang('error_missing_access_guard', 'method.install.php'), 7, $install_file);
            }
        }

        // Check method.uninstall.php
        $uninstall_file = $module_path . $ds . 'method.uninstall.php';
        if (is_file($uninstall_file)) {
            $contents = $this->getFileContents($uninstall_file);

            // Check for RemovePermission
            $install_contents = is_file($install_file) ? $this->getFileContents($install_file) : '';
            if (preg_match('/CreatePermission\s*\(/', $install_contents)) {
                if (!preg_match('/RemovePermission\s*\(/', $contents)) {
                    $results[] = $this->warning('uninstall_no_remove_permissions',
                        $this->mod->Lang('warning_uninstall_no_remove_permissions'), 5, $uninstall_file);
                }
            }

            // Check for DropTableSQL (if install creates tables)
            if (preg_match('/CreateTableSQL\s*\(/', $install_contents)) {
                if (!preg_match('/DropTableSQL\s*\(/', $contents)) {
                    $results[] = $this->warning('uninstall_no_drop_tables',
                        $this->mod->Lang('warning_uninstall_no_drop_tables'), 6, $uninstall_file);
                }
            }

            // Check for access guard
            if (!$this->hasAccessGuard($contents)) {
                $results[] = $this->error('uninstall_no_access_guard',
                    $this->mod->Lang('error_missing_access_guard', 'method.uninstall.php'), 7, $uninstall_file);
            }
        }

        // Check UninstallPreMessage exists (recommended)
        if ($module && !method_exists($module, 'UninstallPreMessage')) {
            $results[] = $this->warning('missing_uninstall_pre_message',
                $this->mod->Lang('warning_missing_uninstall_pre_message'), 3);
        } else if ($module) {
            try {
                $msg = $module->UninstallPreMessage();
                if (empty($msg)) {
                    $results[] = $this->warning('empty_uninstall_pre_message',
                        $this->mod->Lang('warning_empty_uninstall_pre_message'), 3);
                }
            } catch (\Exception $e) {}
        }

        return $results;
    }

    private function hasAccessGuard(string $contents): bool
    {
        $lines = explode("\n", $contents);
        $beginning = implode("\n", array_slice($lines, 0, 20));

        $patterns = [
            '/if\s*\(\s*!\s*isset\s*\(\s*\$gCms\s*\)\s*\)\s*(exit|die)\s*;/i',
            '/if\s*\(\s*!\s*defined\s*\(\s*[\'"]CMS_VERSION[\'"]\s*\)\s*\)\s*(exit|die)\s*;/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $beginning)) return true;
        }
        return false;
    }
}
