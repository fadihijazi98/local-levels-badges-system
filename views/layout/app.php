<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>FadiH</title>
</head>
<body>
    <div>header</div>
    <div id="app">
        <?php
            if (isset($_VIEW))
            {
                echo array_pop($_VIEW);
            }
        ?>
    </div>
    <div>footer</div>
</body>
</html>