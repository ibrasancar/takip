<?php

namespace App\Controllers;

use App\Models\CustomersModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Customers extends BaseController
{
    protected $customerValidationRules = [
        'name'  => [
            'rules' => 'required',
            'label' => 'İsim',
        ],
        'email'  => [
            'rules' => 'required',
            'label' => 'E-posta adresi',
        ],
        'phone'  => [
            'rules' => 'required',
            'label' => 'Telefon numarası',
        ],
        'address'  => [
            'rules' => 'required',
            'label' => 'Adres',
        ],
    ];

    protected $customerFormElements = [
        'name' => [
            'type' => 'text',
            'column' => 'col-md-12',
            'label' => 'Tam Adı *',
            'placeholder' => 'Müşterinin adı ve soyadı',
        ],
        'email' => [
            'type' => 'email',
            'column' => 'col-md-4',
            'label' => 'E-Posta Adresi',
            'placeholder' => 'Bildirimlerin gideceği e-posta adresi',
        ],
        'phone' => [
            'type' => 'text',
            'column' => 'col-md-4',
            'label' => 'Telefon Numarası',
            'placeholder' => 'Bildirimlerin gideceği telefon numarası',
        ],
        'birthday' => [
            'type' => 'date',
            'column' => 'col-md-4',
            'label' => 'Doğum Tarihi',
            'placeholder' => 'Müşterinin doğum tarihi',
        ],
        'address' => [
            'type' => 'textarea',
            'column' => 'col-md-12',
            'label' => 'Adres',
            'rows' => 2,
            'placeholder' => 'Müşterinin adres bilgisi...'
        ]
    ];

    protected $pageButtons = [
        'add_customer' => [
            'route' => 'add_customer',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'Müşteri Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'customers' => [
            'route' => 'customers',
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
            'page_title' => 'Müşteriler',
            'active' => ['customers', 'customers'],
            'page_buttons' => [
                $this->pageButtons['add_customer'],
            ],
        ];

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        return view('pages/customers/list', $data);
    }

    public function add()
    {
        helper(['form', 'formElement']);
        $data = [
            'page_title' => 'Müşteri ekle',
            'active' => ['customers', 'add_customer'],
            'page_buttons' => [
                $this->pageButtons['customers'],
            ],
        ];

        $customer_model = new CustomersModel();
        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->customerValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $customer_model->save([
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'birthday' => $this->request->getVar('birthday', FILTER_SANITIZE_STRING),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                    'salesman_id' => session()->get('id'),
                ]);
                $session = session();
                $session->setFlashData('success', 'Müşteri başarıyla eklendi');
                return redirect()->route('customers');
            }
        }

        $data['form_elements'] = makeFormElement($this->customerFormElements, $data['validation'] ?? null);

        return view('pages/customers/add', $data);
    }

    public function edit(int $customer_id)
    {
        helper(['form', 'formElement']);
        $data = [
            'page_title' => 'Müşteri Düzenle',
            'active' => ['customers', 'add_customer'],
            'page_buttons' => [$this->pageButtons['customers'], $this->pageButtons['add_customer']]
        ];

        $customer_model = new CustomersModel();
        $data['customer'] = $customer_model->find($customer_id);

        if (!$data['customer']) {
            session()->setFlashData('error', 'Müşteri bulunamadı!');
            return redirect()->route('customers');
        }

        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->customerValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $customer_model->update($customer_id, [
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'birthday' => $this->request->getVar('birthday', FILTER_SANITIZE_STRING),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                    'salesman_id' => session()->get('id'),
                ]);

                $data['success'] = 'Müşteri başarıyla düzenlendi.';
                $data['customer'] = $customer_model->find($customer_id);
            }
        }

        $data['form_elements'] = makeFormElement($this->customerFormElements, $data['validation'] ?? null, $data['customer']);

        return view('pages/customers/add', $data);
    }

    public function delete(int $customer_id)
    {
        $customer_model = new CustomersModel();
        $session = session();

        $result = $customer_model->where(['deleted_at IS NULL', 'id' => $customer_id])->first();

        if ($result) {
            $customer_model->delete($customer_id);
            $session->setFlashData('success', 'Müşteri başarıyla silindi.');
        } else {
            $session->setFlashData('danger', 'Müşteri bulunamadı.');
        }
        return redirect()->route('customers');
    }

    public function getCustomers()
    {
        helper(['dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter);
        $query = 'SELECT c.name, c.phone, c.created_at, c.id FROM customers c WHERE c.deleted_at IS NULL';

        if (!isAdmin())
            $query .= ' AND salesman_id = ' . session()->get('id');

        $dt->query($query);

        $dt->edit('created_at', function ($data) {
            return dtBeautifyDate($data['created_at']);
        });

        $dt->edit('id', function ($data) {
            return '<div class="btn-group"><a href="' . site_url(route_to('edit_customer', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_customer', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
        });

        echo $dt->generate();
    }

    public function getCustomersForSelect()
    {
        $data = [];

        $page = $this->request->getVar('page');

        $customer_model = new CustomersModel();
        $customer_model->select("customers.id, name as text");


        $id = $this->request->getVar('id');
        if ($id != '') {
            $data = $customer_model->find($id);
            return $this->response->setJSON($data);
        }

        $search = $this->request->getVar('search');
        if ($search != '') {
            $customer_model->like('customers.name', "$search");
        }

        $data['results'] = $customer_model->paginate(20, 'default', $page);

        $data['total'] = count($data['results']);
        $data['pagination']['more'] = $data['total'] < 20 ? false : true;

        return $this->response->setJSON($data);
    }

    public function addCustomer()
    {
        helper(['form']);

        $customer_model = new CustomersModel();
        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->customerValidationRules);
            if (!$input) {
                $data['errors'] = $this->validator->getErrors();
                $this->response->setStatusCode(400);
            } else {
                $customer_model->save([
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'birthday' => $this->request->getVar('birthday', FILTER_SANITIZE_STRING),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                    'salesman_id' => session()->get('id'),
                ]);
                $data['customer_id'] = $customer_model->getInsertID();
                $data['success'] = true;
            }
        }

        $data['csrfName'] = csrf_token();
        $data['csrfHash'] = csrf_hash();

        return $this->response->setJSON($data);
    }
}
