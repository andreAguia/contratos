<?php

class Comissao
{
    /**
     * Abriga as várias rotina referentes a comissao
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     * @var private $idComissao integer null O id do concurso
     */
    private $idComissao = null;
##############################################################

    public function __construct($idComissao = null)
    {
        /**
         * Inicia a Classe somente
         *
         * @param $idContrato integer null O id do concurso
         *
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idComissao = $idComissao;
    }

##############################################################

    public function get_dados($idComissao = null)
    {
        # Joga o valor informado para a variável da classe
        if (!vazio($idComissao)) {
            $this->idComissao = $idComissao;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

        # Pega os dados
        $select = 'SELECT *
                     FROM tbcomissao
                    WHERE idComissao = ' . $this->idComissao;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

#####################################################################################

    public function get_portariaEntrada($idComissao)
    {

        # Joga o valor informado para a variável da classe
        if (!vazio($idComissao)) {
            $this->idComissao = $idComissao;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

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

    public function get_tipo($idComissao)
    {

        # Verifica se o id foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        } else {

            $conteudo          = $this->get_dados($idComissao);
            $dtPublicacaoSaida = $conteudo["dtPublicacaoSaida"];
            $tipo              = $conteudo["tipo"];

            # Verifica se foi informado
            if (vazio($tipo)) {
                return "---";
            } else {

                if ((!empty($dtPublicacaoSaida)) and (jaPassou(date_to_php($dtPublicacaoSaida)))) {
                    return "Saiu";
                } else {
                    switch ($tipo) {
                        case 1:
                            return "Presidente";
                            break;

                        case 2:
                            return "Membro";
                            break;

                        case 3:
                            return "Suplente";
                            break;
                    }
                }
            }
        }
    }

#####################################################################################

    public function get_nomeMembro($idServidor)
    {

        # Verifica se foi informado
        if (empty($idServidor)) {
            echo "---";
        } else {
            $pessoal = new Pessoal();
            p($pessoal->get_nome($idServidor), "pmembroNome");
            p($pessoal->get_lotacao($idServidor), "pmembroLotacao");
            p($pessoal->get_cargo($idServidor), "pmembroLotacao");
        }
    }

#####################################################################################

    public function get_dadosMembro($idServidor)
    {

        # Verifica se foi informado
        if (empty($idServidor)) {
            echo "---";
        } else {
            $pessoal  = new Pessoal();
            $idPessoa = $pessoal->get_idPessoa($idServidor);

            p($pessoal->get_nome($idServidor), "pmembroNome");
            p($pessoal->get_lotacao($idServidor), "pmembroLotacao");
            p($pessoal->get_cargo($idServidor), "pmembroLotacao");
            p("ID: {$pessoal->get_idFuncional($idServidor)}", "pmembroLotacao");
            p("CPF: {$pessoal->get_cpf($idPessoa)}", "pmembroLotacao");
        }
    }

#####################################################################################

    public function listaComissao($idContrato)
    {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT idServidor,
                          idComissao,
                          idComissao
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                      AND dtPublicacaoSaida IS NULL  
                 ORDER BY dtPublicacaoSaida,tipo";

        $row = $contratos->select($select);

        # Colore a llinha de acordo com o tipo
        $formatacaoCondicional = array(
            array('coluna'   => 1,
                'valor'    => "Presidente",
                'operador' => '=',
                'id'       => 'presidenteComissao'),
            array('coluna'   => 1,
                'valor'    => "Membro",
                'operador' => '=',
                'id'       => 'membroComissao'),
            array('coluna'   => 1,
                'valor'    => "Suplente",
                'operador' => '=',
                'id'       => 'cuplenteComissao'),
            array('coluna'   => 1,
                'valor'    => "Saiu",
                'operador' => '=',
                'id'       => 'saiuComissao'),
        );

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo("Comissão de Fiscalização");
        $tabela->set_label(array("Servidor", "Tipo"));
        $tabela->set_align(array("left", "center"));
        $tabela->set_width(array(70, 25));
        $tabela->set_classe(array("Comissao", "Comissao"));
        $tabela->set_metodo(array("get_nomeMembro", "get_tipo"));
        #$tabela->set_numeroOrdem(true);
        $tabela->set_conteudo($row);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);
        $tabela->show();

        # Editar
        $div = new Div("divEdita1Comissao");
        $div->abre();

        $div = new Div("divEdita2");
        $div->abre();

        $botaoEditar = new Link("Editar", "cadastroComissao.php");
        $botaoEditar->set_class('tiny button secondary');
        $botaoEditar->set_title('Editar situação');
        $botaoEditar->show();

        $div->fecha();
        $div->fecha();
    }

#####################################################################################

    public function get_dadosDesignacao($idComissao)
    {

        # Conecta ao Banco de Dados
        $pessoal = new Pessoal();

        # Verifica se foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        } else {

            $conteudo            = $this->get_dados($idComissao);
            $portariaEntrada     = $conteudo["portariaEntrada"];
            $dtPortariaEntrada   = $conteudo["dtPortariaEntrada"];
            $dtPublicacaoEntrada = $conteudo["dtPublicacaoEntrada"];
            $portariaSaida       = $conteudo["portariaSaida"];
            $dtPortariaSaida     = $conteudo["dtPortariaSaida"];
            $dtPublicacaoSaida   = $conteudo["dtPublicacaoSaida"];

            # Designação
            if ((!empty($portariaEntrada)) and (!empty($dtPortariaEntrada))) {
                p("Designação:", "pmembroLotacao");
                p("Portaria {$portariaEntrada} de " . date_to_php($dtPortariaEntrada), "pmembroLotacao");
                if (!empty($dtPublicacaoEntrada)) {
                    p("DOERJ: " . date_to_php($dtPublicacaoEntrada), "pmembroLotacao");
                }
            }

            # Saída
            if ((!empty($portariaSaida)) and (!empty($dtPortariaSaida))) {
                hr("hrComissao");
                p("Saída:", "pmembroLotacao");
                p("Portaria {$portariaSaida} de " . date_to_php($dtPortariaSaida), "pmembroLotacao");
                if (!empty($dtPublicacaoSaida)) {
                    p("DOERJ: " . date_to_php($dtPublicacaoSaida), "pmembroLotacao");
                }
            }
        }
    }

###########################################################

    public function exibeComissao($idContrato = null)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();
        $pessoal   = new Pessoal();

        $select = "SELECT *
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                 ORDER BY tipo";

        $total = $contratos->count($select);

        # Contator
        $contAdt = 1;

        $painel = new Callout();
        $painel->abre();

        titulo("Comissão de Fiscalização");
        br();

        foreach ($contratos->select($select) as $conteudo) {

            # Pega os valores
            $idServidor = $conteudo["idServidor"];
            $idPessoa   = $pessoal->get_idPessoa($idServidor);
            $tipo1      = $conteudo["tipo"];

            $idFuncional = $pessoal->get_idFuncional($idServidor);
            $servidor    = $pessoal->get_nome($idServidor);
            $cargo       = $pessoal->get_cargo($idServidor);
            $lotacao     = $pessoal->get_lotacao($idServidor);
            $cpf         = $pessoal->get_cpf($idPessoa); # Informa o tipo
            # trata Tipo
            if ($tipo1 == 1) {
                $tipo = "Presidente";
            } elseif ($tipo1 == 2) {
                $tipo = "Membro";
            } elseif ($tipo1 == 3) {
                $tipo = "Suplente";
            }

            # Monta o array de exibição
            $dados = [
                ["servidor", 9],
                ["tipo", 3],
            ];

            # Rotina de exibição
            $grid = new Grid();
            $grid->abreColuna(12);

            # incrementa contador
            $contAdt++;

            $grid->fechaColuna();

            foreach ($dados as $item) {

                # Monta a variável para usar o $$
                $pp = $item[0];

                # label
                if (empty($item[2])) {
                    $label = plm($pp);
                } else {
                    $label = $item[2];
                }

                # Verifica se tem variável com esse nome
                if (empty($$pp)) { // Se não tem variável com esse nome
                    if (empty($conteudo[$pp])) { // Se não tiver no array de conteúdo do bd
                        $dado = "---"; // Exibe tracinho
                    } else { // Se tiver conteúdo do bd exibe ele
                        $dado = $conteudo[$pp];
                    }
                } else { // Se tiver variável exibe ela
                    $dado = $$pp;
                }

                $grid->abreColuna($item[1]);
                #p("{$label}:", "contratoLabel");
                p($dado, "contratoConteudo");
                $grid->fechaColuna();
            }
            $grid->fechaGrid();

            if ($contAdt <= $total) {
                hr("hrComissao");
            } else {
                br();
                $div = new Div("divEdita1");
                $div->abre();

                # Editar
                $div = new Div("divEdita2");
                $div->abre();

                # Editar
                $botaoEditar = new Link("Editar", "cadastroComissao.php");
                $botaoEditar->set_class('tiny button secondary');
                $botaoEditar->set_title('Editar comissão');
                $botaoEditar->show();

                $div->fecha();
                $div->fecha();
            }
        }

        # Exibe mensagem de inclusão de membros
        if ($total == 0) {
            p("Nenhum servidor designado.", "center");

            $div = new Div("divEdita1");
            $div->abre();

            # Editar
            $div = new Div("divEdita2");
            $div->abre();

            # Editar
            $botaoEditar = new Link("Incluir", "cadastroComissao.php?fase=editar");
            $botaoEditar->set_class('tiny button secondary');
            $botaoEditar->set_title('Editar comissão');
            $botaoEditar->show();

            $div->fecha();
            $div->fecha();
        }

        $painel->fecha();
    }

    ##############################################################

    public function exibeProcesso($idContrato = null)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $contrato = new Contrato();
        $conteudo = $contrato->get_dados($idContrato);

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
        $painel = new Callout();
        $painel->abre();

        tituloTable("Processos:");
        br();

        if (empty($processo)) {
            br();
            P("Nenhum processo cadastrado", "center", "f14");
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

    public function exibeMenuDocumentos($idContrato = null)
    {

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
        $menu         = new MenuGrafico(3);

        $botao = new BotaoGrafico();
        $botao->set_label('Legislação');
        $botao->set_url('#');
        $botao->set_imagem(PASTA_FIGURAS . 'legislacao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Legislação referente a comissão de fiscalização');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Formulário');
        $botao->set_url('#');
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

    public function getProcesso($idContrato = null)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        $contrato = new Contrato();
        $conteudo = $contrato->get_dados($idContrato);

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
        $painel = new Callout();
        $painel->abre();

        tituloTable("Processos:");
        br();

        if (empty($processo)) {
            br();
            P("Nenhum processo cadastrado", "center", "f14");
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

}
