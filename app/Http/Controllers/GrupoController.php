<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\grupo;
use App\Services\GlobalService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grupo = grupo::all();
        return view("grupo");

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
                'descricao' =>'required|string|max:255',
                'id_emissor' =>'required|string|max:255',
                'emails' =>'required|string|max:255',
                'cc' =>'nullable|string|max:255',
                'bcc' =>'nullable|string|max:255',
            ]);


            $grupo = new grupo;
            $grupo->fill($validatedData);

            $grupo->descricao = $request->descricao;
            $grupo->id_emissor = $request->id_emissor;
            $grupo->emails = $request->emails;
            $grupo->bcc = $request->bcc;


            // Grava os campos de grupo
            $grupo->save();

            //Session::flash('success_cad_fab','Fabricante:  ' . $fabricante->p0005_Descricao_Fabricante);
            return redirect()->back()->setStatusCode(200);

        }catch (\Exception $e) {
            Log::error('Erro ao cadastrar grupo: ' . $e->getMessage());
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
    public function update(Request $request, grupo $grupo)
    {
        //
        $validatedData = $request->validate([
            'descricao' =>'required|string|max:255',
            'emails' =>'required|string|max:255',
            'cc' =>'nullable|string|max:255',
            'bcc' =>'nullable|string|max:255',
        ]);

        // Recupera a solicitação de manutenção pelo ID
        $grupo->fill($validatedData);
        // Atualiza o status da solicitação
        $grupo->update($validatedData);

        Session::flash('success_anl_grupo','Grupo Nr:  ' . $grupo->id);
        return redirect()->back()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(grupo $grupo)
    {
        //
        try {
            // Deletar o grupo
            $deletionSucceeded = $grupo->delete();

            if ($deletionSucceeded) {
                Log::info(' Equipamento número ' . $grupo->id . ' foi deletado com sucesso ' );
                return response()->json(['success_del_eqpto' => ' Grupo número ' . $grupo->id . ' foi deletado com sucesso!'], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao deletar grupo: ' . $e->getMessage());
            return response()->json(['error_del_eqpto' => 'Por favor, tente novamente.'], 500);
        }
    }


    public static function listaGrupo(){

        $result = GlobalService::populaTabelaGrupo();
        return $result;
    }

}
