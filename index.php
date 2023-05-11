<?php
declare(strict_types=1);

$dbObject = new PDO('mysql:host=localhost:3306;dbname=still;charset=utf8mb4','root' , 'mysql',  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

require './Human.php';

/* Проверки и варианты использования класса Human */

//$human = new Human(['name' => 'Vasia', 'surname'=>'Petrov', 'birthday' => '30.06.1992', 'gender' => 1, 'city' => 'Zhodino']);
//$human = new Human(['name' => 'ВАСЯаленаvc', 'surname'=>'Petrov', 'birthday' => '30.06.1950', 'gender' => 0, 'city' => 'Zhodino1']);
$human = new Human(['id'=>20]);

echo Human::getFullYears($human->getBirthday());

echo Human::getGenderDefinition($human->getGender());

$newHuman = $human->getRedactInstance(['birthday' =>'30.06.1994', 'gender' => 1]);
echo $newHuman->getBirthdayToString();
$newHuman->save();

