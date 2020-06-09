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
                    
        # Cobntator de aditivos
        $contAdt = 1;
                    
        foreach($contratos->select($select) as $conteudo){

            $painel = new Callout("success");
            $painel->abre();

            # Pega os valores
            $assinatura = date_to_php($conteudo["dtAssinatura"]);

            # Prazo
            $prazo = $conteudo["prazo"];
            $tipoPrazo = $conteudo["tipoPrazo"];
            $inicio = date_to_php($conteudo["dtInicial"]);

            if ($tipoPrazo == 1) {
                $prazo2 = " dias";
                $vigencia = addDias($inicio, $prazo);
                $prazo .= $prazo2;
                $vigencia .= " ({$prazo})";
            } elseif ($tipoPrazo == 2) {
                $prazo2 = " meses";
                $vigencia = addMeses($inicio, $prazo);
                $prazo .= $prazo2;
                $vigencia .= " ({$prazo})";
            }

            # Publicação
            $dtPublicacao = $conteudo["dtPublicacao"];
            $publicacao = date_to_php($dtPublicacao);
            if (!empty($conteudo["pgPublicacao"])) {
                $publicacao .= " pag: {$conteudo["pgPublicacao"]}";
            }

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
                ["assinatura", 8, "Assinatura"],
                ["inicio", 4, "Início"],
                ["vigencia", 8, "Vigência"],
                ["valor", 4],
                ["garantia", 8],
            ];

            # Rotina de exibição
            $grid = new Grid();
            $grid->abreColuna(12);

            titulo("Aditivo {$contAdt}");
            br();
            
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

            $div = new Div("divEditaNota");
            $div->abre();
            $link = new Link("Editar", "cadastroAditivo.php?fase=editar&id={$idContrato}");
            $link->set_id("editaNota");
            $link->show();
            $div->fecha();

            $painel->fecha();
        }
    }

    ###########################################################
}
