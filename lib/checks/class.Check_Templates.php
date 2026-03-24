<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_Templates extends AbstractCheck
{
    public function getCategories(): array { return ['security', 'best_practices']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $tpl_files = $this->getTplFiles($module_path);
        if (!$tpl_files) return $results;

        foreach ($tpl_files as $file) {
            $contents = $this->getFileContents($file);
            if (!$contents) continue;
            $relative = str_replace($module_path . DIRECTORY_SEPARATOR, '', $file);

            // {php} or {/php} tags forbidden
            if (preg_match('/\{\/?\s*php\s*\}/', $contents)) {
                $results[] = $this->error('template_php_tag',
                    $this->mod->Lang('error_template_php_tag', $relative), 9, $file);
            }

            // {include_php} forbidden
            if (preg_match('/\{include_php\b/', $contents)) {
                $results[] = $this->error('template_include_php',
                    $this->mod->Lang('error_template_include_php', $relative), 9, $file);
            }

            // Direct DB access in templates
            if (preg_match('/\$db\s*->\s*(Execute|GetRow|GetOne|GetAll|GetCol|SelectLimit)\s*\(/', $contents)) {
                $results[] = $this->error('template_db_access',
                    $this->mod->Lang('error_template_db_access', $relative), 8, $file);
            }

            // {fetch} can load remote files
            if (preg_match('/\{fetch\b/', $contents)) {
                $results[] = $this->warning('template_fetch',
                    $this->mod->Lang('warning_template_fetch', $relative), 6, $file);
            }

            // {insert} deprecated
            if (preg_match('/\{insert\b/', $contents)) {
                $results[] = $this->warning('template_insert',
                    $this->mod->Lang('warning_template_insert', $relative), 6, $file);
            }

            // ASP-style tags <% %>
            if (preg_match('/<%.*%>/', $contents)) {
                $results[] = $this->error('template_asp_tags',
                    $this->mod->Lang('error_template_asp_tags', $relative), 8, $file);
            }

            // $smarty.foreach.name (deprecated loop syntax)
            if (preg_match('/\$smarty\.foreach\./', $contents)) {
                $results[] = $this->warning('template_deprecated_foreach',
                    $this->mod->Lang('warning_template_deprecated_foreach', $relative), 5, $file);
            }

            // $smarty.section.name (deprecated section syntax)
            if (preg_match('/\$smarty\.section\./', $contents)) {
                $results[] = $this->warning('template_deprecated_section',
                    $this->mod->Lang('warning_template_deprecated_section', $relative), 5, $file);
            }

            // {$var|php_func} modifier — removed in Smarty 5
            if (preg_match('/\{\$\w+\|[a-z_]+\s*\}/', $contents)) {
                // Check if modifier is a raw PHP function (not a known Smarty modifier)
                $known_modifiers = ['escape','truncate','strip_tags','nl2br','capitalize','lower','upper','spacify',
                    'date_format','string_format','replace','regex_replace','default','count','cat','indent',
                    'wordwrap','strip','count_characters','count_paragraphs','count_sentences','count_words'];
                if (preg_match_all('/\{\$\w+\|([a-z_]+)/', $contents, $matches)) {
                    foreach (array_unique($matches[1]) as $modifier) {
                        if (!in_array($modifier, $known_modifiers) && function_exists($modifier)) {
                            $results[] = $this->warning('template_php_modifier',
                                $this->mod->Lang('warning_template_php_modifier', $modifier, $relative), 6, $file);
                            break;
                        }
                    }
                }
            }
        }

        return $results;
    }

    private function getTplFiles(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'tpl') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}
