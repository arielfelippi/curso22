<?php

require "../database/Conexao.php"; // require - importa uma vez | require_once - importa toda vez que o arquivo pessoas é acessado/chamado.

class Pessoa 
{
    private $conexao = null;
    protected $nomeTabela = "pessoas";

    public $id = 0;
    public $name = "";
    public $CEP = 0;
    public $mail = "";
    public $phone = 0;
    public $socialweb = "";
    public $status = true;

    public function __construct()
    {
        global $conexao; // acessamos (global) a variavel $conexao do arquivo Conexao.php
        $this->conexao = $conexao;
    }

    // desconectamos do banco de dados.
    public function desconect()
    {
        $this->conexao->close();
        $this->conexao = null;
    }

    public function read_all()
    {
        $sql = "SELECT * FROM {$this->nomeTabela} where deleted = false";
        $result = $this->conexao->query($sql);

        return $result ?? [];
    }

    public function read($idUsuario)
    {
        $sql = "SELECT * FROM {$this->nomeTabela} WHERE id={$idUsuario} LIMIT 1";
        $result = $this->conexao->query($sql);

        return $result ?? [];
    }

    public function create($nome, $usuario, $email, $senha, $status, $email_recuperacao)
    {
        $sql = "INSERT
			INTO
				{$this->nomeTabela}
			(
				nome,
				usuario,
				email,
				senha,
				status,
				email_recuperacao
			)
			VALUES(
				'{$nome}',
				'{$usuario}',
				'{$email}',
				'{$senha}',
				'{$status}',
				'{$email_recuperacao}'
			);";

        $result = $this->conexao->query($sql);

        if ($result) {
            return $this->conexao->insert_id; // retorna ultimo id gravado no banco.
        }

        return 0;
    }

    public function update($nome, $usuario, $email, $senha, $status, $email_recuperacao, $idUsuario)
    {
        $sql = "UPDATE
				{$this->nomeTabela}
			SET
				nome = '{$nome}',
				usuario = '{$usuario}',
				email = '{$email}',
				senha = '{$senha}',
				status = '{$status}',
				email_recuperacao = '{$email_recuperacao}'
			WHERE
				id = {$idUsuario};";

        $result = $this->conexao->query($sql);

        if ($result) {
            return $this->conexao->affected_rows; // retorna o numero de linhas atualizadas.
        }

        return 0;
    }

    public function delete($idPessoa)
    {
        # Exclir REAL.
        // $sql = "DELETE FROM {$this->nomeTabela} WHERE id={$idPessoa}";
        // $result = $this->conexao->query($sql);

        # Excluir fake ou Soft Delete

        $sql = "UPDATE
            {$this->nomeTabela}
        SET
            deleted = true
        WHERE
            id = {$idPessoa};";

        $result = $this->conexao->query($sql);

        if ($result) {
            return $this->conexao->affected_rows; // retorna o numero de linhas atualizadas.
        }

        return $result;
    }
}

// $pessoa = new Pessoa();
// $dadosPessoa = [];

// $result = $pessoa->read_all();

// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         $dadosPessoa[] = $row;
//     }
// }

// header('Content-Type: application/json; charset=utf-8');
// echo json_encode($dadosPessoa);
// exit();

