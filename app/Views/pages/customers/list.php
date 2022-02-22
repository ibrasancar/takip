<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <table id="customersTable" class="table" style="width:100%">
          <thead>
            <tr>
              <th>Müşteri Adı Soyadı</th>
              <th>Telefon Numarası</th>
              <th>Oluşturma Tarihi</th>
              <th>Hareketler</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Müşteri Adı Soyadı</th>
              <th>Telefon Numarası</th>
              <th>Oluşturma Tarihi</th>
              <th>Hareketler</th>
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
<?php if (!(session('user_type') == 'su_admin' || session('user_type') == 'admin')) : ?>
  <style>
    table#customersTable tbody tr td a.delete {
      display: none !important;
    }
  </style>
<?php endif; ?>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/datatables/datatables.min.js') ?>"></script>
<script>
  $(document).ready(function() {
    $('#customersTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= base_url(route_to('get_customers')) ?>',
        type: 'GET',
      },
      "pagingType": "full_numbers",
      language: {
        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Turkish.json"
      },
      responsive: true,
      columnDefs: [{
        searchable: false,
        targets: [2, 3],
      }],
      order: [
        [2, "desc"]
      ],
    });
  });
</script>
<?= $this->endsection() ?>