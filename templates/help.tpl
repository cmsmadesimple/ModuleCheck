<style>
.help-tabs { margin: 20px 0 0; border-bottom: 2px solid #ddd; }
.help-tabs a { display: inline-block; padding: 10px 20px; margin-right: 5px; background: #f5f5f5; border: 1px solid #ddd; border-bottom: none; text-decoration: none; color: #333; }
.help-tabs a.active { background: #fff; border-bottom: 2px solid #fff; margin-bottom: -2px; font-weight: bold; }
.help-tabs a:hover { background: #e9e9e9; }
.help-content { padding: 20px; border: 1px solid #ddd; border-top: none; background: #fff; }
.help-section { display: none; }
.help-section.active { display: block; }
</style>

<div class="help-tabs">
  <a href="#overview" class="help-tab active" data-tab="overview">{$mod->Lang('help_tab_overview')}</a>
  <a href="#checks" class="help-tab" data-tab="checks">{$mod->Lang('help_tab_checks')}</a>
  <a href="#scoring" class="help-tab" data-tab="scoring">{$mod->Lang('help_tab_scoring')}</a>
  <a href="#changelog" class="help-tab" data-tab="changelog">{$mod->Lang('help_tab_changelog')}</a>
</div>

<div class="help-content">
  <div id="overview" class="help-section active">
    {include file='module_file_tpl:ModuleCheck;help_overview_tab.tpl'}
  </div>

  <div id="checks" class="help-section">
    {include file='module_file_tpl:ModuleCheck;help_checks_tab.tpl'}
  </div>

  <div id="scoring" class="help-section">
    {include file='module_file_tpl:ModuleCheck;help_scoring_tab.tpl'}
  </div>

  <div id="changelog" class="help-section">
    {include file='module_file_tpl:ModuleCheck;help_changelog_tab.tpl'}
  </div>
</div>

<script>
(function() {
  var tabs = document.querySelectorAll('.help-tab');
  var sections = document.querySelectorAll('.help-section');

  tabs.forEach(function(tab) {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      var target = this.getAttribute('data-tab');

      tabs.forEach(function(t) { t.classList.remove('active'); });
      sections.forEach(function(s) { s.classList.remove('active'); });

      this.classList.add('active');
      document.getElementById(target).classList.add('active');
    });
  });
})();
</script>
