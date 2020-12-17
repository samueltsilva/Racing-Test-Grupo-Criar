<?php

//Extrai as informações de cada linha
function processa_linha($linha){
    return [
        mb_substr($linha, 0, 12),
        mb_substr($linha, 18, 3),
        trim(mb_substr($linha, 24, 26)),
        mb_substr($linha, 58, 1),
        mb_substr($linha, 64, 8),
        mb_substr($linha, 83, 6),
    ];
}

function takePilotIfFirstRun(&$pilots, $volta){
    //A posição 1 da volta armazena o ID do piloto
    $id = $volta[1];

    //Se o piloto não existir no array, insere ele no array com os dados básicos
    //Obs: É passado a referência na memória do vetor de pilotos para não ter que retorná-lo, já que é usado globalmente no algoritmo.
    if(existsPilot($pilots, $id)){
        $pilots[$id] = [
            'posicao' => 0,
            'id' => $id,
            'nome' => $volta[2],
            'melhor_volta' => 0,
            'velocidade_media' => 0,
            'tempo_total' => 0,
            'numero_de_voltas' => 0,
            'hora' => 0,
            'voltas' => [],
            
        ];
    }
}

function processa_volta(&$pilots, $volta){

    $id = $volta[1];
    $piloto = $pilots[$id];

    $_lapTime = stringToTime($volta[4]);
    $_bestLapTime = stringToTime($piloto['melhor_volta']);

    $velocidade_media = $volta[5];

    //Se a volta que está sendo analizada foi mais rápida que a anterior, armazena ela
    if($_bestLapTime != 0) {
        if ($_lapTime < $_bestLapTime) {
            $_bestLap = timeToString($_lapTime);
            $piloto['melhor_volta'] = $_bestLap;
        }
    }else
        $piloto['melhor_volta'] = $volta[4];

    $piloto['numero_de_voltas']++;

    $piloto['voltas'][] = [
        'tempo_volta' => timeToString($_lapTime),
        'velocidade_media' => $velocidade_media
    ];

    $piloto['hora'] = $volta[0];

    $pilots[$id] = $piloto;
}

function processAverages(&$pilots){
    foreach ($pilots as $key => $pilot) {
        $tempo_medio = 0;
        $tempo = 0;
        // soma a velocidade de cada volta
        foreach ($pilot['voltas'] as $k => $volta) {
            $_floatLap  = str_replace(",",".", $volta['velocidade_media']); //Troca a vírgula pelo ponto
            $tempo_medio += floatval($_floatLap);
            $aux = stringToTime($volta['tempo_volta']);
            $tempo += $aux;
        }

        //Divide a soma das velocidades pelo número de voltas
        $floatMedia = $tempo_medio / sizeof($pilot['voltas']);
        $floatMedia = round($floatMedia, 4);

        $pilot['velocidade_media'] = str_replace(".",",", $floatMedia); //Volta do ponto para a vírgula
        $pilot['tempo_total'] =  timeToString($tempo);
        $pilots[$key] = $pilot;

    }
}

//Converte todos os tempos para milissegundos, para comparar qual chegou primeiro
function getPilotsBestLap($pilots){
    
    foreach ($pilots as $piloto) {
        if($piloto['numero_de_voltas'] == 4){
            $posicoes[] = [
                'Tempo_corrida' => stringToTime($piloto['tempo_total'])
                ,'id' => $piloto['id']
            ];
        }elseif($piloto['numero_de_voltas'] == 3){
            $posicoes[] = [
                'Tempo_corrida' => stringToTime($piloto['tempo_total'])
                ,'id' => $piloto['id']
            ];
        }elseif($piloto['numero_de_voltas'] == 2){
            $posicoes[] = [
                'Tempo_corrida' => stringToTime($piloto['tempo_total'])
                ,'id' => $piloto['id']
            ];
        }elseif($piloto['numero_de_voltas'] == 1){
            $posicoes[] = [
                'Tempo_corrida' => stringToTime($piloto['tempo_total'])
                ,'id' => $piloto['id']
            ];
        }
    }
    
    array_multisort($posicoes);

    return $posicoes;
}

function calculaTempo($ordenados){
    $winner = $ordenados[0];
    $hora   = stringToTime($winner['hora'], true);


    foreach ($ordenados as $key => $value) {
        if($winner['id'] != $value['id']){
            $aux = stringToTime($value['hora'], true);
            $sub = $aux - $hora;
            $ordenados[$key]['tempo_recorrente'] = timeToString($sub, true);
        }
    }

    return $ordenados;
}

function melhorVoltaCorrida($ordenados){
    $winner = $ordenados[0];
    $bestLap = [
    	'id' => $winner['id']
        ,'nome' => $winner['nome']
        ,'tempo' => stringToTime($winner['melhor_volta'])
    ];

    foreach($ordenados as $item){
        if($winner['id'] != $item['id']){
            $_time = stringToTime($item['melhor_volta']);
            if($_time < $bestLap['tempo']){
            	$bestLap['id'] = $item['id'];
                $bestLap['nome'] = $item['nome'];
                $bestLap['tempo'] = timeToString($_time);

            }
        } 
    }

    $bestLap['tempo'] = timeToString($bestLap['tempo']);

    foreach ($ordenados as $key => $item) {
    	if($item['id'] == $bestLap['id']){
    		foreach ($item['voltas'] as $index => $value) {
    			if($value['tempo_volta'] == $bestLap['tempo']){
    				$bestLap['volta'] = $index;
    			}
    		}
    	}
    }

    return $bestLap;
}

//Verifica se o piloto existe
function existsPilot($pilots, $id){
    return empty($pilots[$id]);
}

//funcao para converter a string de entrada em unidade de tempo ( milisegundos )
function stringToTime($_time, $hora = false){
    $_miliseconds = 0;
    if($hora){
        $_convertedTime  = str_replace(":",".", $_time);

        $_separated = explode(".",$_convertedTime);
        $_miliseconds = $_separated[0] * 36000000;
        $_miliseconds += $_separated[1] * 60000;
        $_miliseconds += $_separated[2] * 1000;
        $_miliseconds += $_separated[3];
    }else{
        $_convertedTime  = str_replace(":",".", $_time);

        $_separated = explode(".",$_convertedTime);

        $_miliseconds = $_separated[0] * 60000;
        $_miliseconds += $_separated[1] * 1000;
        $_miliseconds += $_separated[2];
    }

   return $_miliseconds;
}

//funcao para converter o tempo em MM:SS.mm
function timeToString($string, $hour = false){
    if($hour){
        $uSec = $string % 1000;
        $string = floor($string / 1000);

        $seconds = $string % 60;
        $string = floor($string / 60);

        $minutes = $string % 60;
        $string = floor($string / 60);

        $hour = $string % 60;
        $string = floor($string / 60);

        $aux = str_pad($hour, 2, '0', STR_PAD_LEFT).":".str_pad($minutes, 2, '0', STR_PAD_LEFT).":".str_pad($seconds, 2, '0', STR_PAD_LEFT).".".str_pad($uSec, 3, '0', STR_PAD_LEFT);
    }else{
        $uSec = $string % 1000;
        $string = floor($string / 1000);

        $seconds = $string % 60;
        $string = floor($string / 60);

        $minutes = $string % 60;
        $string = floor($string / 60);

        $aux = str_pad($minutes, 2, '0', STR_PAD_LEFT).":".str_pad($seconds, 2, '0', STR_PAD_LEFT).".".str_pad($uSec, 3, '0', STR_PAD_LEFT);
    }

    return $aux;
}