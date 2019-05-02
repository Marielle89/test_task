<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
        .hide {
            display:none!important
        }
        .show {
            display:block!important
        }
        .table {
            margin: 25px;
        }
        .table  td {
            color: rgba(9, 9, 9, 1);
            padding: 2px 15px;
            background-color: rgba(200, 243, 237, 0.47);
        }
        .table  th {
            padding: 2px 15px;
            background-color: rgba(243, 192, 222, 0.53);
        }
        a.download_link {
            color: #6fc0ab;
            text-decoration: none;
            margin: 20px 40px;
        }
        a.download_link:hover {
            color: #6fc0ab;
            text-decoration: underline;
        }
        .form_upload {
            margin: 20px 40px;
            padding-top: 20px;
        }
        .fn_b_sorting {
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
@if (!isset($keyWords))
    <div class="form_upload">
        <h3>Загрузите exel-документ с ключевыми словами</h3>
        <form action="/list" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" name="file" accept=".xls,.xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
            <input type="submit" placeholder="Import file">
        </form>
    </div>
@endif
@if (isset($keyWords))
    <table class="table" id="fn_all_phrases">
        <thead>
            <tr>
                <th>All phrases</th>
                <th>TOP5</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($keyWords as $indexKey => $keyWord)
                <tr>
                    <td>{{ $keyWord->word }}</td>
                    <td>
                        @if ($indexKey < 5)
                            {{ $top5_words[$indexKey] }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@if (isset($top1with2_phrases_to_export))
    <div class="fn_b_sorting">
        <a href="javascript:;" id="fn_sorting" class="download_link">Группировать</a>
    </div>
    <div class="hide" id="fn_top_phrases">
        <table class="table">
            <thead>
                <tr>
                    <th>TOP1 with TOP2</th>
                    <th>TOP1</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($top1with2_phrases_to_export as $keyWord)
                    <tr>
                        @if (isset($keyWord[0]))
                            <td>{{ $keyWord[0] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (isset($keyWord[1]))
                            <td>{{ $keyWord[1] }}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="fn_b_sorting">
            <a id="fn_download_file" class="download_link" href="/list/{{ $list_id }}">Сохранить</a>
        </div>
    </div>
@endif

<script>
    fn_sorting.onclick = function(event) {
        var fn_all_phrases = document.getElementById("fn_all_phrases"),
            fn_top_phrases = document.getElementById("fn_top_phrases");
        fn_top_phrases.classList.add("show");
        fn_top_phrases.classList.remove("hide");
        fn_all_phrases.classList.add("hide");
        this.classList.add("hide");
    }
</script>
</body>
</html>