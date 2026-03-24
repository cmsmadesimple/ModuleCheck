<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if (!$this->CheckPermission(ModuleCheck::MANAGE_PERM)) return;

$modules = $this->GetInstalledModules();

$all_categories = [
    'general'        => $this->Lang('cat_general'),
    'security'       => $this->Lang('cat_security'),
    'best_practices' => $this->Lang('cat_best_practices'),
];
$all_types = [
    'error'   => $this->Lang('type_error'),
    'warning' => $this->Lang('type_warning'),
];

$module_list = [];
foreach ($modules as $name => $mod_obj) {
    $module_list[$name] = $name;
    try { $module_list[$name] = $name . ' (' . $mod_obj->GetVersion() . ')'; } catch (\Exception $e) {}
}

// Build check list with display names for JS progress
$check_classes = $this->GetCheckClasses();
$check_names = [];
foreach ($check_classes as $cls) {
    $key = 'check_name_' . strtolower($cls);
    $check_names[$cls] = $this->Lang($key);
}

$smarty = cmsms()->GetSmarty();
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('defaultadmin.tpl'), null, null, $smarty);
$tpl->assign('mod', $this);
$tpl->assign('module_list', $module_list);
$tpl->assign('all_categories', $all_categories);
$tpl->assign('all_types', $all_types);
$tpl->assign('check_classes', json_encode($check_classes));
$tpl->assign('check_names', json_encode($check_names));
$tpl->display();
