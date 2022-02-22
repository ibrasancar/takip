<?php

namespace App\Libraries;

use App\Models\CustomersModel;
use App\Models\OrdersModel;
use App\Models\EmailModel;
use App\Models\OrderMetaModel;
use App\Models\OrderProductsModel;
use App\Models\UsersModel;

class Email
{

  protected function groupArray(array $array, string $key = 'id')
  {
    $group = array();

    foreach ($array as $value) {
      $group[$value[$key]][] = $value;
    }
    return $group;
  }

  public function sendBirthdayMail(array $customers)
  {
    $email = \Config\Services::email();

    $customer_model = new CustomersModel();
    $email_model = new EmailModel();

    foreach ($customers as $k => $v) {
      $m = [
        'user_id'   => $v['id'],
        'email'     => $v['email'],
        'title'     => getenv('app.companyName') . ' Mutlu Yıllar Diler!',
        'user_type' => 'customer',
      ];

      $m['message'] = "<div style='display: block;max-width:600px;text-align:center;margin:0 auto;'>";
      $m['message'] .= "<div style='display: block; margin: 0 auto; width: 300px; height: auto;'><img src='" . getenv("app.companyLogo") . "' alt='" . getenv("app.companyName") . "'></div>";
      $m['message'] .= "<h3>" . getenv("app.companyName") . " Mutlu Yıllar Diler!</h3>";
      $m['message'] .= "Sevgili " . $v['name'] . ", mutlu yıllar dileriz!";
      $m['message'] .= "</div>";


      $email->setTo($m['email']);
      $email->setSubject($m['title']);
      $email->setMessage($m['message']);
      $email->setMailType('html');
      $status = $email->send();
      $m['status'] = !$status;

      $email_model->save($m);
    }
  }

  public function sendOrderMail(int $order_id, string $message)
  {
    $order_model = new OrdersModel();
    $email_model = new EmailModel();
    $email = \Config\Services::email();

    $data = $order_model->getOrderByID($order_id);
    $m = [
      'user_id'   => $data['customer_id'],
      'email'     => $data['email'],
      'user_type' => 'customer',
    ];

    $m['message'] = "<div style='display: block;max-width:600px;text-align:center;margin:0 auto;'>";
    $m['message'] .= "<div style='display: block; margin: 0 auto; width: 300px; height: auto;'><img src='" . getenv("app.companyLogo") . "' alt='" . getenv("app.companyName") . "'></div>";

    if ($message == 'new') {
      $m['title'] = "#" . $data['slug'] . " Numaralı Siparişiniz Oluşturuldu";
      $m['message'] .= "Merhabalar " . $data['name'] . ", " . getenv('app.companyName') . " üzerindeki " . "<span style='background: #f1f1f1; padding: 0 6px;'>#" . $data['slug'] . "</span> nolu, toplam ücreti " . $data['total_price'] . " olan siparişiniz başarıyla oluşturuldu. Siparişi görüntülemek ve onaylamak için aşağıdaki linki kullanabilirsiniz.";
    }
    if ($message == 'update') {
      $m['title'] = "#" . $data['slug'] . " Numaralı Siparişte Değişiklik";
      $m['message'] .= "Merhabalar " . $data['name'] . ", " . getenv('app.companyName') . " üzerindeki " . "<span style='background: #f1f1f1; padding: 0 6px;'>#" . $data['slug'] . "</span> nolu, toplam ücreti " . $data['total_price'] . " olan siparişinizde değişiklik yapıldı. Siparişi görüntülemek ve onaylamak için aşağıdaki linki kullanabilirsiniz.";
    }

    $m['message'] .= "<br/>
      <br/>
      <a href='" . site_url(route_to('show_order',  $data['slug'])) . "' target='_blank' alt='Sipariş Takibi' style='color: #fff; background: #3e7dfa; padding: 6px 20px; border-radius: 5px; text-decoration: none;'>Siparişi Görüntüle</a>
      <br/>
      <br/>
      Eğer linki görüntüleyemiyorsanız, aşağıdaki linki kullanabilirsiniz.
      <br/>
      <br/>
      <div style='background: #f1f1f1; font-size: 90%;padding: 12px; border-radius: 6px;'>" . site_url(route_to('show_order',  $data['slug'])) . "</div>
      ";
    $m['message'] .= "</div>";

    $email->setTo($m['email']);
    $email->setSubject($m['title']);
    $email->setMessage($m['message']);
    $email->setMailType('html');
    $status = $email->send();

    $m['status'] = !$status;
    $email_model->save($m);

    return !$status;
  }

