<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <table id="salesmansTable" class="table" style="width:100%">
          <thead>
            <tr>
              <th>Adı Soyadı</th>
              <th>Telefon Numarası</th>
              <th>Oluşturma Tarihi</th>
              <th>Hareketler</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Adı Soyadı</th>
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
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/datatables/datatables.min.js') ?>"></script>
<script>
  $(document).ready(function() {
    let dataTable = $('#salesmansTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= base_url(route_to('get_salesmans')) ?>',
        type: 'GET',
      },
      "pagingType": "full_numbers",
      language: {
        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Turkish.json"
      },
      responsive: true,
      columnDefs: [{
          searchable: false,
          targets: [3],
        },
        {
          orderable: false,
          targets: [3],
        }
      ],
      order: [
        [3, "desc"]
      ],
    });

  });
</script>
<?= $this->endsection() ?>