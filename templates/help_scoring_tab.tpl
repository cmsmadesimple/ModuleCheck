<h3>{$mod->Lang('help_tab_scoring')}</h3>

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
      <td><span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:bold;color:#fff;background:#d9534f;">8-9</span></td>
      <td>Critical</td>
      <td>SQL injection, missing access guards, obfuscated code</td>
    </tr>
    <tr>
      <td><span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:bold;color:#fff;background:#f0ad4e;">6-7</span></td>
      <td>Important</td>
      <td>Missing permission checks, deprecated global $gCms, raw superglobals</td>
    </tr>
    <tr>
      <td><span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:bold;color:#fff;background:#5bc0de;">4-5</span></td>
      <td>Minor</td>
      <td>Deprecated APIs, missing recommended methods, missing moduleinfo fields</td>
    </tr>
    <tr>
      <td><span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:bold;color:#fff;background:#999;">1-3</span></td>
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
      <td><span style="color:#3c763d;font-weight:bold;">&#10004; PASSED</span></td>
      <td>No errors, no warnings</td>
    </tr>
    <tr>
      <td><span style="color:#8a6d3b;font-weight:bold;">&#9888; PASSED WITH WARNINGS</span></td>
      <td>No errors, but has warnings</td>
    </tr>
    <tr>
      <td><span style="color:#a94442;font-weight:bold;">&#10008; FAILED</span></td>
      <td>Any errors present <strong>OR</strong> score below 70</td>
    </tr>
  </tbody>
</table>

<p><strong>Note:</strong> A module with only warnings can still fail if the cumulative score drops below 70.</p>
