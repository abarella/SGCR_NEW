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


            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA
                    @mes = :mes,
                    @ano = :ano,
                    @ordem = :ordem,
                    @tipo = :tipo,
                    @grupo = :grupo,
                    @pst_numero = :pst_numero";



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


            foreach ($params as $key => $value) {
                $sth->bindValue($key, $value);
            }

            $sth->execute();

            // Tentar obter o erro do SQL Server, se houver
            $errorInfo = $sth->errorInfo();
            if ($errorInfo[0] !== '00000') {

                throw new \Exception("Erro SQL Server: " . $errorInfo[2]);
            }



            $results = [];

            // Tenta obter o primeiro conjunto de resultados
            do {
                try {
                    $rows = $sth->fetchAll(\PDO::FETCH_OBJ);
                    if ($rows) {
                        $results = $rows; // Guarda apenas o último conjunto de resultados válido

                    } else {

                    }
                } catch (\Exception $e) {

                    continue;
                }
            } while ($sth->nextRowset());




            return $results;
        } catch (\Exception $e) {

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


            return $result;
        } catch (\Exception $e) {

            return [];
        }
    }

    /**
     * Obtém detalhes de uma pasta específica
     */
    public function getPasta($numero, $tipo = 'C')
    {

        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_lista4 @pst_numero = :numero, @tipo = :tipo";
            $sth = $dbh->prepare($sql);
            $sth->bindValue(':numero', $numero);
            $sth->bindValue(':tipo', $tipo);
            $sth->execute();

            // Concatena todos os fragmentos XML retornados
            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }

            if (empty($xmlResult)) {

                return null;
            }

            $xmlResult = trim($xmlResult);

            // Garante que o XML tenha um root
            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            // Converte XML para objeto
            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {

                return null;
            }

            // Pega a primeira row do XML e converte para objeto
            if (isset($xml->row[0])) {
                $row = $xml->row[0];
                $pasta = new \stdClass();

                // Mapeia todos os atributos XML para propriedades do objeto
                foreach ($row->attributes() as $key => $value) {
                    $pasta->{$key} = (string) $value;
                }

                // Log das propriedades disponíveis
                \Log::info('Propriedades da pasta carregada:', [
                    'numero' => $numero,
                    'tipo' => $tipo,
                    'propriedades' => array_keys((array)$pasta),
                    'valores' => (array)$pasta
                ]);

                return $pasta;
            }

            return null;
        } catch (\Exception $e) {

            return null;
        }
    }

    /**
     * Atualiza uma pasta usando a procedure crsa.Ppst_Documentacao
     * Executa apenas a procedure do tab ativo (controle ou produção)
     */
    public function updatePasta($numero, $dados)
    {
        try {
            $dbh = DB::connection()->getPdo();

            // Extrai o ano da data de previsão de controle
            $ano = date('Y', strtotime($dados['pst_previsaocontrole']));

            // Converte as datas para formato SQL Server (YYYY-MM-DD HH:MM:SS)
            $data_controle = date('Ymd H:i:s', strtotime($dados['pst_previsaocontrole']));
            $data_producao = date('Ymd H:i:s', strtotime($dados['pst_previsaoproducao']));

            // Parâmetros comuns
            $pst_ano = $ano;
            $pst_numero = $numero;
            $pst_revisadopor = $dados['cmbSitControle'] ?? '';
            $pst_doc_data = $data_controle;
            $pst_observacao = $dados['pst_observacao_controle'] ?? '';
            $cdusuario = auth()->user()->cdusuario ?? '';
            $senha = $dados['password'] ?? '';

            // Determina qual tab está ativo
            $active_tab = $dados['active_tab'] ?? 'controle';

            \Log::info('Atualizando pasta - Tab ativo:', ['active_tab' => $active_tab]);

            //if ($active_tab === 'controle') {
                // Execução apenas para CONTROLE
                $sql_controle = "SET NOCOUNT ON;
                        DECLARE @resulta_controle INT, @mensa_controle NVARCHAR(255);
                        EXEC sgcr.crsa.Ppst_Documentacao
                        @pst_numero = :pst_numero,
                        @pst_status = :pst_status,
                        @pst_prodstatus = :pst_prodstatus,
                        @pst_de = :pst_de,
                        @pst_revisadopor = :pst_revisadopor,
                        @pst_doc_data = :pst_doc_data,
                        @pst_observacao = :pst_observacao,
                        @cdusuario = :cdusuario,
                        @senha = :senha,
                        @resulta = @resulta_controle OUTPUT,
                        @mensa = @mensa_controle OUTPUT";

                $sth_controle = $dbh->prepare($sql_controle);

                $sth_controle->bindValue(':pst_numero', intval($pst_numero));
                $sth_controle->bindValue(':pst_status', intval('C')); // C = Controle (convertido para int)
                $sth_controle->bindValue(':pst_prodstatus', intval($dados['cmdStsControle'] ?? 0)); // Status do controle (convertido para int)
                $sth_controle->bindValue(':pst_de', 'C'); // C = Controle
                $sth_controle->bindValue(':pst_revisadopor', intval($dados['pst_revisadoporc'] ?? 0)); // Revisador do controle (convertido para int)
                $sth_controle->bindValue(':pst_doc_data', $pst_doc_data);
                $sth_controle->bindValue(':pst_observacao', $pst_observacao);
                $sth_controle->bindValue(':cdusuario', intval($cdusuario));
                $sth_controle->bindValue(':senha', $senha);


                $result_controle = $sth_controle->execute();

                //return $result_controle;

            //} else if ($active_tab === 'producao') {
                // Execução apenas para PRODUÇÃO
                $pst_revisadopor_prod = $dados['pst_revisadopor'] ?? '';
                $pst_doc_data_prod = $data_producao;
                $pst_observacao_prod = $dados['pst_observacao_producao'] ?? '';

                $sql_producao = "SET NOCOUNT ON;
                        DECLARE @resulta_producao INT, @mensa_producao NVARCHAR(255);
                        EXEC sgcr.crsa.Ppst_Documentacao
                        @pst_numero = :pst_numero,
                        @pst_status = :pst_status,
                        @pst_prodstatus = :pst_prodstatus,
                        @pst_de = :pst_de,
                        @pst_revisadopor = :pst_revisadopor,
                        @pst_doc_data = :pst_doc_data,
                        @pst_observacao = :pst_observacao,
                        @cdusuario = :cdusuario,
                        @senha = :senha,
                        @resulta = @resulta_producao OUTPUT,
                        @mensa = @mensa_producao OUTPUT";

                $sth_producao = $dbh->prepare($sql_producao);
                $sth_producao->bindValue(':pst_numero', intval($pst_numero));
                $sth_producao->bindValue(':pst_status', intval('P')); // P = Produção (convertido para int)
                $sth_producao->bindValue(':pst_prodstatus', intval($dados['cmdStsProducao'] ?? 0)); // Status da produção (convertido para int)
                $sth_producao->bindValue(':pst_de', 'P'); // P = Produção
                $sth_producao->bindValue(':pst_revisadopor', intval($pst_revisadopor_prod)); // Revisador da produção (convertido para int)
                $sth_producao->bindValue(':pst_doc_data', $pst_doc_data_prod);
                $sth_producao->bindValue(':pst_observacao', $pst_observacao_prod);
                $sth_producao->bindValue(':cdusuario', intval($cdusuario));
                $sth_producao->bindValue(':senha', $senha);


                $result_producao = $sth_producao->execute();

                //return $result_producao;
            //}

            return true; // Se nenhum tab válido foi especificado

        } catch (\Exception $e) {

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

            throw $e;
        }
    }

    public function updatePrevisao($numero, $dados)
    {
        try {
            $dbh = DB::connection()->getPdo();

            $pst_numero = $dados['pst_numero'];
            $pstusu_codigo = $dados['cdusuario'];
            $senha = $dados['senha'];
            $tipo = $dados['tipo'];
            $data_entrega = $dados['data_entrega'] ?? null;
            $observacao = $dados['observacao'] ?? '';

            // Converte data para formato SQL Server se fornecida
            $previsao = null;
            if ($data_entrega) {
                $previsao = date('Ymd H:i:s', strtotime($data_entrega));
            }

            \Log::info('Executando procedure PPST_PREVISAO_A - Parâmetros:', [
                'pst_numero' => intval($pst_numero),
                'pstusu_codigo' => intval($pstusu_codigo),
                'previsao' => $previsao,
                'tipo' => $tipo,
                'obs' => $observacao,
                'senha' => $senha
            ]);

            $sql = "SET NOCOUNT ON;
                    DECLARE @resulta INT, @mensa VARCHAR(100);
                    EXEC sgcr.crsa.PPST_PREVISAO_A
                    @pst_numero = :pst_numero,
                    @pstusu_codigo = :pstusu_codigo,
                    @previsao = :previsao,
                    @tipo = :tipo,
                    @obs = :obs,
                    @senha = :senha,
                    @resulta = @resulta OUTPUT,
                    @mensa = @mensa OUTPUT";

            $sth = $dbh->prepare($sql);
            $sth->bindValue(':pst_numero', intval($pst_numero));
            $sth->bindValue(':pstusu_codigo', intval($pstusu_codigo));
            $sth->bindValue(':previsao', $previsao);
            $sth->bindValue(':tipo', $tipo);
            $sth->bindValue(':obs', $observacao);
            $sth->bindValue(':senha', $senha);

            $result = $sth->execute();

            \Log::info('Resultado da execução da procedure PPST_PREVISAO_A:', [
                'success' => $result,
                'tipo' => $tipo
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('Erro na execução da procedure PPST_PREVISAO_A:', [
                'numero' => $numero,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Busca dados da procedure PPST_LISTA2
     */
    public function getLista2($numero)
    {
        try {
            $dbh = DB::connection()->getPdo();

            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA2 @pst_numero = :numero";

            $sth = $dbh->prepare($sql);
            $sth->bindValue(':numero', intval($numero));
            $sth->execute();

            // Tenta buscar todos os resultados para XML grande
            $allResults = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $result = $allResults ? $allResults[0] : null;

            \Log::info('Resultado bruto da PPST_LISTA2:', [
                'numero' => $numero,
                'total_results' => count($allResults),
                'result' => $result,
                'keys' => $result ? array_keys($result) : []
            ]);

            if ($result && isset($result['XML_F52E2B61-18A1-11d1-B105-00805F49916B'])) {
                $xmlString = $result['XML_F52E2B61-18A1-11d1-B105-00805F49916B'];

                // Se o XML parece truncado, tenta concatenar outros resultados
                if (count($allResults) > 1) {
                    foreach ($allResults as $additionalResult) {
                        if (isset($additionalResult['XML_F52E2B61-18A1-11d1-B105-00805F49916B'])) {
                            $xmlString .= $additionalResult['XML_F52E2B61-18A1-11d1-B105-00805F49916B'];
                        }
                    }
                }

                            \Log::info('XML retornado pela PPST_LISTA2:', [
                'numero' => $numero,
                'xml' => $xmlString
            ]);

            $result = $this->parseXmlToList($xmlString);

            \Log::info('Resultado processado da PPST_LISTA2:', [
                'numero' => $numero,
                'total_registros' => count($result),
                'primeiro_registro' => $result ? get_object_vars($result[0]) : []
            ]);

            return $result;
            }

            // Se não encontrou a coluna XML padrão, procura por outras colunas
            if ($result) {
                foreach ($result as $key => $value) {
                    if (strpos($key, 'XML') !== false || strpos($value, '<?xml') !== false) {
                        \Log::info('Encontrada coluna XML alternativa:', [
                            'key' => $key,
                            'value' => $value
                        ]);
                        return $this->parseXmlToList($value);
                    }
                }
            }

            \Log::warning('Nenhum XML encontrado na PPST_LISTA2:', [
                'numero' => $numero,
                'result' => $result
            ]);

            return [];

        } catch (\Exception $e) {
            \Log::error('Erro na execução da PPST_LISTA2:', [
                'numero' => $numero,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Busca dados da procedure PPST_LISTA3
     */
    public function getLista3($numero)
    {
        try {
            $dbh = DB::connection()->getPdo();

            $sql = "SET NOCOUNT ON; EXEC sgcr.crsa.PPST_LISTA3 @pst_numero = :numero";

            $sth = $dbh->prepare($sql);
            $sth->bindValue(':numero', intval($numero));
            $sth->execute();

            // Busca todos os resultados para concatenar XML fragmentado
            $allResults = $sth->fetchAll(\PDO::FETCH_ASSOC);

            \Log::info('Resultado bruto da PPST_LISTA3:', [
                'numero' => $numero,
                'total_results' => count($allResults),
                'first_result' => $allResults[0] ?? null
            ]);

            if (empty($allResults)) {
                \Log::warning('PPST_LISTA3 retornou resultados vazios', ['numero' => $numero]);
                return [];
            }

            // Concatena todos os fragmentos XML de todas as linhas de resultado
            $xmlString = '';
            $xmlKey = null;

            // Identifica a chave XML no primeiro resultado
            if (isset($allResults[0])) {
                $firstResult = $allResults[0];
                foreach (array_keys($firstResult) as $key) {
                    if (strpos($key, 'XML_') === 0 || strpos($firstResult[$key], '<row') !== false) {
                        $xmlKey = $key;
                        break;
                    }
                }
            }

            if ($xmlKey) {
                // Concatena todos os fragmentos XML
                foreach ($allResults as $result) {
                    if (isset($result[$xmlKey])) {
                        $xmlString .= $result[$xmlKey];
                    }
                }

                \Log::info('XML concatenado da PPST_LISTA3:', [
                    'numero' => $numero,
                    'xml_key' => $xmlKey,
                    'xml_tamanho' => strlen($xmlString),
                    'xml_primeiros_200' => substr($xmlString, 0, 200),
                    'xml_ultimos_200' => substr($xmlString, -200),
                    'xml_row_count' => substr_count($xmlString, '<row'),
                    'total_fragmentos' => count($allResults)
                ]);

                $result = $this->parseXmlToList($xmlString);

                \Log::info('Resultado processado da PPST_LISTA3:', [
                    'numero' => $numero,
                    'total_registros' => count($result),
                    'primeiro_registro' => $result ? get_object_vars($result[0]) : []
                ]);

                return $result;
            }

            \Log::warning('Nenhuma chave XML encontrada na PPST_LISTA3:', [
                'numero' => $numero,
                'keys_disponiveis' => array_keys($allResults[0] ?? [])
            ]);

            return [];

        } catch (\Exception $e) {
            \Log::error('Erro na execução da PPST_LISTA3:', [
                'numero' => $numero,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Converte XML para array de objetos
     */
    private function parseXmlToList($xmlString)
    {
        try {
            // Remove caracteres especiais do início se existirem
            $xmlString = trim($xmlString);

            // Verifica se o XML é válido
            if (empty($xmlString)) {
                \Log::warning('XML vazio:', ['xml' => $xmlString]);
                return [];
            }

            // Corrige caracteres especiais que podem estar causando problemas
            $xmlString = str_replace('&lt;', '<', $xmlString);
            $xmlString = str_replace('&gt;', '>', $xmlString);
            $xmlString = str_replace('&amp;', '&', $xmlString);
            $xmlString = str_replace('&quot;', '"', $xmlString);

            // Se não tem declaração XML, adiciona uma
            if (!preg_match('/^<\?xml/', $xmlString)) {
                // Verifica se tem múltiplas linhas <row>
                $rowCount = substr_count($xmlString, '<row');
                            \Log::info('Análise do XML:', [
                'row_count' => $rowCount,
                'xml_tamanho' => strlen($xmlString),
                'xml_termina_corretamente' => substr($xmlString, -2) === '/>' || substr($xmlString, -1) === '>',
                'xml_original' => $xmlString
            ]);

                // Sempre adiciona a declaração XML e envolve em <root>
                $xmlString = '<?xml version="1.0" encoding="UTF-8"?><root>' . $xmlString . '</root>';
                \Log::info('Adicionada declaração XML:', ['xml_modificado' => $xmlString]);
            }

            // Carrega o XML
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                \Log::error('Erro ao carregar XML:', [
                    'xml' => $xmlString,
                    'errors' => libxml_get_errors()
                ]);
                return [];
            }

            \Log::info('XML carregado com sucesso:', [
                'xml_objeto' => $xml ? 'carregado' : 'falhou',
                'elementos_children' => count($xml->children()),
                'elementos_row' => isset($xml->row) ? count($xml->row) : 0
            ]);

            $result = [];

            // Procura por elementos 'row' no XML
            if (isset($xml->row)) {
                foreach ($xml->row as $row) {
                    $item = new \stdClass();
                    foreach ($row->attributes() as $key => $value) {
                        $item->$key = (string)$value;
                    }
                    $result[] = $item;
                }
            }

            \Log::info('Processamento do XML:', [
                'total_rows_encontradas' => count($result),
                'xml_original' => $xmlString,
                'xml_objeto' => $xml ? 'carregado' : 'falhou',
                'elementos_row' => isset($xml->row) ? count($xml->row) : 0
            ]);

            \Log::info('XML convertido com sucesso:', [
                'total_registros' => count($result),
                'primeiro_registro' => $result ? get_object_vars($result[0]) : []
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('Erro ao processar XML:', [
                'xml' => $xmlString,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

}
