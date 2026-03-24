<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------

// Module info
$lang['friendlyname'] = 'Module Check';
$lang['admindescription'] = 'Checks installed modules for best practices, security, and metadata compliance.';
$lang['help'] = 'See the Help tab for full documentation.';
$lang['help_tab_overview'] = 'Overview';
$lang['help_tab_checks'] = 'Checks';
$lang['help_tab_scoring'] = 'Scoring';
$lang['help_tab_changelog'] = 'Changelog';
$lang['ask_uninstall'] = 'Are you sure you want to uninstall Module Check? No data will be lost.';

// Admin UI
$lang['module_settings'] = 'Module Check';
$lang['select_module'] = 'Select a module to check';
$lang['run_check'] = 'Check it!';
$lang['check_results'] = 'Check Results for: %s';

// Categories & Types
$lang['categories'] = 'Categories';
$lang['types'] = 'Types';
$lang['cat_general'] = 'General';
$lang['cat_security'] = 'Security';
$lang['cat_best_practices'] = 'Best Practices';
$lang['type_error'] = 'Error';
$lang['type_warning'] = 'Warning';
$lang['no_issues_found'] = 'No issues found. This module passes all checks.';
$lang['errors_found'] = '%d error(s) found';
$lang['warnings_found'] = '%d warning(s) found';
$lang['score_out_of'] = '/ 100';
$lang['verdict_pass'] = 'PASSED';
$lang['verdict_warning'] = 'PASSED WITH WARNINGS';
$lang['verdict_fail'] = 'FAILED';
$lang['severity'] = 'Severity';
$lang['type'] = 'Type';
$lang['message'] = 'Message';
$lang['file'] = 'File';
$lang['error'] = 'Error';
$lang['warning'] = 'Warning';
$lang['back_to_list'] = 'Back to module list';
$lang['module_name'] = 'Module';
$lang['version'] = 'Version';
$lang['issues'] = 'Issues';
$lang['status'] = 'Status';
$lang['pass'] = 'Pass';
$lang['fail'] = 'Fail';
$lang['check'] = 'Check';

// Check: Module Class
$lang['error_missing_module_file'] = 'Main module file "%s" is missing. Every module must have a main class file named ModuleName.module.php.';
$lang['error_invalid_class_declaration'] = 'Module class "%s" does not properly extend CMSModule. The class declaration must be: class %s extends CMSModule';
$lang['error_class_name_mismatch'] = 'Class name "%s" does not match the module directory name "%s". These must be identical.';
$lang['error_module_not_loadable'] = 'Module "%s" could not be loaded. It may have syntax errors or missing dependencies.';
$lang['error_missing_required_method'] = 'Required method "%s" is missing. %s.';
$lang['error_empty_required_method'] = 'Required method "%s" returns an empty value.';
$lang['warning_missing_recommended_method'] = 'Recommended method "%s" is missing. %s.';
$lang['error_getname_mismatch'] = 'GetName() returns "%s" but the module directory is named "%s". These must match.';
$lang['warning_invalid_version_format'] = 'Version "%s" does not follow the recommended format (e.g., 1.0.0, 2.1.3-beta1).';
$lang['warning_invalid_author_email'] = 'Author email "%s" is not a valid email address.';
$lang['warning_invalid_min_cms_version'] = 'MinimumCMSVersion "%s" does not follow the expected format (e.g., 2.2.1).';

// Check: Module Info
$lang['warning_missing_moduleinfo_ini'] = 'File moduleinfo.ini is missing. This file is recommended for lazy-loading module metadata.';
$lang['error_invalid_moduleinfo_ini'] = 'File moduleinfo.ini could not be parsed. Check the INI syntax.';
$lang['error_moduleinfo_mismatch'] = 'The "%s" value in moduleinfo.ini ("%s") does not match the module class value ("%s"). These must be identical.';
$lang['warning_moduleinfo_missing_field'] = 'The "%s" field is missing from moduleinfo.ini.';

// Check: Module Structure
$lang['error_missing_file'] = 'Required file "%s" is missing.';
$lang['error_missing_file_admin'] = 'File "%s" is missing. This file is required for modules with an admin interface.';
$lang['error_missing_directory'] = 'Required directory "%s" is missing.';
$lang['warning_missing_directory'] = 'Recommended directory "%s" is missing.';
$lang['warning_missing_file'] = 'Recommended file "%s" is missing.';
$lang['warning_missing_file_plugin'] = 'File "%s" is missing. This file is expected for plugin modules with frontend output.';
$lang['error_invalid_lang_file'] = 'Language file does not contain a valid $lang array.';
$lang['warning_missing_lang_key'] = 'Language key "%s" is missing from en_US.php. This key is typically expected.';

// Check: File Headers
$lang['error_missing_access_guard'] = 'File "%s" is missing a direct access guard. Add: if (!defined(\'CMS_VERSION\')) exit; or if (!isset($gCms)) exit;';
$lang['warning_missing_license_header'] = 'File "%s" is missing a license/copyright header in the first few lines.';

// Check: Install/Uninstall
$lang['warning_install_no_permissions'] = 'method.install.php does not call CreatePermission(). Modules with admin interfaces should define permissions.';
$lang['warning_uninstall_no_remove_permissions'] = 'method.uninstall.php does not call RemovePermission(). Permissions created during install should be removed on uninstall.';
$lang['warning_uninstall_no_drop_tables'] = 'method.uninstall.php does not call DropTableSQL(). Database tables created during install should be dropped on uninstall.';
$lang['warning_missing_uninstall_pre_message'] = 'Module does not implement UninstallPreMessage(). It is recommended to warn users before uninstalling.';
$lang['warning_empty_uninstall_pre_message'] = 'UninstallPreMessage() returns an empty value.';

