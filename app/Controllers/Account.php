<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;

class Account extends BaseController
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
            'column' => 'col-md-6',
            'label' => 'Şifre *',
        ],
        'phone' => [
            'type' => 'text',
            'column' => 'col-md-6',
            'label' => 'Telefon Numarası',
            'placeholder' => 'İletişim kurulacak telefon numarası'
        ],
        'address' => [
            'type' => 'textarea',
            'column' => 'col-md-12',
            'label' => 'Adres',
            'rows' => 2,
            'placeholder' => 'Kullanıcı adresi',
        ]
    ];

    public function index()
    {
        //

        $data = [
            'page_title' => 'Hesabım',
            'page_desc' => 'Hesabınızın ayarlarını buradan yapabilirsiniz.',
        ];

        helper(['form', 'formElement']);

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        $user_id = session()->get('id');

        $user_model = new UsersModel();
        $data['account'] = $user_model->find($user_id);

        if ($this->request->getMethod() == 'post') {
            $validationRules = $this->userValidationRules;

            // check is it current email
            $email = $this->request->getVar('email', FILTER_SANITIZE_EMAIL);
            if ($data['account']['email'] == $email) {
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
                    'full_name'  => $this->request->getVar('full_name', FILTER_SANITIZE_STRING),
                    'email' => $this->request->getVar('email', FILTER_SANITIZE_EMAIL),
                    'password' => $this->request->getVar('password', FILTER_SANITIZE_STRING),
                    'phone' => $this->request->getVar('phone', FILTER_SANITIZE_NUMBER_INT),
                    'address' => $this->request->getVar('address', FILTER_SANITIZE_STRING),
                ]);
                $session = session();
                $session->setFlashData('success', 'Kullanıcı başarıyla düzenlendi');
                return redirect()->route('account');
            }
        }


        $formElements = $this->userFormElements;
        $data['form_elements'] = makeFormElement($formElements, $data['validation'] ?? null, $data['account']);

        return view('pages/account/index', $data);
    }
}
