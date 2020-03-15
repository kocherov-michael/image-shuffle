<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php

if (isset($_POST['load-image'])) {
    // print_r($_POST);
    // echo "<br><pre>";
    // print_r($_FILES);
    // echo "</pre>";
    if (isset($_FILES['image']['name']) && $_FILES['image']['tmp_name'] != "") {

        // Записываем параметры файла в переменные
        // Записываем имя файла
        $fileName = $_FILES["image"]["name"];
        // Записываем временный путь
        $fileTmpLoc = $_FILES["image"]["tmp_name"];
        // записываем тип файла
        $fileType = $_FILES["image"]["type"];
        // записываем размер файла
        $fileSize = $_FILES["image"]["size"];
        // записываем ошибки при добавлении файла, если они есть.
        $fileErrorMsg = $_FILES["image"]["error"];
        // записываем разбиваем файл на две части по разделителю "."
        $kaboom = explode(".", $fileName);
        // берем последний элемент из массива $kaboom
        $fileExt = end($kaboom);

        // записываем ширину и высоту загруженной картинки 
        list($width, $height) = getimagesize($fileTmpLoc);

        move_uploaded_file($fileTmpLoc, "img/$fileName");

        // Создание изображений
        $src = imagecreatefromjpeg("img/$fileName");
        // Создание нового полноцветного изображения
        $dest = imagecreatetruecolor($width, $height);
        
        $wParts = 10;
        $hParts = 10;
        // проверка деления без остатка
        while ($width % $wParts !== 0) {
            $wParts--;
        }
        while ($height % $hParts !== 0) {
            $hParts--;
        }

        $wStep = $width / $wParts;
        $hStep = $height / $hParts;
        // создаём массив, в который запишем позиции шагов и перемешаем
        $wFirstArr = array();
        $hFirstArr = array();
        
        for ($i = 0; $i < $width; $i = $i + $wStep) {
            $wFirstArr[] = $i;
        }
        for ($i = 0; $i < $height; $i = $i + $hStep) {
            $hFirstArr[] = $i;
        }
        $wMixArr = $wFirstArr;
        $hMixArr = $hFirstArr;
        shuffle($wMixArr);

        $innerTemplate = '';
        for ($i = 0; $i < $wParts; $i++) {
            shuffle($hMixArr);
            for ($y = 0; $y < $hParts; $y++) {

                $innerTemplate = $innerTemplate . "<div style='top:". $hFirstArr[$y] ."px; left:". $wFirstArr[$i] ."px; background-position:-". $wMixArr[$i] ."px -". $hMixArr[$y] ."px; background-image: url(img/new.jpeg); background-repeat: no-repeat; position: absolute; width: ". $wStep ."px;  height: ". $hStep ."px;'></div>";
                // копирование части изображения return boolean
                // imagecopy ( целевое изображ , источник изобр , коорд Х цели , коорд Y цели , коорд Х источника , коорд Y источника , ширина копируемой части , высота копируемой части )
                imagecopy ( $dest ,  $src , $wMixArr[$i] , $hMixArr[$y] , $wFirstArr[$i] , $hFirstArr[$y] , $wStep , $hStep );
            }

        }
        $outerTemplate = "<div style='width:". $width ."px; height:". $height ."px; position: relative;'>". $innerTemplate ."</div>";

        // Вывод и освобождение памяти
        imagejpeg($dest, "img/new.jpeg");
        echo '<img src="img/new.jpeg">';
        echo $outerTemplate;
        imagedestroy($dest);
    }
}
?>
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <input id="file-2" type="file" name="image" multiple="">
        <input type="submit" value="Сохранить" name="load-image">
    </form>
</body>
</html>