<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>

<body class="font-sans antialiased">
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                {{-- <th>Position</th>
                <th>Office</th>
                <th>Extn.</th> --}}
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Name</th>
                {{-- <th>Position</th>
                <th>Office</th>
                <th>Extn.</th> --}}
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </tfoot>
    </table>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                "ajax": "data/objects.txt",
                // "iDisplayLength": 1
                "lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
                "columns": [{
                        "data": "name"
                    },
                    // {
                    //     "data": "position"
                    // },
                    // {
                    //     "data": "office"
                    // },
                    // {
                    //     "data": "extn"
                    // },
                    {
                        "data": "start_date"
                    },
                    {
                        "data": "salary"
                    }
                ]
            });
        });
    </script>
</body>

</html>
