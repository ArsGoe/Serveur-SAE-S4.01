<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0",
    description: "Api server documention for TicketMain",
    title: "Api server TicketMain",
)]
#[OA\Server(url: "http://localhost:8000/api", description: "Server doc")]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', bearerFormat: 'JWT', scheme: 'bearer')]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
