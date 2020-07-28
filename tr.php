<?php

require 'vendor/autoload.php';

use Google\Cloud\Translate\V2\TranslateClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$translate = new TranslateClient([
    'key' => $_ENV['API_KEY']
]);

$mysqli = new mysqli($_ENV['LOCALHOST'], $_ENV['DBUSER'], $_ENV['PASSWD'], $_ENV['DBNAME']);
$mysqli->set_charset("utf8");

/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    die();
}

/* Select queries return a resultset */
$results = $mysqli->query("SELECT * FROM ".$_ENV['TABLE']." WHERE id_lang=2");
echo "ARRAY results \n";
foreach ($results as $key => $list) {
    # code...
    $id_product = $list['id_product']; 

    echo $id_product ."\n";

    $description = $translate->translate($list['description'], [
        'target' => 'en'
    ]);

    ### echo $description['text'];  echo "<br>";

    $description_short = $translate->translate($list['description_short'], [
        'target' => 'en'
    ]);

   ### echo  $description_short['text']; echo "<br>";

    $link_rewrite = $translate->translate($list['link_rewrite'], [
        'target' => 'en'
    ]);

   ### echo  $link_rewrite['text']; echo "<br>";

    $name = $translate->translate($list['name'], [
        'target' => 'en'
    ]);

   ### echo  $name['text']; echo "<br>";

    // $description  $description_short  $link_rewrite $name

    $sql = "UPDATE ".$_ENV['TABLE']." SET 
            `description` = '". addslashes($description['text']) ."',  
            `description_short` = '". addslashes($description_short['text'])."',  
            `link_rewrite` = '". addslashes($link_rewrite['text'])."',  
            `name` = '". addslashes($name['text']) ."'
        WHERE `id_lang`=2 AND `id_product`=".$id_product;

    if ($mysqli->query($sql) === TRUE) {
        echo "Record updated successfully \n";
    } else {
        echo "Error updating record: " . $mysqli->error;
    }

   // printf("\n Affected rows (UPDATE): %d\n", $mysqli->affected_rows);
    

}
// Translate text from english to french.
// $result = $translate->translate('BOM DIA', [
//     'target' => 'en'
// ]);

//echo $result['text'] . "\n";

// Detect the language of a string.
//$result = $translate->detectLanguage('Greetings from Michigan!');

//echo $result['languageCode'] . "\n";

// Get the languages supported for translation specifically for your target language.
// $languages = $translate->localizedLanguages([
//     'target' => 'en'
// ]);

// foreach ($languages as $language) {
//     echo $language['name'] . "\n";
//     echo $language['code'] . "\n";
// }

// // Get all languages supported for translation.
// $languages = $translate->languages();

// foreach ($languages as $language) {
//     echo $language . "\n";
// }