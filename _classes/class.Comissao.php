<?php

class Comissao
{
    /**
     * Abriga as várias rotina referentes a comissao
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idComissao integer null O id do concurso
     */
    private $idComissao = null;
##############################################################

    public function __construct($idComissao = null)
    {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer null O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idComissao = $idComissao;
    }

##############################################################

    public function get_dados($idComissao = null)
    {
# Joga o valor informado para a variável da classe
        if (!vazio($idComissao)) {
            $this->idComissao = $idComissao;
        }

# Conecta ao Banco de Dados
        $contratos = new Contratos();

# Verifica se foi informado
        if (vazio($this->idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

# Pega os dados
        $select = 'SELECT * 
                     FROM tbcomissao
                    WHERE idComissao = ' . $this->idComissao;

        $row = $contratos->select($select, false);

# Retorno
        return $row;
    }

#####################################################################################

    public function get_portariaEntrada($idComissao)
    {

# Joga o valor informado para a variável da classe
        if (!vazio($idComissao)) {
            $this->idComissao = $idComissao;
        }

# Conecta ao Banco de Dados
        $contratos = new Contratos();

# Verifica se foi informado
        if (vazio($this->idComissao)) {
            alert("É necessário informar o id.");
            return;
        }

        $return = null;

# Pega os dados
        $select = "SELECT portariaEntrada,
                          dtPortariaEntrada,
                          dtPublicacaoEntrada
                     FROM tbcomissao
                    WHERE idComissao = {$idComissao}";

        $row = $contratos->select($select, false);

# Trata o retorno
        if (empty($row["portariaEntrada"])) {
            return "---";
        } else {
            $return = "Portaria n° {$row["portariaEntrada"]}";

            if (!empty($row["dtPortariaEntrada"])) {
                $return .= " de " . date_to_php($row["dtPortariaEntrada"]);
            }

            if (!empty($row["dtPublicacaoEntrada"])) {
                $return .= "<br/>publicada no DOERJ de " . date_to_php($row["dtPublicacaoEntrada"]);
            }
        }

# Retorno
        return $return;
    }

#####################################################################################

    public function get_tipo($idComissao)
    {

# Verifica se o id foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        } else {

            $conteudo          = $this->get_dados($idComissao);
            $dtPublicacaoSaida = $conteudo["dtPublicacaoSaida"];
            $tipo              = $conteudo["tipo"];

# Verifica se foi informado
            if (vazio($tipo)) {
                return "---";
            } else {


                if ((!empty($dtPublicacaoSaida)) AND (jaPassou(date_to_php($dtPublicacaoSaida)))) {
                    return "Saiu";
                } else {
                    switch ($tipo) {
                        case 1 :
                            return "Presidente";
                            break;

                        case 2 :
                            return "Membro";
                            break;

                        case 3 :
                            return "Suplente";
                            break;
                    }
                }
            }
        }
    }

#####################################################################################

    public function get_nomeMembro($idServidor)
    {

# Verifica se foi informado
        if (empty($idServidor)) {
            echo "---";
        } else {
            $pessoal = new Pessoal();
            p($pessoal->get_nome($idServidor), "pmembroNome");
            p($pessoal->get_lotacao($idServidor), "pmembroLotacao");
        }
    }

#####################################################################################

    public function listaComissao($idContrato)
    {
        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT idServidor,
                      idComissao,
                      idComissao
                 FROM tbcomissao
                WHERE idContrato = {$idContrato}
             ORDER BY dtPublicacaoSaida,tipo";

        $row = $contratos->select($select);

        # Colore a llinha de acordo com o tipo
        $formatacaoCondicional = array(
            array('coluna'   => 1,
                'valor'    => "Presidente",
                'operador' => '=',
                'id'       => 'presidenteComissao'),
            array('coluna'   => 1,
                'valor'    => "Membro",
                'operador' => '=',
                'id'       => 'membroComissao'),
            array('coluna'   => 1,
                'valor'    => "Suplente",
                'operador' => '=',
                'id'       => 'cuplenteComissao'),
            array('coluna'   => 1,
                'valor'    => "Saiu",
                'operador' => '=',
                'id'       => 'saiuComissao'),
        );

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo("Comissão de Fiscalização");
        $tabela->set_label(array("Servidor", "Tipo"));
        $tabela->set_align(array("left", "center"));
        $tabela->set_width(array(70, 25));
        $tabela->set_classe(array("Comissao", "Comissao"));
        $tabela->set_metodo(array("get_nomeMembro", "get_tipo"));
        #$tabela->set_numeroOrdem(true);
        $tabela->set_conteudo($row);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);

        $tabela->set_idCampo("idComissao");
        $tabela->set_editar("cadastroComissao.php?fase=exibeMembro");
        $tabela->set_nomeColunaEditar("Ver");
        $tabela->set_editarBotao("ver.png");
        $tabela->show();
        
        $div = new Div("divEdita1Comissao");
        $div->abre();

        # Editar
        $div = new Div("divEdita2");
        $div->abre();

        # Editar
        $botaoEditar = new Link("Editar", "cadastroComissao.php");
        $botaoEditar->set_class('tiny button secondary');
        $botaoEditar->set_title('Editar situação');
        $botaoEditar->show();

        $div->fecha();

        $div->fecha();
    }

#####################################################################################

