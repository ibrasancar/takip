<?= $this->extend('layouts/order') ?>
<?= $this->section('content') ?>
<div class="row">

  <?php if (isset($success)) : ?>
    <div class="alert alert-success small mt-2 p-2 text-center">
      <?= $success ?>
    </div>
  <?php endif; ?>
  <div class="row">
    <div class="col-md-12 text-center mt-5">
      <img src="<?= getenv('app.companyLogo') ?>" alt="">
    </div>
  </div>
  <div class="card invoice" style="padding: 0">
    <div class="card-body">
      <div class="invoice-header">
        <div class="row">
          <div class="col-md-12">
            <h3><?= $manufacturer['name'] ?> için #<?= $manufacturer['order_value'] ?></h3>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <table class="table invoice-table text-center table-responsive">
            <thead>
              <tr>
                <th scope="col">Adet</th>
                <th scope="col">Ürün Adı</th>
                <th scope="col">Tahmini Teslimat Tarihi</th>
                <th scope="col">Durum</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order_products as $k => $v) : ?>
                <tr>
                  <td class="text-center"><span class="badge badge-light"><?= $v['quantity'] ?></span></td>
                  <td><?= $v['product_name'] ?> <img src="<?= $v['product_image'] ?>" alt="<?= $v['product_name'] ?> için görsel"></td>
                  <td><?= $v['estimated_delivery'] ?></td>
                  <td><?= $v['status'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <div class="row">
        <div class="col-md-4">
          <div class="invoice-info">
            <p>Sipariş Yetkilisi: <span><strong><?= $salesman['full_name'] ?></strong></span></p>
            <p>Telefon: <span><strong><?= $salesman['phone'] ?></strong></span></p>
            <p>E-Posta: <span><strong><?= $salesman['email'] ?></strong></span></p>
          </div>
        </div>
        <div class="col-md-4">

        </div>
        <div class="col-md-4">
          <div class="invoice-info">
            <p>Firma Adı: <span><strong><?= getenv('app.companyName') ?></strong></span></p>
            <p>Telefon: <span><strong><?= getenv('app.companyPhone') ?></strong></span></p>
            <p>E-Posta: <span><strong><?= getenv('app.companyEmail') ?></strong></span></p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mt-2">
          <button class="btn btn-outline btn-outline-primary w-100" id="print" type="button"><i class="material-icons">print</i> Yazdır</button>
        </div>
        <div class="col-md-6 mt-2">
          <button class="btn btn-outline btn-outline-warning w-100" id="pdf" type="button"><i class="material-icons">picture_as_pdf</i> PDF</button>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-12 text-center">
          <small class="text-muted">Sipariş hakkında geri bildirimleriniz için lütfen yetkililer ile iletişime geçin.</small>
        </div>
      </div>
    </div>
  </div>

</div>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token() ?>';
</script>
<style>
  @media (max-width: 992px) {
    table.table.invoice-table.text-center.table-responsive {
      width: 100%;
      overflow-x: scroll;
      display: block;
      padding-bottom: 24px;
    }
  }
</style>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('assets/plugins/sweetalert.min.js') ?>"></script>
<script src="<?= site_url('assets/js/html2pdf.js') ?>"></script>

<script>
  $('#pdf').on('click', function(e) {
    $("button, a").hide();
    let element = document.querySelector('#invoice');
    let opt = {
      margin: 0,
      filename: 'invoice-<?= $order['slug'] ?>',
      jsPDF: {
        align: 'center',
        autoPrint: true,
      },
      image: {
        type: 'jpeg',
        'quality': 1
      },
      html2canvas: {
        backgroundColor: '#fff',
        scale: 2,
      }
    };
    html2pdf(element, opt).then((value) => {
      $("button, a").show();
    });
  });
  $('#print').on('click', function(e) {
    $("button, a").hide();
    $(".card-footer").attr("style", "background: #fff;");
    let element = document.querySelector('#invoice');
    let opt = {
      margin: 0,
      filename: 'invoice-<?= $order['slug'] ?>',
      jsPDF: {
        align: 'center',
        autoPrint: true,
      },
      image: {
        type: 'jpeg',
        'quality': 1
      },
      html2canvas: {
        backgroundColor: '#fff',
        scale: 2,
      }
    };
    html2pdf().from(element).set(opt).toPdf().get('pdf').then(function(pdf) {
      pdf.autoPrint();
      window.open(pdf.output('bloburl'), '_blank');
      $("button, a").show();
      $(".card-footer").attr("style", "background: #f4f7fa");
    });
  });
</script>
<?= $this->endsection() ?>