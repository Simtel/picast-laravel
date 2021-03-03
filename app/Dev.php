<?php


namespace App;


use App\Models\Images;

/**
 * Class Dev
 * @package App
 */
class Dev
{
    /**
     *
     */
    public static function execute(): void
    {
        dump(Images::find(1));
    }
}