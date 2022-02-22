<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-xl-4">
    <div class="card widget widget-stats">
      <div class="card-body">
        <div class="widget-stats-container d-flex">
          <div class="widget-stats-icon widget-stats-icon-primary">
            <i class="material-icons-outlined">paid</i>
          </div>
          <div class="widget-stats-content flex-fill">
            <span class="widget-stats-title">Aylık Satış</span>
            <span class="widget-stats-amount"><?= $monthlySales['thisMonth']['total_price'] != '' ? $monthlySales['thisMonth']['total_price'] : '₺ 0,00' ?></span>
            <span class="widget-stats-info">Bu ay <strong><?= $monthlySales['thisMonth']['total_sales'] ?></strong> sipariş eklendi.</span>
          </div>
          <div class="widget-stats-indicator widget-stats-indicator-<?= $monthlySales['sign'] ? 'positive' : 'negative' ?> align-self-start d-flex">
            <i class="material-icons"><?= $monthlySales['sign'] ? 'keyboard_arrow_up' : 'keyboard_arrow_down' ?></i> <?= $monthlySales['percentage'] ?>%
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card widget widget-stats">
      <div class="card-body">
        <div class="widget-stats-container d-flex">
          <div class="widget-stats-icon widget-stats-icon-warning">
            <i class="material-icons-outlined">person</i>
          </div>
          <div class="widget-stats-content flex-fill">
            <span class="widget-stats-title">Aktif Müşteri</span>
            <span class="widget-stats-amount"><?= number_format($activeCustomer['allTime'], 0) ?></span>
            <span class="widget-stats-info">Bu ay <strong><?= number_format($activeCustomer['thisMonth'], 0) ?></strong> müşteri eklendi.</span>
          </div>
          <div class="widget-stats-indicator widget-stats-indicator-<?= $activeCustomer['sign'] ? 'positive' : 'negative' ?> align-self-start d-flex">
            <i class="material-icons"><?= $activeCustomer['sign'] ? 'keyboard_arrow_up' : 'keyboard_arrow_down' ?></i> <?= $activeCustomer['percentage'] ?>%
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card widget widget-stats">
      <div class="card-body">
        <div class="widget-stats-container d-flex">
          <div class="widget-stats-icon widget-stats-icon-danger">
            <i class="material-icons-outlined">file_download</i>
          </div>
          <div class="widget-stats-content flex-fill">
            <span class="widget-stats-title">Toplam Ürün Sayısı</span>
            <span class="widget-stats-amount"><?= number_format($totalProduct['total_product']['value']) ?></span>
            <span class="widget-stats-info">Bu ay <strong><?= number_format($totalProduct['increment']['value']) ?></strong> yeni ürün eklendi</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <?php if (isAdmin()) : ?>
    <div class="col-md-4">
      <div class="card widget">
        <div class="card-header">
          <div class="card-title">Doğum Günleri <sup>(<?= $birthdays['count'] ?>)</sup></div>
        </div>
        <div class="card-body">
          <?php if ($birthdays['customers']) : ?>
            <ul class="list-group">
              <?php foreach ($birthdays['customers'] as $k => $v) : ?>
                <li class="list-group-item"><?= $v['name'] ?></li>
              <?php endforeach; ?>
              <li class="list-group-item  d-flex">
                <button class="sendBirthday btn btn-primary m-r-xs btn-sm w-100" data-type="email">
                  Tümüne Mail Gönder
                </button>
                <button class="sendBirthday btn btn-info m-l-xs btn-sm w-100" data-type="sms">Tümüne SMS Gönder</button>
              </li>
            </ul>
          <?php else : ?>
            <span class="badge badge-warning">Bugün doğum günü olan kayıtlı müşteri bulunmamaktadır.</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <?php if (isAdmin()) : ?>
    <div class="col-md-8">
      <div class="card widget">
        <div class="card-header d-flex align-items-center mb-2">
          <div class="card-title">Onaylanmamış Siparişler <sup>(<?= $orders['count'] ?>)</sup></div>
          <div class="card-subtitle" style="margin-left: auto">
            <a href="<?= site_url(route_to('orders')) . '?is_admin_confirm=true' ?>" class="btn btn-sm btn-primary">Tümünü Görüntüle</a>
          </div>
        </div>
        <div class="card-body">
          <div class="list-group orders">
            <?php foreach ($orders['data'] as $k => $v) : ?>
              <div href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1">#<?= $v['slug'] ?> - <?= $v['name'] ?></h5>
                  <small><?= $v['created_at'] ?></small>
                </div>
                <p class="mb-1">Satış danışmanı <strong><?= $v['full_name'] ?></strong> oluşturulan <strong><?= $v['total_price'] ?></strong> değerindeki sipariş.</p>
                <a href="<?= site_url(route_to('edit_order', $v['id'])) ?>" class="btn btn-sm btn-outline-info">İncele</a>
                <button class="btn btn-sm btn-outline-success" data-type="confirm" data-id="<?= $v['id'] ?>">Onayla</button>
                <button class="btn btn-sm btn-outline-danger" data-type="cancel" data-id="<?= $v['id'] ?>">Sil</button>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= site_url('assets/plugins/apexcharts/apexcharts.css') ?>">
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token() ?>';
</script>
<?= $this->endsection(); ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('assets/plugins/apexcharts/apexcharts.min.js') ?>"></script>
<?php if (isAdmin()) : ?>
  <script>
    $(document).on('click', '.sendBirthday', function(e) {
      e.preventDefault();
      let button = $(this);
      let buttonText = button.html();
      let messageType = button.data('type');

      swal("Toplu doğum günü bildirimi gönderilsin mi?", {
        buttons: {
          cancel: 'İptal',
          confirm: 'Tamam',
        },
        icon: 'warning',
      }).then((value) => {
        if (value == true) {
          $.ajax({
            url: window.location.href,
            type: 'POST',
            data: {
              messageType: messageType,
              [csrfName]: csrfHash,
            },
            beforeSend: function() {
              button.attr('disabled', 'disabled');
              button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            complete: function() {
              button.removeAttr('disabled');
              button.html(buttonText);
            },
            success: function(data) {
              csrfName = data.csrfName;
              csrfHash = data.csrfHash;
              swal("Başarıyla gönderildi!", {
                icon: 'success',
                button: 'Tamam',
                timer: 2000,
              })
            },
            error: function(jqXHR, status, error) {
              swal('Bir hata oluştu! Lütfen sayfayı yenileyin.', {
                button: 'Tamam',
                icon: 'error',
                dangerMode: true,
              });
              csrfName = jqXHR.responseJSON.csrfName;
              csrfHash = jqXHR.responseJSON.csrfHash;
            },
          });
        }
      });
    });
    $(document).on('click', '.orders button', function(e) {
      e.preventDefault();
      const button = {
        element: $(this),
      };
      button.text = button.element.html();
      button.type = button.element.data('type');
      button.id = button.element.data('id');
      const currentListGroup = button.element.closest('.list-group-item');
      console.log(currentListGroup);
      swal("Emin misiniz?", {
        buttons: {
          cancel: 'İptal',
          confirm: 'Tamam',
        },
        icon: 'warning',
      }).then((value) => {
        if (value == true) {
          $.ajax({
            url: '<?= site_url(route_to('orders')) ?>',
            type: 'POST',
            data: {
              request_type: button.type,
              order_id: button.id,
              [csrfName]: csrfHash,
            },
            beforeSend: function() {
              button.element.attr('disabled', 'disabled');
              button.element.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            complete: function() {
              button.element.removeAttr('disabled');
              button.element.html(button.text);
            },
            success: function(data) {
              console.log(data);
              csrfName = data.csrfName;
              csrfHash = data.csrfHash;
              currentListGroup.remove();
              swal("Başarıyla gönderildi!", {
                icon: 'success',
                button: 'Tamam',
                timer: 2000,
              });
            },
            error: function(jqXHR, status, error) {
              swal(jqXHR.responseJSON.error, {
                button: 'Tamam',
                icon: 'error',
                dangerMode: true,
              });
              csrfName = jqXHR.responseJSON.csrfName;
              csrfHash = jqXHR.responseJSON.csrfHash;
            },
          });
        }
      })
    })
  </script>
<?php endif; ?>
<?= $this->endsection();
