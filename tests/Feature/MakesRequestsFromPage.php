<?php

declare(strict_types=1);

namespace Tests\Feature;

trait MakesRequestsFromPage
{
    protected function fromPage(string $uri): Auth\RegisterTest|Auth\ResetPasswordTest|Auth\ForgotPasswordTest|Auth\LoginTest
    {
        return $this->withServerVariables(['HTTP_REFERER' => $uri]);
    }
}
