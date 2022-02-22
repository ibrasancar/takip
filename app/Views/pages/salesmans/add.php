<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<form action="" id="add-customer" method="post">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row g-3">
            <?php if (isset($success)) : ?>
              <div class="alert alert-success small mt-2 p-2 text-center">
                <?= $success ?>
              </div>
            <?php endif; ?>
            <?= $form_elements ?>
            <div class="col-md-12">
              <button type="submit" name="submit" value="1" class="btn btn-primary w-auto m-r-sm"><i class="material-icons">save</i> Kaydet</button>
              <button type="reset" name="submit" class="btn btn-danger w-auto"><i class="material-icons">delete</i> Temizle</button>
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
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/input-mask/jquery.inputmask.min.js') ?>"></script>
<script>
  $("#phone").inputmask("+\\90 (999) 999 99 99");
  $("#sale_percentage").inputmask("decimal", {
    radixPoint: ".",
    allowMinus: false,
    suffix: " %",
    max: 100,
    digits: 2,
    digitsOptional: false,
    rightAlign: true,
    unmaskAsNumber: true,
    removeMaskOnSubmit: true,
    clearMaskOnLostFocus: false,
});
</script>
<?= $this->endsection() ?>