    public function exibeDadosMembro($idComissao)
    {

# Conecta ao Banco de Dados
        $pessoal = new Pessoal();

# Verifica se foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        } else {

# Pega dados do membro da comissao
            $conteudo = $this->get_dados($idComissao);

# Pega os dados
            $idServidor      = $conteudo["idServidor"];
            $idPessoa        = $pessoal->get_idPessoa($idServidor);
            $portariaEntrada = $this->get_portariaEntrada($idComissao);

            $idFuncional = $pessoal->get_idFuncional($idServidor);
            $servidor    = $pessoal->get_nome($idServidor);
            $cargo       = $pessoal->get_cargo($idServidor);
            $lotacao     = $pessoal->get_lotacao($idServidor);
            $cpf         = $pessoal->get_cpf($idPessoa);

# Pega os telefones
            $telefones = $pessoal->get_telefones($idServidor);

# Pega os Emails
            $emailPessoal = $pessoal->get_emailPessoal($idServidor);
            $emailUenf    = $pessoal->get_emailUenf($idServidor);
            $emails       = null;

# junta os Emails
            if (!vazio($emailPessoal)) {
                $emails .= "$emailPessoal<br/>";
            }

            if (!vazio($emailUenf)) {
                $emails .= "$emailUenf<br/>";
            }

# Monta as colunas
            $grid = new Grid();

#####################################

            $grid->abreColuna(4);

            $painel = new Callout();
            $painel->abre();
# Titulo
            tituloTable("Dados do Servidor");
            br();

# Monta o array de exibição
            $dados = [
                ["servidor", 12],
                ["idFuncional", 6],
                ["cpf", 6],
                ["cargo", 12],
                ["lotacao", 12],
                ["telefones", 12],
                ["emails", 12],
            ];

# Rotina de exibição
            $grid1 = new Grid();

            foreach ($dados as $item) {

# Monta a variável para usar o $$
                $pp = $item[0];

# label
                if (empty($item[2])) {
                    $label = plm($pp);
                } else {
                    $label = $item[2] . ":";
                }

# Verifica se tem variável com esse nome
                if (empty($$pp)) {                      // Se não tem variável com esse nome
                    if (empty($conteudo[$pp])) {        // Se não tiver no array de conteúdo do bd
                        $valor = "---";                 // Exibe tracinho
                    } else {                              // Se tiver conteúdo do bd exibe ele
                        $valor = $conteudo[$pp];
                    }
                } else {                                  // Se tiver variável exibe ela
                    $valor = $$pp;
                }

                $grid1->abreColuna($item[1]);
                p("{$label}:", "contratoLabel");
                p($valor, "contratoConteudo");
                $grid1->fechaColuna();
            }
            $grid1->fechaGrid();
            $painel->fecha();
            $grid->fechaColuna();

#####################################

            $grid->abreColuna(5);

            $painel = new Callout();
            $painel->abre();
# Titulo
            tituloTable("Dados da Designação");
            br();

# Pega os valores
            $tipo1 = $conteudo["tipo"];

# Trata a portaria de Entrada
            if (empty($conteudo["portariaEntrada"])) {
                $portariaEntrada = "---";
            } else {
                $portariaEntrada = "Portaria n° {$conteudo["portariaEntrada"]}";

                if (!empty($conteudo["dtPortariaEntrada"])) {
                    $portariaEntrada .= " de " . date_to_php($conteudo["dtPortariaEntrada"]);
                }
            }

            if (!empty($conteudo["dtPublicacaoEntrada"])) {
                $dtPublicacaoEntrada = date_to_php($conteudo["dtPublicacaoEntrada"]);
            }

# Trata a portaria de SAida
            if (empty($conteudo["portariaSaida"])) {
                $portariaSaida = "---";
            } else {
                $portariaSaida = "Portaria n° {$conteudo["portariaSaida"]}";

                if (!empty($conteudo["dtPortariaSaida"])) {
                    $portariaSaida .= " de " . date_to_php($conteudo["dtPortariaSaida"]);
                }
            }

            if (!empty($conteudo["dtPublicacaoSaida"])) {
                $dtPublicacaoSaida = date_to_php($conteudo["dtPublicacaoSaida"]);
            }

# Informa o tipo
            if ($tipo1 == 1) {
                $tipo = "Presidente";
            } elseif ($tipo1 == 2) {
                $tipo = "Membro";
            } elseif ($tipo1 == 3) {
                $tipo = "Suplente";
            }

# Monta o array de exibição
            $dados = [
                ["tipo", 12],
                ["portariaEntrada", 8, "Portaria de Designação"],
                ["dtPublicacaoEntrada", 4, "Publicado em"],
                ["portariaSaida", 8, "Portaria de Saída"],
                ["dtPublicacaoSaida", 4, "Publicado em"],
                ["obs", 12],
            ];

# Rotina de exibição
            $grid1 = new Grid();

            foreach ($dados as $item) {

# Monta a variável para usar o $$
                $pp = $item[0];

# label
                if (empty($item[2])) {
                    $label = plm($pp);
                } else {
                    $label = $item[2] . ":";
                }

# Verifica se tem variável com esse nome
                if (empty($$pp)) {                      // Se não tem variável com esse nome
                    if (empty($conteudo[$pp])) {        // Se não tiver no array de conteúdo do bd
                        $valor = "---";                 // Exibe tracinho
                    } else {                              // Se tiver conteúdo do bd exibe ele
                        $valor = $conteudo[$pp];
                    }
                } else {                                  // Se tiver variável exibe ela
                    $valor = $$pp;
                }

                $grid1->abreColuna($item[1]);
                p("{$label}:", "contratoLabel");
                p($valor, "contratoConteudo");
                $grid1->fechaColuna();
            }
            $grid1->fechaGrid();

            $painel->fecha();

            $grid->fechaColuna();

#####################################

            $grid->abreColuna(3);
            br();

            $foto = new ExibeFoto();
            $foto->set_fotoLargura(150);
            $foto->set_fotoAltura(200);
#$foto->set_url('?');
            $foto->show($idPessoa);

            $grid->fechaColuna();
            $grid->fechaGrid();

            hr();
        }
    }

