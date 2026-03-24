<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <h3 style="margin:0;">{$mod->Lang('module_settings')}</h3>
  <a href="https://pixelsolutions.biz" target="_blank" rel="noopener noreferrer">
    <img src="https://pixelsolution.s3.eu-south-1.amazonaws.com/logos/LOGO_3_COLOR_300.png" alt="Pixel Solutions" style="height:40px;" />
  </a>
</div>

<div class="pageoverflow" style="margin-bottom:15px;">
  <div style="display:flex;gap:10px;align-items:center;">
    <select id="mc-module-select" style="min-width:300px;">
      <option value="">-- {$mod->Lang('select_module')} --</option>
      {foreach $module_list as $key => $label}
        <option value="{$key}">{$label}</option>
      {/foreach}
    </select>
    <input type="button" id="mc-run-btn" value="{$mod->Lang('run_check')}" />
  </div>
</div>

<div style="display:flex;gap:40px;margin-bottom:20px;">
  <div>
    <h4 style="margin:0 0 5px;">{$mod->Lang('categories')}</h4>
    {foreach $all_categories as $slug => $label}
      <label style="display:block;margin-bottom:3px;">
        <input type="checkbox" class="mc-cat" value="{$slug}" checked /> {$label}
      </label>
    {/foreach}
  </div>
  <div>
    <h4 style="margin:0 0 5px;">{$mod->Lang('types')}</h4>
    {foreach $all_types as $slug => $label}
      <label style="display:block;margin-bottom:3px;">
        <input type="checkbox" class="mc-type" value="{$slug}" checked /> {$label}
      </label>
    {/foreach}
  </div>
</div>

{* Progress bar *}
<div id="mc-progress" style="display:none;margin-bottom:20px;">
  <div style="background:#e9ecef;border-radius:4px;height:24px;overflow:hidden;margin-bottom:6px;">
    <div id="mc-progress-fill" style="height:100%;width:0%;background:#5cb85c;border-radius:4px;transition:width 0.3s;"></div>
  </div>
  <div id="mc-progress-text" style="font-size:13px;color:#555;">
    <span class="ui-icon ui-icon-loading" style="display:inline-block;vertical-align:middle;"></span>
    <span id="mc-progress-label"></span>
  </div>
</div>

{* Results area (populated by JS) *}
<div id="mc-results" style="display:none;"></div>

