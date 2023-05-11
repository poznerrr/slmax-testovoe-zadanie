<?php
declare(strict_types=1);

if (!class_exists('Human')) {
    throw new LogicException("Unable to load class: Human");
}

class Humans
{
    private array $humans = [];
    private string $expression;
    private int $id;
    private \PDO $db;

    public function __construct(string $expression, int $id)
    {
        global $dbObject;
        $this->db = &$dbObject;
        if (in_array($expression, ['<', '>', '!='])) {
            $this->expression = $expression;
            $this->id = $id;
            $stmt = $this->db->prepare("SELECT id FROM humans WHERE id $this->expression ?");
            $stmt->execute([$this->id]);
            while ($humanId = $stmt->fetchColumn()) {
                $this->humans[] = $humanId;
            }
        } else {
            throw new Exception("Недрпустимое значение параметриа условия");
        }
    }

    public function getHumans(): array
    {
        $humansArray = [];
        if (count($this->humans) === 0) {
            return [];
        }
        foreach ($this->humans as $humanId) {
            $humansArray[] = new Human(['id' => $humanId]);
        }
        return $humansArray;
    }

    public function deleteHumans(): void
    {
        if (count($this->humans) === 0) {
            return;
        }
        foreach ($this->humans as $humanId) {
            $human = new Human(['id' => $humanId]);
            if ($human->delete()) {
                unset($this->humans[array_search($humanId, $this->humans)]);
            }
        }
    }
}