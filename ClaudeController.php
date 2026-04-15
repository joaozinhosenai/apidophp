<?php

// =============================================================
//  app/controllers/ClaudeController.php  –  CRUD Controller
// =============================================================

require_once __DIR__ . '/../models/ClaudeModel.php';
require_once __DIR__ . '/../core/Response.php';

class ClaudeController
{
    private ClaudeModel $model;

    public function __construct()
    {
        $this->model = new ClaudeModel();
    }

    // ----------------------------------------------------------
    //  GET /claude
    //  Retorna todos os registros
    // ----------------------------------------------------------
    public function index(): void
    {
        $records = $this->model->findAll();

        Response::success(
            $records,
            count($records) . ' registro(s) encontrado(s).',
            200
        );
    }

    // ----------------------------------------------------------
    //  GET /claude/:id
    //  Retorna um único registro por ID
    // ----------------------------------------------------------
    public function show(array $params): void
    {
        $id = $this->parseId($params['id'] ?? null);

        $record = $this->model->findById($id);

        if (!$record) {
            Response::error("Registro com id={$id} não encontrado.", 404);
        }

        Response::success($record, 'Registro encontrado.', 200);
    }

    // ----------------------------------------------------------
    //  POST /claude
    //  Cria novo registro
    // ----------------------------------------------------------
    public function store(): void
    {
        $data = $this->getJsonBody();

        $errors = $this->model->validate($data, requireAll: true);
        if (!empty($errors)) {
            Response::error('Dados inválidos.', 422, $errors);
        }

        $created = $this->model->create($data);

        if (!$created) {
            Response::error('Erro ao criar o registro.', 500);
        }

        Response::success($created, 'Registro criado com sucesso.', 201);
    }

    // ----------------------------------------------------------
    //  PUT /claude/:id
    //  Atualiza registro existente (aceita atualização parcial)
    // ----------------------------------------------------------
    public function update(array $params): void
    {
        $id   = $this->parseId($params['id'] ?? null);
        $data = $this->getJsonBody();

        // Verifica existência antes de validar
        if (!$this->model->findById($id)) {
            Response::error("Registro com id={$id} não encontrado.", 404);
        }

        $errors = $this->model->validate($data, requireAll: false);
        if (!empty($errors)) {
            Response::error('Dados inválidos.', 422, $errors);
        }

        if (empty($data)) {
            Response::error('Nenhum campo para atualizar foi enviado.', 400);
        }

        $updated = $this->model->update($id, $data);

        if (!$updated) {
            Response::error('Nenhum dado alterado ou erro ao atualizar.', 400);
        }

        Response::success($updated, 'Registro atualizado com sucesso.', 200);
    }

    // ----------------------------------------------------------
    //  DELETE /claude/:id
    //  Remove registro
    // ----------------------------------------------------------
    public function destroy(array $params): void
    {
        $id = $this->parseId($params['id'] ?? null);

        if (!$this->model->findById($id)) {
            Response::error("Registro com id={$id} não encontrado.", 404);
        }

        $deleted = $this->model->delete($id);

        if (!$deleted) {
            Response::error('Erro ao remover o registro.', 500);
        }

        Response::success(null, "Registro com id={$id} removido com sucesso.", 200);
    }

    // ----------------------------------------------------------
    //  Helpers privados
    // ----------------------------------------------------------

    /** Lê e decodifica o body JSON da requisição */
    private function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');

        if (empty($raw)) return [];

        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error('Body JSON inválido: ' . json_last_error_msg(), 400);
        }

        return $data ?? [];
    }

    /** Valida e converte o parâmetro :id para inteiro */
    private function parseId(mixed $raw): int
    {
        if ($raw === null || !ctype_digit((string) $raw) || (int) $raw <= 0) {
            Response::error("Parâmetro 'id' inválido. Deve ser um inteiro positivo.", 400);
        }
        return (int) $raw;
    }
}