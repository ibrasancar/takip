<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Salesmans extends BaseController
{
    protected $userValidationRules = [
        'full_name' => [
            'rules' => 'required',
            'label' => 'Ad soyad',
        ],
        'email' => [
            'rules' => 'required|is_unique[users.email]',
            'label' => 'E-Posta adresi',
        ],
        'password' => [
            'rules' => 'required|min_length[6]',
            'label' => 'Şifre alanı'
        ],
        'phone' => [
            'rules' => 'required',
            'label' => 'Telefon numarası',
        ],
        'sale_percentage' => [
            'rules' => 'required',
            'label' => 'Komisyon',
        ]
    ];

    protected $userFormElements = [
        'full_name' => [
            'type' => 'text',
            'column' => 'col-md-6',
            'label' => 'Ad Soyad *',
        ],
        'email' => [
            'type' => 'email',
            'column' => 'col-md-6',
            'label' => 'E-Posta Adresi *',
        ],
        'password' => [
            'type' => 'password',
            'column' => 'col-md-5',
            'label' => 'Şifre *',
        ],
        'phone' => [
            'type' => 'text',
            'column' => 'col-md-5',
            'label' => 'Telefon Numarası',
        ],
        'sale_percentage' => [
            'type' => 'text',
            'column' => 'col-md-2',
            'label' => 'Komisyon *',
        ],
        'address' => [
            'type' => 'textarea',
            'column' => 'col-md-12',
            'label' => 'Adres',
            'rows' => 2,
        ]
    ];

    protected $pageButtons = [
        'add_salesman' => [
            'route' => 'add_salesman',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'Satış Danışmanı Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'salesmans' => [
            'route' => 'salesmans',
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
            'page_title' => 'Satış Danışmanları',
            'page_desc' => 'Satış danışmanlarını burada listeleyebilir veya düzeneleyebilirsiniz.',
            'active' => ['salesmans', 'salesmans'],
            'page_buttons' => [$this->pageButtons['add_salesman']]
        ];

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        return view('pages/salesmans/list', $data);
    }

    public function add()
    {
        helper(['form', 'formElement']);

        $data = [
            'page_title' => 'Satış Danışmanı Ekle',
            'page_desc' => 'Satış danışmanı ekleme formu.',
            'active' => ['salesmans', 'add_salesman'],
            'page_buttons' => [$this->pageButtons['salesmans']]
        ];

        $user_model = new UsersModel();
        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->userValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $user_model->save([
                    'user_type' => 'salesman',
                    'full_name'  => $this->request->getVar('full_name', FILTER_SANITIZE_STRING),
                    'password'  => $this->request->getVar('password', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);
                $user_model->addMeta([
                    'user_id' => $user_model->getInsertID(),
                    'meta_title' => 'sale_percentage',
                    'meta_value' => $this->request->getVar('sale_percentage'),
                ]);
                $session = session();
                $session->setFlashData('success', 'Satış danışmanı başarıyla eklendi');
                return redirect()->route('salesmans');
            }
        }

        $data['form_elements'] = makeFormElement($this->userFormElements, $data['validation'] ?? null, null);

        return view('pages/salesmans/add', $data);
    }

    public function edit(int $user_id)
    {
        helper(['form', 'formElement']);

        $data = [
            'page_title' => 'Satış Danışmanı Düzenle',
            'page_desc' => 'Satış danışmanı düzenleme formu.',
            'active' => ['salesmans', 'add_salesman'],
            'page_buttons' => [$this->pageButtons['salesmans'], $this->pageButtons['add_salesman']]
        ];

        $user_model = new UsersModel();

        $data['user'] = $user_model->where('user_type', 'salesman')->find($user_id);
        if (!$data['user']) {
            session()->setFlashData('error', 'Satış danışmanı bulunamadı.');
            return redirect()->route('salesmans');
        }

        $data['user']['sale_percentage'] = $user_model->getSingleMeta($data['user']['id'], 'sale_percentage');

        if ($this->request->getMethod() == 'post') {
            $validationRules = $this->userValidationRules;

            // check is it current email
            $email = $this->request->getVar('email', FILTER_SANITIZE_EMAIL);
            if ($data['user']['email'] == $email) {
                $validationRules['email'] = 'required';
            }
            // if password is going to update
            $password = $this->request->getVar('password', FILTER_SANITIZE_STRING) ? $this->request->getVar('password', FILTER_SANITIZE_STRING) : null;
            if (!$password) {
                unset($validationRules['password']);
            }

            $input = $this->validate($validationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $user_model->update($user_id, [
                    'user_type' => 'salesman',
                    'full_name'  => $this->request->getVar('full_name', FILTER_SANITIZE_STRING),
                    'password'  => $this->request->getVar('password', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);

                $user_model->addMeta([
                    'user_id' => $data['user']['id'],
                    'meta_title' => 'sale_percentage',
                    'meta_value' => $this->request->getVar('sale_percentage'),
                ]);

                $session = session();
                $session->setFlashData('success', 'Satış danışmanı başarıyla düzenlendi');
                return redirect()->route('salesmans');
            }
        }

        $data['form_elements'] = makeFormElement($this->userFormElements, $data['validation'] ?? null, $data['user']);

        return view('pages/salesmans/add', $data);
    }

    public function delete(int $user_id)
    {
        $session = session();
        $user_model = new UsersModel();
        $check = $user_model->where(['user_id' => $user_id, 'user_type' => 'salesman'])->first();
        if ($check) {
            $user_model->delete($user_id);
            $session->setFlashData('success', 'Satış danışmanı başarıyla silindi!');
        } else {
            $session->setFlashData('error', 'Satış danışmanı bulunamadı!');
        }

        return redirect()->route('salesmans');
    }

    public function getSalesmans()
    {
        helper(['dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->query('SELECT u.full_name, u.phone, u.created_at, u.id FROM users u WHERE u.user_type = "salesman" AND u.deleted_at IS NULL');

        $dt->edit('created_at', function ($data) {
            return dtBeautifyDate($data['created_at']);
        });

        $dt->edit('id', function ($data) {
            return '<div class="btn-group"><a href="' . site_url(route_to('edit_salesman', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_salesman', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
        });

        echo $dt->generate();
    }
}
