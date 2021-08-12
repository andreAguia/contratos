<?php

/**
 * Cria um alerta
 *
 * @author alat
 */
class AlertaContrato {
    ##############################################################

    public function __construct($idContrato, $painel = false) {
        # Classes usadas
        $contratos = new Contratos();
        $contrato = new Contrato();

        # Inicia a variável de erro
        $erro = array();

        ################################################################################
        # Verifica se tem menos de 3 membros da comissão de fiscalização
        $select = "SELECT idComissao
                     FROM tbcomissao 
                    WHERE idContrato = {$idContrato} 
                      AND dtPublicacaoSaida IS NULL";

        $membros = $contratos->count($select);

        # Verifica se é menor que 3 (Contrário a legislação)
        if (($membros < 3) AND ($membros > 0)) {
            $erro[] = "De acordo com a legislação vigente, a comissão de fiscalização deverá ter, pelo menos, 3 (TRÊS) membros ativos!";
        }

        # Verifica se tem comissão de fiscalização
        if ($membros == 0) {
            $erro[] = "É necessário designar uma comissão de fiscalização para esse contrato !!";
        }

        ################################################################################
        # Verifica se tem 1 presidente
        $select = "SELECT idComissao
                     FROM tbcomissao 
                    WHERE idContrato = {$idContrato} 
                      AND dtPublicacaoSaida IS NULL
                      AND tipo = 1";

        $presidente = $contratos->count($select);

        if ($membros > 0) {
            # Verifica se tem 1 presidente
            if ($presidente > 1) {
                $erro[] = "A comissão deve ter SOMENTE 1 presidente!";
            }

            if (($presidente < 1) OR (empty($presidente))) {
                $erro[] = "A comissão deve ter, ao menos, 1 presidente!";
            }
        }

        ################################################################################
        # Verifica se a data da assinatura combina com a do número do contrato
        $select = "SELECT numero, 
                          YEAR(dtAssinatura)
                     FROM tbcontrato 
                    WHERE idContrato = {$idContrato}";

        $conteudo = $contratos->select($select, false);

        $numero = $conteudo[0];

        # Verifica se tem / em numero
        $posicao = mb_strpos($numero, "/");

        if ($posicao !== false) {
            if (!empty($conteudo[1])) {
                $partes = explode("/", $numero);

                if ($conteudo[1] <> $partes[1]) {
                    $erro[] = "O ano de assinatura ({$conteudo[1]}) está diferente do ano do número do contrato ({$partes[1]})!! Favor alterar!";
                }
            }
        }

        ################################################################################
        # Verifica se o contrato já chegou a 60 meses
        $tempoTotal = $contrato->getTempoTotal($idContrato);

        if ($tempoTotal["meses"] >= 60) {
            $erro[] = "O contrato já tem 60 meses de duração e NÃO poderá mais ser prorrogado !!";
        }

        ################################################################################
        # Verifica se o contrato está ativo e a data de vigência já passou
        $vigencia = $contrato->getVigencia($idContrato);
        $status = $contrato->getStatus($idContrato);

        if (!empty($vigencia)) {
            if (jaPassou($vigencia) AND $status == "Ativo") {
                $erro[] = "A vigência do contrato já passou e ele ainda consta como Ativo !! Algo está errado!!";
            }
        }

        ################################################################################
        # Pega os dados do contrato para os alertas a seguir
        $dados = $contrato->getDados($idContrato);

        #####################        
        # Verifica se a data inicial do contrato é igual ou superior a data de publicação        
        # analisa as datas
        if (!empty($dados["dtInicial"])) {

            $dtPublicacao = date_to_php($dados["dtPublicacao"]);
            $dtInicial = date_to_php($dados["dtInicial"]);

            if ($dtInicial <> $dtPublicacao) {
                if (dataMaior($dtInicial, $dtPublicacao) == $dtPublicacao) {
                    $erro[] = "A data de Publicação NÃO pode ser posterior a data inicial!!";
                }
            }
        }

        #####################
        # Verifica nos contratos de receita se falta 1 mês para o reajuste
        # Verifica se é receita
        $modalidade = new Modalidade();        
        if ($modalidade->getTipo($dados["idModalidade"]) == "Receita") {
            
            # Verifica se foi informada a data da proposta
            if (!empty($dados["dtProposta"])) {
                # Pega a data da proposta
                $dtProposta = date_to_php($dados["dtProposta"]);

                # Transporta para o ano atual
                while (year($dtProposta) < date("Y")):
                    # Adiciona 1 ano
                    $dtProposta = addAnos($dtProposta, 1);
                endwhile;

                # Verifica se estamos a menos de 30 dias do reajuste
                $diferenca = abs(dataDif($dtProposta));
                
                if (dataDif($dtProposta) < 1 AND $diferenca < 30) {

                    if ($diferenca > 1) {
                        $erro[] = "Faltam {$diferenca} dias para a data de reajuste!! ({$dtProposta})";
                    }
                    
                    if ($diferenca == 1) {
                        $erro[] = "Falta 1 dia para a data de reajuste!! ({$dtProposta})";
                    }
                    
                    if ($diferenca == 0) {
                        $erro[] = "HOJE é o dia do reajuste!! ({$dtProposta})";
                    }
                }
            }
        }

        ################################################################################
        # Exibe o erro (se tiver)
        if (count($erro) == 0) {
            return;
        } else {

            if ($painel) {

                $painel = new Callout("alert");
                $painel->abre();

                $grid = new Grid();
                $grid->abreColuna(1);

                $figura = new Imagem(PASTA_FIGURAS_GERAIS . 'aviso.png', 'Alerta', 50, 50);
                $figura->show();

                $grid->fechaColuna();
                $grid->abreColuna(11);

                p("ATENÇÂO !!", "palerta");

                foreach ($erro as $item) {
                    p($item, "palerta");
                }

                $grid->fechaColuna();
                $grid->fechaGrid();
                $painel->fecha();
            } else {
                foreach ($erro as $item) {
                    p($item, "palertaSpainel");
                }
            }
        }
    }

    ##############################################################
}
