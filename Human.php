<?php
declare(strict_types=1);

class Human
{
    private int $id;
    private string $name;
    private string $surname;
    private int $birthday;
    private int $gender;
    private string $city;
    private \PDO $db;


    //Конструктор класса либо создает человека в БД с заданной информацией, либо берет информацию из БД по id (предусмотреть валидацию данных);
    public function __construct(array $params)
    {
        global $dbObject;
        $this->db = &$dbObject;
        if (isset($params['name'], $params['surname'], $params['birthday'], $params['gender'], $params['city'])) {
            if ($this->ValidateNaming($params['name'], 'name')) {
                $this->name = $params['name'];
            }
            if ($this->ValidateNaming($params['surname'], 'surname')) {
                $this->surname = $params['surname'];
            }
            if ($this->ValidateBirthday($params['birthday'])) {
                $this->birthday = strtotime($params['birthday']);
            }
            if ($this->ValidateGender($params['gender'])) {
                $this->gender = $params['gender'];
            }
            $this->city = $params['city'];
            $stmt = $this->db->prepare("INSERT INTO humans (`name`, `surname`, `birthday`, `gender`, `city`) VALUES (?,?,?,?,?)");
            if (!$stmt->execute([$this->name, $this->surname, $this->birthday, $this->gender, $this->city])) {
                throw new Exception("Не удалось записать экземпляр в базу данных");
            }
        } elseif (isset($params['id'])) {
            $this->id = $params['id'];
            $stmt = $this->db->prepare("SELECT * FROM humans WHERE id = ?");
            $stmt->execute([$this->id]);
            if ($myObj = $stmt->fetch()) {
                $this->name = $myObj['name'];
                $this->surname = $myObj['surname'];
                $this->birthday = $myObj['birthday'];
                $this->gender = $myObj['gender'];
                $this->city = $myObj['city'];
            } else {
                throw new Exception("Отсутствует экземпляр с данным id");
            }
        } else {
            throw new Exception("Неверные параметры для создания экземпляра класса");
        }
    }


    public
    function getName(): string
    {
        return $this->name;
    }

    public
    function getSurname(): string
    {
        return $this->Surname;
    }

    public
    function getCity(): string
    {
        return $this->city;
    }

    public
    function getGender(): int
    {
        return $this->gender;
    }

    public
    function getBirthday(): int
    {
        return $this->birthday;
    }

    public function getBirthdayToString(): string
    {
        return date('Y-m-d', $this->getBirthday());
    }

    //Сохранение Полей Экземпляра Класса В Бд;
    public
    function save(): bool
    {
        $stmt = $this->db->prepare("UPDATE humans SET name = ?, surname = ?, birthday = ?, gender = ?, city = ?
             WHERE id = ?");
        return $stmt->execute([$this->name, $this->surname, $this->birthday, $this->gender, $this->city, $this->id]);
    }

    //Удаление человека из БД в соответствии с id объекта;
    public
    function delete(): bool
    {
        $stmt = $this->db->prepare("DELETE FROM humans WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    //static преобразование даты рождения в возраст (полных лет);
    public
    static function getFullYears(int $birthday): int
    {
        $currentDate = new DateTime();
        $diff = $currentDate->diff(new DateTime(date('j.m.Y H:i:s', $birthday)));
        return $diff->y;
    }

    //static преобразование пола из двоичной системы в текстовую (муж, жен);
    public
    static function getGenderDefinition(int $gender): string
    {
        return b"$gender" == 0 ? 'man' : (b"$gender" == 1 ? 'woman' : 'undefined gender');
    }

    //Форматирование человека с преобразованием возраста и (или) пола (п.3 и п.4) в зависимости от параметров (возвращает новый экземпляр stdClass со всеми полями изначального класса).
    public function getRedactInstance(array $params): Human
    {
        $cloneInstance = clone $this;
        if (isset($params['gender']) && $this->ValidateGender($params['gender'])) {
            $cloneInstance->gender = $params['gender'];
        }
        if (isset($params['birthday']) && $this->ValidateBirthday($params['birthday'])) {
            $cloneInstance->birthday = strtotime($params['birthday']);
        }
        return $cloneInstance;
    }

    private function ValidateGender(int $gender): bool
    {
        if ($gender === 0 || $gender === 1) {
            return true;
        } else {
            throw new Exception("Значение гендера только 0 или 1");
        }
    }

    private function ValidateBirthday(string $birthday): bool
    {
        if (strtotime($birthday) !== false) {
            return true;
        } else {
            throw new Exception("Недопустимое значение даты");
        }
    }

    private function ValidateNaming(string $name, string $field): bool
    {
        if (preg_match("/^[A-Za-zА-я]+$/", $name)) {
            return true;
        } else {
            throw new Exception("Значение $field должно содержать только символы");
        }
    }

    public function __toString(): string
    {
        return "ID: {$this->id}, NAME: {$this->name}, SURNAME: {$this->surname}, BIRTH: {$this->getBirthdayToString()}, GENDER:".self::getGenderDefinition($this->gender).", CITY: {$this->city}";
    }
}