<?php

class Aditivo {

    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idConcurso integer null O id do concurso
     */
    ##############################################################

    public function __construct() {
        
    }

    ##############################################################

    /*
     * Informa todos os dados de um único aditivo
     */

    public function getDados($idAditivo = null) {

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

    public function temAditivo($idContrato = null) {

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

    public function getAditivosContrato($idContrato = null) {

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
                 ORDER BY dtAssinatura";

        return $contratos->select($select);
    }

    ###########################################################
    /*
     * Informa a data de publicação mais a página ( se tiver) de um aditivo
     */

    public function getPublicacao($idAditivo = null) {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        # Monta o arquivo
        $arquivo = PASTA_ADITIVOS . $idAditivo . ".pdf";

        # Verifica se ele existe
        if (file_exists($arquivo)) {

            # Monta o link
            $link = new Link(null, $arquivo, "Exibe a Publicação");
            $link->set_imagem(PASTA_FIGURAS_GERAIS . "ver.png", 20, 20);
            $link->set_target("_blank");
            $link->show();
        } else {
            # Botão de Upload
            $botao = new BotaoGrafico();
            $botao->set_title('Faça upload do arquivo!');
            $botao->set_url("cadastroAditivo.php?fase=upload&id={$idAditivo}");
            $botao->set_imagem(PASTA_FIGURAS . 'upload.png', 20, 20);
            $botao->show();
        }

        # Publicação
        p(date_to_php($conteudo["dtPublicacao"]), "pAditivoPublicacao");

        if (!empty($conteudo["pgPublicacao"])) {
            p("pag: {$conteudo["pgPublicacao"]}", "pAditivoPag");
        }


        return;
    }

    ###########################################################

    public function getTipoNumerado($idAditivo = null) {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        # pega os dados do aditivo
        $conteudo = $this->getDados($idAditivo);

        # Pega o idContrato desse aditivo 
        $idContrato = $conteudo["idContrato"];

        # Pega todos os aditivos e apostilas desse contrato
        $aditivosContrato = $this->getAditivosContrato($idContrato);

        # Cria as variáveis para contabilização
        $aditivo = 0;
        $apostila = 0;

        # Percorre o array para contabilizar cada tipo
        foreach ($aditivosContrato as $item) {
            # Aumenta o contador e cria variável do tipo
            if ($item["tipo"] == 1) {
                $aditivo++;
                $tipoNumerado = "Aditivo {$aditivo}";
            } else {
                $apostila++;
                $tipoNumerado = "Apostila {$apostila}";
            }

            # Verifica se é desejado
            if ($idAditivo == $item["idAditivo"]) {
                $retorno = $tipoNumerado;
            }
        }
        return $retorno;
    }

    ###########################################################

    public function exibeTipoNumerado($idAditivo = null) {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        # Pega o tipo numerado
        if (!empty($this->getVinculado($idAditivo))) {
            echo $this->getTipoNumerado($idAditivo);
            if($this->getVinculado($idAditivo) == "contrato"){
                p("(Contrato)", "paditivoVinculado");
            }else{
                p("(" . $this->getTipoNumerado($this->getVinculado($idAditivo)) . ")", "paditivoVinculado");
            }
        } else {
            echo $this->getTipoNumerado($idAditivo);
        }
        return;
    }

    ###########################################################

    public function exibePeriodo($idAditivo = null) {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        if (!empty($conteudo["dtInicial"])) {

            $dtInicial = date_to_php($conteudo["dtInicial"]);
            $prazo = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];

            $tipo = null;
            $dtFinal = null;

            if ($tipoPrazo == 1) {
                $tipo = "Dias";
                $dtFinal = $this->getVigencia($idAditivo);
            } else {
                $tipo = "Meses";
                $dtFinal = $this->getVigencia($idAditivo);
            }
            $retorno = "{$dtInicial}<br/>{$prazo} {$tipo}<br/>$dtFinal";
        } else {
            $retorno = null;
        }

        return $retorno;
    }

    ###########################################################

    public function getValor($idAditivo = null) {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        return [$conteudo["valor"], $conteudo["valorSinal"]];
    }

    ###########################################################

    public function getVinculado($idAditivo = null) {
        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        return $conteudo["vinculado"];
    }

    ###########################################################

    public function exibeGarantia($idAditivo = null) {
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
                $garantia = "R$ " . formataMoeda($garantia) . "<br/>({$conteudo['garantia']}%)";
            } else {
                $garantia = "---";
            }
        } else {
            $garantia = "---";
        }

