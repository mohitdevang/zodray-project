<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Checkout and Payment API",
 *     version="1.0.0",
 *     description="API documentation for Checkout and Payment system"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Tag(name="Checkout", description="Checkout operations")
 * @OA\Tag(name="Payment", description="Payment operations")
 * @OA\Tag(name="Authentication", description="Authentication operations")
 */
abstract class Controller
{
    //
}
