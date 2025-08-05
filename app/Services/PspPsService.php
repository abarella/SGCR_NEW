<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PspPsService
{
    /**
     * Lista as pastas com filtros
     */
    public function listarPastas($filtros)
    {

        try {
            \Log::info('Iniciando listarPastas com filtros:', $filtros);

            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA
                    @mes = :mes,
                    @ano = :ano,
                    @ordem = :ordem,
                    @tipo = :tipo,
                    @grupo = :grupo,
                    @pst_numero = :pst_numero";

            \Log::info('SQL a ser executado:', ['sql' => $sql]);

            $sth = $dbh->prepare($sql);

                // Preparando parâmetros e convertendo para tipos apropriados
                $params = [
                    ':mes' => isset($filtros['mes']) && $filtros['mes'] !== '' ? intval($filtros['mes']) : null,
                    ':ano' => isset($filtros['ano']) && $filtros['ano'] !== '' ? intval($filtros['ano']) : intval(date('Y')),
                    ':ordem' => isset($filtros['ordem']) ? intval($filtros['ordem']) : 0,
                    ':tipo' => isset($filtros['tipo']) && $filtros['tipo'] !== '' ? intval($filtros['tipo']) : null,
                    ':grupo' => isset($filtros['grupo']) && $filtros['grupo'] !== '' ? $filtros['grupo'] : null,
                    ':pst_numero' => isset($filtros['pst_numero']) && $filtros['pst_numero'] !== '' ? $filtros['pst_numero'] : null
                ];
            // Log dos parâmetros após o tratamento
            \Log::info('Parâmetros após tratamento:', $params);

            foreach ($params as $key => $value) {
                $sth->bindValue($key, $value);
            }

            $sth->execute();

            // Tentar obter o erro do SQL Server, se houver
            $errorInfo = $sth->errorInfo();
            if ($errorInfo[0] !== '00000') {
                \Log::error('Erro SQL Server:', ['errorInfo' => $errorInfo]);
                throw new \Exception("Erro SQL Server: " . $errorInfo[2]);
            }

            \Log::info('Procedure executada com sucesso');

            $results = [];

            // Tenta obter o primeiro conjunto de resultados
            do {
                try {
                    $rows = $sth->fetchAll(\PDO::FETCH_OBJ);
                    if ($rows) {
                        $results = $rows; // Guarda apenas o último conjunto de resultados válido
                        \Log::info('Conjunto de resultados encontrado:', [
                            'count' => count($rows),
                            'first_10_rows' => array_slice($rows, 0, 10),
                            'columns' => $rows ? array_keys((array)$rows[0]) : [],
                            'raw_data' => $rows ? json_encode($rows, JSON_PRETTY_PRINT) : null
                        ]);
                    } else {
                        \Log::info('Nenhum resultado encontrado neste conjunto');
                    }
                } catch (\Exception $e) {
                    \Log::info('Ignorando conjunto de resultados sem campos:', [
                        'error' => $e->getMessage(),
                        'code' => $e->getCode()
                    ]);
                    continue;
                }
            } while ($sth->nextRowset());

            // Log detalhado dos resultados finais
            \Log::info('Resultados finais:', [
                'count' => count($results),
                'has_data' => !empty($results),
                'data_type' => !empty($results) ? get_class($results[0]) : null,
                'first_record' => !empty($results) ? json_encode($results[0], JSON_PRETTY_PRINT) : null,
                'columns' => !empty($results) ? array_keys((array)$results[0]) : []
            ]);

            // Log adicional para cada resultado
            if (!empty($results)) {
                foreach ($results as $index => $result) {
                    \Log::info("Resultado $index:", [
                        'tipo' => gettype($result),
                        'classe' => get_class($result),
                        'propriedades' => is_object($result) ? get_object_vars($result) : null,
                        'json' => json_encode($result, JSON_PRETTY_PRINT)
                    ]);
                }
            }

            return $results;
        } catch (\Exception $e) {
            \Log::error('Erro ao listar pastas', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Repassar o erro para ver o detalhe no log
        }
    }

    /**
     * Retorna os status disponíveis
     */
    public function getStatus()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT pststs_codigo, pststs_descricao
                   FROM sgcr.crsa.TPST_STATUS
                   ORDER BY pststs_descricao";

            $sth = $dbh->prepare($sql);
            $sth->execute();

            $result = $sth->fetchAll(\PDO::FETCH_OBJ);
            \Log::info('Status encontrados:', ['count' => count($result), 'data' => $result]);

            return $result;
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar status: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém detalhes de uma pasta específica
     */
    public function getPasta($numero)
    {

        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_lista2 @pst_numero = :numero";
            $sth = $dbh->prepare($sql);
            $sth->bindValue(':numero', $numero);
            $sth->execute();

            return $sth->fetch(\PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar pasta: ' . $e->getMessage());
            return null;
        }

        //return $numero;
    }

    /**
     * Atualiza uma pasta
     */
    public function updatePasta($numero, $dados)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_UPDATE
                    @pst_numero = :numero,
                    @pst_previsaocontrole = :prev_controle,
                    @pst_previsaoproducao = :prev_producao,
                    @pst_observacao = :observacao";

            $sth = $dbh->prepare($sql);
            $sth->bindValue(':numero', $numero);
            $sth->bindValue(':prev_controle', $dados['pst_previsaocontrole']);
            $sth->bindValue(':prev_producao', $dados['pst_previsaoproducao']);
            $sth->bindValue(':observacao', $dados['pst_observacao'] ?? '');

            return $sth->execute();
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar pasta: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Atualiza documentação da pasta
     */
    public function updateDocumentacao($numero, $dados)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_UPDATE_DOC
                    @pst_numero = :numero,
                    @data_entrega = :data_entrega,
                    @observacao = :observacao";

            $sth = $dbh->prepare($sql);
            $sth->bindValue(':numero', $numero);
            $sth->bindValue(':data_entrega', $dados['data_entrega']);
            $sth->bindValue(':observacao', $dados['observacao'] ?? '');

            return $sth->execute();
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar documentação: ' . $e->getMessage());
            throw $e;
        }
    }
}
