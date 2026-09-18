<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Localite;
use App\Models\Nomenclature;
use App\Models\Service;
use App\Models\User;

class ParametreController extends Controller
{
    public function index()
    {
        return view('parametres.index', [
            'counts' => [
                'grades'        => Grade::count(),
                'localites'     => Localite::count(),
                'services'      => Service::count(),
                'nomenclatures' => Nomenclature::count(),
                'categories'    => count(Nomenclature::CATEGORIES),
                'users'         => User::count(),
            ],
        ]);
    }
}
