<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;600;800&display=swap" rel="stylesheet" />
  <?= view('email/assets/reset'); ?>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #e7ecf8;
      font-weight: 400;
      color: #24292e;
      font-size: 14px;
      line-height: 1.6;
      -webkit-transition: background 0.2s ease-in-out;
      -moz-transition: background 0.2s ease-in-out;
      -o-transition: background 0.2s ease-in-out;
      transition: background 0.2s ease-in-out;
    }

    .card {
      margin-top: 64px;
      position: relative;
      display: flex;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #fff;
      background-clip: border-box;
      border: 1px solid rgba(0, 0, 0, 0.125);
      border-radius: 0.25rem;
    }

    .invoice-header {
      margin: -25px -30px 40px -30px;
      border-radius: 10px 10px 0 0;
      padding: 40px 30px;
      background: #2269f3;
      display: block;
      color: #fff;
    }

    .invoice-header h2 {
      margin: 0;
    }

    .card .card-body {
      padding: 25px 30px;
    }

    .table {
      --bs-table-bg: transparent;
      --bs-table-striped-color: #212529;
      --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
      --bs-table-active-color: #212529;
      --bs-table-active-bg: rgba(0, 0, 0, 0.1);
      --bs-table-hover-color: #212529;
      --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
      width: 100%;
      margin-bottom: 1rem;
      color: #212529;
      vertical-align: top;
      border-color: #dee2e6;
    }

    table {
      caption-side: bottom;
      border-collapse: collapse;
    }

    .table>thead {
      vertical-align: bottom;
    }

    .table * {
      border-color: #e1e7ed !important;
    }

    .table>:not(:last-child)>:last-child>* {
      border-bottom-color: currentColor;
    }

    .invoice-table td,
    .invoice-table th {
      vertical-align: middle;
    }

    .table td,
    .table th {
      padding: 15px 20px !important;
    }

    .table th {
      font-weight: 500;
      color: #7b8c9d;
    }

    .table>:not(caption)>*>* {
      padding: 0.5rem 0.5rem;
      background-color: var(--bs-table-bg);
      border-bottom-width: 1px;
      box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }

    .table td {
      text-align: center;
    }

    .table tr {
      border-bottom: 1px solid #e1e7ed;
    }

    .badge {
      display: inline-block;
      padding: 0.35em 0.65em;
      font-size: .75em;
      font-weight: 700;
      line-height: 1;
      color: #fff;
      text-align: center;
      white-space: nowrap;
      vertical-align: baseline;
      border-radius: 0.25rem;
    }

    .badge.badge-primary {
      background: #2269f5;
      color: #fff
    }

    .badge.badge-secondary {
      color: #4d546b;
      background: #fceace
    }

    .badge.badge-success {
      background: #4bad48;
      color: #fff
    }

    .badge.badge-danger {
      background: #ff4857;
      color: #fff
    }

    .badge.badge-warning {
      background: #ff9500;
      color: #fff
    }

    .badge.badge-info {
      background: #61acfc;
      color: #fff
    }

    .badge.badge-light {
      background: #f4f7fa;
      color: #4d546b
    }

    .badge.badge-dark {
      background: #40475c;
      color: #fff
    }

    .btn {
      text-decoration: none;
      display: inline-block;
      padding: 6px 20px;
      border-radius: 5px;
      font-size: 14px;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      -webkit-transition: all .2s ease-in-out !important;
      -moz-transition: all .2s ease-in-out !important;
      -o-transition: all .2s ease-in-out !important;
      transition: all .2s ease-in-out !important;
    }

    .btn-primary,
    .btn-primary.disabled,
    .btn-primary:disabled {
      color: #fff;
      background-color: #2269f5;
      border-color: #2269f5;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div class="invoice-header">
          <h2>Sipariş #<?= $order['slug'] ?></h2>
        </div>
        <div style="margin-bottom: 24px;">
          <div style="margin-bottom: 2px">Merhabalar, <?= $customer['name'] ?>, siparişiniz başarıyla oluşturulmuştur. Aşağıda size bırakılan linkten siparişin durumunu takip edebilirsiniz.</div> <br>
          <a class="btn btn-primary" href="<?= site_url(route_to('show_order', $order['slug'])) ?>">Takip Et</a>
        </div>
        <table class="table invoice-table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Adet</th>
              <th scope="col">Ürün Adı</th>
              <th scope="col">Toplam Fiyat</th>
              <th scope="col">Tahmini Teslimat Tarihi</th>
              <th scope="col">Durum</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($order_products as $k => $v) : ?>
              <tr>
                <td>#<?= $v['id'] ?></td>
                <td><?= $v['quantity'] ?></td>
                <td><?= $v['product_name'] ?></td>
                <td><?= $v['price'] ?></td>
                <td><?= $v['estimated_delivery'] ?></td>
                <td><?= $v['status'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>