<!doctype html>
<?php

require_once 'includes/inputs.php';

//Pega linhas do arquivo e armazena no array $lines
$_raceLog = fopen("corrida.txt", "r");
while(!feof($_raceLog)) {
    $_lines[] = fgets($_raceLog);
}
fclose($_raceLog);

//Principal vetor que vai armazenar todas os pilotos
$pilots = [];

//Foreach para percorrer todas as linhas de voltas
foreach($_lines as $index => $line) {
    $volta = processa_linha($line);
    takePilotIfFirstRun($pilots, $volta);
    processa_volta($pilots, $volta);
}

processAverages($pilots);

$list = getPilotsBestLap($pilots);

//posiciona em ordem de chegada os pilotos no array $ordenados
$ordenados = [];
foreach ($list as $index => $item){
    foreach ($pilots as $pilot) {
        if($item['id'] == $pilot['id']){
            $ordenados[$index] = $pilot;
            $ordenados[$index]['posicao'] = $index + 1;
        }
    }
}

$ordenados = calculaTempo($ordenados);
$bestRaceLap = melhorVoltaCorrida($ordenados);
?>

<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Grupo Criar</title>
    </head>

    <body>
        <div class="col-sm-12">
            <h4 style="margin-top: 10px;">Kart 2020 Ribeirão Preto GP Results</h4>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Position</th>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Laps</th>
                    <th scope="col">Total Time</th>
                    <th scope="col">Best Lap</th>
                    <th scope="col">Average Speed</th>
                    <th scope="col">Hour</th>
                    <th scope="col">Time</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach($ordenados as $index => $item){ ?>
                <tr>
                    <th scope="row"><?php echo $item['posicao']; ?></th>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['nome']; ?></td>
                    <td><?php echo $item['numero_de_voltas']; ?></td>
                    <td><?php echo $item['tempo_total']; ?></td>
                    <td><?php echo $item['melhor_volta']; ?></td>
                    <td><?php echo $item['velocidade_media']; ?> Km/s</td>
                    <td><?php echo $item['hora']; ?></td>
                    <td><?php echo isset($item['tempo_recorrente']) ? "+".$item['tempo_recorrente'] : "+0:00:00:000"; ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <h4 style="margin-top: 10px;">Fastest lap of the race: </h4> <?php echo "Lap: ". $bestRaceLap['volta'] ?> <br> <?php echo "Time: ". $bestRaceLap['tempo'] ?> <br> <?php echo "Pilot: ". $bestRaceLap['nome'] ?>
        </div>
    </body>
</html>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>