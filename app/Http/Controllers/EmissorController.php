<?php

namespace App\Http\Controllers;



use DB;
use Exception;
use App\Models\emissor;
use Illuminate\Http\Request;
use App\Services\GlobalService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EmissorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emissor = emissor::all();
        return view("emissor");
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
    /**
 * Store a newly created emissor resource in storage.
 *
 * This function validates the incoming request data, creates a new emissor
 * instance, fills it with the validated data, and saves it to the database.
 * If successful, it redirects back to the previous page with a 200 status code.
 * If an error occurs, it logs the error, sets a flash message, and redirects back
 * with a 500 status code.
 *
 * @param  \Illuminate\Http\Request  $request  The HTTP request containing the emissor data
 * @return \Illuminate\Http\RedirectResponse    Returns a redirect response to the previous page
 *                                              with either a 200 or 500 status code
 */
public function store(Request $request)
{
    //dd($request);
    try{

        $validatedData = $request->validate([
            'descricao' =>'required|string|max:255',
        ]);


        // Remover o campo 'senha' dos dados validados


        $emissor = new emissor;
        $emissor->fill($validatedData);

        $emissor->descricao = $request->descricao;

        // Grava os campos de usuário e data
        $emissor->save();

        //Session::flash('success_cad_fab','Fabricante:  ' . $fabricante->p0005_Descricao_Fabricante);
        return redirect()->back()->setStatusCode(200);

    }catch (\Exception $e) {
        Log::error('Erro ao cadastrar emissor: ' . $e->getMessage());
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
        dd($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, emissor $emissor)
    {
        $validatedData = $request->validate([
            'descricao' =>'required|string|max:255',
        ]);

        // Recupera a solicitação de manutenção pelo ID
        $emissor->fill($validatedData);
        // Atualiza o status da solicitação
        $emissor->update($validatedData);

        Session::flash('success_anl_emissor','Emissor Nr:  ' . $emissor->id);
        return redirect()->back()->setStatusCode(200);

    }






    /**
     * Remove the specified resource from storage.
     */
    public function destroy(emissor $emissor)
    {
        try {
            // Deletar o emissor
            $deletionSucceeded = $emissor->delete();

            if ($deletionSucceeded) {
                Log::info(' Equipamento número ' . $emissor->id . ' foi deletado com sucesso ' );
                return response()->json(['success_del_eqpto' => ' Equipamento número ' . $emissor->id . ' foi deletado com sucesso!'], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao deletar equipamento: ' . $e->getMessage());
            return response()->json(['error_del_eqpto' => 'Por favor, tente novamente.'], 500);
        }
    }


    public static function listaEmissor(){
        $result = GlobalService::populaTabelaEmissor();
        //$username = Auth::user()->username;
        //dd($username);
        return $result;
    }

}
