<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<form action="" id="add-product" method="post" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <div class="row g-3">
            <?= $form_elements ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="row g3">
            <div class="col-md-12">
              <label for="image" class="form-label">Ürün Resmi</label>
              <div id="img-preview"></div>
              <input type="file" name="image" accept="image/*" class="form-control <?= isset($validation) && $validation->getError('name') ? 'is-invalid' : '' ?>" id="image" value="<?= set_value('image') ?>">

              <?php if (isset($validation) && $validation->getError('image')) { ?>
                <div class='alert alert-danger small mt-2 p-2 text-center'>
                  <?= $error = $validation->getError('image'); ?>
                </div>
              <?php } ?>

            </div>
            <div class="col-md-12 mt-3">
              <button type="submit" name="submit" value="1" class="btn btn-primary m-r-sm"><i class="material-icons">save</i> Kaydet</button>
              <button type="reset" name="submit" value="1" class="btn btn-danger"><i class="material-icons">delete</i> Temizle</button>
              <?= csrf_field() ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="" method="post" id="add_category_form" class="">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryModalLabel">Kategori Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3 mt-2">
            <div class="col-md-12">
              <label for="category_name" class="form-label">Kategori Adı *</label>
              <input type="text" class="form-control " name="category_name" id="category_name" placeholder="Kategori adı">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger btn-style-light" data-bs-dismiss="modal"><i class="material-icons">delete_outline</i> Kapat</button>
              <button type="button" type="submit" id="btn_save_category" class="btn btn-primary btn-style-light"><i class="material-icons">save</i> Kaydet</button>
              <?= csrf_field() ?>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="addManuModal" tabindex="-1" aria-labelledby="addManuModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="" method="post" id="add_manufacturer_form" class="">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="addManuModalLabel">Üretici Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3 mt-2">
            <?= $manufacturer_form_elements ?>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger btn-style-light" data-bs-dismiss="modal"><i class="material-icons">delete_outline</i> Kapat</button>
              <button type="button" type="submit" id="btn_save_manufacturer" class="btn btn-primary btn-style-light"><i class="material-icons">save</i> Kaydet</button>
              <?= csrf_field() ?>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= site_url('/assets/plugins/select2/css/select2.min.css') ?>">
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token() ?>';
</script>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/input-mask/jquery.inputmask.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/i18n/tr.js') ?>"></script>

