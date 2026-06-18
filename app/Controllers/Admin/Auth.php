<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function login(): string|ResponseInterface
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login', [
            'title' => 'Login Admin',
        ]);
    }

    public function attemptLogin(): ResponseInterface
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        $rules = [
            'email'    => 'required|valid_email|max_length[191]',
            'password' => 'required|min_length[8]|max_length[72]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        // Throttling: Max 5 attempts per minute per IP
        $throttler = service('throttler');
        if ($throttler->check(md5($this->request->getIPAddress()), 5, MINUTE) === false) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan login. Silakan coba lagi dalam satu menit.');
        }

        $model = new AdminUserModel();
        $user  = $model->where('email', $email)->first();

        if ($user === null || (int) ($user['is_active'] ?? 0) !== 1 || ! $model->verifyPassword($password, $user)) {
            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi tidak valid.');
        }

        session()->regenerate(true);
        session()->set([
            'admin_logged_in' => true,
            'admin_id'        => (int) $user['id'],
            'admin_email'     => $user['email'],
            'admin_name'      => $user['name'],
        ]);

        return redirect()->to(base_url('admin/dashboard'))->with('message', 'Selamat datang di panel admin.');
    }

    public function logout(): ResponseInterface
    {
        $wasLoggedIn = (bool) session()->get('admin_logged_in');
        session()->remove(['admin_logged_in', 'admin_id', 'admin_email', 'admin_name']);
        session()->regenerate(true);

        $response = redirect()->to(base_url('admin'));

        return $wasLoggedIn ? $response->with('message', 'Anda telah keluar.') : $response;
    }
}
