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

    public function get_dados($idAditivo = null)
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

###########################################################

    public function get_publicacao($idAditivo = null)
    {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->get_dados($idAditivo);

        # Publicação
        $dtPublicacao = $conteudo["dtPublicacao"];
        $publicacao   = date_to_php($dtPublicacao);
        if (!empty($conteudo["pgPublicacao"])) {
            $publicacao .= "<br/>pag: {$conteudo["pgPublicacao"]}";
        }

        return $publicacao;
    }

    ###########################################################

    function get_periodo($idAditivo = null)
    {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->get_dados($idAditivo);

        $dtInicial = date_to_php($conteudo["dtInicial"]);
        $prazo     = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];

        $tipo    = null;
        $dtFinal = null;

        if ($tipoPrazo == 1) {
            $tipo    = "Dias";
            $dtFinal = addDias($dtInicial, $prazo);
        } else {
            $tipo    = "Meses";
            $dtFinal = addMeses($dtInicial, $prazo);
        }
        $retorno = "{$dtInicial}<br/>{$prazo} {$tipo}<br/>$dtFinal";

        return $retorno;
    }

    ###########################################################

    function get_valor($idAditivo = null)
    {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->get_dados($idAditivo);

        # Valor
        if (!empty($conteudo["valor"])) {
            $valor = "R$ " . formataMoeda($conteudo['valor']);
        } else {
            $valor = "---";
        }

        return $valor;
    }

    ###########################################################

    function get_garantia($idAditivo = null)
    {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->get_dados($idAditivo);

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

    public function listaAditivos($idContrato)
    {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT objeto,
                          idAditivo,
                          dtAssinatura,
                          idAditivo,
                          idAditivo
                     FROM tbaditivo
                    WHERE idContrato = {$idContrato}
                 ORDER BY dtInicial";

        $row = $contratos->select($select);

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo("Aditivos");
        $tabela->set_label(array("Objetivo","Publicação", "Assinatura","Duração","Garantia"));
        $tabela->set_align(array("left", "center"));
        $tabela->set_width(array(30,15,15,15,25));
        $tabela->set_classe(array(null,"Aditivo",null,"Aditivo","Aditivo"));
        $tabela->set_metodo(array(null,"get_publicacao",null,"get_periodo","get_garantia"));
        $tabela->set_funcao(array(null,null,"date_to_php"));
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

#####################################################################################
}
