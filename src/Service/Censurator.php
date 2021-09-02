<?php

namespace App\Service;

class Censurator 
{

    const CENSORED = [
        'merde',
        'putain'
    ];

    public function purify(string $text)
    {
        foreach(self::CENSORED as $c) {

            $bip = '*bip*';
            $text = str_ireplace($c, $bip, $text);
        }

        return $text;
    }
}