<?php

class Comissao {

    /**
     * Abriga as várias rotina referentes a comissao
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idComissao integer NULL O id do concurso
     */
    private $idComissao = null;

##############################################################

    public function __construct($idComissao = NULL) {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer NULL O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idComissao = $idComissao;
    }

##############################################################

    public function get_dados($idComissao = NULL) {

        /**
         * Informa os dados da base de dados
         * 
         * @param $idConcurso integer NULL O id do concurso
         * 
         * @syntax $concurso->get_dados([$idConcurso]);
         */
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

        $row = $contratos->select($select, FALSE);

        # Retorno
        return $row;
    }

    #####################################################################################

    public function get_portariaEntrada($idComissao) {

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

        $row = $contratos->select($select, FALSE);

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

    public function get_tipo($tipo) {

        # Verifica se foi informado
        if (vazio($tipo)) {
            return "---";
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

    #####################################################################################

    public function listaComissao($idContrato) {

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # monta o select
        $select = "SELECT tipo,
                          idServidor,
                          idComissao
                     FROM tbcomissao
                    WHERE idContrato = {$idContrato}
                 ORDER BY tipo";

        $row = $contratos->select($select);

        # Editar
        $div = new Div("divEditaNota");
        $div->abre();

        $button = new Button("Editar", "cadastroComissao.php");
        $button->set_class("secondary button small");
        $button->show();

        $div->fecha();

        # Monta a tabela
        $tabela = new Tabela();
        #$tabela->set_titulo("Comissão de Fiscalização");
        $tabela->set_label(array("Tipo", "Nome", "Designado em"));
        $tabela->set_align(array("center", "left", "left"));
        $tabela->set_width(array(10, 50, 40));
        $tabela->set_classe(array("Comissao", "Comissao", "Comissao"));
        $tabela->set_metodo(array("get_tipo", "exibeDadosMembro", "get_portariaEntrada"));
        $tabela->set_numeroOrdem(true);
        $tabela->set_conteudo($row);
        
        $tabela->set_idCampo("idServidor");
        $tabela->set_editar("cadastroComissao.php?fase=exibeFicha");
        $tabela->set_nomeColunaEditar("Acessar");
        $tabela->set_editarBotao("ver.png");
        $tabela->show();
    }

    #####################################################################################

    public function exibeDadosMembro($idComissao) {

        # Conecta ao Banco de Dados
        $pessoal = new Pessoal();

        # Verifica se foi informado
        if (vazio($idComissao)) {
            alert("É necessário informar o id.");
            return;
        }else{
            
            # Pega dados do membro da comissao
            $conteudo = $this->get_dados($idComissao);
            
            # Pega os dados
            $idServidor = $conteudo["idServidor"];
            $idPessoa = $pessoal->get_idPessoa($idServidor);
            $portariaEntrada = $this->get_portariaEntrada($idComissao);
            
            # Dados do Servidor
            get_DadosServidor($idServidor);
            
            # Monta as colunas
            $grid = new Grid();
            
            #####################################
            
            $grid->abreColuna(3);
            
            # Pega os telefones
            $telefones = $pessoal->get_telefones($idServidor);

            # Pega os Emails
            $emailPessoal = $pessoal->get_emailPessoal($idServidor);
            $emailUenf = $pessoal->get_emailUenf($idServidor);
            $emails = NULL;

            # junta os Emails
            if (!vazio($emailPessoal)) {
                $emails .= "$emailPessoal<br/>";
            }

            if (!vazio($emailUenf)) {
                $emails .= "$emailUenf<br/>";
            }

            tituloTable("Contatos:");
            br();

            p($telefones, "center", "f14");
            p($emails, "center", "f14");
            
            tituloTable("CPF:");
            br();

            P($pessoal->get_cpf($idPessoa),"center","14");
            
            $grid->fechaColuna();
            
            #####################################
            
            $grid->abreColuna(6);

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
            }
            elseif ($tipo1 == 2) {
                $tipo = "Membro";
            }
            elseif ($tipo1 == 3) {
                $tipo = "Suplente";
            }

            # Monta o array de exibição
            $dados = [
                ["tipo", 12],
                ["portariaEntrada", 6, "Portaria de Designação"],
                ["dtPublicacaoEntrada", 6, "Publicado em"],
                ["portariaSaida", 6, "Portaria de Saída"],
                ["dtPublicacaoSaida", 6, "Publicado em"],
                ["obs", 12],
            ];

            # Rotina de exibição
            tituloTable("Dados da Designação:");
            br();

            $grid1 = new Grid();

            foreach ($dados as $item) {

                # Monta a variável para usar o $$
                $pp = $item[0];

                # Monta a coluna
                $grid1->abreColuna($item[1]);

                # Exibe o label colocando a primeira letra em maúsculas
                if (empty($item[2])) {
                    p(plm($pp) . ":", "contratoLabel");
                }
                else {
                    p($item[2] . ":", "contratoLabel");
                }


                # Verifica se tem variável com esse nome
                if (empty($$pp)) {                      // Se não tem variável com esse nome
                    if (empty($conteudo[$pp])) {        // Se não tiver no array de conteúdo do bd
                        p("---", "contratoConteudo");   // Exibe tracinho
                    }
                    else {                              // Se tiver conteúdo do bd exibe ele
                        p($conteudo[$pp], "contratoConteudo");
                    }
                }
                else {                                  // Se tiver variável exibe ela
                    p($$pp, "contratoConteudo");
                }

                $grid1->fechaColuna();
            }
            $grid1->fechaGrid();
            
            $grid->fechaColuna();
            
            #####################################
            
            $grid->abreColuna(3);
            tituloTable("Foto");
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
}
