<div class="logo">
  <a href="<?= site_url(route_to('homepage')) ?>" class="logo-icon"><span class="logo-text">ExtraTakip</span></a>
  <div class="sidebar-user-switcher user-activity-online">
    <a href="#">
      <span class="user-info-text"><?= mb_strlen(session('name')) > 20 ? substr(session('name'), 0, 20) . '...' : session('name') ?><br /><span class="user-state-info"><?= beautifyUserType(session('user_type')) ?></span></span>
    </a>
  </div>
</div>