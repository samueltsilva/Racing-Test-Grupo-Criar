<?php

namespace App\Application\Services;

use App\Application\Helpers\Helpers;
use App\Application\Interfaces\Service\ProcessaCorridaService;
use App\Application\Interfaces\Service\RanqueaCorridaService;


class ProcessaCorridaServiceImpl implements ProcessaCorridaService
{
    private $ranqueaVoltaService;

    public function __construct(RanqueaCorridaService $ranqueaVoltaService)
    {
        $this->ranqueaVoltaService = $ranqueaVoltaService;
    }

    //Abri o arquivo e retornar dados
    public function processarCorrida()
    {
        $delimitador = ';';
        $cerca = '"';
        $ordenaVoltas = [];
        $detalhamentoVoltas = [];
        $f = fopen('../temp/' . date('dmY') . '.csv', 'r');
        if ($f) {

            // Ler cabecalho do arquivo
            $cabecalho = fgetcsv($f, 0, $delimitador, $cerca);

            // Enquanto nao terminar o arquivo
            while (!feof($f)) {

                // Ler uma linha do arquivo
                $linha = fgetcsv($f, 0, $delimitador, $cerca);
                if (!$linha) {
                    continue;
                }

                //retorna apenas o identificador através da linha
                $id = Helpers::soNumero($linha[1]);

                //formata as linhas para ficar mais facil de debugar o array
                $dadosVolta = $this->formatarLinhasArray($linha);

                //ordena as voltas por id ou seja um piloto -> varias voltas
                $ordenaVoltas[$id][] = $dadosVolta;

                array_push($detalhamentoVoltas, $dadosVolta);
            }

            // junta o array  de retorno com os dados gerais da corrida
            return array_merge(
                array(
                    'detalhamentoVoltas' => $detalhamentoVoltas
                ),

                //retorna os dados propostos no pdf
                $this->ranqueaVoltaService->ranquearVolta($ordenaVoltas)
            );
        }
    }

    //formata as linhas para ficar mais facil de debugar o array
    private function formatarLinhasArray(array $volta)
    {
        $arrayFormatado = [];
        $arrayFormatado['tempo'] = trim($volta[0]);
        $arrayFormatado['id'] = Helpers::soNumero($volta[1]);
        $arrayFormatado['nome'] = Helpers::soLetra($volta[1]);
        $arrayFormatado['volta'] = trim($volta[2]);
        $arrayFormatado['tempoVolta'] = trim($volta[3]);
        $arrayFormatado['velocidadeMedia'] = trim($volta[4]);
        return $arrayFormatado;
    }
}
