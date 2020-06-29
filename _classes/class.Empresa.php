<?php

class Empresa
{
    /**
     * Abriga as várias rotina referentes a concurso
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idConcurso integer null O id do concurso
     */
    private $idEmpresa = null;
##############################################################

    public function __construct($idEmpresa = null)
    {
        /**
         * Inicia a Classe somente
         * 
         * @param $idContrato integer null O id do concurso
         * 
         * @syntax $concurso = new Concurso([$idConcurso]);
         */
        $this->idEmpresa = $idEmpresa;
    }

##############################################################

    public function getDados($idEmpresa = null)
    {

        /**
         * Informa os dados da base de dados
         * 
         * @param $idConcurso integer null O id do concurso
         * 
         * @syntax $concurso->get_dados([$idConcurso]);
         */
        # Joga o valor informado para a variável da classe
        if (!vazio($idEmpresa)) {
            $this->idEmpresa = $idEmpresa;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idEmpresa)) {
            alert("É necessário informar o id da Empresa.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbempresa
                    WHERE idEmpresa = ' . $this->idEmpresa;

        $row = $contratos->select($select, false);

        # Retorno
        return $row;
    }

    #####################################################################################

    public function getRazaoSocial($idEmpresa)
    {

        # Joga o valor informado para a variável da classe
        if (!vazio($idEmpresa)) {
            $this->idEmpresa = $idEmpresa;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idEmpresa)) {
            alert("É necessário informar o id da Empresa.");
            return;
        }

        # Pega os dados
        $select = 'SELECT razaoSocial 
                     FROM tbempresa
                    WHERE idEmpresa = ' . $this->idEmpresa;

        $row = $contratos->select($select, false);

        # Retorno
        return $row[0];
    }

    ##########################################################################################

    public function getTelefones($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados   = $this->getDados($idEmpresa);
        $retorno = null;

        if (!empty($dados["telefone1"])) {
            $retorno .= "{$dados["telefone1"]}<br/>";
        }

        if (!empty($dados["telefone2"])) {
            $retorno .= "{$dados["telefone2"]}<br/>";
        }

        if (!empty($dados["telefone3"])) {
            $retorno .= "{$dados["telefone3"]}";
        }

        return $retorno;
    }

    ##########################################################################################

    public function getEmails($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados   = $this->getDados($idEmpresa);
        $retorno = null;

        if (!empty($dados["email1"])) {
            $retorno .= "{$dados["email1"]}<br/>";
        }

        if (!empty($dados["email2"])) {
            $retorno .= "{$dados["email2"]}<br/>";
        }

        if (!empty($dados["email3"])) {
            $retorno .= "{$dados["email3"]}";
        }

        return $retorno;
    }

    ##########################################################################################

    public function getContatos($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados   = $this->getDados($idEmpresa);
        $retorno = null;

        if (!empty($dados["contato"])) {
            $retorno .= "{$dados["contato"]}<br/>";
        }

        if (!empty($dados["usuarioSei"])) {
            $retorno .= "SEI: {$dados["usuarioSei"]}<br/>";
        }

        return $retorno;
    }

    ##########################################################################################

    public function getEmpresaCnpj($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados = $this->getDados($idEmpresa);

        $retorno = $dados["razaoSocial"];

        if (!empty($dados["cnpj"])) {
            $retorno .= "<br/><p id='f10'> CNPJ: {$dados["cnpj"]}</p>";
        }

        return $retorno;
    }

    ##########################################################################################

    public function getEndereco($idEmpresa)
    {

        $dados = $this->getDados($idEmpresa);

        if (!empty($dados["endereco"])) {
            return "{$dados["endereco"]}<br/>{$dados["bairro"]}<br/>{$dados["cep"]}";
        } else {
            return null;
        }
    }

    ###########################################################

    function exibeDados($idEmpresa)
    {

        $conteudo = $this->getDados($idEmpresa);
        $email    = $this->getEmails($idEmpresa);
        $telefone = $this->getTelefones($idEmpresa);
        $contatos = $this->getContatos($idEmpresa);
        $endereco = $this->getEndereco($idEmpresa);

        $painel = new Callout("primary");
        $painel->abre();

        # Monta o array de exibição
        $dados = [
            ["razaoSocial", 12, "Razão Social"],
            ["cnpj", 12],
            ["telefone", 12],
            ["email", 12],
            ["contatos", 12],
            ["endereco", 12, "Endereço"],
        ];

        # Rotina de exibição
        $grid = new Grid();
        $grid->abreColuna(12);

        titulo("Empresa Contratada");
        br();

        $grid->fechaColuna();

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

            $grid->abreColuna($item[1]);
            p("{$label}:", "contratoLabel");
            p($valor, "contratoConteudo");
            $grid->fechaColuna();
        }
        $grid->fechaGrid();

        $painel->fecha();

        # Editar
        $div = new Div("divEdita1Comissao");
        $div->abre();

        $div = new Div("divEdita2");
        $div->abre();

        $botaoEditar = new Link("Editar", "cadastroEmpresa.php?fase=editar&id={$idEmpresa}");
        $botaoEditar->set_class('tiny button secondary');
        $botaoEditar->set_title('Editar situação');
        $botaoEditar->show();

        $div->fecha();
        $div->fecha();
    }

    ##############################################################

    public function getNumContratos($idEmpresa = null)
    {
        # Joga o valor informado para a variável da classe
        if (!vazio($idEmpresa)) {
            $this->idEmpresa = $idEmpresa;
        }

        # Conecta ao Banco de Dados
        $contratos = new Contratos();

        # Verifica se foi informado
        if (vazio($this->idEmpresa)) {
            alert("É necessário informar o id da Empresa.");
            return;
        }

        # Pega os dados
        $select = 'SELECT idEmpresa
                 FROM tbcontrato
                WHERE idEmpresa = ' . $this->idEmpresa;

        $numero = $contratos->count($select, false);

        # Retorno
        return $numero;
    }

}
