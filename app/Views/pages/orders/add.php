<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<div class="card invoice" style="padding: 0">
  <form action="" method="POST" id="orderForm">
    <div class="card-body">
      <div class="invoice-header">
        <div class="row align-items-center">
          <div class="col-md-9 d-flex d-md-block justify-content-center">
            <h3>Sipariş #<?= $order['slug'] ?></h3>
          </div>
          <div class="col-md-3 d-flex d-md-block justify-content-center text-center">
            <span class="invoice-issue-date">
              <label class="small" for="">Oluşturma Tarihi:</label>
              <input class="form-control order_date" id="created_at" name="created_at" type="text" placeholder="Tarih seçiniz.." value="<?= $order['created_at'] ?>">
            </span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <p class="invoice-description"><textarea name="order_note" id="order_note" rows="2" class="form-control" placeholder="Bu siparişin tümü için geçerli olan notları buraya yazabilirsiniz..."><?= $order['order_note'] ?></textarea></p>
        </div>
      </div>
      <div class="row">
        <div class="table-responsive">
          <table class="table invoice-table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Adet</th>
                <th scope="col">Ürün adı</th>
                <th scope="col">Toplam Fiyat</th>
                <th scope="col">Tahmini Teslimat Tarihi</th>
                <th scope="col">Durum</th>
                <th scope="col">Hareketler</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($order_products)) : ?>
                <tr>
                  <th class="empty-invoice text-center" colspan="7">
                    <span class="badge badge-warning">Bu siparişte bir ürün bulunmamaktadır.</span>
                  </th>
                </tr>
              <?php else : ?>
                <?php foreach ($order_products as $key => $value) : ?>
                  <tr data-id="<?= $value['id'] ?>">
                    <th scope="row"><?= $value['id'] ?></th>
                    <td class="quantity"><?= $value['quantity'] ?></td>
                    <td data-product-id="<?= $value['product_id'] ?>"><?= $value['product_name'] ?></td>
                    <td class="price"><?= $value['price'] ?></td>
                    <td><?= $value['estimated_delivery'] ?></td>
                    <td><?= $value['status'] ?></td>
                    <td>
                      <button class="btn btn-info btn-sm btn-style-light op_edit" data-id="<?= $value['id'] ?>"><span class="material-icons">edit</span></button>
                      <button class="btn btn-danger btn-sm btn-style-light op_remove" data-id="<?= $value['id'] ?>"><span class="material-icons">clear</span></button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
          <button type="button" class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="material-icons">add</i> Ürün Ekle
          </button>

        </div>
      </div>
    </div>
    <div class="card-footer">
      <div class="row invoice-summary">
        <div class="col-lg-5">
          <div class="invoice-info">
            <p class="d-flex justify-content-between"><span>Müşteri: </span>
              <select name="customer_id" id="customer_id">
                <option value="">Lütfen bir müşteri seçiniz</option>
                <?php if (isset($order['customer_id']) && $order['customer_id'] != null) : ?>
                  <option value="<?= $order['customer_id'] ?>" selected><?= $order['customer_name'] ?></option>
                <?php endif; ?>
              </select>
            </p>
            <p>Sipariş Numarası: <span>#<?= $order['slug'] ?></span></p>
            <p>Sipariş ID: <span><?= $order['id'] ?></span></p>
            <p>Oluşturma Tarihi: <span><?= $order['created_at'] ?></span></p>
            <p class="d-flex justify-content-between"><span>Bitiş Tarihi: </span>
              <input class="form-control form-control-sm completed_at w-50" id="completed_at" name="completed_at" type="text" placeholder="Bitiş tarihi seçiniz.." value="<?= $order['completed_at'] ?>">
            </p>
            <div class="invoice-info-actions d-grid">
              <div class="row">
                <div class="col-md-12">
                  <button class="btn btn-dark w-100" id="add_customer"><i class="material-icons">add_circle_outline</i>Yeni Müşteri</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <a href="<?= site_url(route_to('show_order', $order['slug'])) ?>" target="_blank" class="btn btn-outline-primary w-100 mb-3">
            <i class="material-icons">visibility</i>
            Siparişi Görüntüle
          </a>
          <button class="email btn btn-outline-dark w-100 mb-3" data-type="new">
            <i class="material-icons">
              forward_to_inbox
            </i>
            Müşteriye Mail Gönder
          </button>
          <button class="sms btn btn-outline-dark w-100 mb-3" data-type="new">
            <i class="material-icons">upcoming</i>
            Müşteriye SMS Gönder
          </button>
          <?php if (isAdmin()) : ?>
            <button class="email btn btn-outline-warning w-100 mb-3" data-type="manufacturer-update">
              <i class="material-icons">send</i>
              Üreticilere Mail Gönder
            </button>
            <button class="sms btn btn-outline-warning w-100 mb-3" data-type="manufacturer-update">
              <i class="material-icons">send</i>
              Üreticilere SMS Gönder
            </button>
          <?php endif; ?>
          <!-- <div class="row">
            <div class="col-md-6 mb-3">
              <button class="btn btn-outline btn-outline-primary w-100" id="print" type="button"><i class="material-icons">print</i> Yazdır</button>
            </div>
            <div class="col-md-6 mb-3">
              <button class="btn btn-outline btn-outline-danger w-100" type="button"><i class="material-icons">picture_as_pdf</i> PDF</button>
            </div>
          </div> -->
        </div>
        <div class="col-lg-4 invoice-form-element">
          <div class="row align-items-center">
            <div class="col-md-4 ">
              Ara Toplam
            </div>
            <div class="col-md-8 text-right">
              <span id="subtotal"></span>
            </div>
          </div>
          <div class="row mt-3 align-items-center">
            <div class="col-md-4">
              Kapora
            </div>
            <div class="col-md-8 text-right">
              <input type="text" name="deposit" id="deposit" class="form-control form-control-sm text-right" placeholder="Kapora" value="<?= $order['deposit'] ?>">
            </div>
          </div>
          <div class="row mt-3 align-items-center">
            <div class="col-md-4">
              İndirim
            </div>
            <div class="col-md-8 text-right">
              <input type="text" name="discount" id="discount" class="form-control form-control-sm text-right" placeholder="İndirim" value="<?= $order['discount'] ?>">
            </div>
          </div>
          <div class="row mt-3 align-items-center">
            <div class="col-md-4">
              <strong>Toplam</strong>
            </div>
            <div class="col-md-8 text-right">
              <input type="text" name="total" id="total" class="form-control form-control-sm text-right" placeholder="Toplam" value="<?= $order['total_price'] ?>">
            </div>
          </div>
          <div class="row mt-4 align-items-center">
            <div class="col-md-12 text-right">
              <button type="submit" class="btn btn-primary w-100" id="complete_order" type="button">
                <i class="material-icons">save</i>
                Siparişi Kaydet
              </button>
              <?php if (isAdmin()) : ?>
                <button class="btn btn-success mt-3 w-100 confirm-button" data-type="confirm" data-id="<?= $order['id'] ?>">
                  <i class="material-icons">task_alt</i>
                  Siparişi Onayla
                </button>
                <button class="btn btn-danger mt-3 w-100 confirm-button" data-type="cancel" data-id="<?= $order['id'] ?>">
                  <i class="material-icons">highlight_off</i>
                  Onayı Kaldır
                </button>
              <?php endif; ?>
              <?= csrf_field() ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="" id="add_product" class="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProductModalLabel">Ürün Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3 mt-2">
            <div class="col-md-2">
              <label for="quantity" class="form-label">Adet</label>
              <input type="number" id="quantity" class="form-control" placeholder="Adet" aria-label="Adet" value="1">
            </div>
            <div class="col-md-6">
              <label for="product_id" class="form-label">Ürün</label>
              <select name="product_id" id="product_id">
                <option value="">Lütfen ürün seçiniz...</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="price" class="form-label">Fiyat</label>
              <input type="text" id="price" class="form-control" placeholder="Fiyat" aria-label="Fiyat">
            </div>
            <div class="col-md-6">
              <label for="status" class="form-label">Sipariş Durumu</label>
              <select name="status" id="status">
                <option value="">Sipariş durumu seçiniz</option>
                <?php foreach ($order_statuses as $key => $value) : ?>
                  <option value="<?= $key ?>"><?= $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="estimated_delivery" class="form-label">Tahmini Teslimat Tarihi</label>
              <input class="form-control estimated_delivery" name="estimated_delivery" id="estimated_delivery" type="text" placeholder="Teslimat tarihi seçiniz..">
            </div>
            <div class="col-md-6">
              <label for="shipping_date" class="form-label">Kargoya Veriliş Tarihi</label>
              <input class="form-control shipping_date" name="shipping_date" id="shipping_date" type="text" placeholder="Teslimat tarihi seçiniz..">
            </div>
            <div class="col-md-6">
              <label for="deliver_confirm" class="form-label">Teslimat Bitiş Tarihi</label>
              <input class="form-control deliver_confirm" name="deliver_confirm" id="deliver_confirm" type="text" placeholder="Teslimat tarihi seçiniz..">
            </div>
            <div class="col-md-12">
              <label for=""><strong>Ekstralar</strong></label>
              <ul class="list-unstyled" id="list_extra">
                <li><input type="text" name="extras[]" class="form-control mb-2" placeholder="Renk kartela kodu, ürün boyutu vs."></li>
              </ul>
              <button class="btn btn-info" id="add_extra">Ekstra Ekle</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-style-light" data-bs-dismiss="modal"><i class="material-icons">delete_outline</i> Kapat</button>
          <button type="button" type="submit" id="btn_save_product" class="btn btn-primary btn-style-light"><i class="material-icons">save</i> Kaydet</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="" method="post" id="add_customer_form" class="">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="addCustomerModalLabel">Müşteri Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3 mt-2">
            <div class="col-md-12">
              <label for="name" class="form-label">Tam Adı *</label>
              <input type="text" class="form-control " name="name" id="name" placeholder="Müşterinin adı ve soyadı">
            </div>
            <div class="col-md-4">
              <label for="email" class="form-label">E-Posta Adresi</label>
              <input type="email" class="form-control " name="email" id="email" placeholder="E-posta adresi">
            </div>
            <div class="col-md-4">
              <label for="phone" class="form-label">Telefon Numarası</label>
              <input type="text" class="form-control " name="phone" id="phone" placeholder="Telefon numarası" inputmode="text">
            </div>
            <div class="col-md-4">
              <label for="birthday" class="form-label">Doğum Tarihi</label>
              <input type="date" class="form-control " name="birthday" id="birthday" placeholder="Müşterinin doğum tarihi">
            </div>
            <div class="col-md-12">
              <label for="address" class="form-label">Adres</label>
              <textarea name="address" id="address" class="form-control" rows="2" placeholder="Müşterinin adres bilgisi..."></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-style-light" data-bs-dismiss="modal"><i class="material-icons">delete_outline</i> Kapat</button>
          <button type="button" type="submit" id="btn_save_customer" class="btn btn-primary btn-style-light"><i class="material-icons">save</i> Kaydet</button>
          <?= csrf_field() ?>
        </div>

      </div>
    </form>
  </div>
