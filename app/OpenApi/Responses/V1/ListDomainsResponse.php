<?php

declare(strict_types=1);

namespace App\OpenApi\Responses\V1;

use App\OpenApi\Schemas\DomainSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ListDomainsResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            Schema::array('data')->items(DomainSchema::ref())
        );
        return Response::ok()->content(MediaType::json()->schema($response));
    }
}
