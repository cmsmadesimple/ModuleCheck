<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if (!$this->CheckPermission(ModuleCheck::MANAGE_PERM)) return;

$modules = $this->GetInstalledModules();

$selected_module = isset($params['module_name']) ? trim($params['module_name']) : '';
$results = [];

// Available categories and types
$all_categories = [
    'general'        => $this->Lang('cat_general'),
    'security'       => $this->Lang('cat_security'),
    'best_practices' => $this->Lang('cat_best_practices'),
];
$all_types = [
    'error'   => $this->Lang('type_error'),
    'warning' => $this->Lang('type_warning'),
];

// Collect selected filters (all checked by default)
$selected_categories = [];
$selected_types = [];

if (isset($params['submit'])) {
    foreach (array_keys($all_categories) as $cat) {
        if (!empty($params['cat_' . $cat])) $selected_categories[] = $cat;
    }
    foreach (array_keys($all_types) as $type) {
        if (!empty($params['type_' . $type])) $selected_types[] = $type;
    }
} else {
    $selected_categories = array_keys($all_categories);
    $selected_types = array_keys($all_types);
}

if (!empty($selected_module) && isset($modules[$selected_module])) {
    $results = $this->RunChecks($selected_module, $selected_categories, $selected_types);

    // Calculate score — deduct per unique issue code, not per duplicate
    $score = 100;
    $error_count = 0;
    $warning_count = 0;
    $seen_codes = [];
    foreach ($results as $r) {
        $code = $r['code'] ?? '';
        if ($r['type'] === 'error') {
            $error_count++;
            if (!isset($seen_codes[$code])) {
                $score -= ($r['severity'] ?? 5) * 2;
                $seen_codes[$code] = true;
            }
        } else {
            $warning_count++;
            if (!isset($seen_codes[$code])) {
                $score -= $r['severity'] ?? 3;
                $seen_codes[$code] = true;
            }
        }
    }
    $score = max(0, min(100, $score));

    // Pass: no errors and score >= 70
    // Warning: no errors but has warnings
    // Fail: any errors or score < 70
    if ($error_count > 0 || $score < 70) {
        $verdict = 'fail';
    } elseif ($warning_count > 0) {
        $verdict = 'warning';
    } else {
        $verdict = 'pass';
    }
}

$module_list = [];
foreach ($modules as $name => $mod_obj) {
    $module_list[$name] = $name;
    try { $module_list[$name] = $name . ' (' . $mod_obj->GetVersion() . ')'; } catch (\Exception $e) {}
}

$smarty = cmsms()->GetSmarty();
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('defaultadmin.tpl'), null, null, $smarty);
$tpl->assign('mod', $this);
$tpl->assign('module_list', $module_list);
$tpl->assign('selected_module', $selected_module);
$tpl->assign('results', $results);
$tpl->assign('score', $score ?? null);
$tpl->assign('verdict', $verdict ?? null);
$tpl->assign('error_count', $error_count ?? 0);
$tpl->assign('warning_count', $warning_count ?? 0);
$tpl->assign('all_categories', $all_categories);
$tpl->assign('all_types', $all_types);
$tpl->assign('selected_categories', $selected_categories);
$tpl->assign('selected_types', $selected_types);
$tpl->display();
