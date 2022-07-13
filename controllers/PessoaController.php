<?php

require "../models/Pessoa.php";

class PessoaController
{
    private $rota = null;
    public $request = null;
    protected $pessoaModel = null;

    public function __construct()
    {
        $this->pessoaModel = new Pessoa();
        $this->request = $_REQUEST;
        $this->rota = $this->request['rota'] ?? ""; // se não tiver a palavra rota nos parâmetros da URL informamos vazio.
    }

    // retorna a rota para sabermos qual função utilizar (dadosPessoas | obterDadosPessoa | excluirPessoa...).
    public function getRota()
    {
        return $this->rota;
    }

    // desconectamos do banco de dados pelo model.
    public function desconectarModel()
    {
        $this->pessoaModel->desconectar();
    }

    // setamos o retorno para o front no formato JSON (javascript - objeto)
    public function setResponseAPI($dados)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados);
        exit();
    }

    // obtém todos os Pessoas
    public function listarPessoas()
    {
        $dados = [];

        $result = $this->pessoaModel->read_all();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $dados[] = $row;
            }
        }

        $this->setResponseAPI($dados);
    }

    // obtém dados de 1 Pessoa (EDITAR).
    public function obterDadosPessoa()
    {
        $idPessoa = $this->request["id"] ?? 0;

        $dados = [];

        if (!empty($idPessoa) && is_numeric($idPessoa)) {
            $result = $this->pessoaModel->read($idPessoa);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $dados[] = $row;
                }
            }
        }

        $this->setResponseAPI($dados);
    }

    // apenas mudamos o status o Pessoa para inativo para dizermos que ele está excluído.
    public function excluirPessoa()
    {
        $idPessoa = $this->request["id"] ?? 0;

        $dados = [
            "error" => 500,
            "mensagem" => "Não foi possível excluir o Pessoa. Contate o administrados!"
        ];

        if (!empty($idPessoa) && is_numeric($idPessoa)) {
            $result = $this->pessoaModel->delete($idPessoa);

            if ($result) {
                $dados = [
                    "success" => 201,
                    "mensagem" => "Pessoa excluída."
                ];
            }
        }

        $this->setResponseAPI($dados);
    }

    public function salvarAtualizarPessoa()
    {
        $dados = [
            "error" => 500,
            "mensagem" => "Não foi possível salvar o Pessoa. Contate o administrados!"
        ];

        $idPessoa = $this->request["id"] ?? 0;
        $nome = $this->request["nome"] ?? "";
        $Pessoa = $this->request["Pessoa"] ?? "";
        $email = $this->request["email"] ?? "";
        $senha = $this->request["senha"] ?? "";
        $status = $this->request["status"] ?? 0;
        $email_recuperacao = $this->request["email_recuperacao"] ?? "";

        // ATUALIZAR
        if (!empty($idPessoa) && is_numeric($idPessoa)) {
            $mensagem = "Pessoa atualizado com sucesso.";

            $result = $this->pessoaModel->atualizar($nome, $Pessoa, $email, $senha, $status, $email_recuperacao, $idPessoa);
        } else {
            $mensagem = "Pessoa cadastrado com sucesso.";

            $result = $this->pessoaModel->cadastrar($nome, $Pessoa, $email, $senha, $status, $email_recuperacao);
            $idPessoa = $result;
        }

        if ($result) {
            $dados = [
                "success" => 201,
                "mensagem" => $mensagem,
                "idPessoa" => $idPessoa,
            ];
        }

        $this->setResponseAPI($dados);
    }
}

// inicializamos (instanciamos) nossa variável (objeto).
$objPessoaController = new PessoaController();

// aqui obtemos nossa rota informada la no frontend (javascript) e conforme a rota informada redirecionamos a ação.
switch ($objPessoaController->getRota()) {
    case "listarTodasPessoas":
            $objPessoaController->listarPessoas();
        break;
    case "editarPessoa":
            $objPessoaController->obterDadosPessoa();
        break;
    case "excluirPessoa":
            $objPessoaController->excluirPessoa();
        break;
    case "salvarAtualizarPessoa":
            $objPessoaController->salvarAtualizarPessoa();
        break;
    default:
            $objPessoaController->setResponseAPI(["erro" => "404", "mensagem" => "Rota inválida ou não encontrada."]);
        break;
}

// após termino execução encerramos a conexão do model com o banco
// $objPessoaController->desconectarModel();
