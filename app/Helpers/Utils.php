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

    public static function translateKpiData(array $data, array $translations): array
    {
        $translated = [];

        foreach ($data as $key => $value) {
            $label = $translations[$key] ?? $key;
            $translated[$label] = $value;
        }

        return $translated;
    }
}
