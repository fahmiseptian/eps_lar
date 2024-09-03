<?php

namespace App\Libraries;


class Fungsi
{
// get seoname

    public function getSeoName($firstname, $lastname = null) {

        $nama = $firstname . ' ' . $lastname;

        $seoname =  strtolower(preg_replace('/-+/', '-', preg_replace('/[^\wáéíóú]/', '-', $nama)));

        return $seoname;
    }
}
