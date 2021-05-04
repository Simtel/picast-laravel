<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DomainResourse;
use App\Models\Domain;


class DomainsController extends Controller
{
    public function index()
    {

        $domains = Domain::whereUserId(Auth()->id())->get();

        return DomainResourse::collection($domains);
    }
}
