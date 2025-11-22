<?php

namespace App\Helpers;

class Utils
{
    public static function clearMdSyntax($text)
    {
        $text = preg_replace('/^```json/', '', $text);
        $text = preg_replace('/^```/', '', $text);
        $text = preg_replace('/```$/', '', $text);
        return $text;
    }
}
