<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\usuario_aplicacao;
use App\Services\GlobalService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class Usuario_AplicacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario_aplicacao = usuario_aplicacao::all();
        return view("usuario_aplicacao");

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{

            $validatedData = $request->validate([
                'usuario' =>'required|string|max:255',
            ]);


            $usuario_aplicacao = new usuario_aplicacao;
            $usuario_aplicacao->fill($validatedData);

            $usuario_aplicacao->usuario = $request->usuario;
            $usuario_aplicacao->id_aplicacao = $request->id_aplicacao;



            // Grava os campos de grupo
            $usuario_aplicacao->save();

            //Session::flash('success_cad_fab','Fabricante:  ' . $fabricante->p0005_Descricao_Fabricante);
            return redirect()->back()->setStatusCode(200);

        }catch (\Exception $e) {
            Log::error('Erro ao cadastrar usuario_aplicacao: ' . $e->getMessage());
            Session::flash('error_cad_fab','Por favor, tente novamente.');
            return redirect()->back()->setStatusCode(500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, usuario_aplicacao $usuario_aplicacao)
    {
        //
        $validatedData = $request->validate([
            'id' =>'required|string|max:255',
            //'id_aplicacao' =>'required|string|max:255',
            'usuario' =>'required|string|max:255',

        ]);

        // Recupera a solicitação de manutenção pelo ID
        $usuario_aplicacao->fill($validatedData);

        // Atualiza o status da solicitação
        $usuario_aplicacao->update($validatedData);

        Session::flash('success_anl_grupo','Grupo Nr:  ' . $usuario_aplicacao->id);
        return redirect()->back()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(usuario_aplicacao $usuario_aplicacao)
    {
        //
        try {
            // Deletar o grupo
            $deletionSucceeded = $usuario_aplicacao->delete();

            if ($deletionSucceeded) {
                Log::info(' Equipamento número ' . $usuario_aplicacao->id . ' foi deletado com sucesso ' );
                return response()->json(['success_del_eqpto' => ' Grupo número ' . $usuario_aplicacao->id . ' foi deletado com sucesso!'], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao deletar grupo: ' . $e->getMessage());
            return response()->json(['error_del_eqpto' => 'Por favor, tente novamente.'], 500);
        }
    }


    public static function listaUsuarioAplicacao(){

        $result = GlobalService::populaTabelaUsuarioPermissao();
        return $result;
    }

}
