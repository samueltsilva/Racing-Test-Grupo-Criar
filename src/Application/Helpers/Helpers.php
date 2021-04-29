<?php

namespace App\Application\Helpers;

class Helpers
{

    public static function soLetra(string $str): string
    {
        return trim(preg_replace("/[^A-Za-z]/", " ", $str));
    }

    public static function soNumero($str)
    {
        return intval(preg_replace("/[^0-9]/", "", $str));
    }

    public static function somarHoras($tempoProva)
    {
        $minutos = 0;
        $milisegundo = 0;
        $segundos = 0;
        $milisegundos = 0;

        foreach ($tempoProva as $tempo) {

            list($minuto, $segundo) = explode(':', $tempo);
            $milisegundo += explode(".", $tempo)[1];
            $minutos += $segundo / 60;
            $minutos += $minuto;
            $segundos += $milisegundo / 1000;
            $segundos  += $segundo;
            $milisegundos += $milisegundo;
        }

        $milisegundos = fmod($milisegundos, 1000);
        $segundos = round($segundos);
        $minutos = round($minutos);

        return sprintf('%d:%d.%d', $minutos, $segundos, $milisegundos);
    }

    public static function formatarTempo($tempo)
    {
        $tempo =  explode('.', $tempo);

        $segundos = $tempo[0];
        $milisegundo  = $tempo[1];

        $minutos = $segundos >= 60 ?  $segundos / 60 : 0 ;
       
        $segundos += fmod($segundos, 60) + $milisegundo >= 1000 ? $milisegundo / 1000 : 0;
  
        return sprintf('%d:%d.%d', $minutos, $segundos, fmod($milisegundo, 1000));
    }
}
