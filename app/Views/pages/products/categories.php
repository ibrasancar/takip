<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <table id="categoryTable" class="display" style="width:100%">
          <thead>
            <tr>
              <th>Kategori Adı</th>
              <th>Hareketler</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Kategori Adı</th>
              <th>Hareketler</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <form action="" id="category_form" method="post">
          <label for="name" class="form-label">Kategori adı</label>
          <input type="text" class="form-control mb-3" name="name" id="name" placeholder="Kategori adını giriniz...">
          <button id="submit_category" class="btn btn-primary w-100"><i class="material-icons">save</i> Kaydet</button>
          <button id="cancel_category" class="btn btn-danger mt-3 w-100 d-none"><i class="material-icons">delete_outline</i> Düzenlemeyi İptal Et</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<link href="<?= site_url('/assets/plugins/datatables/datatables.min.css') ?>" rel="stylesheet" />
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token() ?>';
</script>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/lightbox/lightbox.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/datatables/datatables.min.js') ?>"></script>
<script>
  function resetCSRF() {
    $(`input[name='${csrfName}']`).val(csrfHash).trigger('change');
  }
  $(document).ready(function() {
    let table = $('#categoryTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?= base_url(route_to('get_p_categories')) ?>",
        type: 'GET',
      },
      pagingType: "full_numbers",
      language: {
        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Turkish.json"
      },
      responsive: true,
      columnDefs: [{
          orderable: false,
          targets: [1]
        },
        {
          searchable: false,
          targets: [1],
        },
      ],
      order: [
        [1, "desc"]
      ],
    });
    const form = $("#category_form");
    const categoryInput = $("#category_form #name");
    const submitButton = $("#category_form #submit_category");
    const cancelButton = $("#category_form #cancel_category");

    $(document).on('click', '#submit_category', function(e) {
      e.preventDefault();
      let category_name = $("#category_form #name").val();
      let formData = {
        name: category_name,
        [csrfName]: csrfHash,
      };
      if ($(this).attr('data-id'))
        formData.id = $(this).attr('data-id');

      $.ajax({
        url: '<?= site_url(route_to('add_product_category')) ?>',
        method: 'POST',
        data: formData,
        success: function(data) {
          swal(data.message, {
            button: 'Tamam',
            icon: 'success'
          });

          csrfName = data.csrfName;
          csrfHash = data.csrfHash;
          table.ajax.reload();
          resetCSRF();
          categoryInput.val(null);
          cancelButton.addClass('d-none');
          submitButton.removeAttr('data-id');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          let errors = jqXHR.responseJSON.errors;
          let message = '';
          for (const [key, value] of Object.entries(errors)) {
            message += value + "\n";
          }
          swal(message, {
            button: 'Tamam',
            icon: 'error',
          });
          csrfName = jqXHR.responseJSON.csrfName;
          csrfHash = jqXHR.responseJSON.csrfHash;
          resetCSRF();
        }
      });
    });

    $(document).on('click', '#edit_category', function(e) {
      e.preventDefault();
      let id = $(this).data('id');
      let categoryName = $(this).closest('tr').children('td[tabindex="0"]').html();

      categoryInput.val(categoryName);
      submitButton.attr('data-id', id);
      cancelButton.removeClass("d-none");
    });

    $(document).on('click', '#cancel_category', function(e) {
      e.preventDefault();
      categoryInput.val(null);
      cancelButton.addClass('d-none');
      submitButton.removeAttr('data-id');
    });

    $(document).on('click', '#remove_category', function(e) {
      e.preventDefault();
      let button = $(this);
      let buttonText = button.html();
      let id = button.data('id');

      swal("Emin misiniz?", {
        buttons: {
          cancel: 'İptal',
          confirm: 'Evet, sil!',
        },
        icon: 'warning',
      }).then((value) => {
        $.ajax({
          url: '<?= site_url(route_to('delete_category')) ?>',
          type: 'POST',
          data: {
            id: id,
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
            table.ajax.reload();
            swal("İşlem başarılı!", {
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
          }
        })
      });
    });

  });
</script>
<?= $this->endsection() ?>