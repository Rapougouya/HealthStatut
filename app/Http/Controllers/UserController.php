<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Threshold;

class UserController extends Controller
{
    public function index()
    {
        $services = Service::all();
        $thresholds = Threshold::all();

        return view('admin.parametres', compact('services', 'thresholds'));
    }
}
