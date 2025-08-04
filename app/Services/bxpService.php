<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class bxpService
{
    /**
     * Executa a procedure uspBlindagemXPasta com os parâmetros informados.
     *
     * @param string $nrlote
     * @param string $nrserie
     * @return array
     */
    public static function blindagemXPasta(string $nrlote, string $nrserie)
    {
        $dbh = DB::connection()->getPdo();
        $sql = "exec VendasPelicano.dbo.uspBlindagemXPasta '{$nrlote}', '{$nrserie}'";
        $sth = $dbh->prepare($sql);
        $sth->execute();

        // Retorna todos os resultados como array de objetos
        return $sth->fetchAll(\PDO::FETCH_OBJ);
    }
} 