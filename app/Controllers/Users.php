<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Users extends BaseController
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
        'user_type' => [
            'rules' => 'required',
            'label' => 'Kullanıcı tipi'
        ]
    ];


    protected $userFormElements = [
        'full_name' => [
            'type' => 'text',
            'column' => 'col-md-6',
            'label' => 'Ad Soyad *',
            'placeholder' => 'Kullanıcı adı ve soyadı'
        ],
        'email' => [
            'type' => 'email',
            'column' => 'col-md-6',
            'label' => 'E-Posta Adresi *',
            'placeholder' => 'Kullanıcı e-posta adresi'
        ],
        'password' => [
            'type' => 'password',
            'column' => 'col-md-5',
            'label' => 'Şifre *',
        ],
        'phone' => [
            'type' => 'text',
            'column' => 'col-md-4',
            'label' => 'Telefon Numarası',
            'placeholder' => 'İletişim kurulacak telefon numarası'
        ],
        'user_type' => [
            'type' => 'select',
            'column' => 'col-md-3',
            'label' => 'Kullanıcı Tipi *',
            'empty_label' => 'Lütfen bir kullanıcı tipi seçiniz',
            'data' => [],
            'show_value' => 'name',
        ],
        'address' => [
            'type' => 'textarea',
            'column' => 'col-md-12',
            'label' => 'Adres',
            'rows' => 2,
            'placeholder' => 'Kullanıcı adresi',
        ]
    ];

    protected $pageButtons = [
        'add_user' => [
            'route' => 'add_user',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'Yönetici Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'users' => [
            'route' => 'users',
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
            'page_title' => 'Yöneticiler',
            'active' => ['users', 'users'],
            'page_buttons' => [$this->pageButtons['add_user']]
        ];

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        return view('pages/users/list', $data);
    }

    public function add()
    {
        helper(['form', 'formElement']);
        $userHierarchy = config('UserHierarchy');

        $data = [
            'page_title' => 'Yönetici Ekle',
            'page_desc' => 'Yönetici ekleme formu.',
            'active' => ['users', 'add_user'],
            'page_buttons' => [$this->pageButtons['users']]
        ];

        $user_model = new UsersModel();
        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->userValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $user_model->save([
                    'user_type' => $this->request->getVar('user_type', FILTER_SANITIZE_STRING),
                    'full_name'  => $this->request->getVar('full_name', FILTER_SANITIZE_STRING),
                    'password'  => $this->request->getVar('password', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);
                $session = session();
                $session->setFlashData('success', 'Yönetici başarıyla eklendi');
                return redirect()->route('users');
            }
        }

        $formElements = $this->userFormElements;

        $formElements['user_type']['data'] = $userHierarchy->getAdmins();
        $data['form_elements'] = makeFormElement($formElements, $data['validation'] ?? null);
        return view('pages/users/add', $data);
    }

    public function edit(int $user_id)
    {
        helper(['form', 'formElement']);
        $userHierarchy = config('UserHierarchy');

        $data = [
            'page_title' => 'Yönetici Düzenle',
            'page_desc' => 'Yönetici düzenleme formu.',
            'active' => ['users', 'add_user'],
            'page_buttons' => [$this->pageButtons['users'], $this->pageButtons['add_user']]
        ];

        $user_model = new UsersModel();
        $data['user'] = $user_model->whereIn('user_type', $userHierarchy->getAdmins(true))->find($user_id);

        if (!$data['user']) {
            session()->setFlashData('error', 'Kullanıcı bulunamadı!');
            return redirect()->route('users');
        }

        if ($this->request->getMethod() == 'post') {
            $validationRules = $this->userValidationRules;

            // check is it current email
            $email = $this->request->getVar('email', FILTER_SANITIZE_EMAIL);
            if ($data['user']['email'] == $email) {
                $validationRules['email'] = 'required';
            }
            // if password is going to update
            $password = $this->request->getVar('password', FILTER_SANITIZE_STRING) != '' ? $this->request->getVar('password', FILTER_SANITIZE_STRING) : null;
            if (!$password) {
                unset($validationRules['password']);
            }

            $input = $this->validate($validationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {

                $user_model->update($user_id, [
                    'user_type' => $this->request->getVar('user_type', FILTER_SANITIZE_STRING),
                    'full_name'  => $this->request->getVar('full_name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'password' => $this->request->getVar('password', FILTER_SANITIZE_STRING),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);

                $session = session();
                $session->setFlashData('success', 'Kullanıcı başarıyla düzenlendi');
                return redirect()->route('users');
            }
        }
        $formElements = $this->userFormElements;
        $formElements['user_type']['data'] = $userHierarchy->getAdmins();

        $data['form_elements'] = makeFormElement($formElements, $data['validation'] ?? null, $data['user']);

        return view('pages/users/add', $data);
    }

    public function delete(int $user_id)
    {
        $userHierarchy = config('userHierarchy');
        $user_model = new UsersModel();

        $check = $user_model->where(['id' => $user_id])->whereIn('user_type', $userHierarchy->getAdmins(true))->first();
        if ($check) {
            $user_model->delete($user_id);
            session()->setFlashData('success', 'Yönetici başarıyla silindi!');
        } else {
            session()->setFlashData('error', 'Yönetici bulunamadı!');
        }

        return redirect()->route('users');
    }

    public function getUsers()
    {
        helper(['dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->query('SELECT u.user_type, u.full_name, u.phone, u.created_at, u.id FROM users u WHERE (u.user_type = "admin" OR u.user_type = "mod") AND u.deleted_at IS NULL');

        $dt->edit('user_type', function ($data) {
            return dtAdminType($data['user_type']);
        });

        $dt->edit('created_at', function ($data) {
            return dtBeautifyDate($data['created_at']);
        });

        $dt->edit('id', function ($data) {
            return '<div class="btn-group"><a href="' . site_url(route_to('edit_user', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_user', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
        });

        echo $dt->generate();
    }
}
