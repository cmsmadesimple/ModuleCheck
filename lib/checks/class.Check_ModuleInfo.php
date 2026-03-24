<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_ModuleInfo extends AbstractCheck
{
    public function getCategories(): array { return ['general']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $ini_file = $module_path . DIRECTORY_SEPARATOR . 'moduleinfo.ini';

        if (!is_file($ini_file)) {
            $results[] = $this->warning('missing_moduleinfo_ini',
                $this->mod->Lang('warning_missing_moduleinfo_ini'), 6);
            return $results;
        }

        $ini = @parse_ini_file($ini_file, true);
        if ($ini === false) {
            $results[] = $this->error('invalid_moduleinfo_ini',
                $this->mod->Lang('error_invalid_moduleinfo_ini'), 7, $ini_file);
            return $results;
        }

        $section = $ini['module'] ?? $ini;

        if (!$module) return $results;

        // Check name matches
        if (isset($section['name'])) {
            try {
                $class_name = $module->GetName();
                if ($section['name'] !== $class_name) {
                    $results[] = $this->error('moduleinfo_name_mismatch',
                        $this->mod->Lang('error_moduleinfo_mismatch', 'name', $section['name'], $class_name), 7, $ini_file);
                }
            } catch (\Exception $e) {}
        } else {
            $results[] = $this->warning('moduleinfo_missing_name',
                $this->mod->Lang('warning_moduleinfo_missing_field', 'name'), 6, $ini_file);
        }

        // Check version matches
        if (isset($section['version'])) {
            try {
                $class_version = $module->GetVersion();
                if ($section['version'] !== $class_version) {
                    $results[] = $this->error('moduleinfo_version_mismatch',
                        $this->mod->Lang('error_moduleinfo_mismatch', 'version', $section['version'], $class_version), 8, $ini_file);
                }
            } catch (\Exception $e) {}
        } else {
            $results[] = $this->warning('moduleinfo_missing_version',
                $this->mod->Lang('warning_moduleinfo_missing_field', 'version'), 6, $ini_file);
        }

        // Check author matches
        if (isset($section['author'])) {
            try {
                $class_author = $module->GetAuthor();
                if ($section['author'] !== $class_author) {
                    $results[] = $this->warning('moduleinfo_author_mismatch',
                        $this->mod->Lang('error_moduleinfo_mismatch', 'author', $section['author'], $class_author), 5, $ini_file);
                }
            } catch (\Exception $e) {}
        }

        // Check mincmsversion matches
        if (isset($section['mincmsversion'])) {
            try {
                $class_min = $module->MinimumCMSVersion();
                if ($section['mincmsversion'] !== $class_min) {
                    $results[] = $this->warning('moduleinfo_mincms_mismatch',
                        $this->mod->Lang('error_moduleinfo_mismatch', 'mincmsversion', $section['mincmsversion'], $class_min), 5, $ini_file);
                }
            } catch (\Exception $e) {}
        }

        return $results;
    }
}