// Check: Admin Permissions
$lang['error_admin_no_permission_check'] = 'Admin action file "%s" does not contain a CheckPermission() call. Every admin action must verify permissions.';
$lang['warning_visible_no_permission_check'] = 'VisibleToAdminUser() does not use CheckPermission(). This method should verify the user has appropriate permissions.';

// General
$lang['error_module_not_found'] = 'Module "%s" directory was not found.';

// Check: Security
$lang['error_deprecated_global_gcms'] = 'File "%s" uses deprecated "global $gCms". Use cmsms() instead.';
$lang['warning_raw_superglobal'] = 'File "%s" accesses raw $_POST/$_GET/$_REQUEST. Use $params array in action files instead.';
$lang['error_possible_sql_injection'] = 'File "%s" (line %d) may contain SQL with direct variable interpolation. Use parameterized queries with ? placeholders.';
$lang['warning_deprecated_process_template'] = 'File "%s" uses deprecated echo $this->ProcessTemplate(). Use CreateTemplate() with GetTemplateResource() instead.';
$lang['warning_deprecated_gcms_getdb'] = 'File "%s" uses deprecated $gCms->GetDb(). Use cms_utils::get_db() instead.';
$lang['warning_deprecated_create_event'] = 'File "%s" uses deprecated create_event(). Use the modern CMSMS event system instead.';

// Check: Templates
$lang['error_template_php_tag'] = 'Template "%s" contains {php} tags. PHP code is forbidden in Smarty templates.';
$lang['error_template_include_php'] = 'Template "%s" contains {include_php}. PHP includes are forbidden in Smarty templates.';
$lang['error_template_db_access'] = 'Template "%s" contains direct database access. Templates must not access the database.';
$lang['warning_template_fetch'] = 'Template "%s" uses {fetch} which can load remote files. Verify this is intentional.';
$lang['warning_template_insert'] = 'Template "%s" uses deprecated {insert} tag. Use {include} or a registered plugin instead.';
$lang['error_template_asp_tags'] = 'Template "%s" uses ASP-style tags <%...%>. Use standard Smarty {..} delimiters instead.';
$lang['warning_template_deprecated_foreach'] = 'Template "%s" uses deprecated $smarty.foreach syntax. Use modern loop properties like {$item@index} or {$item@first} instead.';
$lang['warning_template_deprecated_section'] = 'Template "%s" uses deprecated $smarty.section syntax. Use modern {foreach} syntax instead.';
$lang['warning_template_php_modifier'] = 'Template "%s" uses raw PHP function "%s" as a Smarty modifier. This is removed in Smarty 5. Register it as a proper Smarty modifier plugin.';

// Check: Code Obfuscation
$lang['error_obfuscated_code'] = 'Code obfuscation tool "%s" detected in "%s". Obfuscated code is not permitted.';

// Check: File Types
$lang['error_compressed_file'] = 'Compressed file "%s" found. Compressed archives should not be included in modules.';
$lang['error_application_file'] = 'Application/binary file "%s" found. Executable and binary files are not permitted.';
$lang['warning_hidden_file'] = 'Hidden file "%s" found. Hidden files should not be included in production modules.';
$lang['warning_vcs_directory'] = 'Version control directory "%s" found. VCS directories should not be included in production modules.';
$lang['warning_ai_directory'] = 'AI instruction directory "%s" found. AI tool directories should not be included in production modules.';

// Check: Localhost
$lang['warning_localhost_reference'] = 'File "%s" contains a localhost/127.0.0.1 reference. Remove hardcoded local URLs before distribution.';

// Check: PRG Pattern
$lang['warning_missing_prg_redirect'] = 'Action file "%s" handles form submit but does not redirect. Use RedirectToAdminTab() after POST processing (PRG pattern).';

// Check: Deprecated PHP
$lang['warning_deprecated_php_split'] = 'File "%s" uses deprecated split(). Use explode() or preg_split() instead.';
$lang['warning_deprecated_php_ereg'] = 'File "%s" uses deprecated ereg functions. Use preg_match()/preg_replace() instead.';
$lang['warning_deprecated_php_mysql'] = 'File "%s" uses deprecated mysql_* functions. Use CMSMS database API (cms_utils::get_db()) instead.';
$lang['warning_deprecated_php_each'] = 'File "%s" uses deprecated each(). Use foreach instead.';
$lang['warning_deprecated_get_module_instance'] = 'File "%s" uses deprecated GetModuleInstance(). Use cms_utils::get_module() instead.';
$lang['warning_deprecated_create_url'] = 'File "%s" uses deprecated CreateURL(). Use create_url() instead.';
$lang['warning_deprecated_curly_offset'] = 'File "%s" uses deprecated {} string offset syntax. Use [] instead.';

// Check: Method Signatures
$lang['warning_wrong_return_type'] = 'Method %s() should return %s but returns %s.';
$lang['warning_invalid_admin_section'] = 'GetAdminSection() returns "%s" which is not a valid section. Valid sections: %s.';
$lang['warning_invalid_dependency_format'] = 'GetDependencies() should return an associative array of module_name => version_string.';
$lang['warning_invalid_dependency_version'] = 'Dependency "%s" has invalid version "%s". Expected format: 1.0 or 1.0.0.';
$lang['warning_has_admin_no_actions'] = 'HasAdmin() returns true but no admin action files were found.';
$lang['warning_is_plugin_no_default'] = 'IsPluginModule() returns true but action.default.php is missing.';
