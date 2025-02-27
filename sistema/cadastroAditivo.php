<?php

/**
 * Cadastro de Campus
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9, 10]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contrato = new Contrato();
    $contratos = new Contratos();
    $pessoal = new Pessoal();
    $comissao = new Comissao();
    $aditivo = new Aditivo();
    $empresa = new Empresa();
    $situacao = new Situacao();
    $modalidade = new Modalidade();

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # pega o contrato
    $idContrato = soNumeros(get_session('sessionContrato'));

    # Começa uma nova página
    $page = new Page();
    if ($fase == "uploadPublicacao" OR $fase == "uploadAditivo") {
        $page->set_ready('$(document).ready(function(){
                                $("form input").change(function(){
                                    $("form p").text(this.files.length + " arquivo(s) selecionado");
                                });
                            });');
    }
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Exibe os dados do Servidor
    if ($fase == "editar") {
        $objeto->set_rotinaExtra("get_DadosContrato");
        $objeto->set_rotinaExtraParametro($idContrato);
    }

    # Nome do Modelo
    if (empty($id)) {
        $objeto->set_nome('Aditivos');
    } else {
        $objeto->set_nome($aditivo->getTipoNumerado($id));
    }

    # Botão de voltar da lista
    $objeto->set_voltarLista('cadastroContrato.php');

    # select da lista
    $objeto->set_selectLista("SELECT idAditivo,
                                     idAditivo,
                                     idAditivo,
                                     idAditivo,
                                     idAditivo,
                                     dtAssinatura,
                                     idAditivo,
                                     idAditivo,
                                     idAditivo
                                FROM tbaditivo
                               WHERE idContrato = {$idContrato}
                            ORDER BY dtAssinatura");

    # select do edita
    $objeto->set_selectEdita('SELECT tipo,
                                     vinculado,
                                     objeto,
                                     dtAssinatura,
                                     dtPublicacao,
                                     pgPublicacao,
                                     valor,
                                     valorSinal,
                                     garantia,                                   
                                     dtInicial,
                                     prazo,
                                     tipoPrazo,
                                     obs,
                                     idContrato,
                                     idAditivo
                                FROM tbaditivo
                              WHERE idAditivo = ' . $id);

    # Caminhos
    if (Verifica::acesso($idUsuario, [1, 9])) {
        $objeto->set_linkEditar('?fase=editar');
        $objeto->set_linkExcluir('?fase=excluir');
    }
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    $objeto->set_exibeTempoPesquisa(false);

    # Parametros da tabela
    $objeto->set_label(["id", "Tipo", "Objeto", "Publicação", "Aditivo", "Assinatura", "Duração", "Garantia", "Valor"]);
    $objeto->set_align(["center", "center", "left", "center", "center", "center", "center", "center", "right"]);
    $objeto->set_width([3, 15, 22, 10, 10, 10, 10, 10, 10]);

    $objeto->set_classe([null, "Aditivo", "Aditivo", "Aditivo", "Aditivo", "Contrato", "Aditivo", "Aditivo", "Aditivo"]);
    $objeto->set_metodo([null, "exibeTipoNumerado", "exibeObjeto", "exibePublicacao", "exibeAditivo", "exibeAssinaturaEReitor", "exibePeriodo", "exibeGarantia", "exibeValor"]);

    # Classe do banco de dados
    $objeto->set_classBd('Contratos');

    # Nome da tabela
    $objeto->set_tabela('tbaditivo');

    # Nome do campo id
    $objeto->set_idCampo('idAditivo');

    # Dados da combo vinculado
    $select = "SELECT idAditivo, idAditivo
                 FROM tbaditivo                 
                WHERE idContrato = {$idContrato}
                AND vinculado IS NULL";

    if (!empty($id)) {
        $select .= " AND idAditivo <> {$id}";
    }

    $select .= " ORDER BY dtAssinatura";
    $row = $contratos->select($select);

    # Inicia o array tratado
    $vinculado[] = [null, "---"];
    $vinculado[] = ["contrato", "Contrato " . $contrato->getNumero($idContrato)];

    # Trabalha o array
    foreach ($row as $item) {
        $vinculado[] = [$item[0], $aditivo->getTipoNumerado($item[0])];
    }

    # Dados da combo tipo
    $tipo = array(
        array(null, null),
        array(1, "Dias"),
        array(2, "Meses")
    );

    # Calcula a data inicial quando for inclusão
    $dataInclusao = $aditivo->getDataInicialNovoAditivo($idContrato);
    if (!empty($dataInclusao)) {
        $dataInclusao = date_to_bd($dataInclusao);
    }

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'tipo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'required' => true,
            'autofocus' => true,
            'array' => array([1, "Aditivo"], [2, "Apostila"]),
            'col' => 3,
            'size' => 15),
        array(
            'linha' => 1,
            'nome' => 'vinculado',
            'label' => 'Vinculado:',
            'tipo' => 'combo',
            'array' => $vinculado,
            'col' => 5,
            'size' => 15),
        array(
            'linha' => 2,
            'nome' => 'objeto',
            'label' => 'Objeto:',
            'tipo' => 'texto',
            'col' => 12,
            'size' => 250),
        array(
            'linha' => 3,
            'nome' => 'dtAssinatura',
            'label' => 'Assinatura:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array(
            'linha' => 3,
            'nome' => 'dtPublicacao',
            'label' => 'Publicação:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15),
        array(
            'linha' => 3,
            'nome' => 'pgPublicacao',
            'label' => 'Pag:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 10),
        array(
            'linha' => 4,
            'nome' => 'valor',
            'label' => 'Valor: (se houver)',
            'tipo' => 'moeda',
            'col' => 3,
            'size' => 15),
        array(
            'linha' => 4,
            'nome' => 'valorSinal',
            'label' => 'Negativo?',
            'tipo' => 'simnao',
            'col' => 3,
            'size' => 3),
        array(
            'linha' => 4,
            'nome' => 'garantia',
            'label' => 'Garantia: (se houver)',
            'tipo' => 'percentagem',
            'col' => 2,
            'size' => 5),
        array(
            'linha' => 5,
            'nome' => 'dtInicial',
            'label' => 'Data Inicial:',
            'tipo' => 'date',
            'col' => 3,
            'padrao' => $dataInclusao,
            'size' => 15),
        array(
            'linha' => 5,
            'nome' => 'prazo',
            'label' => 'Prazo:',
            'tipo' => 'numero',
            'col' => 2,
            'size' => 15),
        array(
            'linha' => 5,
            'nome' => 'tipoPrazo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'array' => $tipo,
            'col' => 2,
            'size' => 15),
        array(
            'linha' => 4,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 3)),
        array(
            "linha" => 5,
            "nome" => "idContrato",
            "label" => "idContrato:",
            'tipo' => 'hidden',
            'padrao' => $idContrato,
            "col" => 3,
            "size" => 11)
    ));

    $objeto->set_botaoVoltarLista(false);
    $objeto->set_botaoIncluir(false);

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    # Botão de Upload
    if (Verifica::acesso($idUsuario, [1, 9])) {
        if (!empty($id)) {

            /*
             * Upload Publicacao
             */
            $pasta = PASTA_ADITIVOS_PUBLICACAO;
            $nome = "Publicação";
            $tabela = "tbaditivo";
            $extensoes = ["pdf"];

            # Botão de Upload Publicação
            $botao1 = new Button("Upload {$nome}");
            $botao1->set_url("cadastroAditivoPublicacaoUpload.php?fase=upload&id={$id}");
            $botao1->set_title("Faz o Upload da {$nome}");
            $botao1->set_target("_blank");

            /*
             * Upload Aditivo
             */
            $pasta = PASTA_ADITIVOS;
            $nome = "Aditivo";
            $tabela = "tbaditivo";
            $extensoes = ["pdf"];

            # Botão de Upload Aditivos
            $botao2 = new Button("Upload {$nome}");
            $botao2->set_url("cadastroAditivoUpload.php?fase=upload&id={$id}");
            $botao2->set_title("Faz o Upload do {$nome}");
            $botao2->set_target("_blank");

            $objeto->set_botaoEditarExtra([$botao1, $botao2]);
        }
    }

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Carrega os dados com contrado editado
            $conteudo = $contrato->getDados($idContrato);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "cadastroContrato.php");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar, "left");

            # Exibe o controle de pgto ou de aluguel dependendo do contrato
            if ($modalidade->getTipo($conteudo["idModalidade"]) == "Despesa") {
                $botaoControle = new Button("Controle de Saldo", "cadastroPagamento.php");
                $botaoControle->set_title("Acessa a rotina de controle de saldo para contratos de despesa");
                $menu1->add_link($botaoControle, "right");

//                    # Cronograma Físico-Financeiro
//                    if (empty($contrato->getNaturezaDespesa($idContrato))) {
//                        $botaoCronograma = new Button("Cronograma Físico-Financeiro");
//                        $botaoCronograma->set_title("te acesso ao Cronograma Físico-Financeiro");
//                        $botaoCronograma->set_onClick("alert('É necessário cadastrar a naturaza da despesa do contrato para emitir o Cronograma Físico-Financeiro')");
//                        $menu1->add_link($botaoCronograma, "right");
//                    } else {
//                        $botaoCronograma = new Button("Cronograma Físico-Financeiro", "../relatorios/contratos.cronograma.fisicoFinanceiro.php");
//                        $botaoCronograma->set_title("te acesso ao Cronograma Físico-Financeiro");
//                        $botaoCronograma->set_target("_blank");
//                        #$botaoCronograma->set_class('button secondary');
//                        $menu1->add_link($botaoCronograma, "right");
//                    }
            } else {
                $botaoControle = new Button("Controle de Aluguel", "cadastroReceita.php");
                $botaoControle->set_title("Acessa a rotina de controle de aluguel para contratos de receita");
                $menu1->add_link($botaoControle, "right");
            }

            if (Verifica::acesso($idUsuario, [1, 9])) {

                # Incluir
                $botaoInserir = new Button("Incluir Aditivo", "?fase=editar");
                $botaoInserir->set_title("Inclui um novo aditivo");
                $menu1->add_link($botaoInserir, "right");

                # Folha de Rosto
                $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
                $botaoRel = new Button();
                $botaoRel->set_title("Folha de Rosto");
                $botaoRel->set_url("../relatorios/contratos.folhaRosto.php");
                $botaoRel->set_target("_blank");
                $botaoRel->set_imagem($imagem);
                $menu1->add_link($botaoRel, "right");
            }

            $menu1->show();

            ##########                       
            # Exibe alerta de Acompanhamento Especial
            if ($conteudo["especial"]) {
                titulo2("Contrato com Acompanhamento Especial", "Contratos com Acompanhamento Especial SEMPRE aparecerão no início de uma lista, independente de qualquer outra característica.");
            }

            ##########
            # Exibe os dados do contrado
            get_DadosContrato($idContrato);

            $grid->fechaColuna();
            $grid->abreColuna(12);

            # Exibe alertas (se tiver)
            $alerta = new AlertaContrato($idContrato, true);

            $grid->fechaColuna();

            # Define as colunas a serem exibidas
            if (Verifica::acesso($idUsuario, [1, 9])) {
                $colunas = [3, 3, 4, 4, 6];
            } else {
                $colunas = [3, 3, 6, 4, 8];
            }

            # Exibe o Valor / Setor Requisitante / Situação
            if ($modalidade->getTipo($conteudo["idModalidade"]) == "Despesa") {

                # Exibe o valor
                $grid->abreColuna($colunas[0]);
                $contrato->exibeValorTotalPainel($idContrato);
                $grid->fechaColuna();

                # Exibe o Setor Requisitante
                $grid->abreColuna($colunas[1]);
                $contrato->exibeRequisitante($idContrato, $idUsuario);
                $grid->fechaColuna();

                # Exibe a situação atual
                $grid->abreColuna($colunas[2]);
                $situacao->exibeSituacaoAtual($idContrato, $idUsuario);
                $grid->fechaColuna();
            } else {
                # Exibe o Setor Requisitante
                $grid->abreColuna($colunas[3]);
                $contrato->exibeRequisitante($idContrato, $idUsuario);
                $grid->fechaColuna();

                # Exibe a situação atual
                $grid->abreColuna($colunas[4]);
                $situacao->exibeSituacaoAtual($idContrato, $idUsuario);
                $grid->fechaColuna();
            }

            # Exibe botão de acompanhamento especial
            if (Verifica::acesso($idUsuario, [1, 9])) {
                $grid->abreColuna(2);

                tituloTable("Acompanhamento Especial?");

                $div = new Div("center");
                $div->abre();
                br();

                # Formuário exemplo de login
                $form = new Form('?fase=especial');

                # botão de acompanhamento especial
                $controle = new Input('especial', 'simnao');
                $controle->set_size(4);
                $controle->set_linha(1);
                $controle->set_valor($conteudo["especial"]);
                $controle->set_col(12);
                $controle->set_title('Informa se o contrato terá Acompanhamento Especial ou não');
                $controle->set_onChange('formPadrao.submit();');
                $form->add_item($controle);

                $form->show();
                $div->fecha();

                $grid->fechaColuna();
            }
            $grid->abreColuna(12);

            # Exibe outros dados do contrato
            $contrato->exibeDadosContrato($idContrato, $idUsuario);

            # Exibe os aditivos
            $objeto->$fase();

            # Verifica se veio do menu inicial
            if (get('i', false)) {
                # Grava no log a atividade
                $atividade = "Acessou a página do contrato " . $conteudo["numero"];
                $data = date("Y-m-d H:i:s");
                $intra->registraLog($idUsuario, $data, $atividade, "tbcontrato", $idContrato, 7);
            }

            $grid->fechaColuna();
            $grid->abreColuna(4);

            # Exibe os Processos de Execução
            $contrato->listaProcessosExecucao($idContrato, $idUsuario);

            $grid->fechaColuna();
            $grid->abreColuna(8);

            # Exibe as Características do Contrato
            $contrato->exibeCaracteristicas($idContrato, $idUsuario);

            $grid->fechaColuna();

            # Exibe dados da empresa
            $grid->abreColuna(6);
            $idEmpresa = $conteudo["idEmpresa"];
            $empresa->exibeDados($idEmpresa, $idUsuario);
            $grid->fechaColuna();

            # Exibe dados da comissão
            $grid->abreColuna(6);
            $comissao->listaComissao($idContrato, $idUsuario);
            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        case "editar" :
        case "excluir" :
            $objeto->$fase($id);
            break;

        case "gravar" :
            $objeto->gravar($id, "cadastroAditivoExtra.php");
            break;

        ################################################################

        case "especial" :
            # Pega o post
            $post = post("especial");

            # Trata o valor
            if ($post) {
                $post = 1;
            } else {
                $post = null;
            }

            # grava
            $objeto = new Contratos;
            $objeto->set_tabela("tbcontrato");
            $objeto->set_idCampo("idContrato");
            $objeto->gravar(["especial"], [$post], $idContrato);

            # Retorna 
            loadPage("?");
            break;

        ################################################################
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}