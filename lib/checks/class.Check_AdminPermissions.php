<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_AdminPermissions extends AbstractCheck
{
    public function getCategories(): array { return ['security']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];

        // Only check if module has admin
        $has_admin = false;
        if ($module) {
            try { $has_admin = $module->HasAdmin(); } catch (\Exception $e) {}
        }
        if (!$has_admin) return $results;

        // Scan all action files for permission checks
        $action_files = glob($module_path . DIRECTORY_SEPARATOR . 'action.*.php');
        if (!$action_files) return $results;

        foreach ($action_files as $file) {
            $basename = basename($file);

            // Skip frontend-only actions
            if ($basename === 'action.default.php' || $basename === 'action.detail.php') continue;

            // Admin actions should have permission checks
            if (strpos($basename, 'action.admin') !== false || $basename === 'action.defaultadmin.php') {
                $contents = $this->getFileContents($file);
                if (!$contents) continue;

                if (!$this->hasPermissionCheck($contents)) {
                    $results[] = $this->error('admin_action_no_permission_check',
                        $this->mod->Lang('error_admin_no_permission_check', $basename), 8, $file);
                }
            }
        }

        // Check VisibleToAdminUser uses permission check
        if ($module && method_exists($module, 'VisibleToAdminUser')) {
            $main_file = $module_path . DIRECTORY_SEPARATOR . $module_name . '.module.php';
            $contents = $this->getFileContents($main_file);
            if ($contents) {
                // Extract VisibleToAdminUser method body
                if (preg_match('/function\s+VisibleToAdminUser\s*\([^)]*\)\s*\{([^}]+)\}/s', $contents, $m)) {
                    $body = $m[1];
                    if (strpos($body, 'CheckPermission') === false) {
                        $results[] = $this->warning('visible_no_permission_check',
                            $this->mod->Lang('warning_visible_no_permission_check'), 6, $main_file);
                    }
                }
            }
        }

        return $results;
    }

    private function hasPermissionCheck(string $contents): bool
    {
        return (bool) preg_match('/CheckPermission\s*\(/', $contents);
    }
}
