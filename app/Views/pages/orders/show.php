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
  <div class="card invoice" id="invoice" style="padding: 0">
    <div class="card-body">
      <div class="invoice-header">
        <div class="row">
          <div class="col-md-6">
            <h3>Sipariş #<?= $order['slug'] ?></h3>
          </div>
          <div class="col-md-6">
            <span class="invoice-issue-date">Oluşturma Tarihi: <?= $order['created_at'] ?></span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <p class="invoice-description"><?= $order['order_note'] ?></p>
        </div>
      </div>
      <div class="row">
        <div class="table-responsive">
          <table class="table invoice-table text-center">
            <thead>
              <tr>
                <th scope="col">Adet</th>
                <th scope="col">Ürün Adı</th>
                <th scope="col">Toplam Fiyat</th>
                <th scope="col">Tahmini Teslimat Tarihi</th>
                <th scope="col">Durum</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order_products as $k => $v) : ?>
                <tr>
                  <td><?= $v['quantity'] ?></td>
                  <td><?= $v['product_name'] ?><img src="<?= $v['image'] ?>" alt="<?= $v['product_name'] ?> ürün görseli"></td>
                  <td class="price"><?= $v['price'] ?></td>
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
      <div class="row invoice-summary">
        <div class="col-lg-5">
          <div class="invoice-info">
            <p>Sipariş Numarası: <span>#<?= $order['slug'] ?></span></p>
            <p>Oluşturma Tarihi: <span><?= $order['created_at'] ?></span></p>
            <p>Bitiş Tarihi: <span><?= $order['completed_at'] ?></span></p>
            <div class="row">
              <div class="col-md-6 mt-2">
                <button class="btn btn-outline btn-outline-primary w-100" id="print" type="button"><i class="material-icons">print</i> Yazdır</button>
              </div>
              <div class="col-md-6 mt-2">
                <button class="btn btn-outline btn-outline-warning w-100" id="pdf" type="button"><i class="material-icons">picture_as_pdf</i> PDF</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3"></div>
        <div class="col-lg-4">
          <div class="invoice-info">
            <p>Ara Toplam <span id="subtotal"></span></p>
            <p>Kapora <span><?= $order['deposit'] ?></span></p>
            <p>İndirim <span><?= $order['discount'] ?></span></p>
            <p class="bold">Toplam <span><?= $order['total_price'] ?></span></p>
            <?php if (empty($order['customer_confirm'])) : ?>
              <div class="invoice-info-actions">
                <a href="#" class="btn btn-primary" id="complete_order" type="button" data-slug="<?= $order['slug'] ?>">Siparişi Onayla</a>
              </div>
            <?php else : ?>
              <div class="invoice-info-actions">
                <span class="badge badge-success">Sipariş Müşteri Tarafından Onaylanmış</span>
              </div>
            <?php endif; ?>
          </div>
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

<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('assets/plugins/sweetalert.min.js') ?>"></script>
<script src="<?= site_url('assets/js/html2pdf.js') ?>"></script>
<script>
  function fixPrice(price) {
    return price.toString().replace(".", ",");
  }

  function clearPrice(price) {
    price = price.toString();
    return price.replace(".", "").replace(",", ".").replace("₺", "");
  }

  function calcSubTotal() {
    let subtotal = 0;
    $("table.invoice-table td.price").each(function(index) {
      let price = parseFloat(clearPrice($(this).html()));
      subtotal += price;
    });
    return subtotal;
  }
  $("#subtotal").html(new Intl.NumberFormat('tr', {
    style: 'currency',
    currency: 'TRY'
  }).format(calcSubTotal()));
  $("#total").val(fixPrice(calcSubTotal()));

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

  $(document).on('click', "#complete_order", function(e) {
    e.preventDefault();
    swal("Siparişin eksiksiz bir şekilde eklendiğini ve size ait olduğunu onaylıyor musunuz?", {
      icon: 'warning',
      buttons: {
        cancel: 'Hayır',
        approve: 'Evet',
      }
    }).then((value) => {
      switch (value) {
        case "cancel":
          break;
        case "approve":
          const slug = $(this).data('slug');
          $.ajax({
            url: "<?= site_url(route_to('confirm_order')) ?>",
            type: 'POST',
            dataType: 'JSON',
            data: {
              slug: '<?= $order['slug'] ?>',
              [csrfName]: csrfHash,
            },
            success: function(data) {
              swal("Sipariş onaylanma süreci tamamlandı!", {
                icon: "success",
                button: "Tamam",
              });
              csrfName = data.csrfName;
              csrfHash = data.csrfHash;
            },
            error: function(jqXHR, textStatus, errorThrown) {
              csrfName = jqXHR.responseJSON.csrfName;
              csrfHash = data.responseJSON.csrfHash;
            }
          });
          break;
      }
    });
  });
</script>
<?= $this->endsection() ?>