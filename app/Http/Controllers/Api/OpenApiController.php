<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Excel Import API",
 *     description="API for importing Excel files and tracking import tasks."
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="Base URL"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApiController extends Controller
{
    // Annotations-only controller for Swagger.
}
