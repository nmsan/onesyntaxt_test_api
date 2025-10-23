<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Post Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .post-content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .post-title {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .post-body {
            margin-top: 15px;
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Post from {{ $website->name ?? 'Our Website' }}</h1>
    </div>

    <div class="post-content">
        <h2 class="post-title">{{ $post->title }}</h2>

        <div class="post-body">
            {{ $post->body }}
        </div>
    </div>

    <div class="footer">
        <p>This email was sent because you subscribed to updates from {{ $website->name ?? 'our website' }}.</p>
        <p>If you no longer wish to receive these notifications, please unsubscribe.</p>
    </div>
</body>
</html>
