<?php
namespace App\Services;
use PDO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * Carrega lotes baseado no produto selecionado
     */
    public static function carregarLotes($produto)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC vendasPelicano.dbo.P0100_Calendario_Produto @p100prod = ?, @tipo = 1";
            $sth = $dbh->prepare($sql);
            $sth->execute([trim($produto)]);

            // Obtém o resultado da procedure, que deve ser XML
            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0]; // Supondo que a coluna 0 contenha o XML
            }

            // Verifica se a procedure retornou algo
            if (empty($xmlResult)) {
                return [];
            }

            // Limpar espaços em branco e remover caracteres não esperados
            $xmlResult = trim($xmlResult);

            // Verifica se o XML retornado já contém uma tag raiz
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            // Tenta carregar o XML
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('Erro ao converter XML para lotes');
                return [];
            }

            $lotes = [];
            foreach ($xml->row as $row) {
                $lotes[] = (object) [
                    'lote' => trim((string) $row['p100lote'])
                ];
            }

            return $lotes;
        } catch (\Exception $e) {
            Log::error('Erro ao carregar lotes: ' . $e->getMessage());
            return [];
        }
    }

    /**
    * Carrega técnicos operadores
    */
    public function carregarTecnicos()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.P1110_USUARIOS_MMRD";
            $sth = $dbh->prepare($sql);
            $sth->execute();
            Log::debug('Procedure executada com sucesso: crsa.P1110_USUARIOS_MMRD');

            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }

            if (empty($xmlResult)) {
                Log::warning('Procedure retornou vazio para técnicos.');
                return [];
            }

            $xmlResult = trim($xmlResult);

            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('Erro ao converter XML para técnicos');
                return [];
            }

            $tecnicos = [];
            foreach ($xml->row as $row) {
                $tecnicos[] = (object) [
                    'cdusuario' => trim((string) $row['p1110_usuarioid']),
                    'nome' => trim((string) $row['p1110_nome'])
                ];
            }

            Log::debug('Técnicos carregados com sucesso:', ['total' => count($tecnicos)]);
            return $tecnicos;
        } catch (\Exception $e) {
            Log::error('Erro ao carregar técnicos: ' . $e->getMessage());
            return []; // Sempre retorna array
        }
    }


    /**
    * Carrega Revisadores
    */
    public function CarregarRevisado()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "set nocount on; EXEC sgcr.crsa.P1110_USUARIOS @p052_grupocd=6, @p1110_ativo='A', @ordem=1";
            $sth = $dbh->prepare($sql);
            $sth->execute();
            Log::debug('Procedure executada com sucesso: crsa.P1110_USUARIOS');

            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }

            if (empty($xmlResult)) {
                Log::warning('Procedure retornou vazio para técnicos.');
                return [];
            }

            $xmlResult = trim($xmlResult);

            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('Erro ao converter XML para técnicos');
                return [];
            }

            $tecnicos = [];
            foreach ($xml->row as $row) {
                $tecnicos[] = (object) [
                    'cdusuario' => trim((string) $row['p1110_usuarioid']),
                    'nome' => trim((string) $row['p1110_nome'])
                ];
            }

            Log::debug('Técnicos carregados com sucesso:', ['total' => count($tecnicos)]);
            return $tecnicos;
        } catch (\Exception $e) {
            Log::error('Erro ao carregar técnicos: ' . $e->getMessage());
            return []; // Sempre retorna array
        }
    }


    /**
    * Carrega Status da Produção
    */
    public function carregarProducaStatus()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.PPST_PRODUCAOSTATUS";
            $sth = $dbh->prepare($sql);
            $sth->execute();
            Log::debug('Procedure executada com sucesso: crsa.PPST_PRODUCAOSTATUS');

            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }

            if (empty($xmlResult)) {
                Log::warning('Procedure retornou vazio para PPST_PRODUCAOSTATUS.');
                return [];
            }

            $xmlResult = trim($xmlResult);

            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('Erro ao converter XML para prodstatus');
                return [];
            }

            $prodstatus = [];
            foreach ($xml->row as $row) {
                $prodstatus[] = (object) [
                    'pstprod_status' => trim((string) $row['pstprod_status']),
                    'pstprod_descricao' => trim((string) $row['pstprod_descricao'])
                ];
            }

            Log::debug('prodstatus carregados com sucesso:', ['total' => count($prodstatus)]);
            return $prodstatus;
        } catch (\Exception $e) {
            Log::error('Erro ao carregar prodstatus: ' . $e->getMessage());
            return []; // Sempre retorna array
        }
    }


    /**
    * Carrega Status
    */
    public function carregarStatus()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "EXEC sgcr.crsa.PPST_STATUS  @codigo = null ";
            $sth = $dbh->prepare($sql);
            $sth->execute();
            Log::debug('Procedure executada com sucesso: crsa.PPST_STATUS');

            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }

            if (empty($xmlResult)) {
                Log::warning('Procedure retornou vazio para PPST_STATUS.');
                return [];
            }

            $xmlResult = trim($xmlResult);

            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                Log::error('Erro ao converter XML para prodstatus');
                return [];
            }

            $status = [];
            foreach ($xml->row as $row) {
                $status[] = (object) [
                    'status_codigo' => trim((string) $row['pststs_codigo']),
                    'status_descricao' => trim((string) $row['pststs_descricao'])
                ];
            }

            Log::debug('status carregados com sucesso:', ['total' => count($status)]);
            return $status;
        } catch (\Exception $e) {
            Log::error('Erro ao carregar prodstatus: ' . $e->getMessage());
            return []; // Sempre retorna array
        }
    }


}
