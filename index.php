<?php

use Aura\Router\RouterFactory;
use Zend\Expressive\AppFactory;
use Zend\Expressive\Router\Aura as AuraBridge;
use Zend\Diactoros\Response\JsonResponse;
use Api\Auth;

require __DIR__.'/config.inc.php';
$loader = require __DIR__.'/vendor/autoload.php';
$loader->add('Api', __DIR__.'/src');

$auraRouter = (new RouterFactory())->newInstance();
$router = new AuraBridge($auraRouter);
$api = AppFactory::create(null, $router);

// Conexão com o banco MySQL
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASSWORD);

/**
 * API ENDPOINT
 * GET /veiculos
 * Retorna todos os veículos
 */
$api->get('/veiculos', function ($request, $response, $next) use ($db) {
    $stmt = $db->prepare('SELECT id, veiculo, marca, ano, updated FROM veiculos');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = new JsonResponse($results, $response->getStatusCode());
    return $response;
});

/**
 * API ENDPOINT
 * GET /veiculos/find
 * Retorna os veículos de acordo com o termo passado como parâmetro
 */
$api->get('/veiculos/find', function ($request, $response, $next) use ($db) {
    $params = $request->getQueryParams();
    $q = $params['q'];
    $stmt = $db->prepare("SELECT id, veiculo, marca, ano FROM veiculos WHERE veiculo LIKE '%$q%' OR marca LIKE '%$q%' OR descricao LIKE '%$q%'");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = new JsonResponse($results, $response->getStatusCode());
    return $response;
});

/**
 * API ENDPOINT
 * GET /veiculos/{:id}
 * Retorna os detalhes do veículo
 */
$api->get('/veiculos/{id}', function ($request, $response, $next) use ($db) {
    $id = $request->getAttribute('id');
    $stmt = $db->prepare('SELECT * FROM veiculos WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = new JsonResponse($results, $response->getStatusCode());
    return $response;
});

/**
 * API ENDPOINT
 * POST /veiculos
 * Adiciona um novo veículo
 */
$api->post('/veiculos', function ($request, $response, $next) use ($db) {
    $data = $request->getParsedBody();
    if (!isset($data['veiculo']) || !isset($data['marca']) || !isset($data['ano']) || !isset($data['descricao'])) {
        return new JsonResponse('Missing parameters', 400);
    }
    $stmt = $db->prepare('INSERT INTO veiculos (veiculo, marca, ano, descricao, vendido, imagem) values (:veiculo, :marca, :ano, :descricao, :vendido, :imagem)');
    $stmt->bindParam(':veiculo',$data['veiculo']);
    $stmt->bindParam(':marca',$data['marca']);
    $stmt->bindParam(':ano',$data['ano']);
    $stmt->bindParam(':descricao',$data['descricao']);
    $stmt->bindParam(':vendido',$data['vendido']);
    $stmt->bindParam(':imagem',$data['imagem']);
    $stmt->execute();
    $data['id'] = $db->lastInsertId();
    $response = new JsonResponse($data, $response->getStatusCode());
    return $response;
});

/**
 * API ENDPOINT
 * PUT /veiculos/{:id}
 * Atualiza os dados de um veículo
 */
// Enviado como POST, pq o PHP não recebe os dados do form com PUT (https://bugs.php.net/bug.php?id=55815)
$api->post('/veiculos/{id}', function ($request, $response, $next) use ($db) {
    $data = $request->getParsedBody();
    $data['id'] = $request->getAttribute('id');
    if (!isset($data['veiculo']) || !isset($data['marca']) || !isset($data['ano']) || !isset($data['descricao'])) {
        return new JsonResponse('Missing parameters', 400);
    }
    $stmt = $db->prepare('UPDATE veiculos SET veiculo = :veiculo, marca = :marca, ano = :ano, descricao = :descricao, vendido = :vendido, imagem = :imagem, updated = CURRENT_TIME() WHERE veiculos.id = :id');
    $stmt->bindParam(':veiculo',$data['veiculo']);
    $stmt->bindParam(':marca',$data['marca']);
    $stmt->bindParam(':ano',$data['ano']);
    $stmt->bindParam(':descricao',$data['descricao']);
    $stmt->bindParam(':vendido',$data['vendido']);
    $stmt->bindParam(':imagem',$data['imagem']);
    $stmt->bindParam(':id',$data['id']);
    $stmt->execute();
    $response = new JsonResponse($data, $response->getStatusCode());
    return $response;
});

/**
 * API ENDPOINT
 * DELETE /veiculos/{:id}
 * Apaga o veículo
 */
$api->delete('/veiculos/{id}', function ($request, $response, $next) use ($db) {
    $id = $request->getAttribute('id');
    $stmt = $db->prepare('DELETE FROM veiculos WHERE veiculos.id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $response = new JsonResponse($id, $response->getStatusCode());
    return $response;
});

$app = AppFactory::create();
$app->pipe(new Auth());
$app->pipe($api);
$app->run();