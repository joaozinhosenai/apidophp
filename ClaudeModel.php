<?php

// =============================================================
//  app/models/ClaudeModel.php  –  Model para tabela `claude`
// =============================================================
//
//  Estrutura da tabela:
//    id            INT AUTO_INCREMENT PK
//    peso          DECIMAL(5,2)          kg
//    altura        DECIMAL(4,2)          m
//    cor_cabelo    VARCHAR(50)
//    cor_olho      VARCHAR(50)
//    created_at    DATETIME
//    updated_at    DATETIME
// =============================================================

require_once __DIR__ . '/../core/Database.php';

class ClaudeModel
{
    private PDO $db;
    private string $table = 'claude';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ----------------------------------------------------------
    //  GET ALL  –  retorna todos os registros
    // ----------------------------------------------------------
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id");
        return $stmt->fetchAll();
    }

    // ----------------------------------------------------------
    //  GET ONE  –  retorna um registro por id
    // ----------------------------------------------------------
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ----------------------------------------------------------
    //  POST  –  insere novo registro
    // ----------------------------------------------------------
    public function create(array $data): array|false
    {
        $sql = "INSERT INTO {$this->table}
                    (peso, altura, cor_cabelo, cor_olho, created_at, updated_at)
                VALUES
                    (:peso, :altura, :cor_cabelo, :cor_olho, NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':peso'       => $data['peso'],
            ':altura'     => $data['altura'],
            ':cor_cabelo' => $data['cor_cabelo'],
            ':cor_olho'   => $data['cor_olho'],
        ]);

        $newId = (int) $this->db->lastInsertId();
        return $this->findById($newId);
    }

    // ----------------------------------------------------------
    //  PUT  –  atualiza registro existente
    // ----------------------------------------------------------
    public function update(int $id, array $data): array|false
    {
        // Somente atualiza campos enviados (PATCH-style dentro do PUT)
        $allowed = ['peso', 'altura', 'cor_cabelo', 'cor_olho'];
        $sets    = [];
        $params  = [':id' => $id];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[]           = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($sets)) return false;

        $sets[] = 'updated_at = NOW()';
        $sql    = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0 ? $this->findById($id) : false;
    }

    // ----------------------------------------------------------
    //  DELETE  –  remove registro
    // ----------------------------------------------------------
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // ----------------------------------------------------------
    //  Validação dos dados de entrada
    // ----------------------------------------------------------
    public function validate(array $data, bool $requireAll = true): array
    {
        $errors   = [];
        $required = ['peso', 'altura', 'cor_cabelo', 'cor_olho'];

        if ($requireAll) {
            foreach ($required as $field) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    $errors[] = "Campo '{$field}' é obrigatório.";
                }
            }
        }

        if (isset($data['peso']) && $data['peso'] !== '') {
            if (!is_numeric($data['peso']) || $data['peso'] <= 0 || $data['peso'] > 500) {
                $errors[] = "Campo 'peso' deve ser numérico entre 0.01 e 500 (kg).";
            }
        }

        if (isset($data['altura']) && $data['altura'] !== '') {
            if (!is_numeric($data['altura']) || $data['altura'] <= 0 || $data['altura'] > 3) {
                $errors[] = "Campo 'altura' deve ser numérico entre 0.01 e 3.00 (m).";
            }
        }

        if (isset($data['cor_cabelo']) && strlen($data['cor_cabelo']) > 50) {
            $errors[] = "Campo 'cor_cabelo' deve ter no máximo 50 caracteres.";
        }

        if (isset($data['cor_olho']) && strlen($data['cor_olho']) > 50) {
            $errors[] = "Campo 'cor_olho' deve ter no máximo 50 caracteres.";
        }

        return $errors;
    }
}