<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=hp_characters', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT * FROM characters");
$stmt->execute();
$character_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harry Potter Characters</title>
    <link rel="stylesheet" href="./css/main.css">
</head>

<body>
    <header>
        <nav class="container bg-shtorm mx-auto flex justify-between items-center p-5">
            <div class="logo">
                <img src="./images/harry-potter-logo-32531.png" alt="logo" class="w-20">
            </div>
            <div class="flex justify-end items-center space-x-6 ">
                <a href="index.php" class="text-white">Home</a>
                <a href="all_characters.php" class="text-white">All Characters</a>
            </div>
        </nav>
    </header>

    <div class="container mx-auto">
        <table class="table-fixed border-collapse mx-auto">
            <thead class="text-center capitalize text-lightShtorm">
                <tr >
                    <td class="p-2">#</td>
                    <td class="p-2">character image</td>
                    <td class="p-2">character name</td>
                    <td class="p-2">nickname</td>
                    <td class="p-2">house name</td>
                </tr>
            </thead>
            <?php foreach ($character_info as $i => $characters) : ?>
                <tr class="text-center  text-lightShtorm">
                    <td class="p-2"><?= $i + 1; ?></td>
                    <td class="p-2"><img src="<?= $characters['charimage'] ?>" alt="" class="w-20"></td>
                    <td class="p-2"><?= $characters['charname'] ?></td>
                    <td class="p-2"><?= $characters['charnick'] ?></td>
                    <td class="p-2"><?= $characters['charhouse'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>

</html>