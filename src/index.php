<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// ____ Load Composer Dependencies
require __DIR__ . '/../vendor/autoload.php';

// ____ Add Config Settings
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = '1234';
$config['db']['dbname'] = 'slim-api';

// ____ Create Slim App
$app = new \Slim\App(['settings' => $config]);

// ____ Add Dependency Injection Container
$container = $app->getContainer();

// ____ Create Logger dependency
$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('slim');
  $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
  $logger->pushHandler($file_handler);
  return $logger;
};

// ____ Create Database Connection Dependency
$container['db'] = function ($c) {
  $db = $c['settings']['db'];
  $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  return $pdo;
};

// ____ Routes
$app->get('/companies', function (Request $request, Response $response) {
  $this->logger->addInfo('Company List');
  $mapper = new CompanyMapper($this->db);
  $companies = $mapper->getCompanies();

  $response->getBody()->write(var_export($companies, true));
  return $response;
});

$app->get('/companies/{id}', function (Request $request, Response $response, $args) {
  $company_id = (int)$args['id'];
  $mapper = new CompanyMapper($this->db);
  $company = $mapper->getCompanyById($company_id);

  $response->getBody()->write(var_export($company, true));
  return $response;
});

$app->post('/companies', function (Request $request, Response $response) {
  $data = $request->getParsedBody();
  $company_data = [];
  $company_data['name'] = filter_var($data['name'], FILTER_SANITIZE_STRING);
  $company_data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING);
  $company_data['address'] = filter_var($data['address'], FILTER_SANITIZE_STRING);

  $company = new CompanyEntity($company_data);
  $mapper = new CompanyMapper($this->db);
  $mapper->createCompany($company);

  $response->getBody()->write('Company Created');
  return $response;
});

$app->delete('/companies/{id}', function (Request $request, Response $response, $args) {
  $company_id = (int)$args['id'];
  $mapper = new CompanyMapper($this->db);
  $mapper->deleteCompanyById($company_id);

  $response->getBody()->write('Company Deleted');
  return $response;
});

// ____ Start Slim App
$app->run();
