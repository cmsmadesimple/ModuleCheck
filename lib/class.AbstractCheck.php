<?php
#--------------------------------------------------
# See LICENSE for full license information.
#--------------------------------------------------
namespace ModuleCheck;

abstract class AbstractCheck
{
    protected $mod;

    public function __construct(\ModuleCheck $mod)
    {
        $this->mod = $mod;
    }

    /**
     * Run the check against a module.
     *
     * @param string          $module_name Module name
     * @param string          $module_path Absolute path to module directory
     * @param \CMSModule|null $module      Module instance (null if not loadable)
     * @return array Array of findings, each: ['type'=>'error'|'warning', 'code'=>string, 'message'=>string, 'severity'=>int, 'file'=>string]
     */
    abstract public function run(string $module_name, string $module_path, ?\CMSModule $module): array;

    /**
     * Return array of category slugs this check belongs to.
     */
    abstract public function getCategories(): array;

    protected function error(string $code, string $message, int $severity = 7, string $file = ''): array
    {
        return ['type' => 'error', 'code' => $code, 'message' => $message, 'severity' => $severity, 'file' => $file, 'check' => static::class];
    }

    protected function warning(string $code, string $message, int $severity = 5, string $file = ''): array
    {
        return ['type' => 'warning', 'code' => $code, 'message' => $message, 'severity' => $severity, 'file' => $file, 'check' => static::class];
    }

    /**
     * Read file contents with caching.
     */
    protected function getFileContents(string $path): ?string
    {
        static $cache = [];
        if (!isset($cache[$path])) {
            $cache[$path] = is_file($path) ? file_get_contents($path) : null;
        }
        return $cache[$path];
    }
}
