<?php
// api/index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use DI\ContainerBuilder;
use App\Controllers\JobController;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
$container = $containerBuilder->build();

$job_controller = $container->get(JobController::class);

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

header('Content-Type: application/json');

try {
    $body = [];
    if ($request_method === 'POST') {
        $input = file_get_contents('php://input');
        $body = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON input: " . json_last_error_msg());
        }
    }

    if ($request_uri === '/avature/api/index.php') {
        if ($request_method === 'GET') {
            $job_controller->searchJobOffers($_GET);
        } elseif ($request_method === 'POST') {
            $job_controller->createJobOffer($body);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Not Found"]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