        return $garantia;
    }

    #####################################################################################

    public function getVigencia($idAditivo) {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        $conteudo = $this->getDados($idAditivo);

        # Verifica se a data inicial foi preenchida
        if (!empty($conteudo["dtInicial"])) {

            $dtInicial = date_to_php($conteudo["dtInicial"]);
            $prazo = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];

            $tipo = null;
            $dtFinal = null;

            if ($tipoPrazo == 1) {
                $dtFinal = addDias($dtInicial, $prazo);
            } else {
                $dtFinal = addMeses($dtInicial, $prazo);
                $dtFinal = addDias($dtFinal, -1, false);      // retira 1 dia
            }
        } else {
            $dtFinal = null;
        }


        return $dtFinal;
    }

    ##############################################################

    /*
     * Informa todos os dados do último aditivo com Data (para calculo de vigencia)
     */

    public function getDadosUltimoAditivocomData($idContrato = null) {

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
                      AND dtInicial IS NOT NULL 
                 ORDER BY dtAssinatura desc LIMIT 1";

        return $contratos->select($select, false);
    }

##############################################################

    /*
     * Informa a data inicial de um aditivo considerando a data anterior
     */

    public function getDataInicialNovoAditivo($idContrato = null) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (empty($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        if ($this->temAditivo($idContrato)) {
            $dados = $this->getDadosUltimoAditivocomData($idContrato);
            $vigencia = $this->getVigencia($dados["idAditivo"]);
            $return = addDias($vigencia, 1, false);
        } else {
            $contrato = new Contrato();
            $vigencia = $contrato->getVigencia($idContrato);
            $return = addDias($vigencia, 1, false);
        }

        return $return;
    }

##############################################################

    /*
     * Informa todos os dados de um aditivo vinculado ao aditivo informado
     * 
     */

    public function getAditivosVinculados($idAditivo = null) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        # monta o select
        $select = "SELECT *
                     FROM tbaditivo
                    WHERE vinculado = {$idAditivo}
                 ORDER BY dtAssinatura";

        return $contratos->select($select);
    }

###########################################################

    public function exibeValor($idAditivo = null) {

        # Verifica se foi informado o id
        if (vazio($idAditivo)) {
            alert("É necessário informar o id do Aditivo.");
            return;
        }

        # Pega os dados
        $conteudo = $this->getDados($idAditivo);

        # Prepara a variável do Total
        $ValorTotal = 0;

        # Verifica se tem vinculado
        $aditivosVinculados = $this->getAditivosVinculados($idAditivo);

        # Valor
        if (empty($conteudo["valor"])) {
            p("----", "p#pvalorNulo");
        } else {
            if ($conteudo["valorSinal"]) {
                if (count($aditivosVinculados) > 0) {
                    p("R$ -" . formataMoeda($conteudo['valor']), "paditivoVinculadoValorNegativo");
                } else {
                    p("R$ -" . formataMoeda($conteudo['valor']), "pvalorNegativo");
                }
                $ValorTotal -= $conteudo['valor'];
            } else {
                if (count($aditivosVinculados) > 0) {
                    p("R$ " . formataMoeda($conteudo['valor']), "paditivoVinculadoValorPositivo");
                } else {
                    p("R$ " . formataMoeda($conteudo['valor']), "pvalorPositivo");
                }
                $ValorTotal += $conteudo['valor'];
            }
        }

        # Percorre os vinculados
        if (count($aditivosVinculados) > 0) {
            foreach ($aditivosVinculados as $item) {
                if (!empty($item["valor"])) {

                    if ($item["valorSinal"]) {
                        p("R$ -" . formataMoeda($item['valor']), "paditivoVinculadoValorNegativo");
                        $ValorTotal -= $item['valor'];
                    } else {
                        p("R$ " . formataMoeda($item['valor']), "paditivoVinculadoValorPositivo");
                        $ValorTotal += $item['valor'];
                    }
                }
            }
            hr("hrComissao");
            if($ValorTotal >=0){
                p("R$ " . formataMoeda($ValorTotal), "pvalorPositivo");
            }else{
                p("R$ " . formataMoeda($ValorTotal), "pvalorNegativo");
            }
        }
        
        # Exibe o vinculo (caso exista)
        if (!empty($this->getVinculado($idAditivo))) {
            if($this->getVinculado($idAditivo) == "contrato"){
                p("(Contrato)", "paditivoVinculado");
            }else{
                p("(" . $this->getTipoNumerado($this->getVinculado($idAditivo)) . ")", "paditivoVinculado");
            }
        }        
    }
    
    ##########################################################
}
