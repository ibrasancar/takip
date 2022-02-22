<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<form action="" id="add-user" method="post" enctype="multipart/form-data">
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
              <div id="img-preview"><img src="<?= $product['image'] ?>" alt="" class="img-thumbnail mb-3"></div>
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
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= site_url('/assets/plugins/select2/css/select2.min.css') ?>">
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/input-mask/jquery.inputmask.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/i18n/tr.js') ?>"></script>

<script>
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

  $("select[name='manufacturer_id']").select2({
    language: "tr",
    dropdownAutoWidth: true,
    width: "100%",
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
    }
  });
</script>
<?= $this->endsection() ?>