###########################################################

    function exibeComissao($idContrato = null)
    {

# Verifica se foi informado
        if (vazio($idContrato)) {
            alert("É necessário informar o id do Contrato.");
            return;
        }

# Conecta ao Banco de Dados
        $contratos = new Contratos();
        $pessoal   = new Pessoal();

        $select = "SELECT * 
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                 ORDER BY tipo";

        $total = $contratos->count($select);

# Contator 
        $contAdt = 1;

        $painel = new Callout();
        $painel->abre();

        titulo("Comissão de Fiscalização");
        br();


        foreach ($contratos->select($select) as $conteudo) {

# Pega os valores]
            $idServidor = $conteudo["idServidor"];
            $idPessoa   = $pessoal->get_idPessoa($idServidor);
            $tipo1      = $conteudo["tipo"];

            $idFuncional = $pessoal->get_idFuncional($idServidor);
            $servidor    = $pessoal->get_nome($idServidor);
            $cargo       = $pessoal->get_cargo($idServidor);
            $lotacao     = $pessoal->get_lotacao($idServidor);
            $cpf         = $pessoal->get_cpf($idPessoa); # Informa o tipo
# trata Tipo
            if ($tipo1 == 1) {
                $tipo = "Presidente";
            } elseif ($tipo1 == 2) {
                $tipo = "Membro";
            } elseif ($tipo1 == 3) {
                $tipo = "Suplente";
            }

# Monta o array de exibição
            $dados = [
                ["servidor", 9],
                ["tipo", 3],
            ];

# Rotina de exibição
            $grid = new Grid();
            $grid->abreColuna(12);

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
#p("{$label}:", "contratoLabel");
                p($dado, "contratoConteudo");
                $grid->fechaColuna();
            }
            $grid->fechaGrid();

            if ($contAdt <= $total) {
                hr("hrComissao");
            } else {
                br();
                $div = new Div("divEdita1");
                $div->abre();

# Editar
                $div = new Div("divEdita2");
                $div->abre();

# Editar
                $botaoEditar = new Link("Editar", "cadastroComissao.php");
                $botaoEditar->set_class('tiny button secondary');
                $botaoEditar->set_title('Editar comissão');
                $botaoEditar->show();

                $div->fecha();

                $div->fecha();
            }
        }

# Exibe mensagem de inclusão de membros    
        if ($total == 0) {
            p("Nenhum servidor designado.", "center");

            $div = new Div("divEdita1");
            $div->abre();

# Editar
            $div = new Div("divEdita2");
            $div->abre();

# Editar
            $botaoEditar = new Link("Incluir", "cadastroComissao.php?fase=editar");
            $botaoEditar->set_class('tiny button secondary');
            $botaoEditar->set_title('Editar comissão');
            $botaoEditar->show();

            $div->fecha();

            $div->fecha();
        }

        $painel->fecha();
    }

###########################################################
}
