=== Module Check ===
Contributors: pixelsolutions
Tags: linter, diagnostics, modules, security, best-practices
Requires at least: 2.2.16
Tested up to: 2.2.22
Stable tag: 1.0.0
Requires PHP: 8.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Scans installed CMSMS modules for best practices, security issues, and structural compliance.

== Description ==

Module Check is a diagnostic tool that scans installed CMS Made Simple modules for compliance with best practices, security patterns, deprecated API usage, and structural requirements. Think of it as a code linter built right into the CMS admin panel.

**14 automated checks** across 3 categories:

* **General** — Module Class, Module Structure, Module Info, Install/Uninstall
* **Security** — File Headers, Admin Permissions, Security, Templates, Code Obfuscation
* **Best Practices** — File Types, Localhost, PRG Pattern, Deprecated PHP, Method Signatures

Each module receives a score from 0–100 with a pass/warning/fail verdict.

== Installation ==

1. Upload the `ModuleCheck` folder to the `modules/` directory.
2. Install via Extensions > Module Manager.
3. Grant the "Manage Module Check" permission to admin users.
4. Navigate to Extensions > Module Check.

== Frequently Asked Questions ==

= How does the scoring work? =

Every module starts at 100 points. Each unique error deducts severity × 2 points, each unique warning deducts severity × 1 point. A score below 70 or any errors results in a fail verdict.

= Does this module modify other modules? =

No. Module Check is read-only. It scans files but never modifies them.

== Screenshots ==

1. Main admin interface with module selector, category/type filters, and score card.
2. Detailed results table showing findings with severity, type, message, and file.

== Changelog ==

= 1.0.0 =
* Initial release.
* 14 automated checks across 3 categories (General, Security, Best Practices).
* Category and type filtering.
* Scoring system with pass/warning/fail verdicts.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
