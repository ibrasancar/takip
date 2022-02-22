<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ManufacturersModel;
use App\Models\OrderProductsModel;
use App\Models\OrdersModel;
use App\Models\UsersModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Manufacturers extends BaseController
{
    protected $orderStatuses = [
        'processing' => [
            'name' => 'İşleniyor',
            'list_style' => '<span class="badge badge-dark">İşleniyor</span>',
        ],
        'shipped' => [
            'name' => 'Hazırlandı',
            'list_style' => '<span class="badge badge-warning">Hazırlandı</span>',
        ],
        'ontheway' => [
            'name' => 'Yolda',
            'list_style' => '<span class="badge badge-info">Yolda</span>',
        ],
        'delivered' => [
            'name' => 'Teslim edildi',
            'list_style' => '<span class="badge badge-success">Teslim edildi</span>',
        ],
        'canceled' => [
            'name' => 'İptal Edildi',
            'list_style' => '<span class="badge badge-danger">İptal</span>',
        ]
    ];

    protected $manufacturerValidationRules = [
        'name'  => [
            'rules' => 'required',
            'label' => 'İsim',
        ],
        'contact_name'  => [
            'rules' => 'required',
            'label' => 'Firma yetkilisi',
        ],
        'phone'  => [
            'rules' => 'required',
            'label' => 'Telefon numarası',
        ],
    ];

    public $manufacturerForm = [
        'name' => [
            'type' => 'text',
            'column' => 'col-md-6',
            'label' => 'Firma adı *',
            'placeholder' => 'Üretici firmanın adı',
        ],
        'contact_name' => [
            'type' => 'text',
            'column' => 'col-md-6',
            'label' => 'Yetkili adı *',
            'placeholder' => 'Üretici firmanın yetkilisinin adı',
        ],
        'email' => [
            'type' => 'email',
            'column' => 'col-md-6',
            'label' => 'E-Posta Adresi',
            'placeholder' => 'Bildirimlerin gönderileceği e-posta adresi',
        ],
        'phone' => [
            'type' => 'text',
            'column' => 'col-md-6',
            'label' => 'Telefon Numarası *',
            'placeholder' => 'Bildirimlerin gönderileceği telefon numarası',
        ],
        'address' => [
            'type' => 'textarea',
            'column' => 'col-md-12',
            'label' => 'Adres',
            'rows' => 2,
            'placeholder' => 'Üretici firmanın iletişim adresi',
        ]
    ];

    protected $pageButtons = [
        'add_manufacturer' => [
            'route' => 'add_manufacturer',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'Üretici Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'manufacturers' => [
            'route' => 'manufacturers',
            'class' => 'btn btn-info',
            'text' => 'Geri Dön',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'arrow_back',
            ]
        ],
    ];

    public function index()
    {
        $data = [
            'page_title' => 'Üreticiler',
            'active' => ['manufacturers', 'manufacturers'],
            'page_buttons' => [$this->pageButtons['add_manufacturer']],
        ];

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        return view('pages/manufacturers/list', $data);
    }

    public function add()
    {
        helper(['form', 'formElement']);
        $data = [
            'page_title' => 'Üretici ekle',
            'active' => ['manufacturers', 'add_manufacturer'],
            'page_buttons' => [$this->pageButtons['manufacturers']]
        ];

        $manufacturer_model = new ManufacturersModel();
        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->manufacturerValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $manufacturer_model->save([
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'contact_name' => $this->request->getVar('contact_name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);
                $session = session();
                $session->setFlashData('success', 'Üretici başarıyla eklendi');
                return redirect()->route('manufacturers');
            }
        }

        $data['form_elements'] = makeFormElement($this->manufacturerForm, $data['validation'] ?? null, null);

        return view('pages/manufacturers/add', $data);
    }

    public function edit(int $manufacturer_id)
    {
        helper(['form', 'formElement']);
        $data = [
            'page_title' => 'Üretici düzenle',
            'active' => ['manufacturers', 'add_manufacturer'],
            'page_buttons' => [$this->pageButtons['manufacturers'], $this->pageButtons['add_manufacturer']]
        ];

        $manufacturer_model = new ManufacturersModel();
        $data['manufacturer'] = $manufacturer_model->find($manufacturer_id);

        if (!$data['manufacturer']) {
            session()->setFlashData('error', 'Üretici bulunamadı!');
            return redirect()->route('manufacturers');
        }

        // check if new added manufacturer
        $data['success'] = session()->getFlashData('success');

        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->manufacturerValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $manufacturer_model->update($manufacturer_id, [
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'contact_name' => $this->request->getVar('contact_name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);
                $data['success'] = 'Üretici başarıyla düzenlendi!';

                $data['manufacturer'] = $manufacturer_model->find($manufacturer_id);
            }
        }

        $data['form_elements'] = makeFormElement($this->manufacturerForm, $data['validation'] ?? null, $data['manufacturer']);


        return view('pages/manufacturers/add', $data);
    }

    public function delete(int $manufacturer_id)
    {
        $manufacturer_model = new ManufacturersModel();

        $check = $manufacturer_model->where('id', $manufacturer_id)->first();

        if ($check) {
            $manufacturer_model->delete($manufacturer_id);
            session()->setFlashData('success', 'Üretici başarıyla silindi.');
        } else {
            session()->setFlashData('error', 'Üretici bulunamadı.');
        }

        return redirect()->route('manufacturers');
    }

    /**
     * Gets manufacturer for manufacturer datatable in list page
     *
     * @return json
     */
    public function getManufacturers()
    {
        helper(['dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->query('SELECT m.name, m.contact_name, m.created_at, m.id FROM manufacturers m WHERE m.deleted_at IS NULL');

        $dt->edit('created_at', function ($data) {
            return dtBeautifyDate($data['created_at']);
        });

        $dt->edit('id', function ($data) {
            return '<div class="btn-group"><a href="' . site_url(route_to('edit_manufacturer', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_manufacturer', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
        });

        echo $dt->generate();
    }

    /**
     * Gets manufacturer for select2.js module
     *
     * @return json
     */
    public function getManufacturersForSelect()
    {
        $data = [];

        $manufacturer_model = new ManufacturersModel();

        $manufacturer_model->select(['id', 'name as text']);

        $id = $this->request->getVar('id');
        if ($id != '') {
            return $this->response->setJSON($manufacturer_model->find($id));
        }

        $page = $this->request->getVar('page');

        $search = $this->request->getVar('search');
        if ($search != '') {
            $manufacturer_model->like('name', "$search");
        }

        $data['results'] = $manufacturer_model->paginate(20, 'default', $page);
        $data['total'] = count($data['results']);
        $data['pagination']['more'] = $data['total'] < 20 ? false : true;

        return $this->response->setJSON($data);
    }

    public function addManufacturer()
    {
        helper('post');

        $data = [];

        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->manufacturerValidationRules);
            if (!$input) {
                $data = [
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                ];
                $this->response->setStatusCode('400');
            } else {
                $model = new ManufacturersModel();
                $model->save([
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'contact_name' => $this->request->getVar('contact_name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);
                $data = [
                    'success' => true,
                    'message' => 'Üretici başarıyla eklendi!',
                    'id' => $model->getInsertID(),
                ];
            }
        } else {
            $data = [
                'success' => false,
                'errors' => [
                    "Geçersiz istek!"
                ],
            ];
            $this->response->setStatusCode('400');
        }

        $data['csrfName'] = csrf_token();
        $data['csrfHash'] = csrf_hash();

        return $this->response->setJSON($data);
    }

    public function showOrder(int $id, string $slug)
    {
        $order_model = new OrdersModel();
        $order_products_model = new OrderProductsModel();
        $salesman_model = new UsersModel();

        $data = [
            'page_title' => 'Sipariş Özeti',
            'order' => $order_model->getOrderByID($id),
        ];

        $data['manufacturer'] = $order_products_model
            ->select('m.name, m.contact_name, m.email, m.phone, om.order_value')
            ->where('order_products.order_id', $data['order']['id'])
            ->where('om.order_value', $slug)
            ->join('products as p', 'order_products.product_id = p.id')
            ->join('manufacturers as m', 'p.manufacturer_id = m.id')
            ->join('order_meta as om', 'om.order_product_id = order_products.id')
            ->first();

        $data['order_products'] = $order_products_model
            ->select('order_products.id, order_products.price, order_products.estimated_delivery, order_products.quantity, order_products.extras, order_products.status, p.id as product_id, p.name as product_name, p.category as product_category, p.image as product_image')
            ->where('order_products.order_id', $data['order']['id'])
            ->where('om.order_value', $slug)
            ->join('products as p', 'order_products.product_id = p.id')
            ->join('manufacturers as m', 'p.manufacturer_id = m.id')
            ->join('order_meta as om', 'om.order_product_id = order_products.id')
            ->findAll();

        $data['salesman'] = $salesman_model
            ->select('full_name, email, phone, address')
            ->find($data['order']['salesman_id']);

        if (empty($data['order_products']) && empty($data['manufacturer'])) {
            return redirect()->to('404');
        } else {
            $data['order_products'] = array_map(function ($item) {
                $item['extras'] = $item['extras'] != '' ? json_decode($item['extras'], true) : '';
                return $item;
            }, $data['order_products']);
        }
        $data['order_products'] = $order_products_model->beautifyStatus($data['order_products'], $this->orderStatuses);
        $data['order_products'] = $order_products_model->beautifyPrice($data['order_products']);
        $data['order_products'] = $order_products_model->beautifyDate($data['order_products']);

        // $data['order_products'] = $this->groupArray($data['order_products'], 'manufacturer_id');
        return view('pages/orders/manufacturer_order', $data);
    }
}