<script>
  function resetCSRF() {
    $(`input[name='${csrfName}']`).val(csrfHash).trigger('change');
  }
  $("select[name='manufacturer_id']").select2({
    dropdownAutoWidth: true,
    dropdownCssClass: "increasedzindexclass",
    width: "100%",
    language: "tr",
    placeholder: 'Lütfen arama yapınız...',
    ajax: {
      url: "<?= site_url(route_to('get_manuf_select')) ?>",
      dataType: 'json',
      delay: 500,
      cache: true,
      data: function(params) {
        let query = {
          search: params.term,
          page: params.page || 1
        };
        return query;
      },
    },
    templateResult: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small text-muted">#${state.id} - </span>${state.text}</span> `);
      return $state;
    },
    templateSelection: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small text-muted">#${state.id} - </span>${state.text}</span> `);
      return $state;
    }
  });

  $("select[name='category']").select2({
    dropdownAutoWidth: true,
    dropdownCssClass: "increasedzindexclass",
    width: "100%",
    language: "tr",
    placeholder: 'Lütfen arama yapınız...',
    ajax: {
      url: "<?= site_url(route_to('get_product_categories')) ?>",
      dataType: 'json',
      delay: 500,
      cache: true,
      data: function(params) {
        let query = {
          search: params.term,
          page: params.page || 1
        };
        return query;
      },
    },
    templateResult: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small text-muted">#${state.id} - </span>${state.text}</span> `);
      return $state;
    },
    templateSelection: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small text-muted">#${state.id} - </span>${state.text}</span> `);
      return $state;
    }
  });

  $("#add_product").on("click", function(e) {
    e.preventDefault();
    $("#addCategoryModal").modal('show');
  });

  $("#add_manufacturer").on("click", function(e) {
    e.preventDefault();
    $("#addManuModal").modal('show');
  });

  $('#addCategoryModal').on('hidden.bs.modal', function(e) {
    $("#category_name").val(null);
  });

  $('#addManuModal').on('hidden.bs.modal', function(e) {
    $("#add_manufacturer_form #name").val(null);
    $("#add_manufacturer_form #contact_name").val(null);
    $("#add_manufacturer_form #email").val(null);
    $("#add_manufacturer_form #phone").val(null);
    $("#add_manufacturer_form #address").val(null);
  });

  $(document).on('click', '#btn_save_category', function(e) {
    e.preventDefault();
    let category_name = $("#category_name").val();
    $.ajax({
      url: '<?= site_url(route_to('add_product_category')) ?>',
      method: 'POST',
      data: {
        name: category_name,
        [csrfName]: csrfHash,
      },
      success: function(data) {
        let category_id = data.id;
        console.log(data);
        swal(data.message, {
          button: 'Tamam',
          icon: 'success'
        });
        $("#addCategoryModal").modal('hide');

        let productCategoriesSelect = $("select[name='category']");
        $.ajax({
          type: 'GET',
          url: "<?= site_url(route_to('get_product_categories')) ?>?id=" + category_id,
        }).then(function(data) {
          let option = new Option(data.text, data.id, true, true);
          productCategoriesSelect.append(option).trigger('change');
        });

        csrfName = data.csrfName;
        csrfHash = data.csrfHash;
        resetCSRF();
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

  $(document).on('click', '#btn_save_manufacturer', function(e) {
    e.preventDefault();
    $.ajax({
      url: '<?= site_url(route_to('add_manufacturer_ajax')) ?>',
      method: 'POST',
      data: {
        name: $("#add_manufacturer_form #name").val(),
        contact_name: $("#add_manufacturer_form #contact_name").val(),
        email: $("#add_manufacturer_form #email").val(),
        phone: $("#add_manufacturer_form #phone").val(),
        address: $("#add_manufacturer_form #address").val(),
        [csrfName]: csrfHash,
      },
      success: function(data) {
        swal(data.message, {
          button: 'Tamam',
          icon: 'success'
        });
        $("#addManuModal").modal('hide');

        let manufacturerSelect = $("select[name='manufacturer_id']");
        $.ajax({
          type: 'GET',
          url: "<?= site_url(route_to('get_manuf_select')) ?>?id=" + data.id,
        }).then(function(data) {
          let option = new Option(data.text, data.id, true, true);
          manufacturerSelect.append(option).trigger('change');
        });

        csrfName = data.csrfName;
        csrfHash = data.csrfHash;
        resetCSRF();
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
  })

  $("#phone").inputmask("+\\90 (999) 999 99 99");

  $("#price").inputmask("decimal", {
    radixPoint: ",",
    groupSeparator: ".",
    allowMinus: false,
    suffix: " ₺",
    digits: 2,
    digitsOptional: false,
    rightAlign: true,
    unmaskAsNumber: true,
    // removeMaskOnSubmit: true,
    clearMaskOnLostFocus: false,
  });

  const chooseFile = document.getElementById("image");
  const imgPreview = document.getElementById("img-preview");

  chooseFile.addEventListener("change", function() {
    let imageSize = chooseFile.files[0].size / 1024 / 1024;
    if (imageSize.toFixed(2) < 2) {
      getImgData();
    } else {
      alert("Büyük dosya boyutu.");
      chooseFile.value = null;
    }
  });

  function getImgData() {
    const files = chooseFile.files[0];
    if (files) {
      const fileReader = new FileReader();
      fileReader.readAsDataURL(files);
      fileReader.addEventListener("load", function() {
        imgPreview.style.display = "block";
        imgPreview.innerHTML =
          '<img src="' + this.result + '" class="img-thumbnail mb-3" />';
      });
    }
  }
</script>
<?= $this->endsection() ?>