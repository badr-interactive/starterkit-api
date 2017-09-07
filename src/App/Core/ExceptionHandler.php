<?php

namespace App\Core;

use App\Core\Factories\LoggerFactory;

class ExceptionHandler
{
    public function __invoke($request, $response, $exception)
    {
        $logger = LoggerFactory::create();
        $logger->error('Server Error', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        return $response
            ->withStatus(500)
            ->withJson([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null]);
    }
}
