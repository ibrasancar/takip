<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<div class="row" id="search-container" style="<?= isset($_GET['search']) ? '' : 'display: none;' ?>">
  <div class="container">
    <div class="card">
      <div class="card-body">
        <form action="" method="GET" class="row g-3">
          <div class="col-12">
            <h4>Arama yap</h4>
          </div>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="title" placeholder="SSH Başlığına göre ara" value="<?= $_GET['title'] ?? '' ?>">
          </div>
          <div class="col-sm-3">
            <input type="date" class="form-control" name="estimated_solve" placeholder="Tarihe Göre Ara" aria-label="Tarihe Göre Ara" value="<?= $_GET['estimated_solve'] ?? '' ?>">
          </div>
          <div class="col-sm-3">
            <select name="order_id" id="order_id">
              <option value="">Lütfen sipariş seçiniz...</option>
            </select>
          </div>
          <div class="col-sm-1">
            <button type="submit" name="search" value="1" class="btn btn-primary w-100 h-100"><i class="material-icons-outlined">search</i>Ara</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="mailbox-container">
      <div class="card">
        <div class="container-fluid">
          <div class="row">
            <div class="mailbox-list col-xl-3">
              <ul class="list-unstyled">
                <?php if (empty($tickets)) : ?>
                  <li class="mailbox-list-item no-ssh">
                    Bugün için oluşturulmuş bir SSH bulunmamaktadır.
                  </li>
                <?php endif; ?>
                <?php foreach ($tickets as $k => $v) : ?>
                  <li class="mailbox-list-item">
                    <a href="#" class="view-ticket" data-id="<?= $v['order_id'] ?>">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input check-ticket" type="checkbox" value="" data-id="<?= $v['id'] ?>">
                      </div>
                      <div class="mailbox-list-item-content">
                        <span class="mailbox-list-item-title">
                          <?= $v['title'] ?>
                        </span>
                        <p class="mailbox-list-item-text">
                          <?= mb_substr($v['message'], 0, 35, 'UTF-8') . '...' ?>
                        </p>
                      </div>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
              <div class="remove-box">
                <button class="btn btn-danger w-100 delete-tickets"><i class="material-icons-outlined">delete</i>Seçili SSH'ları Sil</button>
              </div>
            </div>
            <div class="mailbox-open-content col-xl-9">
              <?php if (empty($tickets)) : ?>
              <?php else : ?>
                <div class="alert alert-info">Lütfen soldan SSH seçiniz.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endsection() ?>
<?= $this->section('styles') ?>
<link href="<?= site_url('/assets/plugins/summernote/summernote-lite.min.css') ?>" rel="stylesheet">
<link rel="stylesheet" href="<?= site_url('/assets/plugins/select2/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?= site_url('assets/plugins/flatpickr/flatpickr.min.css') ?>">

<style>
  li.mailbox-list-item.no-ssh {
    background: #2269f5;
    padding: 12px;
    color: #fff;
  }

  .mailbox-container .mailbox-list {
    display: flex;
    flex-direction: column;
  }

  .remove-box {
    margin-top: auto;
  }

  .mailbox-open-content .mailbox-open-content-email {
    padding: 30px 0 0px;
  }

  table.table.table-striped.border.border-2.table.table-striped td {
    padding: 8px 18px !important;
  }

  @media (max-width: 768px) {
    .mailbox-open-content {
      overflow-x: hidden !important;
    }
  }
</style>
<script>
  let csrfHash = '<?= csrf_hash() ?>';
  let csrfName = '<?= csrf_token() ?>';
</script>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('/assets/plugins/summernote/summernote-lite.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/flatpickr/flatpickr.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/flatpickr/tr.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/select2/js/i18n/tr.js') ?>"></script>

