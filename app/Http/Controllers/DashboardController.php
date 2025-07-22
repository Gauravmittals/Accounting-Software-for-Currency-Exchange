<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function transactions()
    {
        return view('transactions');
    }

    public function inventory()
    {
        return view('inventory');
    }

    public function lending()
    {
        return view('lending');
    }

    public function cashIn()
    {
        return view('cash-in');
    }

    public function cashOut()
    {
        return view('cash-out');
    }

    public function logout(Request $request)
    {

        return redirect('/login');
    }
}