</div>

<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= site_url('assets/plugins/flatpickr/flatpickr.min.css') ?>">
<link rel="stylesheet" href="<?= site_url('/assets/plugins/select2/css/select2.min.css') ?>">
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token() ?>';
</script>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('assets/plugins/input-mask/jquery.inputmask.min.js') ?>"></script>
<script src="<?= site_url('assets/plugins/flatpickr/flatpickr.js') ?>"></script>
<script src="<?= site_url('assets/plugins/flatpickr/tr.js') ?>"></script>
<script src="<?= site_url('assets/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= site_url('assets/plugins/select2/js/i18n/tr.js') ?>"></script>
<script src="<?= site_url('assets/plugins/sweetalert.min.js') ?>"></script>
<script src="<?= site_url('assets/js/order.js') ?>"></script>

<script>
  $(document).on('click', '.email', function(e) {
    e.preventDefault();
    let mailType = $(this).data("type");
    let button = $(this);
    let buttonText = button.html();
    swal("E-posta göndermeden önce lütfen güncelleyiniz.", {
      buttons: {
        cancel: 'İptal',
        confirm: 'Tamam',
      },
      icon: 'warning',
    }).then((value) => {
      if (value === true) {
        $.ajax({
          url: window.location.href,
          type: 'POST',
          data: {
            sendmail: mailType,
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
            swal("E-posta başarıyla gönderildi!", {
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
      }
    });
  });

  $(document).on('click', '.sms', function(e) {
    e.preventDefault();
    let smsType = $(this).data("type");
    let button = $(this);
    let buttonText = button.html();
    swal("E-posta göndermeden önce lütfen güncelleyiniz.", {
      buttons: {
        cancel: 'İptal',
        confirm: 'Tamam',
      },
      icon: 'warning',
    }).then((value) => {
      if (value === true) {
        $.ajax({
          url: window.location.href,
          type: 'POST',
          data: {
            sendsms: smsType,
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
            swal(data.status.message, {
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
      }
    });
  });

  $(document).on('click', '#add_customer', function(e) {
    e.preventDefault();
    $("#addCustomerModal").modal('show');
  });

  // when user close the addCustomerModal
  $('#addCustomerModal').on('hidden.bs.modal', function(e) {
    $("#name, #email, #phone, #birthday, #address").val(null);
  });

  $("#product_id").select2({
    dropdownCssClass: "increasedzindexclass",
    dropdownAutoWidth: true,
    width: "100%",
    language: "tr",
    placeholder: 'Lütfen arama yapınız...',
    ajax: {
      url: "<?= site_url(route_to('get_products_select')) ?>",
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
          price: item.price,
          image: item.image,
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
      var $state = $(`<span><img src="${state.image}" class="select-img"><span class="small text-muted">#${state.id} - </span>${state.text}</span> `);
      return $state;
    },
    templateSelection: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><img src="${state.image || state.element.dataset.image}" class="select-img"><span class="small text-muted">#${state.id} - </span>${state.text}</span> `);
      return $state;
    }
  });

  $("#customer_id").select2({
    dropdownAutoWidth: true,
    width: "100%",
    language: "tr",
    placeholder: 'Lütfen arama yapınız...',
    templateResult: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small text-muted">#${state.id} - </span>${state.text}</span>`);
      return $state;
    },
    templateSelection: function(state) {
      if (!state.id) {
        return state.text;
      }
      var $state = $(`<span><span class="small text-muted">#${state.id || state.element.dataset.id} - </span>${state.text || state.element.dataset.text}</span>`);
      return $state;
    },
    ajax: {
      url: "<?= site_url(route_to('get_customers_select')) ?>",
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
  });

  $(document).on('click', '#btn_save_customer', function(e) {
    e.preventDefault();
    let formElement = {
      name: $("#add_customer_form #name").val(),
      email: $("#add_customer_form #email").val(),
      phone: $("#add_customer_form #phone").val(),
      birthday: $("#add_customer_form #birthday").val(),
      address: $("#add_customer_form #address").val(),
      [csrfName]: csrfHash,
    };
    $.ajax({
      url: "<?= site_url(route_to('add_customer_ajax')) ?>",
      data: formElement,
      method: 'POST',
      success: function(data) {
        csrfName = data.csrfName;
        csrfHash = data.csrfHash;
        // get product price
        let customersSelect = $("#customer_id");
        $.ajax({
          type: 'GET',
          url: "<?= site_url(route_to('get_customers_select')) ?>?id=" + data.customer_id,
        }).then(function(data) {
          let option = new Option(data.text, data.id, true, true);
          customersSelect.append(option).trigger('change');
        });
        $('#addCustomerModal').modal('hide');
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
          dangerMode: true,
        });
        csrfName = jqXHR.responseJSON.csrfName;
        csrfHash = jqXHR.responseJSON.csrfHash;
      }
    })
  });

  // add extra item in extra list - modal
  $(document).on("click", "#add_extra", function(e) {
    e.preventDefault();
    if ($("#list_extra").children().length < 5) {
      $("#list_extra").append(extraInput);
    } else {
      $(this).attr('disabled');
      $(this).addClass('btn-dark');
      $(this).removeClass('btn-info');
      swal("Daha fazla ekstra ekleyemezsiniz!", {
        button: 'Tamam',
        icon: 'warning',
      });
    }
  });
  // when user close the modal
  $('#addProductModal').on('hidden.bs.modal', function(e) {
    estimated_delivery.setDate(null);
    shipping_date.setDate(null);
    deliver_confirm.setDate(null);
    $("#price").val(null);
    $("#quantity").val(1);
    $("#product_id").val(null).trigger('change');
    $("#status").val(null).trigger('change');
    $('#addProductModal').removeAttr("data-orderproductid");
    resetExtraList();
  });
  // when click the add product button
  $("#btn_save_product").on('click', function(e) {
    e.preventDefault();
    const order_product_id = $("#addProductModal").attr('data-orderproductid');
    const form = {
      form: $('#add_product'),
      url: "<?= site_url(route_to('add_order_product')) ?>",
      data: {
        order_id: <?= $order['id'] ?>,
        quantity: $("#quantity").val(),
        product_id: $("#product_id").val(),
        price: $("#price").val(),
        estimated_delivery: $("#estimated_delivery").val(),
        shipping_date: $("#shipping_date").val(),
        deliver_confirm: $("#deliver_confirm").val(),
        status: $("#status").val(),
        extras: $("input[name='extras[]']").map(function() {
          return $(this).val();
        }).get(),
        [csrfName]: csrfHash,
      }
    };
    if (order_product_id != undefined) {
      form.data.order_product_id = order_product_id;
      $.ajax({
        dataType: 'JSON',
        type: "POST",
        url: form.url,
        data: form.data,
        success: function(data) {
          $(`table.invoice-table tbody tr[data-id="${data.data.id}"]`).html(`
            <th scope="row">${data.data.id}</th>
            <td class="quantity">${data.data.quantity}</td>
            <td data-product-id="${data.data.product_id}">${data.data.product_name}</td>
            <td class="price">${data.data.price}</td>
            <td>${data.data.estimated_delivery}</td>
            <td>${data.data.status}</td>
            <td><button class="btn btn-info btn-sm btn-style-light op_edit" data-id="${data.data.id}"><span class="material-icons">edit</span></button>
                  <button class="btn btn-danger btn-sm btn-style-light op_remove" data-id="${data.data.id}"><span class="material-icons">clear</span></button></td>`);

          // close modal
          $('#addProductModal').modal('hide')
          // remove empty invoice badge
          if ($("table.invoice-table").has(".empty-invoice")) {
            $("table.invoice-table .empty-invoice").remove();
          }
          swal("Başarıyla güncellendi!", {
            icon: "success",
            button: "Tamam",
          });
          csrfName = data.csrfName;
          csrfHash = data.csrfHash;
        },
        error: function(jqXHR, textStatus, errorThrown) {
          let errors = jqXHR.responseJSON.msg;
          let message = '';
          for (const [key, value] of Object.entries(errors)) {
            message += value + "\n";
          }
          swal(message, {
            button: 'Tamam',
            icon: 'warning',
          });
          csrfName = jqXHR.responseJSON.csrfName;
          csrfHash = jqXHR.responseJSON.csrfHash;
        }
      });
    } else {
      // send ajax request
      $.ajax({
        dataType: 'JSON',
        type: "POST",
        url: form.url,
        data: form.data,
        success: function(data) {
          console.log(data);
          $("table.invoice-table tbody").append(`
          <tr data-id="${data.data.id}">
            <th scope="row">${data.data.id}</th>
            <td class="quantity">${data.data.quantity}</td>
            <td data-product-id="${data.data.product_id}">${data.data.product_name}</td>
            <td class="price">${data.data.price}</td>
            <td>${data.data.estimated_delivery}</td>
            <td>${data.data.status}</td>
            <td><button class="btn btn-info btn-sm btn-style-light op_edit" data-id="${data.data.id}"><span class="material-icons">edit</span></button>
                      <button class="btn btn-danger btn-sm btn-style-light op_remove" data-id="${data.data.id}"><span class="material-icons">clear</span></button></td>
          </tr>`);

          // close modal
          $('#addProductModal').modal('hide')
          // remove empty invoice badge
          if ($("table.invoice-table").has(".empty-invoice")) {
            $("table.invoice-table .empty-invoice").remove();
          }
          swal("Başarıyla eklendi!", {
            icon: "success",
            button: "Tamam",
          });

          csrfName = data.csrfName;
          csrfHash = data.csrfHash;
        },
        error: function(jqXHR, textStatus, errorThrown) {
          let errors = jqXHR.responseJSON.msg;
          let message = '';

          for (const [key, value] of Object.entries(errors)) {
            message += value + "\n";
          }

          swal(message, {
            button: 'Tamam',
            icon: 'warning',
          });

          csrfName = jqXHR.responseJSON.csrfName;
          csrfHash = jqXHR.responseJSON.csrfHash;
        }
      });
    }
  });

  // when click edit order button
  $(document).on('click', ".op_edit", function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    $('#addProductModal').modal('show');
    $('#addProductModal').attr("data-orderproductid", id);

    $.ajax({
      url: "<?= site_url(route_to('get_order_product')) ?>",
      type: 'POST',
      dataType: 'JSON',
      data: {
        id: id,
        [csrfName]: csrfHash,
      },
      success: function(data) {
        let opdata = data.order_products;

        // get product price
        let orderSelect = $("#product_id");
        $.ajax({
          type: 'GET',
          url: "<?= site_url(route_to('get_products_select')) ?>?id=" + opdata.product_id,
        }).then(function(data) {
          let option = new Option(data.text, data.id, true, true);
          option.dataset.price = data.price;
          option.dataset.image = data.image;
          orderSelect.append(option).trigger('change');
        });

        // $("#product_id").val(opdata.product_id).trigger('change');

        $("#quantity").val(opdata.quantity).trigger('change');
        $("#status").val(opdata.status).trigger('change');
        $("#price").val((opdata.price).replace('.', ',')).trigger('change');

        estimated_delivery.setDate(opdata.estimated_delivery);
        shipping_date.setDate(opdata.shipping_date);
        deliver_confirm.setDate(opdata.shipping_date);

        resetExtraList(true);
        $.each(opdata.extras, function(key, value) {
          $("#list_extra").append(`<li><input type="text" name="extras[]" class="form-control mb-2" placeholder="Renk kartela kodu, ürün boyutu vs." value="${value}"></li>`);
        });
        $("#list_extra li").length < 1 ? resetExtraList() : null;

        csrfName = data.csrfName;
        csrfHash = data.csrfHash;
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
      }
    });
  });

  // when click remove order button
  $(document).on('click', ".op_remove", function(e) {
    e.preventDefault();
    swal("Silmek istediğinize emin misiniz?", {
      icon: 'warning',
      buttons: {
        cancel: 'Hayır',
        approve: 'Evet',
      }
    }).then((value) => {
      switch (value) {
        case "cancel":
          break;
        case "approve":
          const id = $(this).data('id');

          $.ajax({
            url: "<?= site_url(route_to('delete_order_product')) ?>",
            type: 'POST',
            dataType: 'JSON',
            data: {
              id: id,
              [csrfName]: csrfHash,
            },
            success: function(data) {
              const opdata = data.order_products;
              $(`tr[data-id="${opdata.id}"]`).remove();
              swal("Başarıyla silindi!", {
                icon: "success",
                button: "Tamam",
              });
              $(document).trigger('change');
              csrfName = data.csrfName;
              csrfHash = data.csrfHash;
            },
            error: function(jqXHR, textStatus, errorThrown) {
              csrfName = jqXHR.responseJSON.csrfName;
              csrfHash = data.responseJSON.csrfHash;
            }
          });
          break;
      }
    });
  });

  $("#subtotal").html(new Intl.NumberFormat('tr', {
    style: 'currency',
    currency: 'TRY'
  }).format(calcSubTotal()));
  $("#total").val(fixPrice(calcSubTotal()));

  function calcTotalPrice() {
    let subtotal = calcSubTotal();
    let discount = clearPrice($("#discount").val());
    if (discount <= subtotal) {
      let total = subtotal - discount;
      $("#subtotal").html(new Intl.NumberFormat('tr', {
        style: 'currency',
        currency: 'TRY'
      }).format(subtotal));
      $("#total").val(fixPrice(total));
    } else {
      swal("İndirimli fiyat, ara toplamdan büyük olamaz!", {
        icon: 'warning',
        button: 'Tamam',
      });
      $("#discount").val(null);
      let total = subtotal - discount;
      $("#total").val(fixPrice(calcSubTotal()));
    }
  }
  calcTotalPrice();
  // when any changes on page, change total price
  $(document).on('change', function() {
    calcTotalPrice();
  });

  $("#complete_order").on('click', function(e) {
    e.preventDefault();
    const orderData = {
      id: <?= $order['id'] ?>,
      customer_id: $("#customer_id").val(),
      deposit: $("#deposit").val(),
      discount: $("#discount").val(),
      total: $("#total").val(),
      order_note: $("#order_note").val(),
      created_at: $("#created_at").val(),
      completed_at: $("#completed_at").val(),
      [csrfName]: csrfHash,
    }

    swal("Siparişi kaydetmek istediğinize emin misiniz?", {
      icon: 'info',
      buttons: {
        cancel: 'İptal',
        approve: 'Evet',
      },
    }).then(value => {
      switch (value) {
        case "approve":
          $.ajax({
            url: "<?= site_url(route_to('update_order')) ?>",
            type: 'POST',
            dataType: 'JSON',
            data: orderData,
            success: function(data) {
              swal(data.msg, {
                button: 'Tamam',
                icon: 'success',
              });
              csrfName = data.csrfName;
              csrfHash = data.csrfHash;
            },
            error: function(jqXHR, textStatus, errorThrown) {
              let errors = jqXHR.responseJSON.msg;
              let message = '';
              for (const [key, value] of Object.entries(errors)) {
                message += value + "\n";
              }
              swal(message, {
                button: 'Tamam',
                icon: 'warning',
              });
            }
          });
          break;

        case "cancel":
          break;

        default:
          break;
      }
    });

  });
  <?php if (isAdmin()) : ?>
    let confirm = <?= $order['admin_confirm'] != null ? '"' . $order['admin_confirm'] . '"' : 'null' ?>;

    function swapConfirm() {
      if (confirm == null) {
        $('.confirm-button[data-type="confirm"]').show();
        $('.confirm-button[data-type="cancel"]').hide();
        confirm = true;
      } else {
        $('.confirm-button[data-type="confirm"]').hide();
        $('.confirm-button[data-type="cancel"]').show();
        confirm = null;
      }
    }
    swapConfirm();

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
              swal("Başarılı!", {
                icon: 'success',
                button: 'Tamam',
                timer: 2000,
              });
              swapConfirm();
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
      });
    });
  <?php endif; ?>
</script>
<?= $this->endsection() ?>