<script>
  $(document).ready(function() {
    "use strict";
    $('#compose-editor').summernote({
      height: 200,
      placeholder: 'Type...'
    });

    $("input[type='date']").flatpickr({
      locale: "tr",
      enableTime: false,
      altInput: true,
      altFormat: "d-m-Y",
      dateFormat: "Y-m-d",
    });

    function getTicket(data) {
      let content = `<span class="mailbox-open-date"></span>
            <h5 class="mailbox-open-title">
              ${data.tickets[0].title}
            </h5>`;
      for (let i = 0; i < data.tickets.length; i++) {
        let ticket = data.tickets[i];
        content += `
            <div class="mailbox-open-content-email">
              ${ticket.message ?? '<p class="badge badge-danger">Not girilmemiş.</p>'}
            </div>
            <div class="py-3 d-flex align-items-center ${i < data.tickets.length - 1 ? 'border-bottom' : 'mb-5'} py-3">
            ${i > 0 ? `<button class="btn btn-outline-danger delete-update mr-4 btn-sm" data-id="${ticket.id}"><i class="material-icons-outlined">delete</i>Sil</button>` : ''}
            <div class="small" style="margin-left: auto">Güncelleme Tarihi: ${ticket.updated_at}</div>
            </div>
            `;
      }
      console.log(data);
      content = `<div class="row">
                        <div class="col-md-8">
                          
                          ${content}
                        </div>
                        <div class="col-md-4">
                          <div class="invoice-info mt-1 mb-3">
                          <a href="/panel/tickets/edit/${data.tickets[0].id}" class="btn btn-secondary w-100 mb-3"><i class="material-icons-outlined">edit</i>SSH'ı Düzenle</a>
                            <table class="table table-striped border border-2 table table-striped">
                              <tbody><tr>
                                <td>Müşteri: </td>
                                <td class="text-right"><span class="f-strong customer_name">${data.tickets[0].name ?? ''}</span></td>
                              </tr>
                              <tr>
                                <td>Müşteri Telefon:</td>
                                <td class="text-right"><span class="f-strong customer_phone"><a href="tel:${data.tickets[0].phone ?? ''}">${data.tickets[0].phone ?? ''}</a></span></td>
                              </tr>
                              <tr>
                                <td>Müşteri Adres:</td>
                                <td class="text-right"><span class="f-strong customer_address">${data.tickets[0].address ?? ''}</span></td>
                              </tr>
                              <tr>
                                <td>Sipariş No:</td>
                                <td class="text-right"><span class="f-strong order_slug">#${data.tickets[0].slug ?? ''}</span></td>
                              </tr>
                              <tr>
                                <td>Sipariş ID:</td>
                                <td class="text-right"><span class="f-strong order_id">${data.tickets[0].order_id ?? ''}</span></td>
                              </tr>
                              <tr>
                                <td>Toplam Fiyat:</td>
                                <td class="text-right"><span class="f-strong order_total_price">${data.tickets[0].total_price ?? ''}</span></td>
                              </tr>
                              <tr>
                                <td>Sipariş Oluşturma Tarihi:</td>
                                <td class="text-right"><span class="f-strong order_created_at">${data.tickets[0].order_created_at ?? ''}</span></td>
                              </tr>
                              <tr>
                                <td>Sipariş Bitiş Tarihi:</td>
                                <td class="text-right"><span class="f-strong order_completed_at">${data.tickets[0].order_completed_at ?? ''}</span></td>
                              </tr>
                              <tr>
                                <td>Satış Danışmanı:</td>
                                <td class="text-right"><span class="f-strong order_completed_at">${data.tickets[0].full_name ?? ''}</span></td>
                              </tr>
                            </tbody>
                            </table>
                          </div>
                        </div>
                      </div>`;
      content = content + `
          <div class="mailbox-open-content-reply">
              <div id="order_update">
                <div id="reply-editor"></div>
                <button class="btn btn-primary mt-4" id="order_update_button" data-id="${data.tickets[0].order_id}">Güncelleme Ekle</button>
              </div>
          </div>`;
      $(".mailbox-open-content").html(content);
      $('#reply-editor').summernote({
        height: 100,
        placeholder: 'Güncelleme ekle...'
      });
    };

    $(document).on("click", ".view-ticket", function(e) {
      let order_id = $(this).attr('data-id');
      $.ajax({
        url: '<?= site_url(route_to('get_ticket')) ?>',
        data: {
          id: order_id,
        },
        type: 'GET',
        success: function(data) {
          getTicket(data);
        },
        error: function(jqXHR, status, error) {
          swal(jqXHR.responseJSON.tickets.error, {
            button: 'Tamam',
            icon: 'error',
          }).then((value) => {
            console.log(`/panel/tickets/edit/${order_id}`);
          });
        }
      })
    });
    $(document).on("click", "#order_update_button", function(e) {
      e.preventDefault();
      let order_id = $(this).data("id");
      let message = $("#reply-editor").summernote('code');
      $.ajax({
        type: 'POST',
        url: '<?= site_url(route_to('add_update_to_ticket')) ?>',
        data: {
          order_id: order_id,
          message: message,
          [csrfName]: csrfHash,
        },
        success: function(data) {
          swal("Güncelleme başarıyla eklendi!", {
            button: 'Tamam',
            icon: 'success',
          });
          csrfName = data.csrfName;
          csrfHash = data.csrfHash;
          console.log(data);
          getTicket(data);
          $(`input[name="${csrfName}"]`).val(csrfHash).trigger('change');
        },
        error: function(jqXHR, status, error) {
          csrfName = jqXHR.csrfName;
          csrfHash = jqXHR.csrfHash;
          $(`input[name="${csrfName}"]`).val(csrfHash).trigger('change');
        }
      })
    });
    $(document).on("click", ".delete-update", function(e) {
      e.preventDefault();
      let ticket_id = $(this).data("id");
      $.ajax({
        url: '<?= site_url(route_to('delete_ticket_update')) ?>',
        type: 'GET',
        data: {
          ticket_id: ticket_id
        },
        success: function(data) {
          swal("Güncelleme başarıyla silindi!", {
            button: 'Tamam',
            icon: 'success',
          });
          getTicket(data);
        }
      });

    });
    $(document).on("click", ".delete-tickets", function(e) {
      e.preventDefault();
      swal("Seçili SSH'ları silmek istediğinize emin misiniz?", {
        buttons: {
          yes: 'Evet',
          no: 'Hayır'
        },
        icon: "warning"
      }).then((value) => {
        switch (value) {
          case "yes":
            let ids = '';
            $('.check-ticket:checked').each(function() {
              ids += $(this).data("id") + ',';
            });
            ids = ids.slice(0, -1);
            if (ids != '') {
              window.location.replace(`/panel/tickets/delete/${ids}`);
            }
            break;
        }
      })
    });

    <?php if (isset($_GET['order_id']) && $_GET['order_id']) : ?>
      let orderSelect = $("#order_id");
      $.ajax({
        type: 'GET',
        url: "<?= site_url(route_to('get_orders_select')) ?>?id=<?= $_GET['order_id'] ?>",
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
      placeholder: 'Siparişe göre arama...',
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

    $("#open-search").click(function(e) {
      e.preventDefault();
      $("#search-container").toggle(300);
    })


  });
</script>
<?= $this->endsection() ?>