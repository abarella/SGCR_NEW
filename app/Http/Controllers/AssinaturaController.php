<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use App\Models\assinatura;
use Illuminate\Http\Request;
use App\Services\GlobalService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class AssinaturaController extends Controller
{
    public function index()
    {
        $assinatura = assinatura::all();
        return view("assinatura");
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //dd($request);
        try{

            $validatedData = $request->validate([
                'assinatura' =>'required|string|max:965535',
            ]);

            // Remover o campo 'senha' dos dados validados


            $assinatura = new assinatura;
            $assinatura->fill($validatedData);

            $assinatura->assinatura = $request->assinatura;

            // Grava os campos de usuário e data
            $assinatura->save();

            //Session::flash('success_cad_fab','Fabricante:  ' . $fabricante->p0005_Descricao_Fabricante);
            return redirect()->back()->setStatusCode(200);

        }catch (\Exception $e) {
            Log::error('Erro ao cadastrar emissor: ' . $e->getMessage());
            Session::flash('error_cad_fab','Por favor, tente novamente.');
            return redirect()->back()->setStatusCode(500);
        }
    }


    public function show(string $id)
    {
        //
        dd($id);
    }

    public function edit(string $id)
    {
        //dd($id);
        //try {
            // Recupera a solicitação de manutenção pelo ID
        //     $emissor = emissor::findOrFail($id);

        //     dd($emissor);

        //     Session::flash('success_anl_solicitacao','Emissor Nr:  ' . $emissor->id);
        //     return view('emissor', compact('emissor'));

        // } catch (\Exception $e) {
        //     Log::error('Erro ao anlisar solicitação: ' . $e->getMessage());
        //     Session::flash('error_anl_solicitacao','Por favor, tente novamente.');
        //     return redirect()->back()->setStatusCode(500);
        // }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'assinatura' =>'required|string|max:965535'
        ]);

        //Log::info($validatedData);
        //Log::info($id);

        // Encontrar o item para atualizar
        //$assinatura = Assinatura::find($assinatura->id);
        $assinatura = assinatura::find($id);
        Log::info($assinatura);


        if (!$assinatura) {
            return redirect()->back()->with('error', 'Assinatura não encontrada.');
        }

        //$assinatura->assinaturaU = $request->input('assinaturaU');





        // Recupera a solicitação de manutenção pelo ID
        $assinatura->fill($validatedData);
        // Atualiza o status da solicitação
        $assinatura->update($validatedData);

        Session::flash('success_anl_emissor','Assinatura Nr:  ' . $assinatura->id);
        return view('assinatura', compact('assinatura'));

    }


    public function destroy(assinatura $assinatura)
    {
        try {
            // Deletar o emissor
            $deletionSucceeded = $assinatura->delete();

            if ($deletionSucceeded) {
                Log::info(' Equipamento número ' . $assinatura->id . ' foi deletado com sucesso ' );
                return response()->json(['success_del_eqpto' => ' Equipamento número ' . $assinatura->id . ' foi deletado com sucesso!'], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao deletar equipamento: ' . $e->getMessage());
            return response()->json(['error_del_eqpto' => 'Por favor, tente novamente.'], 500);
        }
    }


    public static function listaAssinatura(){
        $result = GlobalService::populaTabelaAssinatura();
        //$username = Auth::user()->username;
        //dd($username);
        return $result;
    }

}
