<?php


namespace App\Http\Controllers\Helpers;


use Illuminate\Validation\Rules\Enum;

class MatchDetailsType extends Enum
{
    const Goal = 'Goal';
    const RedCard = 'RedCard';
    const YellowCard = 'YellowCard';
}
