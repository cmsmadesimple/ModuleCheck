<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

require_once __DIR__ . '/../class.AbstractCheck.php';

class Check_FileTypes extends AbstractCheck
{
    const COMPRESSED_EXTENSIONS = ['zip', 'gz', 'tgz', 'rar', 'tar', '7z'];
    const APPLICATION_EXTENSIONS = ['exe', 'bin', 'dmg', 'iso', 'sh', 'so', 'o', 'obj', 'phar', 'dll'];
    const VCS_DIRECTORIES = ['.git', '.svn', '.hg', '.bzr'];
    const AI_DIRECTORIES = ['.cursor', '.claude', '.aider', '.continue', '.windsurf', '.ai'];

    const SKIP_DIRECTORIES = ['releases', 'vendor', 'node_modules', '.git', '.svn', '.hg', '.bzr'];

    public function getCategories(): array { return ['best_practices']; }

    public function run(string $module_name, string $module_path, ?\CMSModule $module): array
    {
        $results = [];
        $all_files = $this->getAllFiles($module_path);

        foreach ($all_files as $file) {
            $relative = str_replace($module_path . DIRECTORY_SEPARATOR, '', $file);
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $basename = basename($file);

            // Compressed files
            if (in_array($ext, self::COMPRESSED_EXTENSIONS)) {
                $results[] = $this->error('compressed_file',
                    $this->mod->Lang('error_compressed_file', $relative), 8, $file);
            }

            // Application/binary files
            if (in_array($ext, self::APPLICATION_EXTENSIONS)) {
                $results[] = $this->error('application_file',
                    $this->mod->Lang('error_application_file', $relative), 8, $file);
            }

            // Hidden files (starting with .) — allow .gitignore, .distignore
            if (strpos($basename, '.') === 0 && $basename !== '.' && $basename !== '..' 
                && !in_array($basename, ['.gitignore', '.distignore'])) {
                $results[] = $this->warning('hidden_file',
                    $this->mod->Lang('warning_hidden_file', $relative), 6, $file);
            }
        }

        // VCS directories
        foreach (self::VCS_DIRECTORIES as $vcs) {
            if (is_dir($module_path . DIRECTORY_SEPARATOR . $vcs)) {
                $results[] = $this->warning('vcs_directory',
                    $this->mod->Lang('warning_vcs_directory', $vcs), 6);
            }
        }

        // AI instruction directories
        foreach (self::AI_DIRECTORIES as $ai) {
            if (is_dir($module_path . DIRECTORY_SEPARATOR . $ai)) {
                $results[] = $this->warning('ai_directory',
                    $this->mod->Lang('warning_ai_directory', $ai), 7);
            }
        }

        return $results;
    }

    private function getAllFiles(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            // Skip known non-code directories
            if ($item->isDir() && in_array($item->getFilename(), self::SKIP_DIRECTORIES)) {
                $iterator->next();
                continue;
            }
            if ($item->isFile()) $files[] = $item->getPathname();
        }
        return $files;
    }
}
