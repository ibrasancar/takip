<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;

class Auth extends BaseController
{
    protected $user;

    protected $loginValidationRules = [
        'email'  => [
            'rules' => 'required|valid_email',
            'label' => 'E-posta',
        ],
        'password' => [
            'rules' => 'required',
            'label' => 'Şifre',
        ]
    ];

    public function __construct()
    {
        $this->user = new UsersModel();
    }
    
    public function index()
    {
        return redirect()->route('login');
    }

    public function login()
    {
        helper('form');
        $session = session();
        $data = [
            'page_title' => 'Giriş yap',
        ];

        if ($this->request->getMethod() == 'post') {

            if ($input = !$this->validate($this->loginValidationRules)) {
                $data['error'] = $this->validator->getErrors();

                return view('login', $data);
            }

            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');

            if ($userData = $this->user->where('email', $email)->first()) {
                $authPass = password_verify($password, $userData['password']);
                if ($authPass) {
                    $session_data = [
                        'id' => $userData['id'],
                        'user_type' => $userData['user_type'],
                        'name' => $userData['full_name'],
                        'email' => $userData['email'],
                        'isLoggedIn' => true,
                    ];
                    $session->set($session_data);
                    $session->setFlashData('success', 'Başarıyla giriş yapıldı!');
                    return redirect()->route('homepage');
                } else {
                    $data['error'] = 'Şifreniz yanlış.';
                }
            } else {
                $data['error'] = 'Böyle bir kullanıcı bulunmamaktadır.';
            }
        }

        return view('login', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->route('login');
    }
}
