<?= view('layouts/static/head'); ?>
<?= view('layouts/static/sidebar'); ?>
<?= view('layouts/static/header'); ?>

<div class="content-wrapper">
  <div class="row">
    <div class="col">
      <div class="page-description d-flex align-items-center">
        <div class="page-description-content flex-grow-1">
          <h1><?= $page_title ?? '' ?></h1>
          <span><?= $page_desc ?? '' ?></span>
        </div>
        <?php if (isset($page_buttons)) : ?>
          <div class="page-description-actions">
            <?php foreach ($page_buttons as $key => $value) : ?>
              <a href="<?= isset($value['route']) ? site_url(route_to($value['route'])) : (isset($value['href']) ? $value['href'] : '') ?>" class="<?= $value['class'] ?>" <?= isset($value['id']) ? 'id="' . $value['id'] . '"' : '' ?>>
                <i class="<?= $value['icon']['class'] ?>"><?= $value['icon']['name'] ?></i><?= $value['text'] ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?= $this->renderSection('content') ?>
</div>

<?= view('layouts/static/footer'); ?>