<?php

namespace App\OpenApi\Responses\V1;

use App\OpenApi\Schemas\DomainSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class DomainResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::ok()->content(MediaType::json()->schema(DomainSchema::ref()));
    }
}
