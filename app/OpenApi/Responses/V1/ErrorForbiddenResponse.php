<?php

declare(strict_types=1);

namespace App\OpenApi\Responses\V1;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorForbiddenResponse extends ResponseFactory
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            Schema::string('message')->example('Unauthenticated')
        );
        return Response::unauthorized()->content(MediaType::json()->schema($response))->description('Unauthenticated');
    }
}
