<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>We Miss You!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 4px;
            padding: 20px;
        }
        h2 {
            color: #2b6cb0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello!</h2>
        <p>{{ $messageText }}</p>
        <hr style="border: 0; border-top: 1px solid #eeeeee;">
        <p style="font-size: 12px; color: #777777;">You are receiving this promotional message because you registered with our platform.</p>
    </div>
</body>
</html>
