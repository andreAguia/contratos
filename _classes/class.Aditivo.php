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
    function exibeAditivoContrato($idContrato = null)
    {

        # Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        $select = "SELECT * 
                     FROM tbaditivo
                    WHERE idContrato = {$idContrato}
                 ORDER BY dtInicial";

        $total = $contratos->count($select);

        # Cobntator de aditivos
        $contAdt = 1;

        $painel = new Callout("success");
        $painel->abre();

        titulo("Aditivos");       

        foreach ($contratos->select($select) as $conteudo) {


            # Pega os valores
            $assinatura = date_to_php($conteudo["dtAssinatura"]);
            $idAditivo = $conteudo["idAditivo"];

            # Prazo
            $prazo = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];
            $inicio = date_to_php($conteudo["dtInicial"]);

            if ($tipoPrazo == 1) {
                $prazo2 = " d";
                $vigencia = addDias($inicio, $prazo);
                $prazo .= $prazo2;
                $vigencia .= " ({$prazo})";
            } elseif ($tipoPrazo == 2) {
                $prazo2 = " m";
                $vigencia = addMeses($inicio, $prazo);
                $prazo .= $prazo2;
                $vigencia .= " ({$prazo})";
            }

            # Publicação
            $publicacao = $this->get_publicacao($idAditivo);

            # Valor
            if (!empty($conteudo["valor"])) {
                $valor = "R$ " . formataMoeda($conteudo['valor']);
            }

            # Garantia
            if (!empty($conteudo["valor"])) {
                if (!empty($conteudo["garantia"])) {
                    $garantia = $conteudo['valor'] * ($conteudo['garantia'] / 100);
                    $garantia = "R$ " . formataMoeda($garantia) . " ({$conteudo['garantia']}%)";
                }
            }

            $dtAssinatura = $conteudo["dtAssinatura"];

            # Monta o array de exibição
            $dados = [
                ["objeto", 12],
                ["publicacao", 4, "Publicação DOERJ"],
                ["assinatura", 2, "Assinatura"],
                ["inicio", 2, "Início"],
                ["vigencia", 4, "Vigência"],
                ["valor", 4],
                ["garantia", 8],
            ];
            
            
            # Exibe o número de aditivos
            p($contAdt,"pNumAdt");

            # Rotina de exibição
            $grid = new Grid();

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
                if (empty($$pp)) {                      // Se não tem variável com esse nome
                    if (empty($conteudo[$pp])) {        // Se não tiver no array de conteúdo do bd
                        $dado = "---";                 // Exibe tracinho
                    } else {                              // Se tiver conteúdo do bd exibe ele
                        $dado = $conteudo[$pp];
                    }
                } else {                                  // Se tiver variável exibe ela
                    $dado = $$pp;
                }

                $grid->abreColuna($item[1]);
                p("{$label}:", "contratoLabel");
                p($dado, "contratoConteudo");
                $grid->fechaColuna();
            }
            $grid->fechaGrid();

            if ($contAdt < $total) {
                hr("hrComissao");
                
            } else {
                br();
                $div = new Div("divEdita1");
                $div->abre();

                # Editar
                $div = new Div("divEdita2");
                $div->abre();

                # Editar
                $botaoEditar = new Link("Editar", "cadastroAditivo.php");
                $botaoEditar->set_class('tiny button secondary');
                $botaoEditar->set_title('Editar comissão');
                $botaoEditar->show();

                $div->fecha();

                $div->fecha();
            }
            
            # incrementa contador
            $contAdt++;
        }

        $painel->fecha();
    }

    ##############################################################
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
        $publicacao = date_to_php($dtPublicacao);
        if (!empty($conteudo["pgPublicacao"])) {
            $publicacao .= " pag: {$conteudo["pgPublicacao"]}";
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
        $prazo = $conteudo["prazo"];
        $tipoPrazo = $conteudo["tipoPrazo"];

        $tipo = null;
        $dtFinal = null;

        if ($tipoPrazo == 1) {
            $tipo = "Dias";
            $dtFinal = addDias($dtInicial, $prazo);
        } else {
            $tipo = "Meses";
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
        }else{
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
            }else{
                $garantia = "---";
            }
        }else{
            $garantia = "---";
        }

        return $garantia;
    }

    #############################################################
}
