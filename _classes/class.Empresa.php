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
    private $permiteEditar = true;
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
    public function get_dados($idEmpresa = null)
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
    public function get_razaoSocial($idEmpresa)
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
    public function get_telefones($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados = $this->get_dados($idEmpresa);
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
    public function get_emails($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados = $this->get_dados($idEmpresa);
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
    public function get_contatos($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados = $this->get_dados($idEmpresa);
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
    public function get_empresaCnpj($idEmpresa)
    {

        # Função que retorna os telefones do servidor cadastrado no sistema
        #
        # Parâmetro: id do servidor

        $dados = $this->get_dados($idEmpresa);
        
        $retorno = plm($dados["razaoSocial"]);

        if (!empty($dados["cnpj"])) {
            $retorno .= "<br/> CNPJ: {$dados["cnpj"]}";
        }

        return $retorno;
    }

    ##########################################################################################
}
