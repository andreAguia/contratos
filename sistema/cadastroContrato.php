<?php

/**
 * Cadastro de Campus
 *
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include "_config.php";

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 9, 10]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $contratos = new Contratos();
    $contrato = new Contrato();
    $pessoal = new Pessoal();

    # Faz o backup de hora em hora
    # Verifica se o backup automático está habilitado
    if ($intra->get_variavel("backupAutomatico")) {

        # Verifica as horas
        $horaBackup = $intra->get_variavel("backupHora");
        $horaAtual = date("H");

        # Compara se são diferentes
        if ($horaAtual <> $horaBackup) {
            # Realiza backup
            $processo = new Processo();
            $processo->run("php /var/www/html/areaServidor/sistema/backup.php 1 $idUsuario");
        }
    }

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # zera a sessionContrato
    set_session('sessionContrato', $id);

    # Pega os parâmetros
    $parametroAno = post('parametroAno', get_session('parametroAno'));
    $parametroStatus = post('parametroStatus', get_session('parametroStatus', 1));
    $parametroModalidade = post('parametroModalidade', get_session('parametroModalidade'));
    $parametroEmpresa = post('parametroEmpresa', get_session('parametroEmpresa'));
    $inclusaoEmpresa = post('inclusaoEmpresa', get_session('inclusaoEmpresa'));

    # Joga os parâmetros par as sessions
    set_session('parametroAno', $parametroAno);
    set_session('parametroStatus', $parametroStatus);
    set_session('parametroModalidade', $parametroModalidade);
    set_session('parametroEmpresa', $parametroEmpresa);
    set_session('inclusaoEmpresa', $inclusaoEmpresa);

    ################################################################
    # Começa uma nova página
    $page = new Page();
    # Rotina que aparece o número de arquivos selecionados para upload
    if ($fase == "uploadContrato" OR $fase == "uploadPublicacao") {
        $page->set_ready('$("form input").change(function(){
                          $("form p").text(this.files.length + " arquivo(s) selecionado");
                          });');
    }

    # Rotina que esconde o numero do pregão quando a modalidade é outra
    if ($fase == "editar") {
        # Rotina em Jscript
        $jscript = '// Pega os valores da modalidade
                var modalidade = $("#idModalidade").val();

                // Exibe ou não o número do pregão a partir do valor da modalidade
                // quando o form é carregado
                if(modalidade == 2){
                    $("#numPregao").show();
                    $("#labelnumPregao").show();
                }else{
                    $("#numPregao").hide();
                    $("#labelnumPregao").hide();
                }
                
                // Verifica o valor do número do pregão quando se muda o valor da modalidade
                $("#idModalidade").change(function(){
                    var modalidade = $("#idModalidade").val();
                    if(modalidade == 2){
                        $("#numPregao").show();
                        $("#labelnumPregao").show();
                    }else{
                        $("#numPregao").hide();
                        $("#labelnumPregao").hide();
                    }
                });';

        $page->set_ready($jscript);
    }
    $page->iniciaPagina();

    ################################################################
    # Cabeçalho da Página
    AreaServidor::cabecalho();

    if ($fase == "listar") {
        p(SISTEMA, 'grhTitulo');
        p("Versão: " . VERSAO, "versao");
    }

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome('Contratos');

    # Botão de voltar da lista
    $objeto->set_voltarLista('cadastroContrato.php');

    # select da lista
    $select = "SELECT idContrato,
                      idContrato,
                      idEmpresa,
                      idContrato,
                      idContrato,
                      idContrato,
                      idContrato                      
                 FROM tbcontrato JOIN tbmodalidade USING (idModalidade)
                                 JOIN tbstatus USING (idStatus)
                                 JOIN tbempresa USING (idEmpresa)
                WHERE true";

    if (!empty($parametroEmpresa)) {
        $select .= " AND idEmpresa = {$parametroEmpresa}";
    }

    if (!empty($parametroAno)) {
        $select .= " AND (SUBSTRING(numero,-4) = {$parametroAno} OR YEAR(dtAssinatura) = {$parametroAno})";
    }

    if (!empty($parametroModalidade)) {
        $select .= " AND idModalidade = {$parametroModalidade}";
    }

    if (!empty($parametroStatus)) {
        $select .= " AND idStatus = {$parametroStatus}";
    }

    /*
     * Rotina abaixo ordena seguindo os seguintes critérios:
     * 1 - pela data de término do último termo aditivo onde o prezo não é nulo
     *     quando tiver termo aditivo. Caso não tenha termo pega a data de término
     *     do contrato.
     * 2 - pelo número do contrato
     */

    $select .= " ORDER BY especial desc, (IFNULL(
                      (SELECT IF(tipoPrazo = 2,
                          SUBDATE(ADDDATE(dtInicial, INTERVAL prazo MONTH), INTERVAL 1 DAY),
                          ADDDATE(dtInicial, INTERVAL prazo-1 DAY)) as dtFinal
                     FROM tbaditivo
                    WHERE tbaditivo.idContrato = tbcontrato.idContrato
                      AND prazo IS NOT NULL 
                 ORDER BY dtAssinatura desc LIMIT 1),
                 IF(tipoPrazo = 2,SUBDATE(ADDDATE(dtInicial, INTERVAL prazo MONTH), INTERVAL 1 DAY),ADDDATE(dtInicial, INTERVAL prazo-1 DAY)))), numero";

    $objeto->set_selectLista($select);

    # select do edita
    $objeto->set_selectEdita('SELECT numero,
                                     idModalidade,
                                     numPregao,
                                     siafe,
                                     idStatus,
                                     dtProposta,
                                     dtAssinatura,
                                     idEmpresa,
                                     objeto,
                                     especial,
                                     maoDeObra,
                                     processoSei,
                                     processo,
                                     processoPrestacao,
                                     valor,
                                     garantia,
                                     dtPublicacao,
                                     pgPublicacao,
                                     dtInicial,
                                     prazo,
                                     tipoPrazo,
                                     rubrica,
                                     obs
                                FROM tbcontrato
                              WHERE idContrato = ' . $id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_botaoEditar(false);
    $objeto->set_linkGravar('?fase=gravar');

    # Diferencia o botão de voltar para as rotinas de editar ou incluir
    if (empty($id)) {
        $objeto->set_voltarForm("?");
    } else {
        $objeto->set_voltarForm("cadastroAditivo.php");
    }

    $objeto->set_linkListar("cadastroAditivo.php");

    $objeto->set_label(["Contrato", "Objeto", "Empresa", "Processo", "Duração & Vigência", "Situação", "Acessar"]);
    $objeto->set_classe(["Contrato", "Contrato", "Empresa", "Contrato", "Contrato", "Situacao"]);
    $objeto->set_metodo(["exibeNumeroContrato", "exibeObjeto", "exibeEmpresaCnpj", "getProcesso", "exibeTempoEVigencia", "getSituacaoAtualEAlerta"]);
    $objeto->set_width([10, 20, 22, 18, 10, 20]);
    $objeto->set_align(["center", "left", "left", "left", "center", "left"]);
    $objeto->set_exibeTempoPesquisa(false);

    # Botão 
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Acessar Contrato');
    $botao->set_url("?fase=editaContrato&id={$id}");
    $botao->set_imagem(PASTA_FIGURAS_GERAIS . "olho.png", 20, 20);

    # Coloca o objeto link na tabela
    $objeto->set_link(array("", "", "", "", "", "", $botao));

    # Classe do banco de dados
    $objeto->set_classBd('Contratos');

    # Nome da tabela
    $objeto->set_tabela('tbcontrato');

    # Nome do campo id
    $objeto->set_idCampo('idContrato');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Dados da combo status
    $status = $contratos->select('SELECT idStatus,
                                        status
                                   FROM tbstatus
                               ORDER BY status');

    array_unshift($status, array(null, null));

    # Dados da combo tipo
    $tipo = array(
        array(null, null),
        array(1, "Dias"),
        array(2, "Meses"),
    );

    # Dados da combo modalidade
    $modalidade = $contratos->select('SELECT idModalidade, modalidade, tipo
                                        FROM tbmodalidade
                                    ORDER BY tipo,modalidade');

    array_unshift($modalidade, array(null, null));

    # Dados da combo empresa
    $empresa = $contratos->select('SELECT idEmpresa,
                                         razaoSocial
                                    FROM tbempresa
                              ORDER BY razaoSocial');

    array_unshift($empresa, array(null, null));

    # Campos para o formulario
    $objeto->set_campos(array(
        array(
            'linha' => 1,
            'nome' => 'numero',
            'label' => 'Número:',
            'tipo' => 'texto',
            'required' => true,
            'unique' => true,
            'autofocus' => true,
            'col' => 3,
            'size' => 10,
            'padrao' => $contrato->getNovoNumeroContrato(),
        ),
        array(
            'linha' => 1,
            'nome' => 'idModalidade',
            'label' => 'Modalidade:',
            'tipo' => 'combo',
            'required' => true,
            'optgroup' => true,
            'array' => $modalidade,
            'col' => 3,
            'size' => 15,
        ),
        array(
            'linha' => 1,
            'nome' => 'numPregao',
            'label' => 'Num.Pregão:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 9,
        ),
        array(
            'linha' => 1,
            'nome' => 'siafe',
            'label' => 'Siafe:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 15,
        ),
        array(
            'linha' => 1,
            'nome' => 'idStatus',
            'label' => 'Status:',
            'tipo' => 'combo',
            'array' => $status,
            'required' => true,
            'col' => 2,
            'size' => 30,
            'padrao' => 1
        ),
        array(
            'linha' => 2,
            'nome' => 'dtProposta',
            'label' => 'Proposta:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15,
        ),
        array(
            'linha' => 2,
            'nome' => 'dtAssinatura',
            'label' => 'Assinatura:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15,
        ),
        array(
            'linha' => 2,
            'nome' => 'idEmpresa',
            'label' => 'Empresa:',
            'tipo' => 'combo',
            'array' => $empresa,
            'required' => true,
            'col' => 6,
            'size' => 200,
            'padrao' => $inclusaoEmpresa,
        ),
        array(
            'linha' => 3,
            'nome' => 'objeto',
            'label' => 'Objeto:',
            'tipo' => 'textarea',
            'size' => array(80, 3),
            'col' => 8,
        ),
        array(
            'linha' => 3,
            'nome' => 'especial',
            'label' => 'Acompanhamento Especial:',
            'tipo' => 'simnao',
            'title' => 'Informa se o contrato terá acompanhamento especial.',
            'col' => 2,
            'size' => 5,
            'padrao' => 0
        ),
        array(
            'linha' => 3,
            'nome' => 'maoDeObra',
            'label' => 'Mão de Obra Alocada:',
            'tipo' => 'simnao',
            'title' => 'Informa se o contrato tem ou não mão de obra alocada na UENF.',
            'col' => 2,
            'size' => 5,
            'padrao' => 0
        ),
        array(
            'linha' => 5,
            'nome' => 'processoSei',
            'label' => 'Processo Sei:',
            'tipo' => 'sei',
            'col' => 4,
            'size' => 50,
        ),
        array(
            'linha' => 5,
            'nome' => 'processo',
            'label' => 'Processo Físico:',
            'tipo' => 'processo',
            'col' => 4,
            'size' => 50,
        ),
        array(
            'linha' => 5,
            'nome' => 'processoPrestacao',
            'label' => 'Processo de Prestação de Contas:',
            'tipo' => 'processo',
            'col' => 4,
            'size' => 50,
        ),
        array(
            'linha' => 6,
            'nome' => 'valor',
            'label' => 'Valor:',
            'tipo' => 'moeda',
            'col' => 3,
            'size' => 15,
        ),
        array(
            'linha' => 6,
            'nome' => 'garantia',
            'label' => 'Garantia: (se houver)',
            'tipo' => 'percentagem',
            'col' => 2,
            'size' => 5,
        ),
        array(
            'linha' => 6,
            'nome' => 'dtPublicacao',
            'label' => 'Publicação:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15,
        ),
        array(
            'linha' => 6,
            'nome' => 'pgPublicacao',
            'label' => 'Pag:',
            'tipo' => 'texto',
            'col' => 2,
            'size' => 10,
        ),
        array(
            'linha' => 7,
            'nome' => 'dtInicial',
            'label' => 'Data Inicial:',
            'tipo' => 'date',
            'col' => 3,
            'size' => 15,
        ),
        array(
            'linha' => 7,
            'nome' => 'prazo',
            'label' => 'Prazo:',
            'tipo' => 'numero',
            'col' => 2,
            'size' => 15,
        ),
        array(
            'linha' => 7,
            'nome' => 'tipoPrazo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'array' => $tipo,
            'col' => 2,
            'size' => 15,
        ),
        array(
            'linha' => 7,
            'nome' => 'rubrica',
            'label' => 'Rubrica:',
            'tipo' => 'texto',
            'col' => 3,
            'size' => 20,
        ),
        array(
            'linha' => 9,
            'nome' => 'obs',
            'label' => 'Observação:',
            'tipo' => 'textarea',
            'size' => array(80, 3),
        ),
    ));

    # Retira os botões de voltar e incluir da rotina de editar
    $objeto->set_botaoVoltarLista(false);
    $objeto->set_botaoIncluir(false);

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "":
        case "listar":
            # Zera a session da inclusão de contrato
            set_session('inclusaoEmpresa');

            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            /*
             * Menu
             */

            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "../../areaServidor/sistema/areaServidor.php");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar, "left");

            # Relatórios
            $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
            $botaoRel = new Button();
            $botaoRel->set_title("Área de Relatório");
            $botaoRel->set_url("relatorios.php");
            $botaoRel->set_imagem($imagem);
            $menu1->add_link($botaoRel, "right");

            if (Verifica::acesso($idUsuario, [1, 9])) {

                # Incluir
                $botaoInserir = new Button("Incluir Contrato", "?fase=incluir");
                $botaoInserir->set_title("Incluir");
                $menu1->add_link($botaoInserir, "right");
            }

            $menu1->show();

            /*
             * Menu auxiliar
             */

            if (Verifica::acesso($idUsuario, [1, 9])) {
                #hr("hrMenusecundario");
                $menu2 = new MenuBar();

                # Empresas
                $botao = new Button("Empresas", "cadastroEmpresa.php?i=true");
                $botao->set_title("Cadastro de Empresas");
                $botao->set_class("button secondary");
                $menu2->add_link($botao, "right");

                # Comissões
                $botao = new Button("Comissões", "cadastroMembros.php?i=true");
                $botao->set_title("Cadastro de Comissões");
                $botao->set_class("button secondary");
                $menu2->add_link($botao, "right");

                # Modalidade
                $botao = new Button("Modalidade", "cadastroModalidade.php?i=true");
                $botao->set_title("Cadastro de Modalidade");
                $botao->set_class("button secondary");
                $menu2->add_link($botao, "right");

                # Status
                $botao = new Button("Status", "cadastroStatus.php?i=true");
                $botao->set_title("Cadastro de Status");
                $botao->set_class("button secondary");
                $menu2->add_link($botao, "right");

                # Natureza
                $botao = new Button("Natureza", "cadastroNatureza.php?i=true");
                $botao->set_title("Cadastro de Natureza");
                $botao->set_class("button secondary");
                $menu2->add_link($botao, "right");

                # Checklist
                $botao = new Button("Checklist", "cadastroChecklist.php");
                $botao->set_title("Modelos de Checklist");
                $botao->set_class("button secondary");
                #$menu2->add_link($botao, "right");

                $menu2->show();
            }

            # Formulário de Pesquisa
            $form = new Form('?');

            /*
             * Ano do Contrato
             */

            # Pega os dados
            $comboAno = $contratos->select('SELECT DISTINCT YEAR(dtAssinatura), YEAR(dtAssinatura)
                                                  FROM tbcontrato
                                                 WHERE dtAssinatura IS NOT NULL
                                              ORDER BY YEAR(dtAssinatura) DESC');

            array_unshift($comboAno, array(null, "Todos"));

            # Ano
            $controle = new Input('parametroAno', 'combo', 'Ano:', 1);
            $controle->set_size(20);
            $controle->set_title('Ano da assinatura do contrato');
            $controle->set_valor($parametroAno);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(2);
            $controle->set_array($comboAno);
            $controle->set_autofocus(true);
            $form->add_item($controle);

            /*
             * Status
             */

            # Pega os dados
            $comboStatus = $contratos->select('SELECT idStatus, status
                                               FROM tbstatus
                                           ORDER BY idStatus');

            array_unshift($comboStatus, array(null, "Todos"));

            # Status
            $controle = new Input('parametroStatus', 'combo', 'Status:', 1);
            $controle->set_size(20);
            $controle->set_title('Status do contrato');
            $controle->set_valor($parametroStatus);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(2);
            $controle->set_array($comboStatus);
            $controle->set_autofocus(true);
            $form->add_item($controle);

            /*
             * Modalidade
             */

            # Pega os dados
            $comboModalidade = $contratos->select('SELECT idModalidade, modalidade, tipo
                                               FROM tbmodalidade
                                           ORDER BY tipo, modalidade');

            array_unshift($comboModalidade, array(null, "Todos"));

            # Modalidade
            $controle = new Input('parametroModalidade', 'combo', 'Modalidade:', 1);
            $controle->set_size(20);
            $controle->set_title('Modalidade do contrato');
            $controle->set_valor($parametroModalidade);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_optgroup(true);
            $controle->set_col(3);
            $controle->set_array($comboModalidade);
            $controle->set_autofocus(true);
            $form->add_item($controle);

            /*
             * Empresa
             */

            # Pega os dados
            $comboEmpresa = $contratos->select('SELECT idEmpresa, razaoSocial
                                               FROM tbempresa
                                           ORDER BY razaoSocial');

            array_unshift($comboEmpresa, array(null, "Todas"));

            # Empresa
            $controle = new Input('parametroEmpresa', 'combo', 'Empresa:', 1);
            $controle->set_size(20);
            $controle->set_title('Empresa contratada');
            $controle->set_valor($parametroEmpresa);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(5);
            $controle->set_array($comboEmpresa);
            $controle->set_autofocus(true);
            $form->add_item($controle);

            $form->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            $objeto->listar();

            # Exibe o rodapé
            AreaServidor::rodape($idUsuario, false);
            break;

        ################################################################

        case "editar":
        case "excluir":
            $objeto->$fase($id);
            break;

        ################################################################

        case "gravar":
            $objeto->gravar($id, null, "cadastroContratoPosGravacao.php");
            break;

        ################################################################

        case "incluir":

            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar", "?");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar, "left");

            # Incluir
            $botaoInserir = new Button("Incluir Contrato", "?fase=incluir");
            $botaoInserir->set_title("Incluir");
            #$menu1->add_link($botaoInserir, "right");

            $menu1->show();

            # Formulário de Pesquisa
            $form = new Form('?fase=valida');

            # Titulo
            titulo("Inclusão de Contrato");
            br(2);

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Limita o tamanho da tela
            $grid = new Grid("center");
            $grid->abreColuna(8);

            callout("Inicialmente informe a empresa contratada, caso seja uma empresa nova o cadastro deverá ser feito antes do cadastro do contrato !!");
            br();

            # Pega os dados
            $comboEmpresa = $contratos->select('SELECT idEmpresa, razaoSocial
                                               FROM tbempresa
                                           ORDER BY razaoSocial');

            array_unshift($comboEmpresa, array(null, null));
            array_unshift($comboEmpresa, array("nova", "Nova Empresa"));

            # Empresa
            $controle = new Input('inclusaoEmpresa', 'combo', 'Empresa:', 1);
            $controle->set_size(100);
            $controle->set_title('Empresa contratada');
            $controle->set_valor(null);
            $controle->set_linha(1);
            $controle->set_col(12);
            $controle->set_array($comboEmpresa);
            $controle->set_autofocus(true);
            $form->add_item($controle);

            # submit
            $controle = new Input('submit', 'submit');
            $controle->set_valor('Continua');
            $controle->set_linha(3);
            $controle->set_tabIndex(3);
            $controle->set_accessKey('E');
            $form->add_item($controle);

            $form->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        ################################################################

        case "valida":

            # Verifica se é nova
            if ($inclusaoEmpresa == "nova") {
                loadPage("cadastroEmpresa.php?fase=editar");
            }

            if (empty($inclusaoEmpresa)) {
                loadPage("?fase=incluir");
            }

            if (is_numeric($inclusaoEmpresa)) {
                loadPage("?fase=editar");
            }

            break;

        ################################################################

        case "editaContrato":
            /*
             * O sistema passa por aqui somente por causa da session no início desse código
             */

            loadPage("cadastroAditivo.php?i=true");
            break;

        ################################################################

        case "uploadPublicacao":
            $grid = new Grid("center");
            $grid->abreColuna(12);

            # Botão voltar
            botaoVoltar('cadastroAditivo.php');

            # Título
            tituloTable("Upload de Publicação");

            # Limita a tela
            $grid->fechaColuna();
            $grid->abreColuna(6);

            # Monta o formulário
            echo "<form class='upload' method='post' enctype='multipart/form-data'><br>
                        <input type='file' name='doc'>
                        <p>Click aqui ou arraste o arquivo.</p>
                        <button type='submit' name='submit'>Enviar</button>
                    </form>";

            # Pasta onde será guardado o arquivo
            $pasta = PASTA_CONTRATOS_PUBLICACAO;

            # Se não existe o programa cria
            if (!file_exists($pasta) || !is_dir($pasta)) {
                mkdir($pasta, 0755);
            }

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
            $texto .= "<br/>Tamanho Máximo do Arquivo: {$limite} M";

            br();
            p($texto, "f14", "center");

            if ((isset($_POST["submit"])) && (!empty($_FILES['doc']))) {
                $upload = new UploadDoc($_FILES['doc'], $pasta, $id, $extensoes);

                # Salva e verifica se houve erro
                if ($upload->salvar()) {

                    # Registra log
                    $Objetolog = new Intra();
                    $data = date("Y-m-d H:i:s");
                    $atividade = "Fez o upload da publicação do contrato " . $contrato->getNumero($id);
                    $Objetolog->registraLog($idUsuario, $data, $atividade, null, $id, 8);

                    # Volta para o menu
                    loadPage('cadastroAditivo.php');
                } else {
                    loadPage("?fase=uploadPublicacao&id=$id");
                }
            }

            # Informa caso exista um arquivo com o mesmo nome
            $arquivoDocumento = $pasta . $id . ".pdf";
            if (file_exists($arquivoDocumento)) {
                p("Já existe um documento para este registro no servidor!!<br/>O novo documento irá sobrescrevê-lo e o antigo será apagado !!", "puploadMensagem");
                br();
            }

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        ################################################################

        case "uploadContrato":
            $grid = new Grid("center");
            $grid->abreColuna(12);

            # Botão voltar
            botaoVoltar('cadastroAditivo.php');

            # Título
            tituloTable("Upload do Contrato");

            # Limita a tela
            $grid->fechaColuna();
            $grid->abreColuna(6);

            # Monta o formulário
            echo "<form class='upload' method='post' enctype='multipart/form-data'><br>
                        <input type='file' name='doc'>
                        <p>Click aqui ou arraste o arquivo.</p>
                        <button type='submit' name='submit'>Enviar</button>
                    </form>";

            # Pasta onde será guardado o arquivo
            $pasta = PASTA_CONTRATOS;

            # Se não existe o programa cria
            if (!file_exists($pasta) || !is_dir($pasta)) {
                mkdir($pasta, 0755);
            }

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
            $texto .= "<br/>Tamanho Máximo do Arquivo: {$limite} M";

            br();
            p($texto, "f14", "center");

            if ((isset($_POST["submit"])) && (!empty($_FILES['doc']))) {
                $upload = new UploadDoc($_FILES['doc'], $pasta, $id, $extensoes);

                # Salva e verifica se houve erro
                if ($upload->salvar()) {

                    # Registra log
                    $Objetolog = new Intra();
                    $data = date("Y-m-d H:i:s");
                    $atividade = "Fez o upload do contrato " . $contrato->getNumero($id);
                    $Objetolog->registraLog($idUsuario, $data, $atividade, null, $id, 8);

                    # Volta para o menu
                    loadPage('cadastroAditivo.php');
                } else {
                    loadPage("?fase=uploadContrato&id=$id");
                }
            }

            # Informa caso exista um arquivo com o mesmo nome
            $arquivoDocumento = $pasta . $id . ".pdf";
            if (file_exists($arquivoDocumento)) {
                p("Já existe um documento para este registro no servidor!!<br/>O novo documento irá sobrescrevê-lo e o antigo será apagado !!", "puploadMensagem");
                br();
            }

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        ################################################################
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
