<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.6.12/plyr.css" />

</head>

<body>
    <video controls="" autoplay="" name="media"><source src="http://localhost:8000/storage/audio/2022/01/06/xPAQWla2yAFgW5YQPZzESqt5BjkLO39946nFh7rD.mp3" type="audio/x-wav"></video>
    {{-- <audio  id="player" controls>
        <source src="http://localhost:8000/storage/audio/2022/01/06/xPAQWla2yAFgW5YQPZzESqt5BjkLO39946nFh7rD.mp3" type="audio/mp3" />
    </audio> --}}

    <script src="https://cdn.plyr.io/3.6.12/plyr.polyfilled.js"></script>
    <script>
        const player = new Plyr('#player',{});
    </script>
</body>

</html>
