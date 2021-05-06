<?php

namespace App\OpenApi\Responses\V1;

use App\OpenApi\Schemas\DomainSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class DomainResponse extends ResponseFactory
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            DomainSchema::ref('data')
        );
        return Response::ok()->content(MediaType::json()->schema($response));
    }
}