  public function sendNewOrderToManufacturer(int $order_id, string $message = 'new')
  {
    $order_model = new OrdersModel();
    $order_products_model = new OrderProductsModel();
    $order_meta = new OrderMetaModel();
    $salesman_model = new UsersModel();

    $email_model = new EmailModel();
    $email = \Config\Services::email();

    $data = $order_model->getOrderByID($order_id);


    $data['order_products'] = $order_products_model
      ->select('order_products.id, order_products.price, order_products.estimated_delivery, order_products.quantity, order_products.extras, p.id as product_id, p.name as product_name, p.category as product_category, p.image as product_image, m.id as manufacturer_id, m.name as manufacturer_name, m.contact_name as manufacturer_contact, m.email as manufacturer_email, m.phone as manufacturer_phone')
      ->where('order_id', $data['id'])
      ->join('products as p', 'order_products.product_id = p.id')
      ->join('manufacturers as m', 'p.manufacturer_id = m.id')
      ->findAll();

    $data['salesman'] = $salesman_model
      ->select('full_name, email, phone, address')
      ->find($data['salesman_id']);

    $data['order_products'] = $this->groupArray($data['order_products'], 'manufacturer_id');

    $env = [
      'logo' => getenv("app.companyLogo"),
      'name' => getenv("app.companyName"),
    ];

    // send mail 
    foreach ($data['order_products'] as $order) {
      $om = [
        'order_title' => 'manufacturer_show',
        'order_value' => $order_model->createSlug(),
      ];

      $env['route'] = site_url(route_to('show_manufacturer_order', $order_id, $om['order_value']));

      $m = [
        'user_id' => $order[0]['manufacturer_id'],
        'email' => $order[0]['manufacturer_email'],
        'user_type' => 'manufacturer',
        'title' => '#' . $om['order_value'] . ' No\'lu Sipariş Bildirimi',
      ];

      $products = '';
      foreach ($order as $k => $v) {
        $products .= <<<EOS
          <tr>
            <td>{$v['product_name']}</td>
            <td>{$v['quantity']}</td>
          </tr>
        EOS;
        $om['order_product_id'] = $v['id'];

        if ($exists = $order_meta->where('order_product_id', $v['id'])->first()) {
          $om['order_value'] = $exists['order_value'];
          $m['title'] = '#' . $om['order_value'] . ' No\'lu Sipariş İçin Güncelleme';
          $env['route'] = site_url(route_to('show_manufacturer_order', $order_id, $om['order_value']));
        } else {
          $order_meta->save($om);
        }
      }

      $m['message'] = <<<EOS
          <div style="display: block; max-width: 600px; text-align: center; margin: 0 auto;">
            <div style="display: block; margin: 12px auto; width: 300px; height: auto">
              <img src="{$env['logo']}" alt="{$env['name']}">
            </div>
            <div>
              <p>Merhabalar {$order[0]['manufacturer_name']} yetkilisi {$order[0]['manufacturer_contact']}. {$data['salesman']['full_name']} yetkili satış danışmanımızın oluşturduğu sipariş için <span style='background: #f1f1f1; padding: 0 6px;'>#{$om['order_value']}</span> no'lu ürün siparişi için gerekli incelemeleri yapıp lütfen bize geri dönüş yapınız. </p>
              <table style="width: 100%">
                <thead>
                  <tr>
                    <th>Ürün Adı</th>
                    <th>Adet</th>
                  </tr>
                </thead>
                <tbody>
                  {$products}
                </tbody>
              </table>
              <br>
              <p>Eğer siparişin tamamını görmek isterseniz, aşağıdaki linkten ulaşabilirsiniz.</p>
              <br>
              <a href="{$env['route']}" target='_blank' alt='Sipariş Takibi' style='color: #fff; background: #3e7dfa; padding: 6px 20px; border-radius: 5px; text-decoration: none;'>Siparişi Görüntüle</a>
              <p>Eğer linki görüntüleyemiyorsanız, aşağıdaki linki kullanabilirsiniz.</p>
              <div style='background: #f1f1f1; font-size: 90%;padding: 12px; border-radius: 6px;'>{$env['route']}</div>
            </div>
          </div>
      EOS;

      $email->setTo($m['email']);
      $email->setSubject($m['title']);
      $email->setMessage($m['message']);
      $email->setMailType('html');
      $status = $email->send();

      $m['status'] = !$status;
      $email_model->save($m);
    }

    return true;
  }
}
