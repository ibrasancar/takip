<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="col-md-12">
  <div class="card">
    <div class="card-body">
      <table id="ordersTable" class="table" style="width:100%">
        <thead>
          <tr>
            <th>Sipariş No</th>
            <th>Satış Danışmanı</th>
            <th>Müşteri</th>
            <th>Toplam Fiyat</th>
            <th>Oluşturma Tarihi</th>
            <th>Tamamlanma Tarihi</th>
            <th>Hareketler</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="7">
              <div class="row">
                <div class="col-2">
                  <input type="text" name="order_slug" class="form-control" id="order_slug" placeholder="Sipariş no">
                </div>
                <div class="col-2">
                  <input type="text" name="customer_name" class="form-control" id="customer_name" placeholder="Müşteri adı">
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
</div>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<link href="<?= site_url('/assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= site_url('assets/plugins/flatpickr/flatpickr.min.css') ?>">
<?php if (!(session('user_type') == 'su_admin' || session('user_type') == 'admin')) : ?>
  <style>
    table#ordersTable thead th:nth-child(2),
    table#ordersTable tbody tr td:nth-child(2) {
      display: none;
    }
  </style>
<?php endif; ?>
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token() ?>';
</script>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
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
  $(document).ready(function() {
    const orderTable = $('#ordersTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= base_url(route_to('get_orders')) . (isset($_GET['is_admin_confirm']) ? '?is_admin_confirm=true' : '') ?>',
        type: 'GET',
        data: function(d) {
          d.minPrice = $("#min_price").val();
          d.maxPrice = $("#max_price").val();
          d.minDate = $("#min_date").val();
          d.maxDate = $("#max_date").val();
          return d;
        }
      },
      "pagingType": "full_numbers",
      language: {
        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Turkish.json"
      },
      responsive: true,
      columnDefs: [{
          searchable: false,
          targets: [4, 5, 6],
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
        [4, "desc"]
      ],
      bFilter: true,
    });
    $(document).on("click", ".copyme", function(e) {
      e.preventDefault();
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val($(this).html()).select();
      document.execCommand("copy");
      $temp.remove();
    });

    $("#order_slug").on("keyup change", function() {
      orderTable.column(0).search(this.value).draw();
    });

    $("#customer_name").on("keyup change", function() {
      orderTable.column(2).search(this.value).draw();
    });

    $("#min_price, #max_price").on("keyup change", function() {
      if ($("#min_price").val() < $("#max_price"))
        orderTable.ajax.reload();

    });

    $("#min_date, #max_date").on("keyup change", function() {
      orderTable.ajax.reload();
    });
    <?php if (isAdmin()) : ?>
      $(document).on('click', '.confirm-button', function(e) {
        e.preventDefault();
        const button = {
          element: $(this),
        };
        button.text = button.element.html();
        button.type = button.element.data('type');
        button.id = button.element.data('id');
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
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                orderTable.ajax.reload();
                swal("Başarılı!", {
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
      });
    <?php endif; ?>
  });
</script>
<?= $this->endsection() ?>