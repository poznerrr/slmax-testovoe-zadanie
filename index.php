<?php
declare(strict_types=1);

$dbObject = new PDO('mysql:host=localhost:3306;dbname=still;charset=utf8mb4','root' , 'mysql',  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

require './Human.php';
require  './Humans.php';

/* Проверки и варианты использования класса Human */
//$human = new Human(['name' => 'Vasia', 'surname'=>'Petrov', 'birthday' => '30.06.1992', 'gender' => 1, 'city' => 'Zhodino']);
//$human = new Human(['name' => 'ВАСЯGooD', 'surname'=>'Petrov', 'birthday' => '30.06.1950', 'gender' => 0, 'city' => 'Zhodino']);
$human = new Human(['id'=>10]);

echo Human::getFullYears($human->getBirthday()).'<br>';

echo Human::getGenderDefinition($human->getGender()).'<br>';
echo $human.'<br>';
$newHuman = $human->getRedactInstance(['birthday' =>'30.06.1994', 'gender' => 1]);
echo $newHuman->getBirthdayToString().'<br>';
$newHuman->save();

/*Проверки и варианты использования класса Humans*/
/*EXPRESSION VALUES '>' '<' '!=' */
$humans = new Humans('<', 5);

echo "До удаления <br>";
foreach ($humans->getHumans() as $everyHuman) {
    echo $everyHuman.'<br>';
}

$humans->deleteHumans();

echo "После удаления <br>";
foreach ($humans->getHumans() as $everyHuman) {
    echo $everyHuman.'<br>';
}



