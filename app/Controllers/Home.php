<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
<<<<<<< HEAD
        return view('index');
    }

    public function about(): string
    {
        return view('about');
    }

    public function contact(): string
    {
        return view('contact');
=======
        return view('index'); // Homepage
>>>>>>> 4ce6d5449c1f03dd0a546ba78ef04f097ef7b778
    }

    public function about()
    {
        return view('about'); // About page
    }

    public function contact() // Contact page
    {
        return view('contact');
    }

    public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        return view('dashboard');
    }
}