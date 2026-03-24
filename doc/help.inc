<h3>Overview</h3>

<p>Module Check is a diagnostic tool that scans installed CMS Made Simple modules for compliance with best practices, security patterns, deprecated API usage, and structural requirements. Think of it as a code linter built right into the CMS admin panel.</p>

<h4>Quick Start</h4>
<ol>
  <li>Navigate to <strong>Extensions &gt; Module Check</strong></li>
  <li>Select a module from the dropdown</li>
  <li>Optionally filter by <strong>Categories</strong> (General, Security, Best Practices) and <strong>Types</strong> (Error, Warning)</li>
  <li>Click <strong>Check it!</strong></li>
  <li>Review the score card and detailed findings</li>
</ol>

<h4>System Requirements</h4>
<ul>
  <li>CMS Made Simple 2.2.1 or higher</li>
  <li>PHP 8.2 or higher</li>
</ul>

<hr />

<h3>Checks</h3>

<p>Module Check runs <strong>14 automated checks</strong> organized into three categories.</p>

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
      <td>SQL injection via string interpolation, <code>global $gCms</code>, raw $_POST/$_GET/$_REQUEST access, and deprecated APIs.</td>
    </tr>
    <tr>
      <td><strong>Templates</strong></td>
      <td>Forbidden Smarty tags: {php}, {include_php}, ASP-style tags. Deprecated: {insert}, $smarty.foreach / $smarty.section syntax, raw PHP function modifiers. Also detects direct database access and {fetch} usage.</td>
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
      <td>Deprecated PHP functions: split(), ereg(), mysql_*(), each(). Deprecated CMSMS APIs: GetModuleInstance(), CreateURL(). Deprecated {} string offset syntax.</td>
    </tr>
    <tr>
      <td><strong>Method Signatures</strong></td>
      <td>Wrong return types for module methods, invalid GetAdminSection values, invalid dependency formats, HasAdmin/IsPluginModule cross-checks.</td>
    </tr>
  </tbody>
</table>

<h4>Excluded Directories</h4>
<p>The file type scanner automatically skips these directories: <code>releases/</code>, <code>vendor/</code>, <code>node_modules/</code>, and VCS directories.</p>

<hr />

<h3>Scoring</h3>

<h4>How Scoring Works</h4>
<p>Every module starts with a score of <strong>100</strong>. Points are deducted for each <em>unique</em> issue found:</p>

<table class="pagetable">
  <thead><tr><th>Finding Type</th><th>Deduction</th></tr></thead>
  <tbody>
    <tr><td><strong>Error</strong></td><td>severity &times; 2</td></tr>
    <tr><td><strong>Warning</strong></td><td>severity &times; 1</td></tr>
  </tbody>
</table>

<p>Duplicate findings with the same issue code are only counted once for scoring purposes (though all instances are listed in the results table).</p>

<h4>Severity Levels</h4>
<table class="pagetable">
  <thead><tr><th>Severity</th><th>Meaning</th><th>Example</th></tr></thead>
  <tbody>
    <tr>
      <td><strong>8-9</strong></td>
      <td>Critical</td>
      <td>SQL injection, missing access guards, obfuscated code</td>
    </tr>
    <tr>
      <td><strong>6-7</strong></td>
      <td>Important</td>
      <td>Missing permission checks, deprecated global $gCms, raw superglobals</td>
    </tr>
    <tr>
      <td><strong>4-5</strong></td>
      <td>Minor</td>
      <td>Deprecated APIs, missing recommended methods, missing moduleinfo fields</td>
    </tr>
    <tr>
      <td><strong>1-3</strong></td>
      <td>Info</td>
      <td>Missing optional files, empty UninstallPreMessage</td>
    </tr>
  </tbody>
</table>

<h4>Verdict</h4>
<table class="pagetable">
  <thead><tr><th>Verdict</th><th>Condition</th></tr></thead>
  <tbody>
    <tr>
      <td><strong>&#10004; PASSED</strong></td>
      <td>No errors, no warnings</td>
    </tr>
    <tr>
      <td><strong>&#9888; PASSED WITH WARNINGS</strong></td>
      <td>No errors, but has warnings</td>
    </tr>
    <tr>
      <td><strong>&#10008; FAILED</strong></td>
      <td>Any errors present <strong>OR</strong> score below 70</td>
    </tr>
  </tbody>
</table>

<p><strong>Note:</strong> A module with only warnings can still fail if the cumulative score drops below 70.</p>

<hr />

<h4>Credits</h4>
<p>Developed by <strong>Pixel Solutions</strong> | <a href="mailto:info@pixelsolutions.biz">info@pixelsolutions.biz</a> | <a href="https://pixelsolutions.biz" target="_blank">pixelsolutions.biz</a></p>
