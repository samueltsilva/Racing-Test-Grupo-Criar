<?php

namespace App\Application\Services;

use App\Application\Helpers\Helpers;
use App\Application\Interfaces\Service\RanqueaCorridaService;

class RanqueaCorridaServiceImpl implements RanqueaCorridaService
{

    public function ranquearVolta(array $voltas)
    {
        //captura o primeiro piloto do array
        $idPrimeiroPiloto = array_key_first($voltas);

        //seta ele como a melhor volta da corrida
        $melhorVoltaCorrida = $voltas[$idPrimeiroPiloto][0];
        $melhorVoltaPiloto = [];

        //varre o array de voltas do  piloto
        foreach ($voltas as $voltasPiloto) {

            //retorna a melhor volta do piloto
            $auxMelhorVolta = $this->melhorVolta($voltasPiloto, $velMedia, $tempoProva);

            //compara com a melhor volta atualmente, se a atual for maior ela recebe a menor
            if ($melhorVoltaCorrida['tempoVolta'] > $auxMelhorVolta['tempoVolta']) {
                $melhorVoltaCorrida = $auxMelhorVolta;
            }

            //retorna a velocidade media total até então deste piloto
            $auxMelhorVolta['velociadeMediaTotal'] = Helpers::formatarTempo($velMedia);

            //vai somando o tempo das voltas, menor tempo é o vencedor, (levando em conta numero de voltas)
            $auxMelhorVolta['tempoTotalProva'] = Helpers::somarHoras($tempoProva);

            // calcula a diferença do melhor tempo total da prova sobre as demais
            if (count($melhorVoltaPiloto) > 0) {

                $difencaParaPrimeiroColocado = $this->calcularDiferenca(
                    $auxMelhorVolta['tempoTotalProva'],
                    $melhorVoltaPiloto[0]['tempoTotalProva']
                );
                //retorna a diferença de forma mais amigavel para o usuario
                $auxMelhorVolta['tempoAtrasPrimeiroColado'] = Helpers::formatarTempo($difencaParaPrimeiroColocado);
            }

            //retorna as voltas
            array_push($melhorVoltaPiloto, $auxMelhorVolta);
        }

        //retorna os arrays com os dados
        return array(
            'melhorVoltaCorrida' => $melhorVoltaCorrida,
            'melhorVoltaPiloto' => $melhorVoltaPiloto
        );
    }

    //calcula melhor volta do piloto
    private function melhorVolta($voltasPiloto, &$velMedia, &$tempoProva): array
    {
        $min = $voltasPiloto[0];
        $tempoProva = [];
        $velMedia = 0;
        $media = 0;

        foreach ($voltasPiloto as $volta) {

            if (Helpers::soNumero($min['tempoVolta']) >  Helpers::soNumero($volta['tempoVolta'])) {
                $min = $volta;
            }

            array_push($tempoProva, $volta['tempoVolta']);

            $velocidadeMediaVolta = floatval(str_replace(',', '.', $volta['velocidadeMedia']));
            $media = $media + $velocidadeMediaVolta;
        }

        $velMedia = $media / count($voltasPiloto);
        return $min;
    }

    private function calcularDiferenca($primeroValor, $segundoValor)
    {
        $primeroValor = str_replace(',', '.', str_replace(':', '', $primeroValor));
        $segundoValor = str_replace(',', '.', str_replace(':', '', $segundoValor));
        return floatval($primeroValor) - floatval($segundoValor);
    }
}
