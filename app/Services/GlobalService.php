<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;

/**
 * Uma especie de classe facade com todas as procedures necessária para o sistema
 *
 * @author Alberto Barella Junior <alberto@abjinfo.com.br>
 */
class GlobalService
{

    public static function validaAcesso(string $usuario, string $senha)
    {        
        $result = "";
        $dbh = DB::connection()->getPdo();

        $sql = "exec SGCR.crsa.P1110_Login '".$usuario ."','" . $senha . "', '','',''";
        $sth = $dbh->prepare($sql);
        $sth->execute();

        // Só tenta fetchObject se houver colunas no resultado
        if ($sth->columnCount() > 0) {
            $obj = $sth->fetchObject();
            if ($obj != "") {
                return $obj;
            }
        }

        return "";
    }

    public static function dadosUsuario(string $cdusuario)
    {        
        $result = "";
        $dbh = DB::connection()->getPdo();

        $sql = "select * from SGCR.crsa.T1110_USUARIOS where p1110_usuarioid = " . $cdusuario;
        $sth = $dbh->prepare($sql);
        $sth->execute();
        $obj = $sth->fetchObject();

        if ($obj != "") {
            return $obj;
        }

        return "";
    }

    /**
     * Retorna lista de produtos via procedure P0250_PRODUTO_SELECIONA
     * @return array
     */

    public static function listarProdutos()
    {
        $dbh = DB::connection()->getPdo();
        $sql = "exec vendasPelicano.dbo.P0250_PRODUTO_SELECIONA ";
        $sth = $dbh->prepare($sql);
        $sth->execute();
        $xmlRows = [];

        while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
            $xmlRows[] = trim($row[0]);
        }
        if (count($xmlRows)) {
            $xml = '<root>' . implode('', $xmlRows) . '</root>';
            $produtos = [];
            $xmlObj = @simplexml_load_string($xml);
            if ($xmlObj) {
                foreach ($xmlObj->row as $item) {
                    $produtos[] = [
                        'codigo' => (string)($item['prod_cod510'] ?? ''),
                        'descricao' => (string)($item['prod_cod510'] ?? '') ,
                    ];
                }
            }
            return $produtos;
        }
        return [];
    }
}
