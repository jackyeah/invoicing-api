<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/30
 * Time: 上午 11:29
 */
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1><?php  var_dump(config('define.self.token')) ?></h1>
<div class="panel-body">
    <form action="upload" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
            <input type="file" name="web_image"/>
        </div>
        <div class="col-md-9">
            <input type="file" name="mobile_image"/>
        </div>
        <div class="col-md-9">
           url <input type="text" name="url"/>
        </div>
        <div class="col-md-9">
            status <input type="text" name="status"/>
        </div>
        <div class="col-md-9">
            1<input type="checkBox" name="p_code[]" value="1"/>
            2<input type="checkBox" name="p_code[]" value="2"/>
        </div>
        <div class="col-md-9">
            <button type="submit" class="btn btn-success">upload</button>
        </div>
    </form>
</div>
</body>
</html>
