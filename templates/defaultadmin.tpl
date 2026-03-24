<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <h3 style="margin:0;">{$mod->Lang('module_settings')}</h3>
  <a href="https://pixelsolutions.biz" target="_blank" rel="noopener noreferrer">
    <img src="https://pixelsolution.s3.eu-south-1.amazonaws.com/logos/LOGO_3_COLOR_300.png" alt="Pixel Solutions" style="height:40px;" />
  </a>
</div>

{form_start}
<div class="pageoverflow" style="margin-bottom:15px;">
  <div style="display:flex;gap:10px;align-items:center;">
    <select name="{$actionid}module_name" style="min-width:300px;">
      <option value="">-- {$mod->Lang('select_module')} --</option>
      {foreach $module_list as $key => $label}
        <option value="{$key}" {if $key == $selected_module}selected{/if}>{$label}</option>
      {/foreach}
    </select>
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('run_check')}" />
  </div>
</div>

<div style="display:flex;gap:40px;margin-bottom:20px;">
  <div>
    <h4 style="margin:0 0 5px;">{$mod->Lang('categories')}</h4>
    {foreach $all_categories as $slug => $label}
      <label style="display:block;margin-bottom:3px;">
        <input type="checkbox" name="{$actionid}cat_{$slug}" value="1"
          {if in_array($slug, $selected_categories)} checked{/if} /> {$label}
      </label>
    {/foreach}
  </div>
  <div>
    <h4 style="margin:0 0 5px;">{$mod->Lang('types')}</h4>
    {foreach $all_types as $slug => $label}
      <label style="display:block;margin-bottom:3px;">
        <input type="checkbox" name="{$actionid}type_{$slug}" value="1"
          {if in_array($slug, $selected_types)} checked{/if} /> {$label}
      </label>
    {/foreach}
  </div>
</div>
{form_end}

{if $selected_module}
  <h4>{$mod->Lang('check_results', $selected_module)}</h4>

  {* Score card *}
  <div style="display:flex;gap:20px;align-items:center;margin-bottom:20px;padding:15px;border-radius:6px;
    {if $verdict == 'pass'}background:#dff0d8;border:1px solid #d6e9c6;
    {elseif $verdict == 'warning'}background:#fcf8e3;border:1px solid #faebcc;
    {else}background:#f2dede;border:1px solid #ebccd1;{/if}">
    <div style="text-align:center;min-width:80px;">
      <div style="font-size:36px;font-weight:bold;line-height:1;
        {if $score >= 80}color:#3c763d;
        {elseif $score >= 60}color:#8a6d3b;
        {else}color:#a94442;{/if}">{$score}</div>
      <div style="font-size:11px;color:#666;">{$mod->Lang('score_out_of')}</div>
    </div>
    <div>
      <div style="font-size:18px;font-weight:bold;
        {if $verdict == 'pass'}color:#3c763d;
        {elseif $verdict == 'warning'}color:#8a6d3b;
        {else}color:#a94442;{/if}">
        {if $verdict == 'pass'}&#10004; {$mod->Lang('verdict_pass')}
        {elseif $verdict == 'warning'}&#9888; {$mod->Lang('verdict_warning')}
        {else}&#10008; {$mod->Lang('verdict_fail')}{/if}
      </div>
      <div style="font-size:13px;color:#555;margin-top:3px;">
        {if $error_count > 0}<span style="color:#a94442;font-weight:bold;">{$mod->Lang('errors_found', $error_count)}</span>{/if}
        {if $error_count > 0 && $warning_count > 0} &mdash; {/if}
        {if $warning_count > 0}<span style="color:#8a6d3b;font-weight:bold;">{$mod->Lang('warnings_found', $warning_count)}</span>{/if}
        {if $error_count == 0 && $warning_count == 0}{$mod->Lang('no_issues_found')}{/if}
      </div>
    </div>
  </div>

  {* Results table *}
  {if count($results) > 0}
    <table class="pagetable">
      <thead>
        <tr>
          <th style="width:60px;">{$mod->Lang('severity')}</th>
          <th style="width:70px;">{$mod->Lang('type')}</th>
          <th>{$mod->Lang('message')}</th>
          <th>{$mod->Lang('file')}</th>
        </tr>
      </thead>
      <tbody>
      {foreach $results as $r}
        <tr style="{if $r.type == 'error'}background:#f2dede;{else}background:#fcf8e3;{/if}">
          <td style="text-align:center;">
            <span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:11px;font-weight:bold;color:#fff;
              {if $r.severity >= 8}background:#d9534f;
              {elseif $r.severity >= 6}background:#f0ad4e;
              {elseif $r.severity >= 4}background:#5bc0de;
              {else}background:#999;{/if}">{$r.severity}</span>
          </td>
          <td>
            {if $r.type == 'error'}
              <span style="color:#a94442;font-weight:bold;">{$mod->Lang('error')}</span>
            {else}
              <span style="color:#8a6d3b;">{$mod->Lang('warning')}</span>
            {/if}
          </td>
          <td>{$r.message}</td>
          <td style="font-size:11px;color:#666;word-break:break-all;">
            {if !empty($r.file)}{$r.file|basename}{/if}
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {/if}
{/if}