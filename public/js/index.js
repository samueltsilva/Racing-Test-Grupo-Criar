window.onload = function () {
    $("form#form-file").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        requestUtils.doPostFile('processa-corrida', formData, function (data) {
            popularMelhorVolta(data.melhorVoltaCorrida);
            popularMelhoresVoltas(data.melhorVoltaPiloto);
            popularDetalhamentoProva(data.detalhamentoVoltas)
            $(".detalhes-corrida").show();
            Swal.close();
        })
    });

    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
}

function popularMelhorVolta(dataMelhorVolta) {
    $("#tempo-melhor-volta").empty().append(`Tempo da volta: ${dataMelhorVolta.tempoVolta}`);
    $("#id-piloto-melhor-volta").empty().append(`Id do piloto: ${dataMelhorVolta.id}`);
    $("#nome-pilo-melhor-volta").empty().append(`Nome do piloto: ${dataMelhorVolta.nome}`);
    $("#numero-volta").empty().append(`Número da volta: ${dataMelhorVolta.volta}`);
}

function popularMelhoresVoltas(dados) {

    let html = '';
    $.each(dados, function (indice, dataMelhorVoltaPiloto) {
        ++indice;
        html += `  <div class="card">
        <div class="card-header" id="head-${indice}">
            <h5 class="mb-0">
                   <button class="btn btn-link" data-toggle="collapse" data-target="#collapse${indice}"
                   aria-expanded="true" aria-controls="collapse${indice}">
                   Posicao - ${indice}
               </button>
           </h5>
       </div>

       <div id="collapse${indice}" class="collapse" aria-labelledby="head-${indice}"
           data-parent="#accordion">
           <div class="card-body">
               <ul class="list-group">
                   <li class="list-group-item active">Melhor Volta: ${dataMelhorVoltaPiloto.tempoVolta}</li>
                   <li class="list-group-item">Id do piloto: ${dataMelhorVoltaPiloto.id}</li>
                   <li class="list-group-item">Nome do piloto: ${dataMelhorVoltaPiloto.nome}</li>
                   <li class="list-group-item">Número da volta: ${dataMelhorVoltaPiloto.volta}</li>
                   <li class="list-group-item">Velocidade média total: ${dataMelhorVoltaPiloto.velocidadeMediaTotal}</li>
                   <li class="list-group-item">Difrença para o 1° colocado: ${dataMelhorVoltaPiloto.tempoAtrasPrimeiroColado == undefined ? 'Vencedor' : dataMelhorVoltaPiloto.tempoAtrasPrimeiroColado}</li>
                   <li class="list-group-item">Tempo total de prova: ${dataMelhorVoltaPiloto.tempoTotalProva}</li>
                   </ul>
           </div>
       </div>
   </div>`;
        $("#accordion").empty().append(html);
    })
}

function popularDetalhamentoProva(data) {
    $(".grid").jsGrid({
        width: "100%",
        height: "auto",
        editing: false,
        sorting: true,
        paging: true,
        autoload: !0,
        filtering: true,
        data: data,

        fields: [
            { name: "id", type: "text", width: "auto", title: "Id do piloto" },
            { name: "nome", type: "text", width: "auto", title: "Nome do piloto" },
            { name: "tempoVolta", type: "text", width: "auto", title: "Tempo da volta" },
            { name: "velocidadeMedia", type: "text", width: "auto", title: "Velocidade Média" },
            { name: "volta", type: "text", width: "auto", title: "Volta" }
        ]
    });
}
