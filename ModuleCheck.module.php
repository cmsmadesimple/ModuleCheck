<?php
#--------------------------------------------------
# Module: ModuleCheck
# Author: Pixel Solutions
# Copyright: (C) 2025 Pixel Solutions, info@pixelsolutions.biz
# Licence: GNU General Public License version 3
# See LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;

class ModuleCheck extends CMSModule
{
    const MANAGE_PERM = 'manage_modulecheck';

    public function GetName() { return 'ModuleCheck'; }
    public function GetFriendlyName() { return $this->Lang('friendlyname'); }
    public function GetVersion() { return '1.0.0'; }
    public function MinimumCMSVersion() { return '2.2.1'; }
    public function GetAdminDescription() { return $this->Lang('admindescription'); }
    public function IsPluginModule() { return FALSE; }
    public function HasAdmin() { return TRUE; }
    public function GetAuthor() { return 'Pixel Solutions'; }
    public function GetAuthorEmail() { return 'info@pixelsolutions.biz'; }
    public function GetAdminSection() { return 'extensions'; }
    public function GetDependencies() { return []; }
    public function UninstallPreMessage() { return $this->Lang('ask_uninstall'); }

    public function VisibleToAdminUser()
    {
        return $this->CheckPermission(self::MANAGE_PERM);
    }

    
    public function __construct(){
		spl_autoload_register( array($this, '_autoloader') );
		
		parent::__construct();

    }

	private function _autoloader($classname)
	{
		$parts = explode('\\', $classname);
		$classname = end($parts);
		$base = $this->GetModulePath() . DIRECTORY_SEPARATOR . 'lib';
		$filename = 'class.' . $classname . '.php';

		$fn = $base . DIRECTORY_SEPARATOR . $filename;
		if (file_exists($fn)) { require_once($fn); return; }

		$fn = $base . DIRECTORY_SEPARATOR . 'checks' . DIRECTORY_SEPARATOR . $filename;
		if (file_exists($fn)) { require_once($fn); }
	}

    public function GetHelp()
    {
        $smarty = cmsms()->GetSmarty();
        $mod = $this;
        $tpl = $smarty->CreateTemplate($this->GetTemplateResource('help.tpl'), null, null, $smarty);
        $tpl->assign('mod', $mod);
        return $tpl->fetch();
    }

    public function GetChangeLog()
    {
        return '<ul><li>Version 1.0.0 - Initial release with 13 automated checks, category/type filtering, and scoring system.</li></ul>';
    }

    public function InitializeAdmin() {}
    public function InitializeFrontend() {}

    /**
     * Get list of installed modules available for checking.
     */
    public function GetInstalledModules(): array
    {
        $modops = \ModuleOperations::get_instance();
        $modules = $modops->GetInstalledModules();
        $result = [];

        foreach ($modules as $module_name) {
            $mod = \cms_utils::get_module($module_name);
            if (!is_object($mod)) continue;
            $result[$module_name] = $mod;
        }

        ksort($result);
        return $result;
    }

    /**
     * Run all checks on a given module.
     */
    public function RunChecks(string $module_name, array $categories = [], array $types = []): array
    {
        $module_path = CMS_ROOT_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module_name;

        if (!is_dir($module_path)) {
            return [['type' => 'error', 'code' => 'module_not_found', 'message' => $this->Lang('error_module_not_found', $module_name), 'severity' => 9]];
        }

        $mod = \cms_utils::get_module($module_name);
        $results = [];

        foreach (glob(__DIR__ . '/lib/checks/class.Check_*.php') as $file) {
            $class_name = 'ModuleCheck\\' . str_replace('class.', '', basename($file, '.php'));
            if (!class_exists($class_name)) continue;

            $check = new $class_name($this);

            // Filter by categories
            if (!empty($categories) && !array_intersect($check->getCategories(), $categories)) continue;

            $check_results = $check->run($module_name, $module_path, $mod);

            // Filter by types
            if (!empty($types)) {
                $check_results = array_filter($check_results, fn($r) => in_array($r['type'], $types));
            }

            $results = array_merge($results, $check_results);
        }

        usort($results, fn($a, $b) => ($b['severity'] ?? 0) <=> ($a['severity'] ?? 0));
        return $results;
    }
}
