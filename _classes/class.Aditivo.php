<?php

class Aditivo
{

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idConcurso integer null O id do concurso
     */
##############################################################

    public function __construct()
    {
        
    }

##############################################################

    /*
     * Informa todos os dados de um único aditivo
     */

    public function getDados($idAditivo = null)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbaditivo
                    WHERE idAditivo = ' . $idAditivo;

        return $contratos->select($select, false);
    }

##############################################################

    /*
     * Informa se o contrato tem  ou não aditivo
     */

    public function temAditivo($idContrato = null)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbaditivo
                    WHERE idContrato = ' . $idContrato;

        if ($contratos->count($select) > 0) {
            return true;
        } else {
            return false;
        }
    }

##############################################################

    /*
     * retorna um array com os dados de todos os aditivos de um contrato
     */

    public function getAditivosContrato($idContrato = null)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # monta o select
        $select = "SELECT *
                     FROM tbaditivo
                    WHERE idContrato = {$idContrato}
                 ORDER BY dtInicial";

        return $contratos->select($select);
    }

###########################################################
    /*
     * Informa a data de publicação mais a página ( se tiver) de um aditivo
     */

    public function getPublicacao($idAditivo = null)
    {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        # Publicação
        $dtPublicacao = $conteudo["dtPublicacao"];
        $publicacao   = date_to_php($dtPublicacao);
        if (!empty($conteudo["pgPublicacao"])) {
            $publicacao .= "<br/>pag: {$conteudo["pgPublicacao"]}";
        }

        return $publicacao;
    }

    ###########################################################

    function getPeriodo($idAditivo = null)
    {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        if (!empty($conteudo["dtInicial"])) {

            $dtInicial = date_to_php($conteudo["dtInicial"]);
            $prazo     = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];

            $tipo    = null;
            $dtFinal = null;

            if ($tipoPrazo == 1) {
                $tipo    = "Dias";
                $dtFinal = $this->getVigencia($idAditivo);
            } else {
                $tipo    = "Meses";
                $dtFinal = $this->getVigencia($idAditivo);
            }
            $retorno = "{$dtInicial}<br/>{$prazo} {$tipo}<br/>$dtFinal";
        } else {
            $retorno = null;
        }

        return $retorno;
    }

    ###########################################################

    function getValor($idAditivo = null)
    {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        # Valor
        if (!empty($conteudo["valor"])) {
            $valor = "R$ " . formataMoeda($conteudo['valor']);
        } else {
            $valor = "---";
        }

        return $valor;
    }

    ###########################################################

    function getGarantia($idAditivo = null)
    {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        # Garantia
        if (!empty($conteudo["valor"])) {
            if (!empty($conteudo["garantia"])) {
                $garantia = $conteudo['valor'] * ($conteudo['garantia'] / 100);
                $garantia = "R$ " . formataMoeda($garantia) . " ({$conteudo['garantia']}%)";
            } else {
                $garantia = "---";
            }
        } else {
            $garantia = "---";
        }

        return $garantia;
    }

    #####################################################################################
    /*
     * Lista os aditivos de um contraro
     */

    public function listaAditivos($idContrato)
    {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # monta o select
        $select = "SELECT objeto,
                          idAditivo,
                          dtAssinatura,
                          idAditivo,
                          idAditivo
                     FROM tbaditivo
                    WHERE idContrato = {$idContrato}
                 ORDER BY dtAssinatura";

        $row = $contratos->select($select);

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo("Termo(s) Aditivo(s)");
        $tabela->set_label(array("Objetivo", "Publicação", "Assinatura", "Duração", "Garantia"));
        $tabela->set_align(array("left", "center"));
        $tabela->set_width(array(30, 15, 15, 15, 25));
        $tabela->set_classe(array(null, "Aditivo", null, "Aditivo", "Aditivo"));
        $tabela->set_metodo(array(null, "getPublicacao", null, "getPeriodo", "getGarantia"));
        $tabela->set_funcao(array(null, null, "date_to_php"));
        $tabela->set_numeroOrdem(true);
        $tabela->set_conteudo($row);
        #$tabela->set_formatacaoCondicional($formatacaoCondicional);
        $tabela->show();

        # Editar
        $div = new Div("divEdita1Comissao");
        $div->abre();

        $div = new Div("divEdita2");
        $div->abre();

        # Editar
        $botaoEditar = new Link("Editar", "cadastroAditivo.php");
        $botaoEditar->set_class('tiny button secondary');
        $botaoEditar->set_title('Editar aditivo');
        $botaoEditar->show();

        $div->fecha();
        $div->fecha();
    }

    ###########################################################

    public function getVigencia($idAditivo)
    {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        $dtInicial = date_to_php($conteudo["dtInicial"]);
        $prazo     = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];

        $tipo    = null;
        $dtFinal = null;

        if ($tipoPrazo == 1) {
            $dtFinal = addDias($dtInicial, $prazo);
        } else {
            $dtFinal = addMeses($dtInicial, $prazo);
            $dtFinal = addDias($dtFinal, -1, false);      // retira 1 dia
        }

        return $dtFinal;
    }

    ##############################################################

    /*
     * Informa todos os dados do último aditivo
     */

    public function getDadosUltimoAditivo($idContrato = null)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # monta o select
        $select = "SELECT *
                     FROM tbaditivo
                    WHERE idContrato = {$idContrato}
                 ORDER BY dtInicial desc LIMIT 1";

        return $contratos->select($select, false);
    }

##############################################################

    /*
     * Informa a data inicial de um aditivo considerando a data anterior
     */

    public function getDataInicialNovoAditivo($idContrato = null)
    {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        if ($this->temAditivo($idContrato)) {
            $dados    = $this->getDadosUltimoAditivo($idContrato);
            $vigencia = $this->getVigencia($dados["idAditivo"]);
            $return   = addDias($vigencia, 1, false);
        } else {
            $contrato = new Contrato();
            $vigencia = $contrato->getVigencia($idContrato);
            $return   = addDias($vigencia, 1, false);
        }

        return $return;
    }

#####################################################################################
}
