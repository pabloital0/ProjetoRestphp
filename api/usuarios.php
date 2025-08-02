<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$method = $_SERVER['REQUEST_METHOD'];
$arquivo = '../db.json';

if (!file_exists($arquivo)) {
    file_put_contents($arquivo, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$dados = json_decode(file_get_contents($arquivo), true);

function salvar($arquivo, $dados) {
    file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
switch ($method) {
    case 'GET':
        echo json_encode($dados);
        break;

    case 'POST':
        $entrada = json_decode(file_get_contents('php://input'), true);
        if (!isset($entrada['nome']) || trim($entrada['nome']) === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome é obrigatório']);
            exit;
        }

        $ultimoId = !empty($dados) ? end($dados)['id'] : 0;

        $novo = [
            'id' => $ultimoId + 1,
            'nome' => $entrada['nome']
        ];

        $dados[] = $novo;
        salvar($arquivo, $dados);

        echo json_encode($novo);
        break;

    case 'PUT':
        $entrada = json_decode(file_get_contents("php://input"), true);
        if (!isset($entrada['id'], $entrada['nome'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados incompletos']);
            exit;
        }

        foreach ($dados as &$usuario) {
            if ($usuario['id'] === (int)$entrada['id']) {
                $usuario['nome'] = $entrada['nome'];
                salvar($arquivo, $dados);
                echo json_encode(['mensagem' => 'Usuário atualizado']);
                exit;
            }
        }

        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado']);
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $entrada);
        if (!isset($entrada['id'])) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID não informado']);
            exit;
        }

        $id = (int)$entrada['id'];
        $dados = array_filter($dados, fn($u) => $u['id'] !== $id);
        salvar($arquivo, array_values($dados)); // Reindexar

        echo json_encode(['mensagem' => 'Usuário excluído']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não permitido']);
        break;
}
