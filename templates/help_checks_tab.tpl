<h3>{$mod->Lang('help_tab_checks')}</h3>

<p>Module Check runs <strong>13 automated checks</strong> organized into three categories.</p>

<h4>General</h4>
<table class="pagetable">
  <thead><tr><th>Check</th><th>What it detects</th></tr></thead>
  <tbody>
    <tr>
      <td><strong>Module Class</strong></td>
      <td>Missing or empty required methods (GetName, GetVersion, GetAuthor, etc.), class name mismatches, invalid version format, invalid author email.</td>
    </tr>
    <tr>
      <td><strong>Module Structure</strong></td>
      <td>Missing required files (method.install.php, method.uninstall.php, lang/en_US.php), missing action files for admin/plugin modules, invalid language file structure.</td>
    </tr>
    <tr>
      <td><strong>Module Info</strong></td>
      <td>Missing or invalid moduleinfo.ini, mismatches between INI values and module class values (name, version, author).</td>
    </tr>
    <tr>
      <td><strong>Install / Uninstall</strong></td>
      <td>Missing CreatePermission/RemovePermission calls, tables created but not dropped on uninstall, missing access guards, missing UninstallPreMessage.</td>
    </tr>
  </tbody>
</table>

<h4>Security</h4>
<table class="pagetable">
  <thead><tr><th>Check</th><th>What it detects</th></tr></thead>
  <tbody>
    <tr>
      <td><strong>File Headers</strong></td>
      <td>Missing direct access guards (<code>if (!defined('CMS_VERSION')) exit;</code>) on action/method/module files. Missing license/copyright headers.</td>
    </tr>
    <tr>
      <td><strong>Admin Permissions</strong></td>
      <td>Admin action files without CheckPermission() calls. VisibleToAdminUser() not using permission checks.</td>
    </tr>
    <tr>
      <td><strong>Security</strong></td>
      <td>SQL injection via string interpolation, <code>global $gCms</code>, raw $_POST/$_GET/$_REQUEST access, and deprecated APIs: <code>$gCms->GetDb()</code>, <code>$smarty->assign()</code>, <code>ProcessTemplate()</code>, <code>create_event()</code>, <code>$this->GetPreference()</code>.</td>
    </tr>
    <tr>
      <td><strong>Templates</strong></td>
      <td>Forbidden Smarty tags: <code>{ldelim}php{rdelim}</code>, <code>{ldelim}include_php{rdelim}</code>, ASP-style <code>&lt;%...%&gt;</code> tags. Deprecated: <code>{ldelim}insert{rdelim}</code>, <code>$smarty.foreach</code> / <code>$smarty.section</code> syntax, raw PHP function modifiers (<code>{ldelim}$var|php_func{rdelim}</code>). Also detects direct database access and <code>{ldelim}fetch{rdelim}</code> usage in templates.</td>
    </tr>
    <tr>
      <td><strong>Code Obfuscation</strong></td>
      <td>Zend Guard, SourceGuardian, and ionCube encoded files.</td>
    </tr>
  </tbody>
</table>

<h4>Best Practices</h4>
<table class="pagetable">
  <thead><tr><th>Check</th><th>What it detects</th></tr></thead>
  <tbody>
    <tr>
      <td><strong>File Types</strong></td>
      <td>Compressed archives (.zip, .tar.gz, etc.), binary/executable files, hidden files, VCS directories (.git, .svn), AI instruction directories (.cursor, .claude, etc.).</td>
    </tr>
    <tr>
      <td><strong>Localhost</strong></td>
      <td>Hardcoded localhost, 127.0.0.1, 0.0.0.0, or [::1] URLs in PHP files.</td>
    </tr>
    <tr>
      <td><strong>PRG Pattern</strong></td>
      <td>Action files that handle form submissions but don't redirect afterwards (Post-Redirect-Get pattern).</td>
    </tr>
    <tr>
      <td><strong>Deprecated PHP</strong></td>
      <td>Deprecated PHP functions: <code>split()</code>, <code>ereg()</code>, <code>mysql_*()</code>, <code>each()</code>. Deprecated CMSMS APIs: <code>GetModuleInstance()</code>, <code>CreateURL()</code>. Deprecated <code>{ldelim}{rdelim}</code> string offset syntax.</td>
    </tr>
  </tbody>
</table>

<h4>Excluded Directories</h4>
<p>The file type scanner automatically skips these directories: <code>releases/</code>, <code>vendor/</code>, <code>node_modules/</code>, and VCS directories.</p>
