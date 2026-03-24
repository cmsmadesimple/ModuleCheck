<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if (!$this->CheckPermission(ModuleCheck::MANAGE_PERM)) {
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}

$module_name = isset($params['module_name']) ? trim($params['module_name']) : '';
$check_class = isset($params['check_class']) ? trim($params['check_class']) : '';
$categories  = isset($params['categories']) ? (array)$params['categories'] : [];
$types       = isset($params['types']) ? (array)$params['types'] : [];

$response = ['success' => false, 'message' => ''];

if (empty($module_name) || empty($check_class)) {
    $response['message'] = 'Missing parameters';
} else {
    try {
        $results = $this->RunSingleCheck($check_class, $module_name, $categories, $types);
        $response['success'] = true;
        $response['results'] = $results;
    } catch (\Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

$handlers = ob_list_handlers();
for ($i = 0; $i < count($handlers); $i++) { ob_end_clean(); }
header('Content-Type: application/json');
echo json_encode($response);
exit;
