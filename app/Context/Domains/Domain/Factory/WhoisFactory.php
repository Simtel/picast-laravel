<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Factory;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Whois>
 */
final class WhoisFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Whois>
     */
    protected $model = Whois::class;

    /**
     * Define the model's default state.
     *
     * @return array{domain_id: Closure, text:string}
     */
    public function definition(): array
    {

        return [
            'domain_id' => static fn () => Domain::factory()->create()->first()->id,
            'text'      => '% TCI Whois Service. Terms of use:
% https://tcinet.ru/documents/whois_ru_rf.pdf (in Russian)
% https://tcinet.ru/documents/whois_su.pdf (in Russian)

domain: PROSF.RU
nserver: ns1.selectel.org.
nserver: ns2.selectel.org.
nserver: ns3.selectel.org.
nserver: ns4.selectel.org.
state: REGISTERED, DELEGATED, VERIFIED
person: Private Person
registrar: REGTIME-RU
admin-contact: https://whois.webnames.ru
created: 2010-08-18T13:55:55Z
paid-till: 2025-08-18T14:55:55Z
free-date: 2025-09-18
source: TCI

Last updated on 2024-08-07T20:56:30Z',
        ];
    }
}
