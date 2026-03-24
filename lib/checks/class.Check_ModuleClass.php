<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_ModuleClass extends AbstractCheck
{
    const REQUIRED_METHODS = [
        'GetName'             => 'Returns the module name identifier',
        'GetFriendlyName'     => 'Returns the human-readable module name',
        'GetVersion'          => 'Returns the module version string',
        'GetAuthor'           => 'Returns the module author name',
        'GetAuthorEmail'      => 'Returns the module author email',
        'MinimumCMSVersion'   => 'Returns the minimum CMS version required',
        'HasAdmin'            => 'Indicates if the module has an admin interface',
        'IsPluginModule'      => 'Indicates if the module is a frontend plugin',
        'GetAdminDescription' => 'Returns a short admin description',
    ];

    const RECOMMENDED_METHODS = [
        'GetAdminSection'     => 'Returns the admin menu section',
        'GetHelp'             => 'Returns help text for the module',
        'GetChangeLog'        => 'Returns the module changelog',
        'GetDependencies'     => 'Returns module dependencies',
        'VisibleToAdminUser'  => 'Controls admin visibility based on permissions',
    ];

    public function getCategories(): array { return ['general']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $main_file = $module_path . DIRECTORY_SEPARATOR . $module_name . '.module.php';

        // Check main module file exists
        if (!is_file($main_file)) {
            $results[] = $this->error('missing_module_file',
                $this->mod->Lang('error_missing_module_file', $module_name . '.module.php'), 9);
            return $results;
        }

        $contents = $this->getFileContents($main_file);

        // Check class declaration extends CMSModule
        if (!preg_match('/class\s+' . preg_quote($module_name) . '\s+extends\s+CMSModule/i', $contents)) {
            $results[] = $this->error('invalid_class_declaration',
                $this->mod->Lang('error_invalid_class_declaration', $module_name), 9, $main_file);
        }

        // Check class name matches module directory name
        if (preg_match('/class\s+(\w+)\s+extends\s+CMSModule/i', $contents, $m)) {
            if ($m[1] !== $module_name) {
                $results[] = $this->error('class_name_mismatch',
                    $this->mod->Lang('error_class_name_mismatch', $m[1], $module_name), 8, $main_file);
            }
        }

        if (!$module) {
            $results[] = $this->error('module_not_loadable',
                $this->mod->Lang('error_module_not_loadable', $module_name), 9);
            return $results;
        }

        // Check required methods exist and return non-empty values
        foreach (self::REQUIRED_METHODS as $method => $desc) {
            if (!method_exists($module, $method)) {
                $results[] = $this->error('missing_required_method',
                    $this->mod->Lang('error_missing_required_method', $method, $desc), 8, $main_file);
                continue;
            }

            try {
                $value = $module->$method();
                if ($method === 'HasAdmin' || $method === 'IsPluginModule') continue;

                if (empty($value) && $value !== '0' && $value !== false) {
                    $results[] = $this->error('empty_required_method',
                        $this->mod->Lang('error_empty_required_method', $method), 7, $main_file);
                }
            } catch (\Exception $e) {
                // Method exists but throws — skip
            }
        }

        // Check recommended methods
        foreach (self::RECOMMENDED_METHODS as $method => $desc) {
            if (!method_exists($module, $method)) {
                $results[] = $this->warning('missing_recommended_method',
                    $this->mod->Lang('warning_missing_recommended_method', $method, $desc), 4, $main_file);
            }
        }

        // Validate GetName matches directory name
        try {
            $name = $module->GetName();
            if ($name !== $module_name) {
                $results[] = $this->error('getname_mismatch',
                    $this->mod->Lang('error_getname_mismatch', $name, $module_name), 8, $main_file);
            }
        } catch (\Exception $e) {}

        // Validate version format
        try {
            $version = $module->GetVersion();
            if (!preg_match('/^\d+\.\d+(\.\d+)?([.-]?(alpha|beta|rc|dev)\d*)?$/i', $version)) {
                $results[] = $this->warning('invalid_version_format',
                    $this->mod->Lang('warning_invalid_version_format', $version), 6, $main_file);
            }
        } catch (\Exception $e) {}

        // Validate author email
        try {
            $email = $module->GetAuthorEmail();
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results[] = $this->warning('invalid_author_email',
                    $this->mod->Lang('warning_invalid_author_email', $email), 5, $main_file);
            }
        } catch (\Exception $e) {}

        // Validate MinimumCMSVersion format
        try {
            $min_cms = $module->MinimumCMSVersion();
            if (!empty($min_cms) && !preg_match('/^\d+\.\d+(\.\d+)?$/', $min_cms)) {
                $results[] = $this->warning('invalid_min_cms_version',
                    $this->mod->Lang('warning_invalid_min_cms_version', $min_cms), 5, $main_file);
            }
        } catch (\Exception $e) {}

        return $results;
    }
}
