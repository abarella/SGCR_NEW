<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\bxpService;

class bxpController extends Controller
{
    public function index(Request $request)
    {
        $nrlote = (string) $request->input('nrlote', '');
        $nrserie = (string) $request->input('nrserie', '');
        $resultados = [];

        // Só busca se nrlote for informado
        if ($nrlote) {
            $resultados = bxpService::blindagemXPasta($nrlote, $nrserie);
        }

        return view('bxp.index', compact('resultados', 'nrlote', 'nrserie'));
    }
} 