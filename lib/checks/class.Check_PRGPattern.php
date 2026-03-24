<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_PRGPattern extends AbstractCheck
{
    public function getCategories(): array { return ['best_practices']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $action_files = glob($module_path . DIRECTORY_SEPARATOR . 'action.*.php');
        if (!$action_files) return $results;

        foreach ($action_files as $file) {
            $contents = $this->getFileContents($file);
            if (!$contents) continue;
            $relative = basename($file);

            // Skip non-form actions
            if ($relative === 'action.default.php' || $relative === 'action.detail.php') continue;

            // If action handles submit but doesn't redirect
            $has_submit = preg_match('/\$params\s*\[\s*[\'"]submit[\'"]\s*\]/', $contents);
            if (!$has_submit) continue;

            $has_redirect = preg_match('/RedirectToAdminTab|Redirect\s*\(|->redirect/i', $contents);
            if (!$has_redirect) {
                $results[] = $this->warning('missing_prg_redirect',
                    $this->mod->Lang('warning_missing_prg_redirect', $relative), 6, $file);
            }
        }

        return $results;
    }
}
