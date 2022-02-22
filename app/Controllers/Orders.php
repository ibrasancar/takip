<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Email;
use App\Libraries\SMS;
use App\Models\OrderMetaModel;
use App\Models\OrderProductsModel;
use App\Models\OrdersModel;
use CodeIgniter\I18n\Time;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Orders extends BaseController
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

    protected $pageButtons = [
        'add_order' => [
            'route' => 'add_order',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'Sipariş Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'orders' => [
            'route' => 'orders',
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
            'page_title' => 'Siparişler',
            'page_buttons' => [
                $this->pageButtons['add_order']
            ],
            'active' => ['orders', 'orders'],
        ];

        $session = session();
        $data['success'] = $session->getFlashData('success');
        $data['error'] = $session->getFlashData('error');

        if ($this->request->getMethod() == 'get' && $this->request->getVar('is_admin_confirm') == true) {
            $data['page_buttons'] = [];
            $data['page_buttons'][] = $this->pageButtons['orders'];
            $data['page_title'] = 'Yönetici onayı olmayan siparişler';
        } else {
            $data['page_buttons'][] = [
                'href' => site_url(route_to('orders')) . '?is_admin_confirm=true',
                'class' => 'btn btn-primary',
                'text' => 'Onaylanmamış Siparişleri Görüntüle',
                'icon' => [
                    'class' => 'material-icons-outlined',
                    'name' => 'check_circle',
                ]
            ];
        }
        if ($this->request->getMethod() == 'post' && ($requestType = $this->request->getVar('request_type')) != '' && ($order_id = $this->request->getVar('order_id')) != '') {
            if (isAdmin()) {
                $order_model = new OrdersModel();
                $time = new Time('now');

                if ($requestType == 'confirm') {
                    $order_model->set('admin_confirm', $time->toDateTimeString())->update($order_id);

                    // send manufacturers mail
                    $mail = new Email();
                    $mail->sendNewOrderToManufacturer($order_id);
                }

                if ($requestType == 'cancel') {
                    $order_model->set('admin_confirm', null)->update($order_id);
                }

                $data = [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                ];
                return $this->response->setJSON($data);
            } else {
                $this->response->setStatusCode('400');
                $data = [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                    'error' => 'Yetkiniz bulunmamaktadır.',
                ];
                return $this->response->setJSON($data);
            }
        }
        return view('pages/orders/list', $data);
    }

    public function add()
    {
        $order_model = new OrdersModel();
        $order_model->insert([
            'slug' => $order_model->createSlug(),
            'salesman_id' => session()->get('id'),
        ]);

        $session = session();
        $session->setFlashData('success', 'Sipariş başarıyla oluşturuldu! Hemen düzenlemeye başlayabilirsiniz.');
        return redirect()->route('edit_order', [$order_model->getInsertID()]);
    }

    public function edit(int $id)
    {
        helper('number');
        $orderModel = new OrdersModel();
        $orderProductsModel = new OrderProductsModel();
        $orderMetaModel = new OrderMetaModel();

        $data = [
            'page_title' => 'Siparişi Düzenle',
            'active' => ['orders', 'orders'],
            'order_statuses' => $this->orderStatuses,
            'page_buttons' => [$this->pageButtons['orders']]
        ];

        $data['order'] = $orderModel
            ->select('orders.id, orders.slug, orders.status, orders.customer_id, c.name as customer_name, c.id as customer_id, orders.total_price, orders.deposit, orders.discount, orders.salesman_id, orders.customer_confirm, orders.admin_confirm, orders.order_note, orders.completed_at, orders.created_at, orders.updated_at, orders.deleted_at, c.phone')
            ->join('customers as c', 'c.id = orders.customer_id', 'left');

        if (session('user_type') != 'su_admin' && session('user_type') != 'admin') {
            $data['order']->where('orders.salesman_id', session('id'));
        }
        $data['order'] = $data['order']->find($id);

        $session = session();
        if (!$data['order']) {
            $session->setFlashData('error', 'Sipariş bulunamadı!');
            return redirect()->route('orders');
        }

        $data['success'] = $session->getFlashData('success');

        $data['order']['deposit'] = $orderModel->beautifyPrice($data['order']['deposit']);
        $data['order']['discount'] = $orderModel->beautifyPrice($data['order']['discount']);
        $data['order']['total_price'] = $orderModel->beautifyPrice($data['order']['total_price']);

        $data['order_products'] = $orderProductsModel
            ->select('order_products.id, order_products.quantity, p.id as product_id, p.name as product_name, order_products.price, order_products.estimated_delivery, order_products.status')
            ->where('order_id', $data['order']['id'])
            ->join('products as p', 'p.id = order_products.product_id', 'left')
            ->findAll();

        $data['order_products'] = $orderProductsModel->beautifyStatus($data['order_products'], $this->orderStatuses);
        $data['order_products'] = $orderProductsModel->beautifyPrice($data['order_products']);
        $data['order_products'] = $orderProductsModel->beautifyDate($data['order_products']);


        if ($this->request->getMethod() == 'post') {
            if ($this->request->getVar('sendmail')) {
                $mail = new Email();
                if (in_array($this->request->getVar('sendmail'), ['new', 'update'])) {
                    $response = $mail->sendOrderMail($id, $this->request->getVar('sendmail'));
                }
                if ($this->request->getVar('sendmail') == 'manufacturer-update') {
                    $response = $mail->sendNewOrderToManufacturer($id, 'new');
                }
                return $this->response->setJSON([
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                    'success' => $response,
                ]);
            }
            if ($this->request->getVar('sendsms')) {
                $sms = new SMS();
                if ($this->request->getVar('sendsms') == 'new') {
                    $request = $sms->sendRequest('http://api.iletimerkezi.com/v1/send-sms', $response = $sms->sendOrderSMS($data['order']), array('Content-Type: text/xml'));
                    $result = simplexml_load_string($request);
                    return $this->response->setJSON([
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash(),
                        'status' => $result->status,
                    ]);
                }
                if ($this->request->getVar('sendsms') == 'manufacturer-update') {
                    $order_products = $orderProductsModel
                        ->select('order_products.id, m.id as manufacturer_id, m.name , m.contact_name , m.email , m.phone, om.order_title, om.order_value')
                        ->where('order_id', $id)
                        ->join('products as p', 'order_products.product_id = p.id')
                        ->join('manufacturers as m', 'p.manufacturer_id = m.id')
                        ->join('order_meta as om', 'om.order_product_id = order_products.id', 'left')
                        ->findAll();
                    foreach ($order_products as $k => $v) {
                        if ($v['order_title'] == '') {
                            $om = [
                                'order_product_id' => $v['id'],
                                'order_title' => 'manufacturer_show',
                                'order_value' => $orderModel->createSlug(),
                            ];
                            $orderMetaModel->save($om);
                            $order_products[$k]['order_title'] = $om['order_title'];
                            $order_products[$k]['order_value'] = $om['order_value'];
                        }
                    }

                    $order_products = groupArray($order_products, 'manufacturer_id');
                    $order['order'] = $data['order'];
                    $order['order_products'] = $order_products;

                    $request = $sms->sendRequest('http://api.iletimerkezi.com/v1/send-sms', $response = $sms->sendNewOrderToManufacturer($order), array('Content-Type: text/xml'));
                    $result = simplexml_load_string($request);
                    return $this->response->setJSON([
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash(),
                        'status' => $result->status,
                    ]);
                }
            }
        }


        return view('pages/orders/add', $data);
    }

    public function updateOrder()
    {
        helper('post');
        if ($this->request->getMethod() == 'post') {
            $rules = [
                'id' => [
                    'rules' => 'required',
                    'label' => 'Sipariş ID',
                ],
                'created_at' => [
                    'rules' => 'required',
                    'label' => 'Oluşturma tarihi'
                ]
            ];
            $input = $this->validate($rules);

            if (!$input) {
                $data = [
                    'success' => false,
                    'msg' => $this->validator->getErrors(),
                ];
                $this->response->setStatusCode('400');
            } else {
                $orderID = $this->request->getVar('id');
                $orderModel = new OrdersModel();

                // check is there a any order with this orderID
                if (!$orderModel->where('id', $orderID)->first()) {
                    $data = [
                        'success' => false,
                        'msg' => 'Böyle bir sipariş ürünü zaten mevcut!',
                    ];
                    $this->response->setStatusCode('400');
                } else {
                    $data['post'] = [
                        'customer_id' => $this->request->getVar('customer_id'),
                        'deposit' => clear_price_mask($this->request->getVar('deposit')),
                        'discount' => clear_price_mask($this->request->getVar('discount')),
                        'total_price' => clear_price_mask($this->request->getVar('total')),
                        'order_note' => $this->request->getVar('order_note'),
                        'completed_at' => $this->request->getVar('completed_at'),
                        'created_at' => $this->request->getVar('created_at'),
                    ];
                    $orderModel->update($orderID, $data['post']);
                    $data = [
                        'success' => true,
                        'msg' => 'Sipariş başarıyla güncellendi!',
                        'redirect' => site_url(route_to('orders')),
                    ];
                }
            }
        }

        $data['csrfName'] = csrf_token();
        $data['csrfHash'] = csrf_hash();
        return $this->response->setJSON($data);
    }

    public function addOrderProduct()
    {
        helper('post');
        $orderProductsModel = new OrderProductsModel();
        if ($this->request->getMethod('post')) {
            $rules = [
                'product_id' => [
                    'rules' => 'required',
                    'label' => 'Ürün',
                ],
                'estimated_delivery' => [
                    'rules' => 'required',
                    'label' => 'Tahmini teslimat tarihi',
                ],
                'price' => [
                    'rules' => 'required',
                    'label' => 'Fiyat'
                ],
                'status' => [
                    'rules' => 'required',
                    'label' => 'Sipariş durumu',
                ],
                'quantity' => [
                    'rules' => 'required',
                    'label' => 'Adet'
                ]
            ];
            $input = $this->validate($rules);

            // if there is an error, edit response status
            if (!$input) {
                $data = [
                    'success' => false,
                    'msg' => $this->validator->getErrors(),
                ];
                $this->response->setStatusCode('400');
                // else check data and insert db
            } else {
                // check is update or insert
                $orderProductsID = $this->request->getVar('order_product_id');
                $getData = [
                    'order_id' => $this->request->getVar('order_id'),
                    'quantity' => $this->request->getVar('quantity'),
                    'product_id' => $this->request->getVar('product_id'),
                    'estimated_delivery' => $this->request->getVar('estimated_delivery'),
                    'shipping_date' => $this->request->getVar('shipping_date') != '' ? $this->request->getVar('shipping_date') : null,
                    'deliver_confirm' => $this->request->getVar('deliver_confirm') != '' ? $this->request->getVar('deliver_confirm') : null,
                    'price' => clear_price_mask($this->request->getVar('price')),
                    'status' => $this->request->getVar('status'),
                    'extras' => !empty($this->request->getVar('extras')) && !empty(array_filter($this->request->getVar('extras'))) ? json_encode(array_filter($this->request->getVar('extras')), JSON_FORCE_OBJECT) : null,
                ];

                // is insert
                if ($orderProductsID == null) {
                    $orderProductsModel->insert($getData);
                    $insertID = $orderProductsModel->getInsertID();
                    // is update
                } else {
                    $orderProductsModel->update($orderProductsID, $getData);
                    $insertID = $orderProductsID;
                }

                $data['data'] = $orderProductsModel
                    ->select('order_products.id, order_products.quantity, p.id as product_id, p.name as product_name, order_products.price, order_products.estimated_delivery, order_products.shipping_date, order_products.deliver_confirm, order_products.status')
                    ->where('order_products.id', $insertID)
                    ->join('products as p', 'p.id = order_products.product_id', 'left')
                    ->first();

                $data['data'] = $orderProductsModel->beautifyStatus($data['data'], $this->orderStatuses);
                $data['data'] = $orderProductsModel->beautifyPrice($data['data']);
                $data['data'] = $orderProductsModel->beautifyDate($data['data']);


                $data['response'] = [
                    'success' => true,
                    'msg' => 'Ürün başarıyla eklendi!',
                ];
            }
            $data['csrfName'] = csrf_token();
            $data['csrfHash'] = csrf_hash();
            return $this->response->setJSON($data);
        }
    }

    public function getOrderProduct()
    {
        if ($this->request->getMethod() == 'post') {
            $orderProductsModel = new OrderProductsModel();
            $data['order_products'] = $orderProductsModel->find($this->request->getVar('id'));
            if ($data['order_products']) {
                $data['order_products']['extras'] = json_decode($data['order_products']['extras']);
                $data['response'] = [
                    'success' => true,
                    'msg' => 'Ürün başarıyla çekildi.',
                ];
            } else {
                $this->response->setStatusCode('400');
                $data['response'] = [
                    'success' => false,
                    'msg' => 'Ürün bulunmadı.',
                ];
            }
        } else {
            $this->response->setStatusCode('400');
            $data['response'] = [
                'success' => false,
                'msg' => 'Geçersiz istek.',
            ];
        }

        $data['csrfName'] = csrf_token();
        $data['csrfHash'] = csrf_hash();

        return $this->response->setJSON($data);
    }

    public function deleteOrderProduct()
    {
        if ($this->request->getMethod() == 'post') {
            $orderProductsModel = new OrderProductsModel();
            $id = $this->request->getVar('id');
            $data['order_products'] = $orderProductsModel->find($id);
            if ($data['order_products']) {
                $orderProductsModel->where('id', $id)->delete();
                $data['response'] = [
                    'success' => true,
                    'msg' => 'Ürün başarıyla silindi.',
                ];
            } else {
                $this->response->setStatusCode('400');
                $data['response'] = [
                    'success' => false,
                    'msg' => 'Ürün bulunmadı.',
                ];
            }
        } else {
            $this->response->setStatusCode('400');
            $data['response'] = [
                'success' => false,
                'msg' => 'Geçersiz istek.',
            ];
        }

        $data['csrfName'] = csrf_token();
        $data['csrfHash'] = csrf_hash();
        return $this->response->setJSON($data);
    }

    public function show(string $slug)
    {
        $data = [
            'page_title' => 'Sipariş #' . $slug,
        ];

        $order_model = new OrdersModel();
        $order_products_model = new OrderProductsModel();
        $data['order'] = $order_model->where('slug', $slug)->first();

        if (empty($data['order'])) {
            return redirect()->route('login');
        }

        $data['order']['created_at'] = $order_model->beautifyDate($data['order']['created_at']);

        $data['order']['deposit'] = $order_model->beautifyPrice($data['order']['deposit']);
        $data['order']['discount'] = $order_model->beautifyPrice($data['order']['discount']);
        $data['order']['total_price'] = $order_model->beautifyPrice($data['order']['total_price']);

        $data['order_products'] = $order_products_model
            ->select('order_products.id, order_products.quantity, p.id as product_id, p.name as product_name, p.image as image, order_products.price, order_products.estimated_delivery, order_products.status')
            ->where('order_id', $data['order']['id'])
            ->join('products as p', 'p.id = order_products.product_id', 'left')
            ->findAll();

        $data['order_products'] = $order_products_model->beautifyStatus($data['order_products'], $this->orderStatuses);
        $data['order_products'] = $order_products_model->beautifyPrice($data['order_products']);
        $data['order_products'] = $order_products_model->beautifyDate($data['order_products']);

        return view('pages/orders/show', $data);
    }

    public function delete(int $id)
    {
        $session = session();
        $orderModel = new OrdersModel();

        if ($orderModel->where('id', $id)->countAllResults() > 0) {
            $orderModel->delete($id);
            $session->setFlashData('success', 'Sipariş başarıyla silindi!');
        } else {
            $session->setFlashData('error', 'Sipariş bulunamadı!');
        }

        return redirect()->route('orders');
    }

    // for orders index page => datatable
    public function getOrders()
    {
        helper(['number', 'dtformatter']);
        $orderModel = new OrdersModel();

        $dt = new Datatables(new Codeigniter4Adapter);

        $default_query = 'SELECT o.slug, u.full_name, c.name, o.total_price, o.created_at, o.completed_at, o.id, o.admin_confirm
                FROM orders o
                LEFT JOIN customers c ON c.id = o.customer_id AND c.deleted_at IS NULL
                LEFT JOIN users u ON u.id = o.salesman_id AND c.deleted_at IS NULL
                WHERE o.deleted_at IS NULL';

        if (!isAdmin()) {
            $default_query .= ' AND o.salesman_id = ' . session()->get('id');
        }

        if ($this->request->getMethod() == 'get' && $this->request->getVar('is_admin_confirm') == true) {
            $default_query .= ' AND o.admin_confirm IS NULL';
        }

        $dt->query($default_query);

        $dt->edit('slug', function ($data) {
            return '<span class="btn btn-outline-dark copyme w-100">#' . $data['slug'] . '</span>';
        });
        $dt->edit('full_name', function ($data) {
            return $data['full_name'] ?? '<span class="badge badge-danger">Tanımlanmamış</span>';
        });
        $dt->edit('name', function ($data) {
            return $data['name'] ?? '<span class="badge badge-danger">Tanımlanmamış</span>';
        });
        // $dt->edit('total_price', function ($data) {
        //     return dtBeautifyPrice($data['total_price']);
        // });

        $minDate = $this->request->getVar('minDate');
        $maxDate = $this->request->getVar('maxDate');
        $minPrice = $this->request->getVar('minPrice');
        $maxPrice = $this->request->getVar('maxPrice');

        if ($minDate && !$maxDate)
            $dt->query($default_query . ' AND o.created_at >= "' . $minDate . '"');

        if ($maxDate && !$minDate)
            $dt->query($default_query . ' AND o.created_at <= "' . $maxDate . '"');

        if ($maxDate && $minDate) {
            if (strtotime($maxDate) > strtotime($minDate))
                $dt->query($default_query . ' AND ( o.created_at BETWEEN "' . $minDate . '" AND "' . $maxDate . '" )');
            else
                $dt->query($default_query . ' AND o.created_at <= "' . $maxDate . '"');
        }

        if ($minPrice && !$maxPrice) {
            $dt->filter('total_price', function () use ($minPrice) {
                return $this->greaterThan($minPrice);
            });
        }
        if ($maxPrice && !$minPrice) {
            $dt->filter('total_price', function () use ($maxPrice) {
                return $this->lessThan($maxPrice);
            });
        }
        if ($maxPrice && $minPrice) {
            $dt->filter('total_price', function () use ($maxPrice, $minPrice) {
                if ($maxPrice > $minPrice)
                    return $this->between($minPrice, $maxPrice);
                else
                    return  $this->lessThan($maxPrice);
            });
        }

        $dt->edit('created_at', function ($data) {
            return dtBeautifyDate($data['created_at']);
        });
        $dt->edit('completed_at', function ($data) {
            return dtBeautifyDate($data['completed_at']) != '' ? dtBeautifyDate($data['completed_at']) : '<span class="badge badge-warning">Tamamlanmamış</span>';
        });

        $dt->edit('id', function ($data) {

            $string = '<div class="btn-group">';

            if (isAdmin()) {
                if (($data['admin_confirm'] == null)) {
                    $string .= '<button class="btn btn-sm btn-outline-success confirm-button" data-type="confirm" data-id="' . $data['id'] . '"><i class="material-icons">check</i> Onayla</button>';
                } else {
                    $string .= '<button class="btn btn-sm btn-outline-success" data-type="confirm" data-id="' . $data['id'] . '" disabled="disabled"><i class="material-icons">check</i> Onayla</button>';
                }
            }

            $string .= '<a href="' . site_url(route_to('edit_order', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_order', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';

            return $string;
        });

        echo $dt->generate();
    }

    /**
     * get order for add ticket page
     *
     * @return json
     */
    public function getOrder()
    {
        helper('dtformatter');
        $data = [];
        if ($this->request->getMethod('post')) {
            $order_id = $this->request->getVar('order_id');
            $orderModel = new OrdersModel();
            $data['order'] = $orderModel
                ->select('orders.id, orders.slug, orders.created_at, orders.completed_at, orders.total_price, c.name, c.phone, c.address')
                ->where('orders.id', $order_id)
                ->join('customers as c', 'c.id = orders.customer_id', 'left')
                ->first();
            if (!$data['order']) {
                return $this->response->setJSON([
                    'response' => [
                        'success' => false,
                        'msg' => 'Böyle bir sipariş ürünü bulunamadı.',
                    ]
                ]);
            }
            $data['order']['total_price'] = dtBeautifyPrice($data['order']['total_price']);
            $data['order']['created_at'] = dtBeautifyDate($data['order']['created_at']);
            $data['order']['completed_at'] = dtBeautifyDate($data['order']['completed_at'], '<span class="badge badge-warning">Tamamlanmamış</span>');
            $data['csrfName'] = csrf_token();
            $data['csrfHash'] = csrf_hash();
        }

        return $this->response->setJSON($data);
    }

    /**
     * get single order or all orders with pagination for select2 by json data    
     *
     * @return json
     */
    public function getOrdersForSelect()
    {
        $data = [];

        $page = $this->request->getVar('page');

        $order_model = new OrdersModel();
        $order_model->select("orders.slug, orders.id, customers.name as text, orders.created_at");

        $id = $this->request->getVar('id');
        if ($id != '') {
            $data = $order_model->join('customers', 'customers.id = orders.customer_id', 'left')->find($id);
            return $this->response->setJSON($data);
        }

        $search = $this->request->getVar('search');


        if ($search != '') {
            $order_model->like('orders.slug', "$search")->orLike('customers.name', "$search");
        }

        if (!isAdmin()) {
            if (session()->get('user_type') == 'salesman')
                $order_model->where('orders.salesman_id', session('id'));
        }

        $data['results'] = $order_model->join('customers', 'customers.id = orders.customer_id', 'left')->paginate(20, 'default', $page);

        // $data['results'] = array_map(function($item) use ($order_model) {
        //     $item['text'] = "#" . $item['slug'] . " - " . $item['name'] . " - " . $order_model->beautifyDate($item['created_at'], null, 'dd/MM/YYYY HH:mm');
        //     unset($item['slug']);
        //     unset($item['name']);
        //     unset($item['created_at']);
        //     return $item;
        // }, $results);

        $data['total'] = count($data['results']);
        $data['pagination']['more'] = $data['total'] < 20 ? false : true;

        return $this->response->setJSON($data);
    }

    public function confirmCustomer()
    {
        $slug = $this->request->getVar('slug');

        $order_model = new OrdersModel();
        $time = new Time('now');

        $order_model->set('customer_confirm', $time->toDateTimeString())->where('slug', $slug)->update();
        $data = [
            'success' => 'Başarıyla onaylandı!'
        ];
        return $this->response->setJSON($data);
    }
}
