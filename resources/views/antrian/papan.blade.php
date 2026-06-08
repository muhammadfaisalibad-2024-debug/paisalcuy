<!DOCTYPE html>
<html>
<head>

    <title>Papan Antrian</title>

    <style>

        body{
            text-align:center;
            font-family:Arial;
            margin-top:100px;
        }

        #nomor{
            font-size:150px;
            color:red;
        }

        #nama{
            font-size:50px;
        }

    </style>

</head>
<body>

    <h1>ANTRIAN SAAT INI</h1>

    <div id="nomor">-</div>

    <div id="nama">-</div>

<script>

let nomorTerakhir = null;

const evt =
    new EventSource('/sse/antrian');

evt.onmessage = function(e){

    let data =
        JSON.parse(e.data);

    document
        .getElementById('nomor')
        .innerHTML =
        data.nomor ?? '-';

    document
        .getElementById('nama')
        .innerHTML =
        data.nama ?? '-';

    if(
        data.nomor &&
        nomorTerakhir != data.nomor
    ){

        nomorTerakhir =
            data.nomor;

        let suara =
            new SpeechSynthesisUtterance(
                "Nomor antrian "
                +
                data.nomor
                +
                ", atas nama "
                +
                data.nama
            );

        suara.lang = "id-ID";

        speechSynthesis.speak(
            suara
        );
    }
};

</script>

</body>
</html>