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
$acesso = Verifica::acesso($idUsuario, [9, 10]);

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

    # Verifica a fase do programa
    $fase = get("fase", "listar");

    # pega o id (se tiver)
    $id = soNumeros(get("id"));

    # pega o contrato
    $idContrato = soNumeros(get_session('sessionContrato'));

    # Começa uma nova página
    $page = new Page();
    if ($fase == "upload") {
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
    if (Verifica::acesso($idUsuario, 9)) {
        $objeto->set_linkEditar('?fase=editar');
        $objeto->set_linkExcluir('?fase=excluir');
    }
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    $objeto->set_exibeTempoPesquisa(false);

    # Parametros da tabela
    $objeto->set_label(array("Tipo", "Objeto", "Publicação", "Assinatura", "Duração", "Garantia", "Valor"));
    $objeto->set_align(array("center", "left", "center", "center", "center", "center", "right"));
    $objeto->set_width(array(15, 25, 10, 10, 10, 15, 15));
    $objeto->set_classe(array("Aditivo", "Aditivo", "Aditivo", null, "Aditivo", "Aditivo", "Aditivo"));
    if (Verifica::acesso($idUsuario, 9)) {
        $objeto->set_metodo(array("exibeTipoNumerado", "exibeObjeto", "exibePublicacao", null, "exibePeriodo", "exibeGarantia", "exibeValor"));
    } else {
        $objeto->set_metodo(array("exibeTipoNumerado", "exibeObjeto", "getPublicacao", null, "exibePeriodo", "exibeGarantia", "exibeValor"));
    }
    $objeto->set_funcao(array(null, null, null, "date_to_php"));

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
            'required' => true,
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
            'tipo' => 'texto',
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

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "cadastroContrato.php");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar, "left");

            if (Verifica::acesso($idUsuario, 9)) {

                # Pagamentos
                $botaoInserir = new Button("Controle de Saldo", "cadastroPagamento.php");
                $botaoInserir->set_title("Incluir");
                $menu1->add_link($botaoInserir, "right");

                # Incluir
                $botaoInserir = new Button("Incluir Aditivo", "?fase=editar");
                $botaoInserir->set_title("Incluir");
                $menu1->add_link($botaoInserir, "right");

                # Relatórios
                $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
                $botaoRel = new Button();
                $botaoRel->set_title("Relatório dessa pesquisa");
                $botaoRel->set_url("../grhRelatorios/acumulacao.geral.php");
                $botaoRel->set_target("_blank");
                $botaoRel->set_imagem($imagem);
                #$menu1->add_link($botaoRel,"right");
            }

            $menu1->show();

            ##########
            # Exibe os dados do contrado
            get_DadosContrato($idContrato);

            # Exibe alertas (se tiver)
            $alerta = new AlertaContrato($idContrato, true);

            $grid->fechaColuna();
            $grid->abreColuna(4);

            # Exibe o valor
            $contrato->exibeValorTotalPainel($idContrato);

            $grid->fechaColuna();
            $grid->abreColuna(8);

            # Exibe a situação atual
            $situacao->exibeSituacaoAtual($idContrato, $idUsuario);

            $grid->fechaColuna();
            $grid->abreColuna(12);

            # Exibe outros dados do contrato
            $contrato->exibeDadosContrato($idContrato, $idUsuario);

            # Exibe os aditivos
            $objeto->$fase();

            # Carrega os dados com contrado editado
            $conteudo = $contrato->getDados($idContrato);

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

        case "upload":
            $grid = new Grid("center");
            $grid->abreColuna(12);

            # Botão voltar
            botaoVoltar('cadastroContrato.php');

            # Título
            tituloTable("Upload de Publicação");

            # Limita a tela
            $grid->fechaColuna();
            $grid->abreColuna(6);

            # Monta o formulário
            echo "<form name='post' action='?fase=upload&id={$id}&post=true' class='upload' method='post' enctype='multipart/form-data'><br>
                        <input type='file' name='file'>
                        <p>Clique Aqui Para Escolher o Arquivo.</p>
                        <button type='submit' name='submit'>Enviar</button>
                    </form>";

            # Extensões possíveis
            $extensoes = array("pdf");

            # Pega os valores do php.ini
            $postMax = limpa_numero(ini_get('post_max_size'));
            $uploadMax = limpa_numero(ini_get('upload_max_filesize'));
            $limite = menorValor(array($postMax, $uploadMax));

            $texto = "Extensões Permitidas:";
            foreach ($extensoes as $pp) {
                $texto .= " $pp";
            }
            $texto .= "<br/>Tamanho Máximo do Arquivo: $limite M";

            br();
            p($texto, "f14", "center");

            $pasta = PASTA_ADITIVOS;

            # Retorna true se existir um get de nome post e for boleano
            $getPost = filter_input(INPUT_GET, "post", FILTER_VALIDATE_BOOLEAN);

            # Se não existe o programa cria
            if (!file_exists($pasta) || !is_dir($pasta)) {
                mkdir($pasta, 0755);
            }

            # Se existe uma $_FILES e o nome do arquivo não estiver vazio
            if ($_FILES && !empty($_FILES['file']['name'])) {

                $fileUpload = $_FILES["file"];

                # Define no array os nameTypes permitidos
                $allowedTypes = [
                    "application/pdf",
                ];

                # Define o novo nome do arquivo
                $newFileName = $id . mb_strrchr($fileUpload['name'], ".");

                # Percorre o array de tipos permitidos. Se o arquivo uploadeado for igual a um deles...
                if (in_array($fileUpload['type'], $allowedTypes)) {
                    if (move_uploaded_file($fileUpload['tmp_name'], $pasta . $newFileName)) {
                        loadPage("?id={$id}");
                    } else {
                        echo "<p class='trigger error'>Erro Inesperado</p>";
                    }
                } else {
                    echo "<p class='trigger error'>Tipo de arquivo não permitido</p>";
                }
            } elseif ($_FILES) {
                echo "<p class='trigger warning'>Selecione um arquivo antes de enviar</p>";
            } elseif ($getPost) {
                echo "<p class='trigger warning'>Parece que o arquivo é muito grande</p>";
            }

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}