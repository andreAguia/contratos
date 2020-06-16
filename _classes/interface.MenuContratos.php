<?php

class MenuContratos
{

    /**
     * Gera o Menu Principal do Sistema de Contratos
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     */
    private $idUsuario = null;

    ######################################################################################################################

    public function __construct($idUsuario)
    {
        /**
         * Inicia a classe
         */
        # Inicia o Grid
        $grid = new Grid();

        # Preenche variável
        $this->idUsuario = $idUsuario;

        # Primeira Coluna
        $grid->abreColuna(12, 4);

        # Módulos
        $this->moduloCadastroPrincipal();
        $this->moduloTabelasAuxiliares();

        $grid->fechaColuna();

        ###############################################################################################

        # Segunda Coluna
        $grid->abreColuna(12, 8);

        # Módulos
        $this->moduloEventos();

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ######################################################################################################################
    /**
     * Método moduloCadastroPrincipal
     *
     * Exibe o menu de Legislação
     */
    private function moduloCadastroPrincipal()
    {

        $painel = new Callout();
        $painel->abre();

        titulo('Cadastros Principais');
        br();

        $tamanhoImage = 60;
        $menu = new MenuGrafico(2);

        $botao = new BotaoGrafico();
        $botao->set_label('Contratos');
        $botao->set_url('cadastroContrato.php');
        $botao->set_imagem(PASTA_FIGURAS . 'contratos.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Cadastro de Contratos');
        $botao->set_accesskey('C');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Empresas');
        $botao->set_url('cadastroEmpresa.php');
        $botao->set_imagem(PASTA_FIGURAS . 'empresas.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Cadastro de Empresas');
        $botao->set_accesskey('E');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ######################################################################################################################
    /**
     * Método moduloTabelasAuxiliares
     *
     * Exibe o menu de Legislação
     */
    private function moduloTabelasAuxiliares()
    {

        $painel = new Callout();
        $painel->abre();

        titulo('Tabelas Auxiliares');
        br();

        $tamanhoImage = 60;
        $menu = new MenuGrafico(2);

        $botao = new BotaoGrafico();
        $botao->set_label('Modalidade');
        $botao->set_url('cadastroModalidade.php');
        $botao->set_imagem(PASTA_FIGURAS . 'modalidades.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Cadastro de Modalidades de Contrato');
        $botao->set_accesskey('M');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Status');
        $botao->set_url('cadastroStatus.php');
        $botao->set_imagem(PASTA_FIGURAS . 'status.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Cadastro de Status de Contrato');
        $botao->set_accesskey('S');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ######################################################################################################################
    /**
     * Método moduloEventos
     *
     * Exibe o menu de Legislação
     */
    private function moduloEventos()
    {

        $painel = new Callout();
        $painel->abre();

        titulo("Agenda");
        br();

        # Inicia o Grid
        $grid = new Grid();
        $grid->abreColuna(6);

        titulotable("Tarefas");
        br();

        # Exibir as tarefas
        $tarefa = new Tarefa();
        $tarefa->listaTarefas();

        $grid->fechaColuna();
        $grid->abreColuna(6);

        # Calendário
        $cal = new Calendario();
        $cal->show();

        $grid->fechaColuna();
        $grid->fechaGrid();

        $painel->fecha();
    }

    ######################################################################################################################
}
