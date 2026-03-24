<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_ModuleStructure extends AbstractCheck
{
    public function getCategories(): array { return ['general']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $ds = DIRECTORY_SEPARATOR;

        // method.install.php
        if (!is_file($module_path . $ds . 'method.install.php')) {
            $results[] = $this->error('missing_install_method',
                $this->mod->Lang('error_missing_file', 'method.install.php'), 7);
        }

        // method.uninstall.php
        if (!is_file($module_path . $ds . 'method.uninstall.php')) {
            $results[] = $this->error('missing_uninstall_method',
                $this->mod->Lang('error_missing_file', 'method.uninstall.php'), 7);
        }

        // lang directory and en_US.php
        $lang_dir = $module_path . $ds . 'lang';
        if (!is_dir($lang_dir)) {
            $results[] = $this->error('missing_lang_directory',
                $this->mod->Lang('error_missing_directory', 'lang/'), 7);
        } else {
            if (!is_file($lang_dir . $ds . 'en_US.php')) {
                $results[] = $this->error('missing_lang_en_us',
                    $this->mod->Lang('error_missing_file', 'lang/en_US.php'), 7);
            } else {
                $this->checkLangFile($results, $lang_dir . $ds . 'en_US.php', $module_name);
            }
        }

        // action.defaultadmin.php (if HasAdmin)
        $has_admin = false;
        if ($module) {
            try { $has_admin = $module->HasAdmin(); } catch (\Exception $e) {}
        }

        if ($has_admin) {
            if (!is_file($module_path . $ds . 'action.defaultadmin.php')) {
                $results[] = $this->error('missing_defaultadmin_action',
                    $this->mod->Lang('error_missing_file_admin', 'action.defaultadmin.php'), 7);
            }

            // templates directory
            if (!is_dir($module_path . $ds . 'templates')) {
                $results[] = $this->warning('missing_templates_directory',
                    $this->mod->Lang('warning_missing_directory', 'templates/'), 5);
            }
        }

        // action.default.php (if IsPluginModule)
        $is_plugin = false;
        if ($module) {
            try { $is_plugin = $module->IsPluginModule(); } catch (\Exception $e) {}
        }

        if ($is_plugin) {
            if (!is_file($module_path . $ds . 'action.default.php')) {
                $results[] = $this->warning('missing_default_action',
                    $this->mod->Lang('warning_missing_file_plugin', 'action.default.php'), 5);
            }
        }

        // doc/LICENSE (required)
        $doc_dir = $module_path . $ds . 'doc';
        if (!is_dir($doc_dir)) {
            $results[] = $this->error('missing_doc_directory',
                $this->mod->Lang('error_missing_directory', 'doc/'), 7);
        } elseif (!is_file($doc_dir . $ds . 'LICENSE')) {
            $results[] = $this->error('missing_license_file',
                $this->mod->Lang('error_missing_file', 'doc/LICENSE'), 7);
        }

        // method.upgrade.php (recommended)
        if (!is_file($module_path . $ds . 'method.upgrade.php')) {
            $results[] = $this->warning('missing_upgrade_method',
                $this->mod->Lang('warning_missing_file', 'method.upgrade.php'), 3);
        }

        return $results;
    }

    private function checkLangFile(array &$results, string $file, string $module_name): void
    {
        $contents = $this->getFileContents($file);
        if (!$contents) return;

        // Check $lang array is used
        if (strpos($contents, '$lang[') === false) {
            $results[] = $this->error('invalid_lang_file',
                $this->mod->Lang('error_invalid_lang_file'), 7, $file);
            return;
        }

        // Check for friendlyname key
        if (!preg_match('/\$lang\s*\[\s*[\'"]friendlyname[\'"]\s*\]/', $contents)) {
            $results[] = $this->warning('missing_lang_friendlyname',
                $this->mod->Lang('warning_missing_lang_key', 'friendlyname'), 5, $file);
        }

        // Check for admindescription key (if has admin)
        if (!preg_match('/\$lang\s*\[\s*[\'"](?:admindescription|description|moddescription)[\'"]\s*\]/', $contents)) {
            $results[] = $this->warning('missing_lang_description',
                $this->mod->Lang('warning_missing_lang_key', 'admindescription'), 4, $file);
        }
    }
}
