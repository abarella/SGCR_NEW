<?php

namespace App\Services;

use PDO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service para funcionalidades migradas do SGFP
 * Contém funções migradas dos arquivos functions.php e functionsOutros.php
 *
 * @author Sistema SGCR
 */
class SGFPService
{
    /**
     * Construtor do service
     */
    public function __construct()
    {
        // Inicializações se necessário
    }

    // ===================================
    // FUNÇÕES MIGRADAS DE functions.php
    // ===================================

    // ===================================
    // VALIDAÇÃO E AUTENTICAÇÃO
    // ===================================

    /**
     * Valida senha do usuário
     * 
     * @param string $usuario
     * @param string $senha
     * @return string
     */
    public function validaSenha($usuario, $senha)
    {
    
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "exec sgcr.crsa.[P1110_CONFSENHA] @p1110_usuarioid = :p1110_usuarioid, @p1110_senha = :p1110_senha, @resulta = :resulta, @mensa = :mensa";
            $stmt = $dbh->prepare($sql);

            $resulta = "0";
            $mensa = "";

            // Input parameters
            $stmt->bindParam(':p1110_usuarioid', $usuario, \PDO::PARAM_STR);
            $stmt->bindParam(':p1110_senha', $senha, \PDO::PARAM_STR);
            // Output parameters
            $stmt->bindParam(':resulta', $resulta, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT, 4000);
            $stmt->bindParam(':mensa', $mensa, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT, 4000);
            
            $stmt->execute();
            $stmt->nextRowset();
            
            return $mensa;
        } catch (\Exception $e) {
            Log::error('Erro em validaSenha: ' . $e->getMessage());
            return "Erro na validação de senha";
        }
    }

    // ===================================
    // GESTÃO DE EQUIPAMENTOS
    // ===================================

    /**
     * Monta grid de equipamentos
     * 
     * @param int $pstNumero
     * @return string
     */
    public function montaGridEquipamentos($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0600_EQPTO_LISTA " . $pstNumero . ",''";
            $stmt = $dbh->query($query);
            
            $prep = "<form action='' target='_top' method='POST' name='form1eqp' id='form1eqp' enctype='multipart/form-data'>";
            $prep .= "<input type='hidden' name='ideqp' id='ideqp' value='' />";
            $prep .= "<input type='hidden' name='ideqpAlt' id='ideqpAlt' value='' />";
            $prep .= "<input type='hidden' name='idcateg' id='idcateg' value='' />";
            $prep .= "<input type='hidden' name='acao' id='acao' value='' />";
            $prep .= "<input type='hidden' name='tpst_numero' id='tpst_numero' value='" . $pstNumero . "' />";
            
            $contador1 = 1;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<tr>";
                $prep .= "<td>";
                $prep .= "<button type='submit' class='btn btn-sm btn-outline-primary' onclick='excluiEqp(" . $row["p1500_eqptoid"] . ", " . $contador1 . ")' id='ExcluirEQPTO" . $contador1 . "' name='ExcluirEQPTO" . $contador1 . "' target='processarEQ' form='form1eqp'><i class='fas fa-trash'></i></button>";
                $prep .= "<input type='hidden' id='row1500" . $contador1 . "' name='row1500" . $contador1 . "' value='" . $row["p1500_eqptoid"] . "' style='width:40px;' />";
                $prep .= "<input type='hidden' id='row26" . $contador1 . "' name='row26" . $contador1 . "' value='" . $row["p026_categoriaId"] . "' style='width:40px;' />";
                $prep .= "<input type='hidden' id='row600" . $contador1 . "' name='row600" . $contador1 . "' value='" . $row["p600_equipamentoid"] . "' style='width:60px;' />";
                $prep .= "</td>";
                $prep .= "<td>" . $row["p026_categoria"] . "</td>";
                $prep .= "<td>" . $this->retornaNroCREqpto($row["p026_categoriaId"], $row["p1500_eqptoid"], $contador1) . "</td>";
                $prep .= "<td>" . $row["p600_validade"] . "</td>";
                $prep .= "<td>" . $row["p600_preventiva_validade"] . "</td>";
                $prep .= "<td>" . $row["descricao"] . "</td>";
                $prep .= "</tr>";
                $contador1++;
            }
            $prep .= "</form>";
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em montaGridEquipamentos: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna número CR do equipamento
     * 
     * @param int $ideqp
     * @param int $eqptoid
     * @param int $contador
     * @return string
     */
    public function retornaNroCREqpto($ideqp, $eqptoid, $contador)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P1500_Equipamento " . $ideqp . ",'1'";
            $stmt = $dbh->query($query);
            
            $prep = "<select onchange='alteraEqp(this.value, " . $contador . ")' id='cmbEquipamento" . $contador . "' name='cmbEquipamento" . $contador . "'>";
            $selected = "";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                if ($row["p1500_EqptoID"] == $eqptoid) {
                    $selected = "selected";
                }
                $prep .= "<option " . $selected . " value='" . $row["p1500_EqptoID"] . "'>" . $row["p1500_NumCR"] . "</option>";
                $selected = "";
            }
            $prep .= "</select>";
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaNroCREqpto: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Carrega categorias de equipamentos
     * 
     * @param int $pstNumero
     * @return string
     */
    public function categoriaEquipamentos($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Primeiro query para gerar lista JSON
            $query = "select p026_CategoriaID, p1500_EqptoID, p1500_NumCR, p1500_dspatrim from sgcr.crsa.T1500_EQUIPAMENTO where p026_CategoriaID is not null ";
            $query .= "and p1500_EqptoID not in (Select p1500_eqptoid From sgcr.crsa.T0600_EQUIPAMENTO where pst_numero = '" . $pstNumero . "')";
            $query .= "order by 1,2 ";
            
            $stmt = $dbh->query($query);
            $prep1 = "[";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep1 .= '{"id":"' . $row['p026_CategoriaID'] . '"';
                $prep1 .= ',"idsel":"' . $row['p1500_EqptoID'] . '"';
                $prep1 .= ',"descr":"' . $row["p1500_NumCR"] . ' - ';
                $prep1 .= preg_replace('/[^A-Za-z0-9\- ]/', '', $row["p1500_dspatrim"]) . '"},';
            }
            
            $prep1 = substr($prep1, 0, -1);
            $prep1 .= "]";
            
            // Segundo query para combo
            $query = "exec sgcr.crsa.P0026_EQPTO_CATEGORIA 'A'";
            $stmt = $dbh->query($query);
            $prep = "<select name='eqpCategoria' id='eqpCategoria' class='form-control form-control-sm' onchange='TrocaCategoria(this.value)' style='width:100%' >";
            $prep .= "<option value=0>Selecione</option>";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<option value=" . $row["p026_CategoriaID"] . ">" . $row["p026_Categoria"] . "</option>";
            }
            $prep .= "</select>";
            
            return ['html' => $prep, 'json' => $prep1];
        } catch (\Exception $e) {
            Log::error('Erro em categoriaEquipamentos: ' . $e->getMessage());
            return ['html' => '', 'json' => '[]'];
        }
    }

    // ===================================
    // GESTÃO DE MATERIAIS
    // ===================================

    /**
     * Monta grid de materiais
     * 
     * @param int $lote
     * @return string
     */
    public function montaGridMateriais($lote = null)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            if ($lote) {
                $loteArray = explode(" ", $lote);
                $query = "set nocount on; exec sgcr.crsa.P0600_MATERIAL_SELlote " . $loteArray[0];
            } else {
                $query = "select NrEtqFrc, prodetq_produto, LoteCR, convert(varchar(10),ProdVali,103) ProdVali from sgcr.crsa.tetq402_produto a left outer join sgcr.crsa.tetq400_etqfrasco b on(a.prodetq_codigo=b.prodetq_codigo) where 1=1 and prodetq_Ativo = 'S'";
            }
            
            $stmt = $dbh->query($query);
            $prep = "";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<tr>";
                $prep .= "<td class='text-center'><i class='fas fa-check' style='color: #005af5; height:20px; cursor:pointer'></td>";
                
                if ($lote) {
                    $prep .= "<td>" . $row["codigo"] . "</td>";
                    $prep .= "<td>" . $row["material"] . "</td>";
                    $prep .= "<td>" . $row["lote_ipen"] . "</td>";
                    $prep .= "<td>" . $row["lote_fornecedor"] . "</td>";
                    $prep .= "<td>" . $row["lote_cr"] . "</td>";
                    $prep .= "<td>" . $row["validade"] . "</td>";
                    $prep .= "<td>" . $row["quantidade"] . "</td>";
                    $prep .= "<td>" . $row["un"] . "</td>";
                    $prep .= "<td>" . $row["origem"] . "</td>";
                    $prep .= "<td>" . $row["p015_marcacd"] . "</td>";
                    $prep .= "<td>" . $row["p030_material_tipo_id"] . "</td>";
                } else {
                    $prep .= "<td>" . $row["NrEtqFrc"] . "</td>";
                    $prep .= "<td>" . $row["prodetq_produto"] . "</td>";
                    $prep .= "<td>" . $row["LoteCR"] . "</td>";
                    $prep .= "<td>" . $row["ProdVali"] . "</td>";
                }
                
                $prep .= "</tr>";
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em montaGridMateriais: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Carrega marca de materiais
     * 
     * @return string
     */
    public function marcaMateriais()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0015_MARCA_LISTA '1'";
            $stmt = $dbh->query($query);
            
            $prep = "<select name='matMarca' id='matMarca' class='form-control form-control-sm' style='width:100%' >";
            $prep .= "<option value=0>Selecione</option>";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<option value=" . $row["p015_marcacd"] . ">" . $row["p015_marca"] . "</option>";
            }
            $prep .= "</select>";
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em marcaMateriais: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Carrega tipos de materiais
     * 
     * @return string
     */
    public function materialMateriais()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0030_MATERIAL_TIPO '1'";
            $stmt = $dbh->query($query);
            
            $prep = "<select name='matMaterial' id='matMaterial' class='form-control form-control-sm' style='width:100%' >";
            $prep .= "<option value=0>Selecione</option>";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<option value=" . $row["p030_material_tipo_id"] . ">" . $row["p030_material_tipo"] . "</option>";
            }
            $prep .= "</select>";
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em materialMateriais: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna lista de materiais
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaMateriais($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "set nocount on; exec sgcr.crsa.P0600_MATERIAIS_LISTA " . $pstNumero . ",1, ''";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            
            $prep = "";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $paramedit = "onclick = fu_edtMaterial('";
                $paramedit .= trim($row["p600_id"]) . "','";
                $paramedit .= trim($row["p600_sistema"]) . "','";
                $paramedit .= trim($row["p600_codAlmoxMaterial"]) . "','";
                $paramedit .= str_replace(" ", "¬", trim($row["p600_lote"])) . "','";
                $paramedit .= trim($row["p600_lote2"]) . "','";
                $paramedit .= trim($row["p015_marcacd"]) . "','";
                $paramedit .= trim($row["p030_material_tipo_id"]) . "','";
                $paramedit .= $this->formataDataISO(trim($row["p600_Validade"])) . "','";
                $paramedit .= str_replace(" ", "¬", trim($row["p600_Tipo_Cor_CR"])) . "','";
                $paramedit .= str_replace(" ", "¬", trim($row["p600_LteCtrQtde"])) . "','";
                $paramedit .= trim($row["p047_UnidadeDsc"]) . "','";
                $paramedit .= str_replace(" ", "¬", trim($row["p600_Material"])) . "','";
                $paramedit .= number_format($row["p600_qtde1"], 2) . "','";
                $paramedit .= $row["p600_posicaotela"] . "','";
                $paramedit .= trim($row["p600_LoteIpen"]) . "','";
                $paramedit .= str_replace(" ", "¬", trim($row["p600_LoteFornec"]));
                $paramedit .= "')";

                $prep .= "<tr>";
                $prep .= "<td>";
                $prep .= "<button type='button' class='btn btn-sm btn-outline-primary' " . $paramedit . " id='editmat' name='editmat'><i class='fas fa-edit'></i></button>";
                $prep .= "<button type='button' class='btn btn-sm btn-outline-primary' onclick=fu_delMaterial('" . $row["p600_id"] . "') id='delmat' name='delmat'><i class='fas fa-trash'></i></button>";
                $prep .= "<input type='hidden' name='param1' value='" . $row["p600_id"] . "' />";
                $prep .= "<input type='hidden' name='param2' value='" . trim($row["p600_codAlmoxMaterial"]) . "' />";
                $prep .= "</td>";
                $prep .= "<td>" . $row["p600_posicaotela"] . "</td>";
                $prep .= "<td>" . $row["p600_material"] . "</td>";
                $prep .= "<td>" . $row["p600_lote"] . "</td>";
                $prep .= "<td>" . $row["p015_marca"] . "</td>";
                $prep .= "<td>" . $row["p600_Validade"] . "</td>";
                $prep .= "<td>" . number_format($row["p600_qtde1"], 2, ',', '.') . "</td>";
                $prep .= "<td>" . $row["p047_UnidadeDsc"] . "</td>";
                $prep .= "</tr>";
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaMateriais: ' . $e->getMessage());
            return "";
        }
    }

    // ===================================
    // PROCESSOS E ANÁLISES
    // ===================================

    /**
     * Retorna verificação de cela
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaVerifCela($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0600_LISTA_CELA " . $pstNumero;
            $stmt = $dbh->query($query);
            
            $prep = "";
            $id1600 = "";
            $categoria = "";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "CELA CR: " . $row["p1500_numcr"];
                $prep .= "<br>";
                $prep .= "Responsável: " . $row["p600_tecniconome"];
                $prep .= "<br>";
                $prep .= "Data " . $row["p600_tecnicodata"];
                $prep .= "<br>";
                $prep .= "Observação: " . $row["p600_obs"];
                $prep .= "<br>";
                $prep .= "Qtde não conforme: " . $row["nao_conforme2"];
                $id1600 = $row["p600_checklist_id"];
                $categoria = $row["p026_categoriaid"];
            }
            
            return [
                'html' => $prep,
                'id1600' => $id1600,
                'categoria' => $categoria
            ];
        } catch (\Exception $e) {
            Log::error('Erro em retornaVerifCela: ' . $e->getMessage());
            return ['html' => '', 'id1600' => '', 'categoria' => ''];
        }
    }

    /**
     * Retorna limpeza de cela
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaLimpCela($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0601_Materiais " . $pstNumero;
            $stmt = $dbh->query($query);
            
            $prep = "";
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "Responsável: " . $row["p1110_nome"];
                $prep .= "<br>";
                $prep .= "Data " . $this->formataDataHora($row["dat_atualizacao"]);
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaLimpCela: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna fornecedores de diluição
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaFornDilu($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "select nr_ID,nome_lote from sgcr.crsa.T643_I131_InformRadioisotopo where pst_numero = " . $pstNumero;
            $stmt = $dbh->query($query);
            
            $prep = "";
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<option value='" . $row["nr_ID"] . "'>" . $row["nome_lote"] . "</option>";
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaFornDilu: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna combo de produtos
     * 
     * @return string
     */
    public function retornaCMBProdutos()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "select prod_codigo, nome_comercial from vendaspelicano.dbo.T0250_PRODUTO where prod_ativo='A' order by nome_comercial";
            $stmt = $dbh->query($query);
            
            $prep = "<select id='cmbprod' name='cmbprod' class='form-control '>";
            $prep .= "<option value='0'>Selecione</option>";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<option value='" . $row["prod_codigo"] . "'>" . $row["nome_comercial"] . "</option>";
            }
            $prep .= "</select>";
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaCMBProdutos: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna atividades solicitadas
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaAtividadeSolicitadas($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "set nocount on;exec sgcr.crsa.[P0110_ATIVIDADE_SOLICITADA] " . $pstNumero . ", '', '', '', '', ''";
            $stmt = $dbh->query($query);
            
            $v1 = 0;
            $v2 = 0;
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $v1 = $v1 + ($row["p110atv"] * $row["partidas"]);
                $v2 = $v2 + (($row["p110atv"] * $row["partidas"]) * 37);
            }
            
            return $v1 . "-" . $v2;
        } catch (\Exception $e) {
            Log::error('Erro em retornaAtividadeSolicitadas: ' . $e->getMessage());
            return "0-0";
        }
    }

    /**
     * Retorna lista de solicitadas
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaSolicitadasLista($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "set nocount on;exec sgcr.crsa.[P0110_ATIVIDADE_SOLICITADA] " . $pstNumero . ", '', '', '', '', ''";
            $stmt = $dbh->query($query);
            
            $prep = "";
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<tr>";
                $prep .= "<td>" . $row["p110atv"] . "</td>";
                $prep .= "<td>" . $row["partidas"] . "</td>";
                $prep .= "</tr>";
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaSolicitadasLista: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna fracionamento cliente
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaFracCliente($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0600_FRACIONAMENTO_RD " . $pstNumero . ", '', '', ''";
            $stmt = $dbh->query($query);
            
            $prep = "";
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<tr>";
                $prep .= "<td>" . $row["p110chve"] . "</td>";
                $prep .= "<td>" . $row["Cli_Dest_Responsavel"] . "</td>";
                $prep .= "<td>" . $row["atvidade_mci"] . "</td>";
                $prep .= "<td>" . $row["atividade_mbq"] . "</td>";
                $prep .= "<td>" . $row["atividade_enviada"] . "</td>";
                $prep .= "<td>" . $row["p110qtde"] . "</td>";
                $prep .= "<td>" . $row["p110volu"] . "</td>";
                $prep .= "<td>" . $row["status"] . "</td>";
                $prep .= "</tr>";
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaFracCliente: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna liberação de área
     * 
     * @param int $pstNumero
     * @param string $produto
     * @return string
     */
    public function retornaLiberaArea($pstNumero, $produto)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            switch ($produto) {
                case 'rd_i131':
                case 'rd_tl':
                    $query = "exec sgcr.crsa.P0643_I131_LIBERAAREA " . $pstNumero . ", @tiposaida=1";
                    break;
                case 'rd_ga67':
                    $query = "exec sgcr.crsa.P0607_LIBERAAREA " . $pstNumero . ", @tiposaida=1";
                    break;
                default:
                    return "";
            }
            
            $stmt = $dbh->query($query);
            $prep = "";
            $conta = 1;
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $chkS = ($row["P643_Flag"] == "S") ? "checked" : "";
                $chkN = ($row["P643_Flag"] != "S") ? "checked" : "";
                
                $prep .= "<tr>";
                $prep .= "<td>" . $row["P643_LibArea"] . "</td>";
                $prep .= "<td>" . $row["P643_Descricao"] . "</td>";
                $prep .= "<td>";
                $prep .= '<div class="text-nowrap">';
                $prep .= "<input type='radio' id='" . $conta . "linhaS' name='" . $conta . "linhaS' " . $chkS . " value='S'> SIM&nbsp;&nbsp;";
                $prep .= "<input type='radio' id='" . $conta . "linhaS' name='" . $conta . "linhaS' " . $chkN . " value='N'> NÃO";
                $prep .= '</div>';
                $prep .= '<td style="display:none"><input type="text" id="IDfield' . $conta . '" name="IDfield' . $conta . '" value="' . $row['P643_Id'] . '"/></td>';
                $prep .= "</td>";
                $prep .= "</tr>";
                $conta++;
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaLiberaArea: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Retorna embalagem primária
     * 
     * @param int $pstNumero
     * @param string $produto
     * @return string
     */
    public function retornaEmbalagemPrimaria($pstNumero, $produto)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            switch ($produto) {
                case 'rd_i131':
                    $query = "exec sgcr.crsa.P0643_I131_EMBPRIMARIA " . $pstNumero . ", @tiposaida=1";
                    break;
                case 'rd_tl':
                    $query = "exec sgcr.crsa.P0643_TLCL3_EMBPRIMARIA " . $pstNumero . ", @tiposaida=1";
                    break;
                case 'rd_ga67':
                    $query = "exec sgcr.crsa.P0607_EMBPRIMARIA " . $pstNumero . ", @tiposaida=1";
                    break;
                default:
                    return "";
            }
            
            $stmt = $dbh->query($query);
            $prep = "";
            $conta = 1;
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $chkS = ($row["P643_Flag"] == "S") ? "checked" : "";
                $chkN = ($row["P643_Flag"] != "S") ? "checked" : "";
                
                $prep .= "<tr>";
                $prep .= "<td>" . $row["P643_EmbPrimaria"] . "</td>";
                $prep .= "<td>" . $row["P643_Descricao"] . "</td>";
                $prep .= "<td>";
                $prep .= '<div class="text-nowrap">';
                $prep .= "<input type='radio' id='" . $conta . "linhaS' name='" . $conta . "linhaS' " . $chkS . " value='S'> SIM&nbsp;&nbsp;";
                $prep .= "<input type='radio' id='" . $conta . "linhaS' name='" . $conta . "linhaS' " . $chkN . " value='N'> NÃO";
                $prep .= '</div>';
                $prep .= '<td style="display:none"><input type="text" id="IDfield' . $conta . '" name="IDfield' . $conta . '" value="' . $row['P643_Id'] . '"/></td>';
                $prep .= "</td>";
                $prep .= "</tr>";
                $conta++;
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaEmbalagemPrimaria: ' . $e->getMessage());
            return "";
        }
    }

    // ===================================
    // GESTÃO DE TÉCNICOS
    // ===================================

    /**
     * Carrega técnico responsável
     * 
     * @param int $tipo
     * @param int $nivel
     * @return string
     */
    public function carregaTecnicoResponsavel($tipo, $nivel = null)
    {
        try {
            $cmbidname = "";
            switch ($tipo) {
                case 1: $cmbidname = "cmbPinca"; break;
                case 2: $cmbidname = "cmbCalculo"; break;
                case 3: $cmbidname = "cmbSAS"; break;
                case 4: $cmbidname = "cmbLacracao"; break;
                case 5: $cmbidname = "cmbTecnicoCalc"; break;
            }
            
            if ($nivel) {
                $cmbidname .= $nivel;
            }
            
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P1110_USUARIOS ";
            $stmt = $dbh->query($query);
            
            $prep = "<select id='" . $cmbidname . "' name='" . $cmbidname . "' class='form-control form-control-sm'>";
            
            if ($nivel) {
                $prep .= "<option value='0'>Selecione</option>";
            }
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $selected = "";
                if (!$nivel && $row["p1110_usuarioid"] == session('usuarioID')) {
                    $selected = "selected";
                }
                $prep .= "<option " . $selected . " value='" . $row["p1110_usuarioid"] . "'>" . $row["p1110_nome"] . "</option>";
            }
            $prep .= "</select>";
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em carregaTecnicoResponsavel: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Carrega técnico I131
     * 
     * @return string
     */
    public function carregaTecnico()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P1110_USUARIOS_I131 ";
            $stmt = $dbh->query($query);
            
            $prep = "<select id='cmbTecnico' name='cmbTecnico' class='form-control form-control-sm'>";
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $selected = "";
                if ($row["p1110_usuarioid"] == session('usuarioID')) {
                    $selected = "selected";
                }
                $prep .= "<option " . $selected . " value='" . $row["p1110_usuarioid"] . "'>" . $row["p1110_nome"] . "</option>";
            }
            $prep .= "</select>";
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em carregaTecnico: ' . $e->getMessage());
            return "";
        }
    }

    // ===================================
    // GESTÃO DE AMOSTRAS
    // ===================================

    /**
     * Retorna amostras
     * 
     * @param int $pstNumero
     * @return string
     */
    public function retornaAmostras($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            
            // Primeiro busca o ID da CQ
            $sql0 = "set nocount on; exec sgcr.crsa.P0551_LISTA0 " . $pstNumero . "," . session('usuarioID');
            $stmt0 = $dbh->prepare($sql0);
            $stmt0->execute();
            
            $p551_cq_id = '0';
            while ($row0 = $stmt0->fetch(\PDO::FETCH_ASSOC)) {
                $p551_cq_id = $row0["p551_cq_id"];
            }
            
            // Agora busca as amostras
            $sql = "set nocount on; exec sgcr.crsa.P0551_LISTA " . $p551_cq_id . ",1," . session('usuarioID');
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            
            $prep = "";
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $paramedit = "onclick = fu_edtAmostra('";
                $paramedit .= trim($row["p551_frascos_id"]) . "','";
                $paramedit .= trim($row["p551_cq_id"]) . "','";
                $paramedit .= trim($row["p551_identificacaoamostra"]) . "','";
                $paramedit .= number_format(trim($row["p551_atividade"]), 2) . "','";
                $paramedit .= number_format(trim($row["p551_volume"]), 2);
                $paramedit .= "')";
                
                $prep .= "<tr>";
                $prep .= "<td>";
                $prep .= "<button type='button' class='btn btn-sm btn-outline-primary' " . $paramedit . "><i class='fas fa-edit'></i></button>";
                $prep .= "<button type='button' class='btn btn-sm btn-outline-primary' onclick=fu_delAmostra(" . $row["p551_frascos_id"] . "," . trim($row["p551_cq_id"]) . ")><i class='fas fa-trash'></i></button>";
                $prep .= "</td>";
                $prep .= "<td>" . $row["p551_identificacaoamostra"] . "</td>";
                $prep .= "<td>" . number_format($row["p551_atividade"], 2, ',', '.') . "</td>";
                $prep .= "<td>" . number_format($row["p551_volume"], 2, ',', '.') . "</td>";
                $prep .= "</tr>";
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em retornaAmostras: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Carrega soluções
     * 
     * @param int $pstNumero
     * @return array
     */
    public function carregaSolucao($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "select NrEtqFrc, prodetq_produto, LoteCR, convert(varchar(10),ProdVali,103) ProdVali from sgcr.crsa.tetq402_produto a left outer join sgcr.crsa.tetq400_etqfrasco b on(a.prodetq_codigo=b.prodetq_codigo) inner join sgcr.crsa.T0601_Solucoes c on c.id_solucoes = b.NrEtqFrc where 1=1 and prodetq_Ativo = 'S' and pst_numero = " . $pstNumero;
            
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            
            $result = [
                'NrEtqFrc' => '',
                'prodetq_produto' => '',
                'loteCR' => '',
                'validade' => ''
            ];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $result['NrEtqFrc'] = $row["NrEtqFrc"];
                $result['prodetq_produto'] = $row["prodetq_produto"];
                $result['loteCR'] = $row["LoteCR"];
                $result['validade'] = $row["ProdVali"];
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Erro em carregaSolucao: ' . $e->getMessage());
            return ['NrEtqFrc' => '', 'prodetq_produto' => '', 'loteCR' => '', 'validade' => ''];
        }
    }

    /**
     * Carrega amostras
     * 
     * @param int $pstNumero
     * @return array
     */
    public function carregaAmostras($pstNumero)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "set nocount on; exec sgcr.crsa.P0551_LISTA0 " . $pstNumero . "," . session('usuarioID');
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            
            $result = [
                'p551_cq_id' => '',
                'p551_obs' => '',
                'p551_ph' => '',
                'p551_horaamostragem' => '',
                'p031_aspectoid' => '',
                'p612_transferenciaid' => ''
            ];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $result['p551_cq_id'] = $row["p551_cq_id"];
                $result['p551_obs'] = $row["p551_obs"];
                $result['p551_ph'] = $row["p551_ph"];
                $result['p551_horaamostragem'] = substr($row["p551_horaamostragem"], 0, 2) . ":" . substr($row["p551_horaamostragem"], 2, 2);
                $result['p031_aspectoid'] = $row["p031_aspectoid"];
            }
            
            // Busca transferência
            if ($result['p551_cq_id']) {
                try {
                    $sql1 = "exec sgcr.crsa.R0612_AMOSTRAGEM_LISTA " . $result['p551_cq_id'];
                    $stmt1 = $dbh->prepare($sql1);
                    $stmt1->execute();
                    
                    while ($row1 = $stmt1->fetch(\PDO::FETCH_ASSOC)) {
                        $result['p612_transferenciaid'] = $row1["p612_transferenciaid"];
                    }
                } catch (\Exception $e) {
                    // Ignora erro na busca de transferência
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Erro em carregaAmostras: ' . $e->getMessage());
            return ['p551_cq_id' => '', 'p551_obs' => '', 'p551_ph' => '', 'p551_horaamostragem' => '', 'p031_aspectoid' => '', 'p612_transferenciaid' => ''];
        }
    }

    // ========================================
    // FUNÇÕES MIGRADAS DE functionsOutros.php
    // ========================================

    /**
     * Carrega blindagem por pasta
     * 
     * @param string $lote
     * @param string $serie
     * @return array
     */
    public function carregaBlindagemXPasta($lote, $serie)
    {
        try {
            if ($lote == "") {
                $lote = "0";
            }
            
            $dbh = DB::connection()->getPdo();
            $sql = "set nocount on;exec vendaspelicano.dbo.BlindagemXPasta " . $lote . ",'" . $serie . "'";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            
            $prep = "";
            $conta = 0;
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $consist = '';
                if (trim($row["RgSaida_Castelo"]) != trim($row["RgSaida_Pasta"])) {
                    $consist = 'background-color:red;';
                    $conta++;
                }
                
                if (trim($row["RgSaida_Castelo"]) == '' && trim($row["RgSaida_Pasta"]) == '') {
                    $consist = 'background-color:red;';
                    $conta++;
                }
                
                $prep .= "<tr style='" . $consist . "'>";
                $prep .= "<td>" . $row["Pasta_Lote"] . "</td>";
                $prep .= "<td>" . $row["RgSaida_Castelo"] . "</td>";
                $prep .= "<td>" . $row["RgSaida_Pasta"] . "</td>";
                $prep .= "<td>" . $row["Serie"] . "</td>";
                $prep .= "<td>" . $row["Transp"] . "</td>";
                $prep .= "<td>" . $row["Razao_Social"] . "</td>";
                $prep .= "<td>" . $row["Medico_Responsavel"] . "</td>";
                $prep .= "</tr>";
            }
            
            return [
                'html' => $prep,
                'contagem' => $conta
            ];
        } catch (\Exception $e) {
            Log::error('Erro em carregaBlindagemXPasta: ' . $e->getMessage());
            return ['html' => '', 'contagem' => 0];
        }
    }

    /**
     * Carrega acompanhamento
     * 
     * @return string
     */
    public function carregaAcompanhamento()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "select id, lote from sgcr.crsa.TBacomplote where status = 'A'";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            
            $prep = "";
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $prep .= "<tr>";
                $prep .= "<td>" . $row["id"] . "</td>";
                $prep .= "<td>" . $row["lote"] . "</td>";
                $prep .= "</tr>";
            }
            
            return $prep;
        } catch (\Exception $e) {
            Log::error('Erro em carregaAcompanhamento: ' . $e->getMessage());
            return "";
        }
    }

    // ===================================
    // MÉTODOS AUXILIARES
    // ===================================

    /**
     * Executa uma procedure do banco de dados e retorna o resultado como XML
     * 
     * @param string $sql
     * @param array $params
     * @return string
     */
    protected function executeProcedureXML($sql, $params = [])
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sth = $dbh->prepare($sql);
            $sth->execute($params);

            $xmlResult = "";
            while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                $xmlResult .= $row[0];
            }

            return trim($xmlResult);
        } catch (\Exception $e) {
            Log::error('Erro ao executar procedure: ' . $e->getMessage());
            return "";
        }
    }

    /**
     * Converte resultado XML para array de objetos
     * 
     * @param string $xmlResult
     * @return array
     */
    protected function parseXmlToArray($xmlResult)
    {
        try {
            if (empty($xmlResult)) {
                return [];
            }

            if (!str_starts_with($xmlResult, "<root>")) {
                $xmlResult = "<root>$xmlResult</root>";
            }

            $xml = simplexml_load_string($xmlResult);
            if (!$xml) {
                return [];
            }

            $result = [];
            foreach ($xml->row as $row) {
                $item = [];
                foreach ($row->attributes() as $key => $value) {
                    $item[$key] = trim((string) $value);
                }
                $result[] = (object) $item;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Erro ao converter XML para array: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Executa uma query simples e retorna o resultado
     * 
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    protected function executeQuery($sql, $params = [])
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sth = $dbh->prepare($sql);
            $sth->execute($params);

            // Verifica se há colunas no resultado
            if ($sth->columnCount() > 0) {
                return $sth->fetchAll(\PDO::FETCH_OBJ);
            }

            return true; // Para queries que não retornam dados (INSERT, UPDATE, DELETE)
        } catch (\Exception $e) {
            Log::error('Erro ao executar query: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Formata data para padrão ISO
     * 
     * @param string $strdata
     * @return string
     */
    public function formataDataISO($strdata)
    {
        try {
            $p1 = substr($strdata, 6, 4);
            $p2 = substr($strdata, 3, 2);
            $p3 = substr($strdata, 0, 2);
            return $p1 . '-' . $p2 . '-' . $p3;
        } catch (\Exception $e) {
            Log::error('Erro ao formatar data ISO: ' . $e->getMessage());
            return $strdata;
        }
    }

    /**
     * Formata data e hora
     * 
     * @param string $strdata
     * @return string
     */
    public function formataDataHora($strdata)
    {
        try {
            $p1 = substr($strdata, 0, 4);
            $p2 = substr($strdata, 5, 2);
            $p3 = substr($strdata, 8, 2);
            $p4 = $p3 . '/' . $p2 . '/' . $p1;
            $p4 = $p4 . substr($strdata, 10, 6);
            return $p4;
        } catch (\Exception $e) {
            Log::error('Erro ao formatar data/hora: ' . $e->getMessage());
            return $strdata;
        }
    }

    // ===================================
    // OPERAÇÕES DE CÁLCULO E DILUIÇÃO
    // ===================================

    /**
     * Calcula diluições
     * 
     * @param int $pstNumero
     * @param string $produto
     * @param int $partIni
     * @param int $partFim
     * @param string $lote
     * @return array
     */
    public function calcularDiluicao($pstNumero, $produto, $partIni, $partFim, $lote)
    {
        try {
            $produto = $this->converterProduto($produto);
            $loteNum = substr($lote, 0, 3);
            
            if ($partIni == '') $partIni = 0;
            if ($partFim == '') $partFim = 0;
            
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0110_PEDIDOS_CONTA " . $pstNumero . "," . $partIni . "," . $partFim;
            $stmt = $dbh->query($query);
            
            $regs = 0;
            $parts = 0;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $regs = $row["nro_regs"];
                $parts = $row["p110atv"];
            }
            
            // Busca lotes atendidos
            $query = "select sgcr.crsa.fn_LotesAtendidos('" . $produto . "'," . $partIni . ", " . $partFim . ", " . $loteNum . ")";
            $stmt = $dbh->query($query);
            
            $lotes_atendidos = "";
            try {
                while ($row = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
                    $lotes_atendidos = $row;
                }
                $lotes_atendidos = trim($lotes_atendidos[0]['']);
            } catch (\Exception $e) {
                // Ignora erro
            }
            
            // Calcula partidas menores
            $partIniMenor = $partIni - 1;
            $query = "exec sgcr.crsa.P0110_PEDIDOS_CONTA " . $pstNumero . ",0," . $partIniMenor;
            $stmt = $dbh->query($query);
            
            $regsMenor = 0;
            $partsMenor = 0;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $regsMenor = $row["nro_regs"];
                $partsMenor = $row["p110atv"];
            }
            
            return [
                'partidas' => $regs,
                'totatv' => $parts,
                'pedatend' => $lotes_atendidos,
                'partidasmenor' => $regsMenor,
                'totatvmenor' => $partsMenor
            ];
        } catch (\Exception $e) {
            Log::error('Erro em calcularDiluicao: ' . $e->getMessage());
            return [
                'partidas' => 0,
                'totatv' => 0,
                'pedatend' => '',
                'partidasmenor' => 0,
                'totatvmenor' => 0
            ];
        }
    }

    /**
     * Converte código do produto
     * 
     * @param string $produto
     * @return string
     */
    private function converterProduto($produto)
    {
        switch ($produto) {
            case "rd_i131":
                return "I-131";
            case "rd_ga67":
                return "ga-67";
            case "rd_tl":
                return "TLCL3";
            case "rd_mo":
                return "I-131";
            default:
                return $produto;
        }
    }

    // ===================================
    // OPERAÇÕES CRUD ESPECÍFICAS
    // ===================================

    /**
     * Grava limpeza de cela com responsáveis
     * 
     * @param array $dados
     * @return bool
     */
    public function gravarLimpezaCelaResponsaveis($dados)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $stmt = $dbh->prepare("exec sgcr.crsa.P0706_LIMPEZA_RESPONSA :p1, :p2, :p3, :p4, :p5, :p6, :p7");
            
            $stmt->bindParam(':p1', $dados['id_limpeza'], \PDO::PARAM_STR);
            $stmt->bindParam(':p2', $dados['check'], \PDO::PARAM_STR);
            $stmt->bindParam(':p3', $dados['usuario'], \PDO::PARAM_STR);
            $stmt->bindParam(':p4', $dados['usuario_session'], \PDO::PARAM_STR);
            $stmt->bindParam(':p5', $dados['senha'], \PDO::PARAM_STR);
            $stmt->bindParam(':p6', '', \PDO::PARAM_STR);
            $stmt->bindParam(':p7', '', \PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em gravarLimpezaCelaResponsaveis: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Grava soluções
     * 
     * @param array $dados
     * @return bool
     */
    public function gravarSolucoes($dados)
    {
        try {
            $solucoes = substr($dados['solucoes'], 0, -1); // Remove último caractere
            
            $dbh = DB::connection()->getPdo();
            $query = "exec sgcr.crsa.P0601_Solucoes " . $dados['id_limpeza'] . "," . $dados['pasta'] . "," . $dados['lote'] . ",'" . $solucoes . "','" . $dados['usuario'] . "'";
            $stmt = $dbh->prepare($query);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em gravarSolucoes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Grava operadores
     * 
     * @param array $dados
     * @return bool
     */
    public function gravarOperadores($dados)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $stmt = $dbh->prepare("exec sgcr.crsa.P0095_Operadores :p01, :p02, :p03, :p04, :p05, :p06, :p07, :p08, :p09, :p10, :p11, :p12, :p13, :p14, :p15");
            
            $stmt->bindParam(':p01', $dados["pstnro"], \PDO::PARAM_STR);
            $stmt->bindParam(':p02', $dados["cmbCalculo1"], \PDO::PARAM_STR);
            $stmt->bindParam(':p03', $dados["cmbCalculo2"], \PDO::PARAM_STR);
            $stmt->bindParam(':p04', $dados["cmbCalculo3"], \PDO::PARAM_STR);
            $stmt->bindParam(':p05', $dados["cmbPinca1"], \PDO::PARAM_STR);
            $stmt->bindParam(':p06', $dados["cmbPinca2"], \PDO::PARAM_STR);
            $stmt->bindParam(':p07', $dados["cmbPinca3"], \PDO::PARAM_STR);
            $stmt->bindParam(':p08', $dados["cmbSAS1"], \PDO::PARAM_STR);
            $stmt->bindParam(':p09', $dados["cmbSAS2"], \PDO::PARAM_STR);
            $stmt->bindParam(':p10', $dados["cmbSAS3"], \PDO::PARAM_STR);
            $stmt->bindParam(':p11', $dados["cmbLacracao1"], \PDO::PARAM_STR);
            $stmt->bindParam(':p12', $dados["cmbLacracao2"], \PDO::PARAM_STR);
            $stmt->bindParam(':p13', $dados["cmbLacracao3"], \PDO::PARAM_STR);
            $stmt->bindParam(':p14', $dados["cmbTecnico"], \PDO::PARAM_STR);
            $stmt->bindParam(':p15', "1", \PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em gravarOperadores: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Inclui acompanhamento
     * 
     * @param string $lote
     * @return bool
     */
    public function incluirAcompanhamento($lote)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "INSERT INTO sgcr.crsa.TBacomplote ([lote], [created_at], [status]) VALUES (:lote, GETDATE(), 'A')";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':lote', $lote);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em incluirAcompanhamento: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui acompanhamento
     * 
     * @param string $lote
     * @return bool
     */
    public function excluirAcompanhamento($lote)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "DELETE sgcr.crsa.TBacomplote where lote = '" . $lote . "'";
            $stmt = $dbh->prepare($sql);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em excluirAcompanhamento: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica se lote existe
     * 
     * @param string $lote
     * @return array
     */
    public function verificarLoteExiste($lote)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $stmt = $dbh->prepare("exec sgcr.crsa.P0110_LOTE_NUMERO :param1");
            $stmt->bindParam(':param1', $lote, \PDO::PARAM_STR);
            $stmt->execute();
            
            $result = [
                'atividade' => '0',
                'data_producao' => '',
                'data_validade' => '',
                'concentracao' => '0',
                'volume' => '0',
                'atividade_especifica' => '0'
            ];
            
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                if ($row['p110situ'] == 1) {
                    $result['atividade'] = '0';
                } else {
                    $result['atividade'] = number_format($row['p110atv'], 0);
                    
                    // Formatar data de produção
                    $dataProd = $row['p110dtpx'];
                    $result['data_producao'] = substr($dataProd, 6, 4) . '-' . substr($dataProd, 3, 2) . '-' . substr($dataProd, 0, 2) . substr($dataProd, 10, 6);
                    
                    // Formatar data de validade
                    $dataVal = $row['p110dtvl'];
                    $result['data_validade'] = substr($dataVal, 6, 4) . '-' . substr($dataVal, 3, 2) . '-' . substr($dataVal, 0, 2) . substr($dataVal, 10, 6);
                    
                    $result['concentracao'] = number_format($row['p110cr'], 2);
                    $result['volume'] = number_format($row['p110volu'], 2);
                    $result['atividade_especifica'] = number_format($row['p110espe'], 2);
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Erro em verificarLoteExiste: ' . $e->getMessage());
            return [
                'atividade' => '0',
                'data_producao' => '',
                'data_validade' => '',
                'concentracao' => '0',
                'volume' => '0',
                'atividade_especifica' => '0'
            ];
        }
    }

    // ===================================
    // ESCALA DE TAREFAS
    // ===================================

    /**
     * Retorna lista de tarefas da escala
     * 
     * @return array
     */
    public function retornaEscalaTarefas()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT ID, Nome, cdUsuario, datatualizacao FROM sgcr.crsa.T0111_ESCALA_TAREFAS ORDER BY ID DESC";
            $stmt = $dbh->query($sql);
            
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\Exception $e) {
            Log::error('Erro em retornaEscalaTarefas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna lista de tarefas da escala em formato JSON para DataTables
     * 
     * @return array
     */
    public function retornaEscalaTarefasJson()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT ID, Nome, cdUsuario, datatualizacao FROM sgcr.crsa.T0111_ESCALA_TAREFAS ORDER BY ID DESC";
            $stmt = $dbh->query($sql);
            
            $tarefas = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tarefas[] = [
                    'ID' => $row['ID'],
                    'Nome' => $row['Nome'],
                    'datatualizacao' => date('d/m/Y H:i', strtotime($row['datatualizacao'])),
                    'cdUsuario' => $row['cdUsuario']
                ];
            }
            
            return $tarefas;
        } catch (\Exception $e) {
            Log::error('Erro em retornaEscalaTarefasJson: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Insere uma nova tarefa na escala
     * 
     * @param string $nomeTarefa
     * @param string $usuario
     * @return bool
     */
    public function inserirEscalaTarefa($nomeTarefa, $usuario)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "INSERT INTO sgcr.crsa.T0111_ESCALA_TAREFAS (Nome, datatualizacao, cdUsuario) VALUES (:nome, GETDATE(), :usuario)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':nome', $nomeTarefa, \PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $usuario, \PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em inserirEscalaTarefa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza uma tarefa existente na escala
     * 
     * @param int $id
     * @param string $nomeTarefa
     * @param string $usuario
     * @return bool
     */
    public function atualizarEscalaTarefa($id, $nomeTarefa, $usuario)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "UPDATE sgcr.crsa.T0111_ESCALA_TAREFAS SET Nome = :nome, datatualizacao = GETDATE(), cdUsuario = :usuario WHERE ID = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':nome', $nomeTarefa, \PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $usuario, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em atualizarEscalaTarefa: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui uma tarefa da escala
     * 
     * @param int $id
     * @param string $usuario
     * @return bool
     */
    public function excluirEscalaTarefa($id, $usuario)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "DELETE FROM sgcr.crsa.T0111_ESCALA_TAREFAS WHERE ID = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em excluirEscalaTarefa: ' . $e->getMessage());
            return false;
        }
    }

    // ===================================
    // FUNÇÕES PARA ESCALA SEMANAL
    // ===================================

    /**
     * Retorna tipos de processo para escala semanal
     * 
     * @return string
     */
    public function retornaEscalaTipoProcesso()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT DISTINCT nome FROM sgcr.crsa.T0111_ESCALA_TAREFAS WHERE nome IS NOT NULL ORDER BY nome";
            $stmt = $dbh->query($sql);
            
            $html = '<option value="">Selecione...</option>';
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $html .= '<option value="' . $row['nome'] . '">' . $row['nome'] . '</option>';
            }
            
            return $html;
        } catch (\Exception $e) {
            Log::error('Erro em retornaEscalaTipoProcesso: ' . $e->getMessage());
            return '<option value="">Erro ao carregar tipos de processo</option>';
        }
    }

    /**
     * Retorna tarefas para escala semanal
     * 
     * @return string
     */
    public function retornaEscalaTarefasSenanal()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT ID, Nome FROM sgcr.crsa.T0111_ESCALA_TAREFAS ORDER BY Nome";
            $stmt = $dbh->query($sql);
            
            $html = '<option value="">Selecione...</option>';
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $html .= '<option value="' . $row['ID'] . '">' . $row['Nome'] . '</option>';
            }
            
            return $html;
        } catch (\Exception $e) {
            Log::error('Erro em retornaEscalaTarefasSenanal: ' . $e->getMessage());
            return '<option value="">Erro ao carregar tarefas</option>';
        }
    }

    /**
     * Retorna lista de usuários para combobox
     * 
     * @return string
     */
    public function retornaListaUsuariosCMB()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT p1110_usuarioid, p1110_nome FROM sgcr.crsa.T1110_USUARIOS WHERE p1110_Ativo = 'A' and p1110_nome <> '' ORDER BY p1110_nome";
            $stmt = $dbh->query($sql);
            
            $html = '';
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $html .= '<option value="' . $row['p1110_usuarioid'] . '">' . $row['p1110_nome'] . '</option>';
            }
            
            return $html;
        } catch (\Exception $e) {
            Log::error('Erro em retornaListaUsuariosCMB: ' . $e->getMessage());
            return '<option value="">Erro ao carregar usuários</option>';
        }
    }

    /**
     * Retorna usuários associados para um lote e tarefa específicos
     * 
     * @param string $lote
     * @param string $tarefa
     * @return string
     */
    public function retornaUsuariosAssocCMB($lote, $tarefa)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT DISTINCT u.cdUsuario, u.Nome, 'dispon' as tipo
                    FROM sgcr.crsa.Usuarios u
                    WHERE u.Ativo = 1 
                    AND u.cdUsuario NOT IN (
                        SELECT DISTINCT Responsavel 
                        FROM sgcr.crsa.T0111_ESCALA_TAREFAS 
                        WHERE Lote = :lote AND TarefaID = :tarefa
                    )
                    UNION ALL
                    SELECT DISTINCT u.cdUsuario, u.Nome, 'assoc' as tipo
                    FROM sgcr.crsa.Usuarios u
                    INNER JOIN sgcr.crsa.T0111_ESCALA_TAREFAS e ON u.cdUsuario = e.Responsavel
                    WHERE u.Ativo = 1 
                    AND e.Lote = :lote2 AND e.TarefaID = :tarefa2
                    ORDER BY tipo, Nome";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':lote', $lote, \PDO::PARAM_STR);
            $stmt->bindParam(':tarefa', $tarefa, \PDO::PARAM_STR);
            $stmt->bindParam(':lote2', $lote, \PDO::PARAM_STR);
            $stmt->bindParam(':tarefa2', $tarefa, \PDO::PARAM_STR);
            $stmt->execute();
            
            $resultado = '';
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $resultado .= $row['tipo'] . ',' . $row['cdUsuario'] . ',' . $row['Nome'] . ',';
            }
            
            return rtrim($resultado, ',');
        } catch (\Exception $e) {
            Log::error('Erro em retornaUsuariosAssocCMB: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Retorna escala semanal
     * 
     * @return string
     */
    public function retornaEscalaSemanal()
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "SELECT e.ID, e.Lote, p.Nome as Produto, e.TipoProcesso, 
                           e.DataInicio, e.DataFim, e.DataExecucao, t.Nome as Tarefa,
                           e.Responsaveis
                    FROM sgcr.crsa.T0111_ESCALA_TAREFAS e
                    LEFT JOIN sgcr.crsa.Produtos p ON e.ProdutoID = p.ID
                    LEFT JOIN sgcr.crsa.T0111_ESCALA_TAREFAS t ON e.TarefaID = t.ID
                    ORDER BY e.DataInicio DESC, e.Lote";
            $stmt = $dbh->query($sql);
            
            $html = '';
            $contador = 1;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<button type="button" class="btn btn-sm btn-outline-primary" onclick="fuEditaEscala(' . 
                         $row['ID'] . ',\'' . $row['Lote'] . '\',\'' . $row['Tarefa'] . '\',\'' . 
                         $row['Produto'] . '\',\'' . $this->formataDataISO($row['DataInicio']) . '\',\'' . 
                         $this->formataDataISO($row['DataFim']) . '\',\'\',\'' . 
                         $this->formataDataHora($row['DataExecucao']) . '\',\'' . $row['TipoProcesso'] . '\')">';
                $html .= '<i class="fas fa-edit"></i></button>';
                $html .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="fuDeleta(' . $row['ID'] . ')">';
                $html .= '<i class="fas fa-trash"></i></button>';
                $html .= '</td>';
                $html .= '<td style="text-align:center;">' . $contador . '</td>';
                $html .= '<td>' . $row['Lote'] . '</td>';
                $html .= '<td>' . $row['Produto'] . '</td>';
                $html .= '<td>' . $row['TipoProcesso'] . '</td>';
                $html .= '<td>' . $this->formataDataISO($row['DataInicio']) . '</td>';
                $html .= '<td>' . $this->formataDataISO($row['DataFim']) . '</td>';
                $html .= '<td>' . $this->formataDataHora($row['DataExecucao']) . '</td>';
                $html .= '<td>' . $row['Tarefa'] . '</td>';
                $html .= '<td>' . $row['Responsaveis'] . '</td>';
                $html .= '</tr>';
                $contador++;
            }
            
            return $html;
        } catch (\Exception $e) {
            Log::error('Erro em retornaEscalaSemanal: ' . $e->getMessage());
            return '<tr><td colspan="10">Erro ao carregar dados da escala semanal</td></tr>';
        }
    }

    /**
     * Insere uma nova escala semanal
     * 
     * @param array $dados
     * @return bool
     */
    public function inserirEscalaSemanal($dados)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "INSERT INTO sgcr.crsa.T0111_ESCALA_TAREFAS 
                    (Lote, ProdutoID, TipoProcesso, DataInicio, DataFim, TarefaID, DataExecucao, Responsaveis, UsuarioCriacao, DataCriacao) 
                    VALUES (:lote, :produto, :tipoProcesso, :dataInicio, :dataFim, :tarefa, :dataExecucao, :responsaveis, :usuario, GETDATE())";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':lote', $dados['lote'], \PDO::PARAM_STR);
            $stmt->bindParam(':produto', $dados['produto'], \PDO::PARAM_STR);
            $stmt->bindParam(':tipoProcesso', $dados['tipoProcesso'], \PDO::PARAM_STR);
            $stmt->bindParam(':dataInicio', $dados['dataInicio'], \PDO::PARAM_STR);
            $stmt->bindParam(':dataFim', $dados['dataFim'], \PDO::PARAM_STR);
            $stmt->bindParam(':tarefa', $dados['tarefa'], \PDO::PARAM_STR);
            $stmt->bindParam(':dataExecucao', $dados['dataExecucao'], \PDO::PARAM_STR);
            $stmt->bindParam(':responsaveis', $dados['usuarios'], \PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $dados['usuario'], \PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em inserirEscalaSemanal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza uma escala semanal existente
     * 
     * @param array $dados
     * @return bool
     */
    public function atualizarEscalaSemanal($dados)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "UPDATE sgcr.crsa.T0111_ESCALA_TAREFAS 
                    SET ProdutoID = :produto, TipoProcesso = :tipoProcesso, DataExecucao = :dataExecucao, 
                        Responsaveis = :responsaveis, UsuarioAtualizacao = :usuario, DataAtualizacao = GETDATE()
                    WHERE ID = :id";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':produto', $dados['produto'], \PDO::PARAM_STR);
            $stmt->bindParam(':tipoProcesso', $dados['tipoProcesso'], \PDO::PARAM_STR);
            $stmt->bindParam(':dataExecucao', $dados['dataExecucao'], \PDO::PARAM_STR);
            $stmt->bindParam(':responsaveis', $dados['usuarios'], \PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $dados['usuario'], \PDO::PARAM_STR);
            $stmt->bindParam(':id', $dados['id'], \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em atualizarEscalaSemanal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exclui uma escala semanal
     * 
     * @param int $id
     * @param string $usuario
     * @return bool
     */
    public function excluirEscalaSemanal($id, $usuario)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "DELETE FROM sgcr.crsa.T0111_ESCALA_TAREFAS WHERE ID = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em excluirEscalaSemanal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Duplica a última escala semanal
     * 
     * @param string $usuario
     * @return bool
     */
    public function duplicarEscalaSemanal($usuario)
    {
        try {
            $dbh = DB::connection()->getPdo();
            $sql = "INSERT INTO sgcr.crsa.T0111_ESCALA_TAREFAS 
                    (Lote, ProdutoID, TipoProcesso, DataInicio, DataFim, TarefaID, DataExecucao, Responsaveis, UsuarioCriacao, DataCriacao)
                    SELECT TOP 1 Lote, ProdutoID, TipoProcesso, 
                           DATEADD(week, 1, DataInicio) as DataInicio, 
                           DATEADD(week, 1, DataFim) as DataFim, 
                           TarefaID, 
                           DATEADD(week, 1, DataExecucao) as DataExecucao, 
                           Responsaveis, :usuario, GETDATE()
                    FROM sgcr.crsa.T0111_ESCALA_TAREFAS 
                    ORDER BY DataCriacao DESC";
            
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':usuario', $usuario, \PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            Log::error('Erro em duplicarEscalaSemanal: ' . $e->getMessage());
            return false;
        }
    }
}