<script>
(function() {
  var checkClasses = {$check_classes};
  var checkNames = {$check_names};
  var ajaxUrl = '{cms_action_url action="ajax_check" forjs=1}';
  var actionId = '{$actionid}';

  $('#mc-run-btn').on('click', function() {
    var moduleName = $('#mc-module-select').val();
    if (!moduleName) return;

    var cats = [];
    $('.mc-cat:checked').each(function() { cats.push($(this).val()); });
    var types = [];
    $('.mc-type:checked').each(function() { types.push($(this).val()); });

    var btn = $(this);
    btn.prop('disabled', true);
    $('#mc-results').hide().empty();
    $('#mc-progress').show();
    $('#mc-progress-fill').css('width', '0%');

    var allResults = [];
    var idx = 0;

    function runNext() {
      if (idx >= checkClasses.length) {
        $('#mc-progress').hide();
        btn.prop('disabled', false);
        renderResults(moduleName, allResults);
        return;
      }

      var cls = checkClasses[idx];
      var label = checkNames[cls] || cls;
      var pct = Math.round((idx / checkClasses.length) * 100);
      $('#mc-progress-fill').css('width', pct + '%');
      $('#mc-progress-label').text(label + '…');

      var postData = {};
      postData[actionId + 'module_name'] = moduleName;
      postData[actionId + 'check_class'] = cls;
      for (var i = 0; i < cats.length; i++) {
        postData[actionId + 'categories[' + i + ']'] = cats[i];
      }
      for (var i = 0; i < types.length; i++) {
        postData[actionId + 'types[' + i + ']'] = types[i];
      }

      $.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(resp) {
          if (resp.success && resp.results) {
            allResults = allResults.concat(resp.results);
          }
          idx++;
          runNext();
        },
        error: function() {
          idx++;
          runNext();
        }
      });
    }

    runNext();
  });

  function renderResults(moduleName, results) {
    // Sort by severity desc
    results.sort(function(a, b) { return (b.severity || 0) - (a.severity || 0); });

    // Calculate score
    var score = 100, errors = 0, warnings = 0, seen = {};
    for (var i = 0; i < results.length; i++) {
      var r = results[i];
      if (r.type === 'error') {
        errors++;
        if (!seen[r.code]) { score -= (r.severity || 5) * 2; seen[r.code] = true; }
      } else {
        warnings++;
        if (!seen[r.code]) { score -= (r.severity || 3); seen[r.code] = true; }
      }
    }
    score = Math.max(0, Math.min(100, score));

    var verdict;
    if (errors > 0 || score < 70) verdict = 'fail';
    else if (warnings > 0) verdict = 'warning';
    else verdict = 'pass';

    var verdictColors = { pass: '#3c763d', warning: '#8a6d3b', fail: '#a94442' };
    var verdictBg = { pass: 'background:#dff0d8;border:1px solid #d6e9c6;', warning: 'background:#fcf8e3;border:1px solid #faebcc;', fail: 'background:#f2dede;border:1px solid #ebccd1;' };
    var scoreColor = score >= 80 ? '#3c763d' : (score >= 60 ? '#8a6d3b' : '#a94442');
    var verdictIcon = verdict === 'pass' ? '&#10004;' : (verdict === 'warning' ? '&#9888;' : '&#10008;');
    var verdictLabel = verdict === 'pass' ? '{$mod->Lang('verdict_pass')}' : (verdict === 'warning' ? '{$mod->Lang('verdict_warning')}' : '{$mod->Lang('verdict_fail')}');

    var html = '<h4>{$mod->Lang('check_results', '')}'.replace('', moduleName) + '</h4>';

    // Score card
    html += '<div style="display:flex;gap:20px;align-items:center;margin-bottom:20px;padding:15px;border-radius:6px;' + verdictBg[verdict] + '">';
    html += '<div style="text-align:center;min-width:80px;">';
    html += '<div style="font-size:36px;font-weight:bold;line-height:1;color:' + scoreColor + ';">' + score + '</div>';
    html += '<div style="font-size:11px;color:#666;">{$mod->Lang('score_out_of')}</div></div>';
    html += '<div><div style="font-size:18px;font-weight:bold;color:' + verdictColors[verdict] + ';">' + verdictIcon + ' ' + verdictLabel + '</div>';
    html += '<div style="font-size:13px;color:#555;margin-top:3px;">';
    if (errors > 0) html += '<span style="color:#a94442;font-weight:bold;">' + errors + ' error(s) found</span>';
    if (errors > 0 && warnings > 0) html += ' &mdash; ';
    if (warnings > 0) html += '<span style="color:#8a6d3b;font-weight:bold;">' + warnings + ' warning(s) found</span>';
    if (errors === 0 && warnings === 0) html += '{$mod->Lang('no_issues_found')}';
    html += '</div></div></div>';

    // Results table
    if (results.length > 0) {
      html += '<table class="pagetable"><thead><tr>';
      html += '<th style="width:60px;">{$mod->Lang('severity')}</th>';
      html += '<th style="width:70px;">{$mod->Lang('type')}</th>';
      html += '<th>{$mod->Lang('message')}</th>';
      html += '<th>{$mod->Lang('file')}</th>';
      html += '</tr></thead><tbody>';
      for (var i = 0; i < results.length; i++) {
        var r = results[i];
        var rowBg = r.type === 'error' ? 'background:#f2dede;' : 'background:#fcf8e3;';
        var sevBg = r.severity >= 8 ? '#d9534f' : (r.severity >= 6 ? '#f0ad4e' : (r.severity >= 4 ? '#5bc0de' : '#999'));
        var typeLabel = r.type === 'error'
          ? '<span style="color:#a94442;font-weight:bold;">{$mod->Lang('error')}</span>'
          : '<span style="color:#8a6d3b;">{$mod->Lang('warning')}</span>';
        var fileName = r.file ? r.file.replace(/^.*[\\\/]/, '') : '';
        html += '<tr style="' + rowBg + '">';
        html += '<td style="text-align:center;"><span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:bold;color:#fff;background:' + sevBg + ';">' + r.severity + '</span></td>';
        html += '<td>' + typeLabel + '</td>';
        html += '<td>' + (r.message || '') + '</td>';
        html += '<td style="font-size:11px;color:#666;word-break:break-all;">' + fileName + '</td>';
        html += '</tr>';
      }
      html += '</tbody></table>';
    }

    $('#mc-results').html(html).show();
  }
})();
</script>
