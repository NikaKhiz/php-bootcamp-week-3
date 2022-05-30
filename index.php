<?php
//connection
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=hp_characters', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$url = "https://harry-potter-api-english-production.up.railway.app/characters";
$errors = [];
//check if there is post request or not 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $character_name = $_POST['character_name'];
    //fetch data from api
    $resource = curl_init($url);
    curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($resource);
    $decoded = json_decode($result, true);
    //creates directory for images if it doesnt exist
    if (!file_exists('images')) {
        mkdir('images');
    }
    //validation of input field .. if its fulfilled , we declare character's info future usage ,else we present error messages
    if (!empty($character_name)) {
        foreach ($decoded as $characters) {
            if (stristr($characters['character'], $character_name, false)) {
                $chars_name = $characters['character'];
                $chars_nick = $characters['nickname'];
                $chars_house = $characters['hogwartsHouse'];
                $chars_image = $characters['image'];
                $img = $chars_name . ".png";
                $img_path = 'images/' . $img;
                file_put_contents($img_path, file_get_contents($chars_image));
            }
        }
        if (!isset($chars_name) && !isset($chars_nick)) {
            $errors[0] = "incorrect character name";
        }
    } else {
        $errors[0] = "empty value,please fill up the field";
    }



    // check if there is no errors presented , if not check if there is a record in data about character name from input field
    // if there is no record in db about character,add it and then fetch it 

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM characters WHERE charname LIKE :charname");
        $stmt->bindValue(':charname', $chars_name);
        $stmt->execute();
        $character_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$character_info) {
            $statement = $pdo->prepare('INSERT INTO characters (charimage,charname,charnick,charhouse)
                                                 VALUES (:charimg, :charnm, :charnck, :charhs)');
            $statement->bindValue(':charimg', $img_path);
            $statement->bindValue(':charnm', $chars_name);
            $statement->bindValue(':charnck', $chars_nick);
            $statement->bindValue(':charhs', $chars_house);
            $statement->execute();
            $stmt = $pdo->prepare("SELECT * FROM characters WHERE charname LIKE :charname");
            $stmt->bindValue(':charname', $chars_name);
            $stmt->execute();
            $character_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
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
    <!-- we are displaing this before post request -->
    <?php if (empty($_POST)) : ?>
        <div class="container mx-auto flex justify-center p-5 ">
            <form method="POST">
                <input type="text" name="character_name" aria-label="Character name" placeholder="Character name" class="rounded p-2 text-lightShtorm capitalize">
                <button type="submit" class="rounded p-2 bg-shtorm text-white capitalize">fetch character info</button>
            </form>
        </div>
        <!-- after this line everything heppens if there is submition with post request -->
    <?php elseif (!empty($_POST)) : ?>
        <!-- print out errors if there are any of them -->
        <?php if (!empty($errors)) : ?>
            <div class="container mx-auto flex justify-center p-5 text-shtorm capitalize">
                <?= $errors[0]; ?>
            </div>
        <?php endif; ?>
        <!-- if errors dont present , display character info  -->
        <?php if (empty($errors)) : ?>
            <?php foreach ($character_info as $characters) : ?>
                <div class="max-w-sm rounded overflow-hidden shadow-lg mx-auto p-2">
                    <img class="w-full" src="<?= $characters['charimage'] ?>" alt="">
                    <div class="px-6 py-4">
                        <div class="text-xl mb-2 text-shtorm font-bold"> <?= $characters['charname'] ?></div>
                        <p class="text-gray-700 text-base mb-1 capitalize"><?= "nickname: " . $characters['charnick'] ?></p>
                        <p class="text-gray-700 text-base capitalize"><?= "housename: " . $characters['charhouse'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>