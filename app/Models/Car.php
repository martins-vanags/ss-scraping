<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

final class Car extends Model
{
    protected function priceInCents(): Attribute
    {
        return Attribute::make(
            get: fn(int $value) => $value / 100,
        );
    }

    protected function specifications(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
        );
    }
}
