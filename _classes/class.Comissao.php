<?php

class Comissao {

    /**
     * Abriga as várias rotina referentes a comissao
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     * @var private $idComissao integer null O id do concurso
     */
##############################################################

    public function __construct() {
        /**
         * Inicia a Classe somente
         *
         * @param $idContrato integer null O id do concurso
         *
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
    }

##############################################################

    public function getDados($idComissao = null) {
        # Verifica se foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Pega os dados
        $select = "SELECT *
                     FROM tbcomissao
                    WHERE idComissao = {$idComissao}";

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

#####################################################################################

    public function getPortariaEntrada($idComissao) {

        # Verifica se foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $return = null;

        # Pega os dados
        $select = "SELECT portariaEntrada,
                          dtPortariaEntrada,
                          dtPublicacaoEntrada
                     FROM tbcomissao
                    WHERE idComissao = {$idComissao}";

        $row = $contratos->select($select, false);

        # Trata o retorno
        if (empty($row["portariaEntrada"])) {
            return "---";
        } else {
            $return = "Portaria n° {$row["portariaEntrada"]}";

            if (!empty($row["dtPortariaEntrada"])) {
                $return .= " de " . date_to_php($row["dtPortariaEntrada"]);
            }

            if (!empty($row["dtPublicacaoEntrada"])) {
                $return .= "<br/>publicada no DOERJ de " . date_to_php($row["dtPublicacaoEntrada"]);
            }
        }

        # Retorno
        return $return;
    }

#####################################################################################

    public function get_membroTipo($idComissao) {

        # Verifica se o id foi informado
        if (empty($idComissao)) {
            return null;
        } else {

            $conteudo = $this->getDados($idComissao);
            $tipo = $conteudo["idTipoMembro"];
            $dtPublicacaoSaida = $conteudo["dtPublicacaoSaida"];

            # Sem tipo
            if (empty($tipo)) {
                return null;
            }

            # Conecta ao Banco de Dados
            $contratos = new Contratos();

            # Pega o tipo
            $select = "SELECT tipo FROM tbtipomembro WHERE idTipoMembro = {$tipo}";
            $row = $contratos->select($select, false);

            if (empty($dtPublicacaoSaida)) {
                return $row[0];
            } else {
                return "{$row[0]}<br/>(Saiu)";
            }
        }
    }

#####################################################################################

    public function exibe_membroTipo($idComissao) {

        if (empty($idComissao)) {
            return null;
        } else {

            $retorno = $this->get_membroTipo($idComissao);

            if ($this->e_membroExterno($idComissao)) {
                $retorno .= "<br/><span class='label warning' title='Membro Externo'>Externo</span>";
            }

            return $retorno;
        }
    }

#####################################################################################

    public function e_membroExterno($idComissao) {

        if (empty($idComissao)) {
            return null;
        } else {

            if (empty($this->getDados($idComissao)["idServidor"])) {
                return true;
            } else {
                return false;
            }
        }
    }

#####################################################################################

    public function getNomeMembro($idComissao) {

        # Verifica se o id foi informado
        if (empty($idComissao)) {
            return null;
        }

        # Classes
        $pessoal = new Pessoal();
        $membro = new MembroExterno();

        # Pega os dados desse membro
        $dados = $this->getDados($idComissao);
        $idServidor = $dados["idServidor"];
        $idMembroExterno = $dados["idMembroExterno"];

        # Se for membro servidor
        if (!empty($idServidor)) {
            return $pessoal->get_nome($idServidor);
        }

        # Se for membro externo
        if (!empty($idMembroExterno)) {
            $dados2 = $membro->get_dados($idMembroExterno);
            return $dados2["nome"];
        }
    }

#####################################################################################

    public function get_dadosMembro($idComissao) {

        # Verifica se o id foi informado
        if (empty($idComissao)) {
            return null;
        }

        # Classes
        $pessoal = new Pessoal();
        $membro = new MembroExterno();

        # Pega os dados desse membro
        $dados = $this->getDados($idComissao);
        $idServidor = $dados["idServidor"];
        $idMembroExterno = $dados["idMembroExterno"];

        # Se for membro servidor
        if (!empty($idServidor)) {
            $idPessoa = $pessoal->get_idPessoa($idServidor);

            p($pessoal->get_nome($idServidor), "pmembroNome");
            p($pessoal->get_lotacao($idServidor), "pmembroLotacao");
            p($pessoal->get_cargo($idServidor), "pmembroLotacao");
            p("ID: {$pessoal->get_idFuncional($idServidor)}", "pmembroLotacao");
            p("CPF: {$pessoal->get_cpf($idPessoa)}", "pmembroLotacao");
        }

        # Se for membro externo
        if (!empty($idMembroExterno)) {
            $dados2 = $membro->get_dados($idMembroExterno);

            p($dados2["nome"], "pmembroNome");
            p("Órgão: {$dados2['orgao']}", "pmembroLotacao");
            p("CPF: {$dados2['cpf']}", "pmembroLotacao");
            p("Membro Externo", "pmembroLotacao");
        }

        # Dados do que foi substituído
        if (!empty($dados["substituindo"])) {
            hr("hrComissao");
            $dadossubs = $this->getDados($dados["substituindo"]);
            p("Substutuindo:", "pmembroLotacao");

            # Se for servidor
            if (!empty($dadossubs["idServidor"])) {
                p($pessoal->get_nome($dadossubs["idServidor"]), "pmembroLotacao");
            } else {
                p($membro->get_nome($dadossubs["idMembroExterno"]), "pmembroLotacao");
            }
        }
    }

#####################################################################################

    public function get_contatosMembro($idComissao) {

        # Verifica se o id foi informado
        if (empty($idComissao)) {
            return null;
        }

        # Pega os dados desse membro
        $dados = $this->getDados($idComissao);
        $idServidor = $dados["idServidor"];
        $idMembroExterno = $dados["idMembroExterno"];

        # Se for membro servidor
        if (!empty($idServidor)) {
            $pessoal = new Pessoal();
            $pessoal->get_contatos($idServidor);
        }

        # Se for membro externo
        if (!empty($idMembroExterno)) {
            $membro = new MembroExterno();
            $dados2 = $membro->get_dados($idMembroExterno);

            p($dados2["telefone"], "pmembroLotacao");
            p($dados2["email"], "pmembroLotacao");
        }
    }

#####################################################################################

    public function get_emailMembro($idComissao) {

        # Verifica se o id foi informado
        if (empty($idComissao)) {
            return null;
        }

        # Pega os dados desse membro
        $dados = $this->getDados($idComissao);
        $idServidor = $dados["idServidor"];
        $idMembroExterno = $dados["idMembroExterno"];

        # Se for membro servidor
        if (!empty($idServidor)) {
            $pessoal = new Pessoal();
            return $pessoal->get_emailUenf($dados["idServidor"]) . " " . $pessoal->get_emailPessoal($dados["idServidor"]) . " " . $pessoal->get_emailOutro($dados["idServidor"]);
        }

        # Se for membro externo
        if (!empty($idMembroExterno)) {
            $membro = new MembroExterno();
            $dados2 = $membro->get_dados($idMembroExterno);
            return $dados2["email"];
        }
    }

#####################################################################################

    public function listaComissao($idContrato, $idUsuario) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT idComissao,
                          idComissao,
                          idComissao,
                          idComissao
                     FROM tbcomissao JOIN tbtipomembro USING (idTipoMembro)
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL  
                 ORDER BY dtPublicacaoSaida, numOrdem";

        $row = $contratos->select($select);

        # Colore a llinha de acordo com o tipo
        $formatacaoCondicional = array(
            array('coluna' => 2,
                'valor' => "Presidente",
                'operador' => '=',
                'id' => 'presidenteComissao'),
            array('coluna' => 2,
                'valor' => "Gestor",
                'operador' => '=',
                'id' => 'presidenteComissao'),
            array('coluna' => 2,
                'valor' => "Membro",
                'operador' => '=',
                'id' => 'membroComissao'),
            array('coluna' => 2,
                'valor' => "Membro Externo",
                'operador' => '=',
                'id' => 'membroComissao'),
            array('coluna' => 2,
                'valor' => "Suplente",
                'operador' => '=',
                'id' => 'cuplenteComissao'),
            array('coluna' => 2,
                'valor' => "Saiu",
                'operador' => '=',
                'id' => 'saiuComissao'),
        );

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo("Comissão de Fiscalização");
        $tabela->set_label(["Foto", "Membro", "Tipo"]);
        $tabela->set_align(["center", "left", "center"]);
        $tabela->set_width([20, 60, 20]);
        $tabela->set_classe(["Comissao", "Comissao", "Comissao"]);
        $tabela->set_metodo(["get_foto", "get_dadosMembro", "exibe_membroTipo"]);
        $tabela->set_conteudo($row);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);
        $tabela->show();

        # Editar
        if (Verifica::acesso($idUsuario, [1, 9])) {
            $div = new Div("divEdita1Comissao");
            $div->abre();

            $div = new Div("divEdita2");
            $div->abre();

            $botaoEditar = new Link("Editar", "cadastroComissao.php");
            $botaoEditar->set_class('tiny button secondary');
            $botaoEditar->set_title('Editar comissão');
            $botaoEditar->show();

            $div->fecha();
            $div->fecha();
        }
    }

##########################################################################################

    public function get_foto($idComissao) {

        # Função que retorna a foto do servidor
        #
        # Parâmetro: idComissao
        # Pega os dados desse membro de comissão
        $dados = $this->getDados($idComissao);

        if (!empty($dados["idServidor"])) {

            $pessoal = new Pessoal();
            $foto = new ExibeFoto();
            $foto->set_fotoAltura("100%");
            $foto->set_fotoLargura("100%");
            $foto->show($pessoal->get_idPessoa($dados["idServidor"]));
        }

        if (!empty($dados["idMembroExterno"])) {

            $figura = new Imagem(PASTA_FIGURAS . 'foto.png', null, 100, 150);
            $figura->show();
        }
    }

    ##########################################################################################

    public function listaComissaoRel($idContrato, $log) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();
        $contrato = new Contrato();

        tituloRelatorio("Comissão de Fiscalização");

        # Monta o select
        $select = "(SELECT uenf_grh.tbpessoa.nome,
                          idServidor,
                          idComissao,
                          idComissao,
                          numOrdem
                     FROM tbcomissao JOIN uenf_grh.tbservidor USING(idServidor)
                                     JOIN uenf_grh.tbpessoa USING(idPessoa)
                                     JOIN tbtipomembro USING (idTipoMembro)
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL)
                 UNION 
                    (SELECT tbmembroexterno.nome,
                          idComissao,
                          idComissao,
                          idComissao,
                          numOrdem
                     FROM tbcomissao JOIN tbmembroexterno USING (idMembroExterno)
                                     JOIN tbtipomembro USING (idTipoMembro)
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL)
                 ORDER BY 5";

        $row = $contratos->select($select);

        # Monta o Relatório
        $relatorio = new Relatorio();
        $relatorio->set_conteudo($row);
        $relatorio->set_label(["Servidor", "Lotação", "Designação", "Tipo"]);
        $relatorio->set_align(["left", "left", "left"]);
        $relatorio->set_width([30, 30, 30, 10]);
        $relatorio->set_classe([null, "Pessoal", "Comissao", "Comissao"]);
        $relatorio->set_metodo([null, "get_lotacao", "getPortariaEntrada", "exibe_membroTipo"]);

        $relatorio->set_subTotal(false);
        $relatorio->set_totalRegistro(false);
        $relatorio->set_dataImpressao(false);
        $relatorio->set_cabecalhoRelatorio(false);
        $relatorio->set_menuRelatorio(false);
        $relatorio->set_bordaInterna(true);

        # Grava o log, pois esta rotina é usada no relatório de folha de rosto do contrato
        $relatorio->set_logDetalhe($log);
        $relatorio->gravaLog();
        $relatorio->show();
    }

#####################################################################################

    public function exibeProcessoFiscalizacaoRel($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();
        $contrato = new Contrato();

        tituloRelatorio("Processo de Fiscalização");
        $processo = $this->getProcesso($idContrato);
        if (empty($processo)) {
            br();
            p("Não existe nenhum processo cadastrado !", "f12", "center");
        } else {
            hr("nenhumItem");
            p("Processo", "pprocessoFiscalizacao");
            hr("nenhumItem");
            p($this->getProcesso($idContrato), "pprocessoFiscalizacao");
        }
        hr("nenhumItem");
    }

#####################################################################################

    public function getDadosDesignacao($idComissao) {

        # Conecta ao Banco de Dados
        $pessoal = new Pessoal();

        # Verifica se foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        } else {

            $conteudo = $this->getDados($idComissao);
            $portariaSaida = $conteudo["portariaSaida"];
            $dtPortariaSaida = $conteudo["dtPortariaSaida"];
            $dtPublicacaoSaida = $conteudo["dtPublicacaoSaida"];
            $pgPublicacaoSaida = $conteudo["pgPublicacaoSaida"];

            # Designação
            if ((!empty($conteudo["portariaEntrada"])) and (!empty($conteudo["dtPortariaEntrada"]))) {
                p("Designação:", "pmembroLotacao");
                p("Portaria {$conteudo["portariaEntrada"]} de " . date_to_php($conteudo["dtPortariaEntrada"]), "pmembroLotacao");
                $dados = null;

                if (!empty($conteudo["dtPublicacaoEntrada"])) {
                    $dados .= "DOERJ: " . date_to_php($conteudo["dtPublicacaoEntrada"]);

                    if (!empty($conteudo["pgPublicacaoEntrada"])) {
                        $dados .= " pag: {$conteudo["pgPublicacaoEntrada"]}";
                    }

                    p($dados, "pmembroLotacao");
                }
            }

            # Saída
            if ((!empty($conteudo["portariaSaida"])) and (!empty($conteudo["dtPortariaSaida"]))) {
                hr("hrComissao");
                p("Saída:", "pmembroLotacao");
                p("Portaria {$conteudo["portariaSaida"]} de " . date_to_php($conteudo["dtPortariaSaida"]), "pmembroLotacao");
                $dados = null;

                if (!empty($conteudo["dtPublicacaoSaida"])) {
                    $dados .= "DOERJ: " . date_to_php($conteudo["dtPublicacaoSaida"]);

                    if (!empty($conteudo["pgPublicacaoSaida"])) {
                        $dados .= " pag: {$conteudo["pgPublicacaoSaida"]}";
                    }

                    p($dados, "pmembroLotacao");
                }
            }
        }
    }

    ##############################################################

    public function exibeProcesso($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $processo = $this->getProcesso($idContrato);

        # Exibe os Processos
        $painel = new Callout();
        $painel->abre();

        tituloTable("Processos de Fiscalização:");
        br();

        if (empty($processo)) {
            br();
            P("Nenhum processo cadastrado", "center", "f14");
            br();
        } else {
            br();
            p($processo, "center", "f14");
        }

        # Editar    
        $div = new Div("divEdita1");
        $div->abre();

        $div = new Div("divEdita2");
        $div->abre();

        $botaoEditar = new Link("Editar", "?fase=cadastroProcesso");
        $botaoEditar->set_class('tiny button secondary');
        $botaoEditar->set_title('Editar contrato');
        $botaoEditar->show();

        $div->fecha();
        $div->fecha();

        $painel->fecha();

        return;
    }

    ##############################################################

    public function exibeProcessoRelatorio($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $contrato = new Contrato();
        $conteudo = $contrato->getDados($idContrato);

        $processo = null;

        # Verifica se tem somente um processo
        if ((empty($conteudo["processoComissaoSei"])) xor (empty($conteudo["processoComissao"]))) {
            if (empty($conteudo["processoComissaoSei"])) {
                $processo = $conteudo["processoComissao"];
            } else {
                $processo = "SEI - {$conteudo["processoComissaoSei"]}";
            }
        }

        # Verifica se tem os dois
        if ((!empty($conteudo["processoComissaoSei"])) and (!empty($conteudo["processoComissao"]))) {
            $processo = "SEI - {$conteudo["processoComissaoSei"]} <br/> {$conteudo["processoComissao"]}";
        }

        # Exibe os Processos
        if (empty($processo)) {
            p("---", "center", "f12");
        } else {
            p($processo, "center", "f12");
        }
        return;
    }

    ##############################################################

    public function exibeDocumentosRelatorio($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $this->exibeProcessoRelatorio($idContrato);
        $this->exibePortariasRelatorio($idContrato);
        return;
    }

    ##############################################################

    public function exibeMenuDocumentos($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $painel = new Callout();
        $painel->abre();

        titulotable('Documentos');
        br();

        $tamanhoImage = 60;
        $menu = new MenuGrafico(3);

        $botao = new BotaoGrafico();
        $botao->set_label('Legislação');
        $botao->set_url('#');
        $botao->set_imagem(PASTA_FIGURAS . 'legislacao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Legislação referente a comissão de fiscalização');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Formulário');
        $botao->set_url('#');
        #$botao->set_target("_blank");
        $botao->set_imagem(PASTA_FIGURAS . 'formulario.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Legislação referente a comissão de fiscalização');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Checklist');
        $botao->set_url('#');
        $botao->set_imagem(PASTA_FIGURAS . 'checklist.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Legislação referente a comissão de fiscalização');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ##############################################################

    public function getProcesso($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $contrato = new Contrato();
        $conteudo = $contrato->getDados($idContrato);

        if (empty($conteudo["processoComissaoSei"]) AND empty($conteudo["processoComissao"])) {
            return null;
        } else {
            $processo = null;

            # Verifica se tem somente um processo
            if ((empty($conteudo["processoComissaoSei"])) XOR (empty($conteudo["processoComissao"]))) {
                if (empty($conteudo["processoComissaoSei"])) {
                    $processo = $conteudo["processoComissao"];
                } else {
                    $processo = "SEI - {$conteudo["processoComissaoSei"]}";
                }
            }

            # Verifica se tem os dois
            if ((!empty($conteudo["processoComissaoSei"])) AND (!empty($conteudo["processoComissao"]))) {
                $processo = "SEI - {$conteudo["processoComissaoSei"]} <br/> {$conteudo["processoComissao"]}";
            }

            return $processo;
        }
    }

    ##############################################################

    public function exibePortarias($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Monta o select
        $select = "(SELECT portariaEntrada as portaria,
                          dtPortariaEntrada as data
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato})
                        UNION 
                  (SELECT portariaSaida as portaria,
                          dtPortariaSaida as data
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato})
                 ORDER BY 2";

        $contratos = new Contratos();
        $dados = $contratos->select($select);
        $quantidade = $contratos->count($select);

        $portarias = [];

        # Monta as portarias
        foreach ($dados as $item) {
            if (!empty($item["portaria"])) {
                $portarias[] = "Portaria {$item["portaria"]} de " . date_to_php($item["data"]);
            }
        }

        # Retira as duplicatas
        $portarias = array_unique($portarias);

        # Exibe as portarias
        $painel = new Callout();
        $painel->abre();

        tituloTable("Portarias:");
        br();

        foreach ($portarias as $item) {
            p($item, "pPortaria");
        }

        if ($quantidade == 0) {
            br();
            p("Nenhuma portaria encontrada", "center", "f14");
            br();
        }
        $painel->fecha();

        return;
    }

    ##############################################################

    public function exibePortariasRelatorio($idContrato = null) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Monta o select
        $select = "(SELECT portariaEntrada as portaria,
                          dtPortariaEntrada as data
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato})
                        UNION 
                  (SELECT portariaSaida as portaria,
                          dtPortariaSaida as data
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato})
                 ORDER BY 2";

        $contratos = new Contratos();
        $dados = $contratos->select($select);
        $quantidade = $contratos->count($select);

        $portarias = [];

        # Monta as portarias
        foreach ($dados as $item) {
            if (!empty($item["portaria"])) {
                $portarias[] = "Portaria {$item["portaria"]} de " . date_to_php($item["data"]);
            }
        }

        # Retira as duplicatas
        $portarias = array_unique($portarias);

        # Exibe as portarias
        foreach ($portarias as $item) {
            p($item, "pPortaria");
        }

        if ($quantidade == 0) {
            br();
            p("---", "center", "f14");
            br();
        }
        return;
    }

    #####################################################################################

    public function getUltimaDataPublicacaoEntrada($idContrato) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $return = null;

        # Pega os dados
        $select = "SELECT dtPublicacaoEntrada
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                    ORDER BY dtPublicacaoEntrada desc LIMIT 1";

        $row = $contratos->select($select, false);

        # Trata o retorno
        if (empty($row["dtPublicacaoEntrada"])) {
            $return = null;
        } else {
            $return = date_to_php($row["dtPublicacaoEntrada"]);
        }

        # Retorno
        return $return;
    }

####################################################################################

    public function getUltimaPortariaEntrada($idContrato) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $return = null;

        # Pega os dados
        $select = "SELECT portariaEntrada
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                    ORDER BY dtPublicacaoEntrada desc LIMIT 1";

        $row = $contratos->select($select, false);

        # Trata o retorno
        if (empty($row["portariaEntrada"])) {
            $return = null;
        } else {
            $return = $row["portariaEntrada"];
        }

        # Retorno
        return $return;
    }

####################################################################################

    public function getUltimaDataPortariaEntrada($idContrato) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $return = null;

        # Pega os dados
        $select = "SELECT dtPortariaEntrada
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                    ORDER BY dtPublicacaoEntrada desc LIMIT 1";

        $row = $contratos->select($select, false);

        # Trata o retorno
        if (empty($row["dtPortariaEntrada"])) {
            $return = null;
        } else {
            $return = date_to_php($row["dtPortariaEntrada"]);
        }

        # Retorno
        return $return;
    }

###################################################################################

    public function getUltimaPgPublicacaoEntrada($idContrato) {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $return = null;

        # Pega os dados
        $select = "SELECT pgPublicacaoEntrada
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                    ORDER BY dtPublicacaoEntrada desc LIMIT 1";

        $row = $contratos->select($select, false);

        # Trata o retorno
        if (empty($row["pgPublicacaoEntrada"])) {
            $return = null;
        } else {
            $return = $row["pgPublicacaoEntrada"];
        }

        # Retorno
        return $return;
    }

#####################################################################################

    public function listaComissaoRelatorio($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "(SELECT uenf_grh.tbpessoa.nome,
                          idComissao,
                          numOrdem
                     FROM tbcomissao JOIN uenf_grh.tbservidor USING(idServidor)
                                     JOIN uenf_grh.tbpessoa USING(idPessoa)
                                     JOIN tbtipomembro USING (idTipoMembro)
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL)
                 UNION 
                    (SELECT tbmembroexterno.nome,
                          idComissao,
                          numOrdem
                     FROM tbcomissao JOIN tbmembroexterno USING (idMembroExterno)
                                     JOIN tbtipomembro USING (idTipoMembro)
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL)
                 ORDER BY 3, 1";

        $row = $contratos->select($select);
        $numItem = $contratos->count($select);
        $contador = 1;

        foreach ($row as $item) {

            # Pega o tipo
            $tipo = $this->get_membroTipo($item["idComissao"]);

            # Nome do servidor e designação
            if ($tipo == "Presidente" OR $tipo == "Gestor") {
                p("{$this->getNomeMembro($item['idComissao'])} - {$tipo}", "pComissaoImpressao");
            } else {
                p($this->getNomeMembro($item["idComissao"]), "pComissaoImpressao");
            }

            # p($this->get_emailMembro($item["idComissao"], false, false), "pComissaoImpressao");
            if ($contador < $numItem) {
                hr("geral");
                $contador++;
            }
        }
    }

#####################################################################################

    public function listaComissaoEmailRelatorio($idContrato) {
        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Monta o select
        $select = "(SELECT uenf_grh.tbpessoa.nome,
                          idComissao,
                          numOrdem
                     FROM tbcomissao JOIN uenf_grh.tbservidor USING(idServidor)
                                     JOIN uenf_grh.tbpessoa USING(idPessoa)
                                     JOIN tbtipomembro USING (idTipoMembro)
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL)
                 UNION 
                    (SELECT tbmembroexterno.nome,
                          idComissao,
                          numOrdem
                     FROM tbcomissao JOIN tbmembroexterno USING (idMembroExterno)
                                     JOIN tbtipomembro USING (idTipoMembro)
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL)
                 ORDER BY 3, 1";

        $row = $contratos->select($select);
        $numItem = $contratos->count($select);
        $contador = 1;

        foreach ($row as $item) {

            # Pega o tipo
            $tipo = $this->get_membroTipo($item["idComissao"]);

            # Nome do servidor e designação
            if ($tipo == "Presidente" OR $tipo == "Gestor") {
                p("{$this->getNomeMembro($item['idComissao'])} - {$tipo} - {$this->get_emailMembro($item[1])}", "pComissaoImpressao");
            } else {
                p("{$this->getNomeMembro($item["idComissao"])} - {$this->get_emailMembro($item[1])}", "pComissaoImpressao");
            }

            # p($this->get_emailMembro($item["idComissao"], false, false), "pComissaoImpressao");
            if ($contador < $numItem) {
                hr("geral");
                $contador++;
            }
        }
    }

#####################################################################################

    public function get_responsavel($idContrato) {
        # Verifica se foi informado
        if (empty($idContrato)) {
            alert("É necessário informar o id.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT idComissao
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL   
                      AND (idTipoMembro = 1 OR idTipoMembro = 4)";

        $row = $contratos->select($select, false);

        if (empty($row[0])) {
            return null;
        } else {
            return $this->getNomeMembro($row['idComissao']);
        }
    }

#####################################################################################

    public function get_numMembrosTipo($idTipo) {

        # Verifica se o id foi informado
        if (empty($idTipo)) {
            return null;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT idComissao
                     FROM tbcomissao
                    WHERE idTipoMembro = {$idTipo}";

        return $contratos->count($select);
    }

#####################################################################################
}
