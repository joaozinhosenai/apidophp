<?php

// =============================================================
//  public/index.php  –  Front Controller (ponto de entrada)
// =============================================================

declare(strict_types=1);

// Garante que toda resposta seja JSON
header('Content-Type: application/json; charset=utf-8');

// Autoload manual (sem Composer para manter o projeto portátil)
$autoloadPaths = [
    __DIR__ . '/../app/core/Database.php',
    __DIR__ . '/../app/core/Router.php',
    __DIR__ . '/../app/core/Response.php',
    __DIR__ . '/../app/models/ClaudeModel.php',
    __DIR__ . '/../app/controllers/ClaudeController.php',
];

foreach ($autoloadPaths as $file) {
    require_once $file;
}

// ------------------------------------------------------------------
//  Instancia o roteador e registra as rotas
// ------------------------------------------------------------------
$router = new Router();

// ── Rota de boas-vindas / health-check ────────────────────────────
$router->get('/', function () {
    Response::success(
        ['version' => '1.0.0', 'timestamp' => date('Y-m-d H:i:s')],
        'API REST - PHP MVC | MariaDB'
    );
});

// ── Rotas do recurso /claude ───────────────────────────────────────

/**
 * GET    /claude        → lista todos
 * GET    /claude/:id    → detalha um
 * POST   /claude        → cria novo
 * PUT    /claude/:id    → atualiza
 * DELETE /claude/:id    → remove
 */

$router->get('/claude', function () {
    (new ClaudeController())->index();
});

$router->get('/claude/:id', function (array $params) {
    (new ClaudeController())->show($params);
});

$router->post('/claude', function () {
    (new ClaudeController())->store();
});

$router->put('/claude/:id', function (array $params) {
    (new ClaudeController())->update($params);
});

$router->delete('/claude/:id', function (array $params) {
    (new ClaudeController())->destroy($params);
});

// ------------------------------------------------------------------
//  Despacha
// ------------------------------------------------------------------
$router->dispatch();