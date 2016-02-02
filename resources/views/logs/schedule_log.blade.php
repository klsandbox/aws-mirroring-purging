<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="data:text/css;charset=utf-8," data-href="/assets/css/bootstrap-theme.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Return</th>
                            <th>Datetime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $val)
                            <tr>
                                <td>
                                    {{ $val->id }}
                                </td>
                                <td>
                                    {{ $val->type }}
                                </td>
                                <td>
                                    {{ $val->return }}
                                </td>
                                <td>
                                    {{ $val->created_at }}
                                </td>
                            </tr>
                        @endforeach 
                    </tbody>
                </table>
                {!! $data->links() !!}
            </div>
        </div>
    </body>
</html>