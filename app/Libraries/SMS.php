<?php

namespace App\Libraries;

use App\Models\CustomersModel;
use App\Models\OrdersModel;

class SMS
{
  protected $companyName = 'Extra Takip';
  protected $smsTitle = 'APITEST';
  protected $username = '5071265236';
  protected $password = '97iriPaf96';

  public function sendRequest($site_name, $send_xml, $header_type)
  {
    //die('SITENAME:'.$site_name.'SEND XML:'.$send_xml.'HEADER TYPE '.var_export($header_type,true));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $site_name);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $send_xml);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_type);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    $result = curl_exec($ch);
    return $result;
  }

  public function sendBirthdaySMS(array $customers)
  {
    $message = <<<EOS
      <request>
        <authentication>
          <username>{$this->username}</username>
          <password>{$this->password}</password>
        </authentication>
        <order>
          <sender>{$this->smsTitle}</sender>\n
    EOS;

    foreach ($customers as $k => $v) {
      $m = [
        'user_id'   => $v['id'],
        'phone'     => $v['phone'],
        'title'     => $this->smsTitle,
        'user_type' => 'customer',
      ];
      $message .= <<<EOS
          <message>
              <text><![CDATA[Sevgili {$v['name']}, {$this->companyName} mutlu yıllar diler]]></text>
              <receipents>
                  <number>{$m['phone']}</number>
              </receipents>
          </message>\n
      EOS;
    }

    $message .= <<<EOS
        </order>
      </request>
    EOS;
    return $message;
  }

  public function sendOrderSMS(array $order)
  {
    $order['link'] = site_url(route_to('show_order', $order['slug']));
    $message = <<<EOS
      <request>
        <authentication>
          <username>{$this->username}</username>
          <password>{$this->password}</password>
        </authentication>
        <order>
          <sender>{$this->smsTitle}</sender>
          <message>
            <text><![CDATA[Sevgili {$order['customer_name']}, #{$order['slug']} nolu siparişin başarıyla oluşturuldu. Siparişi görüntülemek için {$order['link']}]]></text>
            <receipents>
                <number>{$order['phone']}</number>
            </receipents>
          </message>
        </order>
      </request>
    EOS;

    return $message;
  }

  public function sendNewOrderToManufacturer(array $order)
  {

    $message = <<<EOS
      <request>
        <authentication>
          <username>{$this->username}</username>
          <password>{$this->password}</password>
        </authentication>
        <order>
          <sender>{$this->smsTitle}</sender>
    EOS;
    foreach ($order['order_products'] as $k => $v) {
      $v['link'] = site_url(route_to('show_manufacturer_order', $order['order']['id'], $v[0]['order_value']));
      $message .= <<<EOS
          <message>
              <text><![CDATA[Sevgili {$v[0]['contact_name']}, #{$v[0]['order_value']} nolu istek oluşturuldu. Siparişi görüntülemek için {$v['link']}]]></text>
              <receipents>
                  <number>{$v[0]['phone']}</number>
              </receipents>
          </message>
      EOS;
    }
    $message .= <<<EOS
        </order>
      </request>
    EOS;
    return $message;
  }

  protected function groupArray(array $array, string $key = 'id')
  {
    $group = array();

    foreach ($array as $value) {
      $group[$value[$key]][] = $value;
    }
    return $group;
  }
}
