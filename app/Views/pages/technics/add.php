<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<form action="" id="add-technic" method="post">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row g-3">
            <?= $form_elements ?>
            <div class="col-md-12">
              <button type="submit" name="submit" value="1" class="btn btn-primary w-auto m-r-sm"><i class="material-icons">save</i> Kaydet</button>
              <button type="reset" name="submit" value="1" class="btn btn-danger w-auto"><i class="material-icons">delete</i> Temizle</button>
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
<script>
  $("select").select2();
  $("#phone").inputmask("+\\90 (999) 999 99 99");
</script>
<?= $this->endsection() ?>