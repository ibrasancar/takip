<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<div class="row">
  <div class="card">
    <div class="card-body">
      <table id="productTable" class="display" style="width:100%">
        <thead>
          <tr>
            <th>Görsel</th>
            <th>Kategori</th>
            <th>Ürün Adı</th>
            <th>Fiyatı</th>
            <th>Üretici</th>
            <th>Eklenme Tarihi</th>
            <th>Hareketler</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th colspan="7">
              <div class="row">
                <div class="col-2">
                  <input type="text" name="category" class="form-control" id="category" placeholder="Kategoriye göre filtrele">
                </div>
                <div class="col-2">
                  <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Ürün adına göre filtrele">
                </div>
                <div class="col-2">
                  <input type="number" name="min_price" class="form-control" id="min_price" placeholder="Min. fiyat">
                </div>
                <div class="col-2">
                  <input type="number" name="max_price" class="form-control" id="max_price" placeholder="Max. fiyat">
                </div>
                <div class="col-2">
                  <input type="text" name="min_date" class="form-control" id="min_date" placeholder="Min. tarih">
                </div>
                <div class="col-2">
                  <input type="text" name="max_date" class="form-control" id="max_date" placeholder="Max. tarih">
                </div>
              </div>
            </th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<link href="<?= site_url('/assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" />
<link href="<?= site_url('/assets/plugins/lightbox/lightbox.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= site_url('assets/plugins/flatpickr/flatpickr.min.css') ?>">
<?php if (!(session('user_type') == 'su_admin' || session('user_type') == 'admin')) : ?>
  <style>
    table#productTable thead th:last-child,
    table#productTable tbody tr td:last-child {
      display: none;
    }
  </style>
<?php endif; ?>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/lightbox/lightbox.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/datatables/datatables.min.js') ?>"></script>
<script src="<?= site_url('assets/plugins/flatpickr/flatpickr.js') ?>"></script>
<script src="<?= site_url('assets/plugins/flatpickr/tr.js') ?>"></script>
<script>
  let min_date = $("#min_date").flatpickr({
    locale: "tr",
    altInput: true,
    altFormat: "d-m-Y",
    dateFormat: "Y-m-d",
  });
  let max_date = $("#max_date").flatpickr({
    locale: "tr",
    altInput: true,
    altFormat: "d-m-Y",
    dateFormat: "Y-m-d",
  });

  lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true
  });
  $(document).ready(function() {
    let table = $('#productTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?= base_url(route_to('products_get')) . (isset($_GET['deleted']) && $_GET['deleted'] != null ? '?deleted=1' : null) ?>",
        type: 'GET',
        data: function(d) {
          d.minPrice = $("#min_price").val();
          d.maxPrice = $("#max_price").val();
          d.minDate = $("#min_date").val();
          d.maxDate = $("#max_date").val();
          return d;
        }
      },
      pagingType: "full_numbers",
      language: {
        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Turkish.json"
      },
      responsive: true,
      columnDefs: [{
          width: "10%",
          targets: 0,
        },
        {
          width: "15%",
          targets: 1,
        },
        {
          width: "15%",
          targets: 2,
        },
        {
          width: "15%",
          targets: 3,
        },
        {
          width: "15%",
          targets: 4,
        },
        {
          width: "15%",
          targets: 5,
        },
        {
          orderable: false,
          targets: [0, 6]
        },
        {
          searchable: false,
          targets: [0, 6],
        },
        {
          render: function(data, type, row) {
            return new Intl.NumberFormat('tr', {
              style: 'currency',
              currency: 'TRY'
            }).format(data);
          },
          targets: 3,
        }
      ],
      order: [
        [5, "desc"]
      ],
    });

    $("#category").on("keyup change", function() {
      table.column(1).search(this.value).draw();
    });

    $("#product_name").on("keyup change", function() {
      table.column(2).search(this.value).draw();
    });

    $("#min_price, #max_price").on("keyup change", function() {
      if ($("#min_price").val() < $("#max_price"))
        table.ajax.reload();
    });

    $("#min_date, #max_date").on("keyup change", function() {
      table.ajax.reload();
    });

  });
</script>
<?= $this->endsection() ?>