<?php

namespace App\Helpers;

class AnswerHelper
{
    public static function low($str)
    {
        return mb_strtolower(preg_replace( "/[^a-zA-ZА-Яа-я0-9]/ui", '', $str));
    }
}
