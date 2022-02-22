<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Technics extends BaseController
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
            'rules' => 'required',
            'label' => 'Şifre alanı'
        ],
        'phone' => [
            'rules' => 'required',
            'label' => 'Telefon numarası',
        ],
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
            'column' => 'col-md-6',
            'label' => 'Şifre *',
        ],
        'phone' => [
            'type' => 'text',
            'column' => 'col-md-6',
            'label' => 'Telefon Numarası',
        ],
        'address' => [
            'type' => 'textarea',
            'column' => 'col-md-12',
            'label' => 'Adres',
            'rows' => 2,
        ]
    ];

    protected $pageButtons = [
        'add_technic' => [
            'route' => 'add_technic',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'Teknik Servis Personeli Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'technics' => [
            'route' => 'technics',
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
            'page_title' => 'Teknik Servis Personelleri',
            'page_desc' => 'Teknik servis personellerini burada listeleyebilir veya düzeneleyebilirsiniz.',
            'active' => ['technics', 'technics'],
            'page_buttons' => [$this->pageButtons['add_technic']]
        ];

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        return view('pages/technics/list', $data);
    }

    public function add()
    {
        helper(['form', 'formElement']);

        $data = [
            'page_title' => 'Teknik Servis Personeli Ekle',
            'page_desc' => 'Teknik servis personeli ekleme formu.',
            'active' => ['technics', 'add_technic'],
            'page_buttons' => [$this->pageButtons['technics']]
        ];

        $user_model = new UsersModel();
        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->userValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $user_model->save([
                    'user_type' => 'technic',
                    'full_name'  => $this->request->getVar('full_name', FILTER_SANITIZE_STRING),
                    'password'  => $this->request->getVar('password', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);
                $session = session();
                $session->setFlashData('success', 'Teknik servis personeli başarıyla eklendi');
                return redirect()->route('technics');
            }
        }

        $data['form_elements'] = makeFormElement($this->userFormElements, $data['validation'] ?? null, null);

        return view('pages/technics/add', $data);
    }

    public function edit(int $user_id)
    {
        helper(['form', 'formElement']);

        $data = [
            'page_title' => 'Teknik Servis Personeli Düzenle',
            'page_desc' => 'Teknik servis personeli düzenleme formu.',
            'active' => ['technics', 'add_technic'],
            'page_buttons' => [$this->pageButtons['technics'], $this->pageButtons['add_technic']]
        ];

        $user_model = new UsersModel();
        $data['user'] = $user_model->where(['id' => $user_id, 'user_type' => 'technic'])->first();

        if (!$data['user']) {
            session()->setFlashData('error', 'Teknik servis personeli bulunamadı!');
            return redirect()->route('technics');
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
                    'user_type' => 'technic',
                    'full_name'  => $this->request->getVar('full_name', FILTER_SANITIZE_STRING),
                    'password'  => $this->request->getVar('password', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);

                $session = session();
                $session->setFlashData('success', 'Teknik servis personeli başarıyla düzenlendi!');
                return redirect()->route('technics');
            }
        }

        $data['form_elements'] = makeFormElement($this->userFormElements, $data['validation'] ?? null, $data['user']);

        return view('pages/technics/add', $data);
    }

    public function delete(int $user_id)
    {
        $user_model = new UsersModel();

        $check = $user_model->where(['id' => $user_id, 'user_type' => 'technic'])->first();

        if ($check) {
            $user_model->delete($user_id);
            session()->setFlashData('success', 'Teknik servis personeli başarıyla silindi!');
        } else {
            session()->setFlashData('error', 'Teknik servis personeli bulunamadı!');
        }

        return redirect()->route('technics');
    }

    public function getTechnics()
    {
        helper(['dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->query('SELECT u.full_name, u.phone, u.created_at, u.id FROM users u WHERE u.user_type = "technic" AND u.deleted_at IS NULL');

        $dt->edit('created_at', function ($data) {
            return dtBeautifyDate($data['created_at']);
        });

        $dt->edit('id', function ($data) {
            return '<div class="btn-group"><a href="' . site_url(route_to('edit_technic', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_technic', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
        });

        echo $dt->generate();
    }

    public function getTechnicsForSelect()
    {
        $data = [];

        $user_model = new UsersModel();
        $user_model->select("users.id as id, users.full_name as text")->where('user_type', 'technic');

        $id = $this->request->getVar('id');

        $search = $this->request->getVar('search');

        $id = $this->request->getVar('id');

        if ($id != '') {
            $data = $user_model->find($id);
            return $this->response->setJSON($data);
        }

        if ($search != '') {
            $user_model->like('users.full_name', "$search");
        }

        $page = $this->request->getVar('page');
        $data['results'] = $user_model->paginate(20, 'default', $page);
        $data['total'] = count($data['results']);
        $data['pagination']['more'] = $data['total'] < 20 ? false : true;

        return $this->response->setJSON($data);
    }
}
