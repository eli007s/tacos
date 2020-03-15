<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWC Clean</title>
</head>
<body>
    <div id="container">
        <form action="/clean" method="post">
            <textarea name="text"><?php echo isset($_POST['text']) && strlen($_POST['text']) > 0 ? $_POST['text'] : ''; ?></textarea>
            <input type="submit" name="submit" />
        </form>
    </div>
</body>
</html>
