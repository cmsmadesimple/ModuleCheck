<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_MethodSignatures extends AbstractCheck
{
    const TYPE_CHECKS = [
        'GetName'             => 'string',
        'GetFriendlyName'     => 'string',
        'GetVersion'          => 'string',
        'GetAuthor'           => 'string',
        'GetAuthorEmail'      => 'string',
        'GetAdminDescription' => 'string',
        'GetAdminSection'     => 'string',
        'MinimumCMSVersion'   => 'string',
        'HasAdmin'            => 'bool',
        'IsPluginModule'      => 'bool',
        'GetHelp'             => 'string',
        'GetChangeLog'        => 'string',
        'GetDependencies'     => 'array',
    ];

    const VALID_ADMIN_SECTIONS = [
        'content', 'layout', 'usersgroups', 'extensions',
        'siteadmin', 'myprefs', 'main',
    ];

    public function getCategories(): array { return ['best_practices']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        if (!$module) return $results;

        $main_file = $module_path . DIRECTORY_SEPARATOR . $module_name . '.module.php';

        // Check return types
        foreach (self::TYPE_CHECKS as $method => $expected_type) {
            if (!method_exists($module, $method)) continue;

            try {
                $value = $module->$method();
            } catch (\Exception $e) {
                continue;
            }

            $actual_type = gettype($value);

            if ($expected_type === 'bool' && !is_bool($value)) {
                $results[] = $this->warning('wrong_return_type',
                    $this->mod->Lang('warning_wrong_return_type', $method, 'boolean', $actual_type),
                    6, $main_file);
            } elseif ($expected_type === 'string' && !is_string($value) && $value !== null) {
                $results[] = $this->warning('wrong_return_type',
                    $this->mod->Lang('warning_wrong_return_type', $method, 'string', $actual_type),
                    6, $main_file);
            } elseif ($expected_type === 'array' && !is_array($value) && $value !== null) {
                $results[] = $this->warning('wrong_return_type',
                    $this->mod->Lang('warning_wrong_return_type', $method, 'array', $actual_type),
                    6, $main_file);
            }
        }

        // Validate GetAdminSection value
        if (method_exists($module, 'GetAdminSection')) {
            try {
                $section = $module->GetAdminSection();
                if (is_string($section) && !empty($section) && !in_array($section, self::VALID_ADMIN_SECTIONS)) {
                    $results[] = $this->warning('invalid_admin_section',
                        $this->mod->Lang('warning_invalid_admin_section', $section, implode(', ', self::VALID_ADMIN_SECTIONS)),
                        5, $main_file);
                }
            } catch (\Exception $e) {}
        }

        // Validate GetDependencies structure
        if (method_exists($module, 'GetDependencies')) {
            try {
                $deps = $module->GetDependencies();
                if (is_array($deps) && !empty($deps)) {
                    foreach ($deps as $dep_name => $dep_version) {
                        if (is_int($dep_name)) {
                            $results[] = $this->warning('invalid_dependency_format',
                                $this->mod->Lang('warning_invalid_dependency_format'),
                                6, $main_file);
                            break;
                        }
                        if (!is_string($dep_version) || (!empty($dep_version) && !preg_match('/^\d+\.\d+(\.\d+)?$/', $dep_version))) {
                            $results[] = $this->warning('invalid_dependency_version',
                                $this->mod->Lang('warning_invalid_dependency_version', $dep_name, (string)$dep_version),
                                5, $main_file);
                        }
                    }
                }
            } catch (\Exception $e) {}
        }

        // Cross-check: HasAdmin true but no admin action files
        try {
            $has_admin = $module->HasAdmin();
            if ($has_admin) {
                $admin_actions = glob($module_path . DIRECTORY_SEPARATOR . 'action.*admin*.php');
                if (empty($admin_actions)) {
                    $results[] = $this->warning('has_admin_no_actions',
                        $this->mod->Lang('warning_has_admin_no_actions'),
                        5, $main_file);
                }
            }
        } catch (\Exception $e) {}

        // Cross-check: IsPluginModule true but no frontend action
        try {
            $is_plugin = $module->IsPluginModule();
            if ($is_plugin && !is_file($module_path . DIRECTORY_SEPARATOR . 'action.default.php')) {
                $results[] = $this->warning('is_plugin_no_default',
                    $this->mod->Lang('warning_is_plugin_no_default'),
                    5, $main_file);
            }
        } catch (\Exception $e) {}

        return $results;
    }
}
