<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<form action="" id="add-technic" method="post">
  <div class="row">
    <div class="col-md-12">
      <div class="row g-3">
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <?= $form_elements ?>
              </div>
              <?= csrf_field(); ?>
              <button type="submit" name="submit" value="1" class="btn btn-primary w-auto m-r-sm"><i class="material-icons">save</i> Kaydet</button>
              <button type="reset" name="submit" value="1" class="btn btn-danger w-auto"><i class="material-icons">delete</i> Temizle</button>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">

              <?php if (session()->get('user_type') != 'technic') : ?>
                <div class="mb-4">
                  <?php if (isset($validation) && $error = $validation->getError('technic_id')) : ?>
                    <div class="alert alert-danger small p-2 text-center"><?= $error ?></div>
                  <?php endif; ?>
                  <select name="technic_id" id="technic_id">
                    <option value="">Lütfen teknik servis elemanı seçiniz...</option>
                  </select>
                </div>
              <?php endif; ?>

              <?php if (isset($validation) && $error = $validation->getError('order_id')) : ?>
                <div class="alert alert-danger small p-2 text-center"><?= $error ?></div>
              <?php endif; ?>
              <select name="order_id" id="order_id">
                <option value="">Lütfen sipariş seçiniz...</option>
              </select>
              <div class="invoice-info mt-3">
                <table class="table table-striped border border-2 table table-striped">
                  <tr>
                    <td>Müşteri: </td>
                    <td class="text-right"><span class="f-strong customer_name"></span></td>
                  </tr>
                  <tr>
                    <td>Müşteri Telefon:</td>
                    <td class="text-right"><span class="f-strong customer_phone"></span></td>
                  </tr>
                  <tr>
                    <td>Müşteri Adres:</td>
                    <td class="text-right"><span class="f-strong customer_address"></span></td>
                  </tr>
                  <tr>
                    <td>Sipariş No:</td>
                    <td class="text-right"><span class="f-strong order_slug"></span></td>
                  </tr>
                  <tr>
                    <td>Sipariş ID:</td>
                    <td class="text-right"><span class="f-strong order_id"></span></td>
                  </tr>
                  <tr>
                    <td>Toplam Fiyat:</td>
                    <td class="text-right"><span class="f-strong order_total_price"></span></td>
                  </tr>
                  <tr>
                    <td>Oluşturma Tarihi:</td>
                    <td class="text-right"><span class="f-strong order_created_at"></span></td>
                  </tr>
                  <tr>
                    <td>Sipariş Bitiş Tarihi:</td>
                    <td class="text-right"><span class="f-strong order_completed_at"></span></td>
                  </tr>
                </table>
              </div>
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
<link rel="stylesheet" href="<?= site_url('assets/plugins/flatpickr/flatpickr.min.css') ?>">
<style>
  table.table.table-striped.border.border-2.table.table-striped td {
    padding: 8px 10px !important;
  }
