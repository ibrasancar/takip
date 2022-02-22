<div class="app-menu">
  <ul class="accordion-menu">
    <?php foreach (get_menu(session('user_type')) as $key => $item) : ?>
      <?php if (is_array($item)) : ?>
        <li class="sidebar-title"><?= $key ?></li>
        <?php foreach ($item as $main => $main_item) : ?>
          <li <?= isset($active) && $active[0] == $main ? 'class="active-page"' : '' ?>>
            <a href="<?= site_url(route_to($main)) ?>">
              <?= $main_item['icon']['two_tone'] ? '<i class="material-icons-two-tone">' . $main_item['icon']['name'] . '</i>' : '<i class="material-icons">' . $main_item['icon']['name'] . '</i>' ?>
              <?= $main_item['name'] ?>
              <?= isset($main_item['sub-menu']) ? '<i class="material-icons has-sub-menu">keyboard_arrow_right</i>' : '' ?>
            </a>
            <?php if (isset($main_item['sub-menu'])) : ?>
              <ul class="sub-menu">
                <?php foreach ($main_item['sub-menu'] as $s_key => $s_item) : ?>
                  <li <?= isset($active[1]) && $active[1] == $s_key ? 'class="bold"' : '' ?>><a href="<?= site_url(route_to($s_key)) ?>"><?= $s_item ?></a></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</div>