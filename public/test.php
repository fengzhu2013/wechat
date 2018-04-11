<?php
    if (isset($_SERVER['https'])) {
        $https = 'https://';
    } else {
        $https = 'http://';
    }
    echo $https;
?>
<?php
    echo $https;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>test</title>
</head>
<body>
    <form action="http://edu.natapp1.cc/wechat/public/admin/index/systemLogin" method="post">
        userId:<input type="text" placeholder="userId" name="userId"><br>
        password:<input type="text" placeholder="password" name="password"><br>
        <input type="submit" value="systemLogin">
    </form>
</body>
</html>
<?php
    echo $https;
?>