</style>
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token(); ?>';
</script>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/flatpickr/flatpickr.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/flatpickr/tr.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/input-mask/jquery.inputmask.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/i18n/tr.js') ?>"></script>
<script>
  <?php if (isset($ticket['order_id']) && $ticket['order_id']) : ?>
    let orderSelect = $("#order_id");
    $.ajax({
      type: 'GET',
      url: "<?= site_url(route_to('get_orders_select')) ?>?id=<?= $ticket['order_id'] ?>",
    }).then(function(data) {
      data.text = data.text || 'Müşteri Yok';

      let option = new Option(data.text, data.id, true, true);

      option.dataset.slug = data.slug;
      option.dataset.created_at = data.created_at;

      orderSelect.append(option).trigger('change');

      orderSelect.trigger({
        type: 'select2:select',
        params: {
          data: data,
        },
      });
    });
  <?php endif; ?>

  $("#order_id").select2({
    dropdownAutoWidth: true,
    width: "100%",
    language: "tr",
    placeholder: 'Lütfen sipariş seçiniz...',
    ajax: {
      url: "<?= site_url(route_to('get_orders_select')) ?>",
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
    processResults: function(data) {
      data = data.map(function(item) {
        return {
          id: item.id_field,
          text: item.text_field,
          slug: item.slug,
          created_at: item.created_at
        };
      });
      return {
        results: data
      };
    },
    templateResult: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small badge badge-light">#${state.slug}</span> ${state.text || 'Müşteri Yok'} <span class="small text-muted">${state.created_at}</span></span>`);
      return $state;
    },
    templateSelection: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small badge badge-light">#${state.slug || state.element.dataset.slug}</span> ${state.text || 'Müşteri Yok'}  <span class="small text-muted">${state.created_at || state.element.dataset.created_at}</span></span>`);

      return $state;
    }
  });
  $("input[type='date']").flatpickr({
    locale: "tr",
    enableTime: true,
    time_24hr: true,
    altInput: true,
    altFormat: "d-m-Y H:i",
    dateFormat: "Y-m-d H:i",
  });
  $("#phone").inputmask("+\\90 (999) 999 99 99");

  $("#order_id").on("select2:select", function() {
    let order_id = $(this).find(":selected").val();
    if (order_id != '') {
      $.ajax({
        url: "<?= site_url(route_to('get_order')) ?>",
        method: "POST",
        data: {
          order_id: order_id,
          [csrfName]: csrfHash,
        },
        success: function(data) {
          $(".customer_name").html(data.order.name);
          $(".customer_phone").html(data.order.phone);
          $(".customer_address").html(data.order.address);
          $(".order_slug").html("#" + data.order.slug);
          $(".order_id").html("#" + data.order.id);
          $(".order_created_at").html(data.order.created_at);
          $(".order_completed_at").html(data.order.completed_at);
          $(".order_total_price").html(data.order.total_price);
          csrfName = data.csrfName;
          csrfHash = data.csrfHash;
          $(`input[name="${csrfName}"]`).val(csrfHash).trigger('change');
        },
        error: function() {
          $(".customer_name").html();
          $(".customer_phone").html();
          $(".order_slug").html();
          $(".order_id").html();
          $(".order_created_at").html();
          $(".order_completed_at").html();
          $(".order_total_price").html();
          csrfName = data.csrfName;
          csrfHash = data.csrfHash;
        }
      });
    } else {
      $(".customer_name").html("");
      $(".customer_phone").html("");
      $(".order_slug").html("");
      $(".order_id").html("");
      $(".order_created_at").html("");
      $(".order_completed_at").html("");
      $(".order_total_price").html("");
    }
  });
  <?php if (session()->get('user_type') != 'technic') : ?>
    <?php if (isset($ticket['technic_id']) && $ticket['technic_id']) : ?>
      let technicSelect = $("#technic_id");
      $.ajax({
        type: 'GET',
        url: "<?= site_url(route_to('get_technic_for_select')) ?>?id=<?= $ticket['technic_id'] ?>",
      }).then(function(data) {
        data.text = data.text || 'Müşteri Yok';

        let option = new Option(data.text, data.id, true, true);

        technicSelect.append(option).trigger('change');

        technicSelect.trigger({
          type: 'select2:select',
          params: {
            data: data,
          },
        });
      });
    <?php endif; ?>
    $("#technic_id").select2({
      dropdownAutoWidth: true,
      width: "100%",
      language: "tr",
      placeholder: 'Lütfen teknik destek personeli seçiniz...',
      ajax: {
        url: "<?= site_url(route_to('get_technic_for_select')) ?>",
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
      processResults: function(data) {
        data = data.map(function(item) {
          return {
            id: item.id_field,
            text: item.text_field,
          };
        });
        return {
          results: data
        };
      },
    });
  <?php endif; ?>
</script>
<?= $this->endsection() ?>