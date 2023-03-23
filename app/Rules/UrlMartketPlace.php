<?php

namespace App\Rules;

use App\Models\MarketPlaces;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

class UrlMartketPlace implements Rule
{
    /**
     * @var Collection<int, MarketPlaces>
     **/
    private Collection $marketplaces;

    /**
     * @var string
     */
    private string $message = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->marketplaces = MarketPlaces::all();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!is_iterable($value)) {
            return false;
        }
        foreach ($value as $market_id => $val) {
            if (!empty($val) && $this->checkUrl($val, $market_id) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $url
     * @param int $market_id
     *
     * @return bool
     */
    private function checkUrl(string $url, int $market_id): bool
    {
        /** @var MarketPlaces|null $market */
        $market = $this->marketplaces->firstWhere('id', $market_id);
        if ($market === null) {
            $this->message = 'Неверный идентификатор магазина';
            return false;
        }
        if (mb_stripos($url, $market->url) !== 0) {
            $this->message = 'Неверный адрес товара для ' . $market->name;
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }
}
