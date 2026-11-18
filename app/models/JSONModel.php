<?php
require_once('IPersistance.php');
require_once('Task.php');
require_once(__DIR__ . '/../../config/constants.php');
class JSONModel implements IPersistance{
    private const STORAGE_PATH = ROOT_PATH . '/storage';
    private const DB_PATH = ROOT_PATH . '/storage/db.json';
    private const EDITABLE_FIELDS = ['name', 'status', 'startDate', 'endDate'];
    public function write(mixed $valueToWrite): void {
        // Crea el archivo si no existe
        $this->createFile();
        // Write new data to file
        file_put_contents(self::DB_PATH,json_encode($valueToWrite));
    }
    // Leer archivo json
    public function read(): mixed {
        if (!file_exists(self::DB_PATH)) {
            $this->createFile();
        }
        // Devuelve array asociativo con true
        return json_decode(file_get_contents(self::DB_PATH), true);
    }
    public function delete(): void {
        if(file_exists(self::DB_PATH)){
            unlink(self::DB_PATH);
        }
    }
    public function createTask(Task $task): void {
        $tasksArray = ($this->read());
        // Make changes to file data
        $tasksArray[] = $task->getArray();
        $this->write($tasksArray);
    }
    public function getTask(string $name) {
        $tasksArray = $this->read();
        foreach($tasksArray as $task) {
            if ($task['name'] === $name) {
                return $task; // Array asociativo
            }
        }
        return null;
    }

    public function updateTask(string $name, array $changes): bool {
        $tasks = $this->read();

        foreach ($tasks as $i => $task) {
            if ($task['name'] !== $name) {
                continue;
            }

            foreach ($changes as $field => $value) {
                if (!in_array($field, self::EDITABLE_FIELDS, true)) {
                    throw new InvalidArgumentException("Campo no válido: $field");
                }
                if ($field === 'status' && TaskStatus::tryFrom($value) === null) {
                    throw new InvalidArgumentException("Estado no válido: $value");
                }
                $tasks[$i][$field] = $value;
            }

            $this->write($tasks);
            return true;
        }

        return false; // no existe una tarea con ese nombre
    }    private function createFile(): void {
        // Checkear que la carpeta storage existe si no, se crea
        if(!is_dir(self::STORAGE_PATH)) {
            mkdir(self::STORAGE_PATH);
        }
        if (!file_exists(self::DB_PATH)) {
            $empty = json_decode("[]");
            file_put_contents(self::DB_PATH, json_encode($empty));
        }
    }
}

$json = new JSONModel();
$json->delete();
$arr = $json->read();
$json->createTask(new Task("Tarrea 1"));
$json->createTask(new Task("Gym"));
// var_dump($json->read());
var_dump($json->getTask("Gym"));
// var_dump($json->read());

?>
