<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Search Engine Project</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .searchLabel {
            color: #636b6f;
            padding: 0 25px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .searchTxt {
            color: #636b6f;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            width: 600px;
        }

        .searchBtn {
            color: #636b6f;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>

    <script>
        function submitQuery()
        {
            var url = '{{ route('submitQuery', [":query"] ) }}';
            url = url.replace(':query', escapeOutput(document.getElementById("query").value));
            window.location.assign(url)
        }

        function escapeOutput(toOutput){
            return toOutput
                .replace(/\&/g, '&amp;')
                .replace(/\</g, '&lt;')
                .replace(/\>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/\'/g, '&#x27')
                .replace(/\//g, '&#x2F');
        }
    </script>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">
            Search Engine Project
        </div>

        <label class="searchLabel" for="query">Results:</label>

        <br><br>

        <input type="text" class="searchTxt" id="query" maxlength="256">

        <br><br>

        <button id="submit" onclick="submitQuery()" class="searchBtn">Submit Query</button>

        <br><br>
    </div>
</div>
</body>
</html>
