<?php

namespace App\Controllers;

use App\Libraries\Email;
use App\Libraries\SMS;
use App\Models\CustomersModel;
use App\Models\OrdersModel;
use App\Models\ProductsModel;
use CodeIgniter\I18n\Time;

class Panel extends BaseController
{
    public function index()
    {
        helper(['number']);
        $data = [
            'page_title' => 'Anasayfa',
            'active' => [
                'homepage'
            ],
        ];

        $ordersModel = new OrdersModel();
        $customersModel = new CustomersModel();
        $productsModel = new ProductsModel();

        $session = session();
        $data['success'] = $session->getFlashData('success');
        $data['error'] = $session->getFlashData('error');

        $data['activeCustomer'] = $customersModel->calcActiveCustomer();
        $data['monthlySales'] = $ordersModel->calcMonthlySales();
        $data['totalProduct'] = $productsModel->calcTotalProduct();

        $data['birthdays']['customers'] = $customersModel->getBirthdays();
        $data['birthdays']['count'] = count($data['birthdays']['customers']);

        $data['orders'] = $ordersModel->getOrders(null, true, 5);
        $data['orders']['data'] = array_map(function ($item) use ($ordersModel) {
            $time = new Time($item['created_at']);
            $item['created_at'] = $time->humanize($time);
            $item['total_price'] = $ordersModel->beautifyPrice($item['total_price']);
            return $item;
        }, $data['orders']['data']);



        if ($this->request->getMethod() == 'post' && $this->request->getVar('messageType') == 'email') {
            $mail = new Email();
            return $this->response->setJSON([
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'status' => $mail->sendBirthdayMail($data['birthdays']['customers']),
            ]);
        }

        if ($this->request->getMethod() == 'post' && $this->request->getVar('messageType') == 'sms') {
            $sms = new SMS();
            $request = $sms->sendRequest('http://api.iletimerkezi.com/v1/send-sms', $sms->sendBirthdaySMS($data['birthdays']['customers']), array('Content-Type: text/xml'));
            $result = simplexml_load_string($request);
            return $this->response->setJSON([
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'status' => $result->status,
            ]);
        }

        $data['birthdays']['customers'] = array_splice($data['birthdays']['customers'], 0, 5);

        return view('index', $data);
    }
}
