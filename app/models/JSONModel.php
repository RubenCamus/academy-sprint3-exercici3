<?php
require_once(__DIR__ . '/IPersistance.php');
require_once(__DIR__ . '/Task.php');
require_once(__DIR__ . '/../../config/constants.php');

class JSONModel implements IPersistance {
    private const STORAGE_PATH = ROOT_PATH . '/storage';
    private const DB_PATH = ROOT_PATH . '/storage/db.json';
    private const EDITABLE_FIELDS = ['name', 'status', 'user', 'startDate', 'endDate'];

    // Escribe el array completo en el archivo
    public function write(mixed $valueToWrite): void {
        // Crea el archivo si no existe
        $this->createFile();
        file_put_contents(
            self::DB_PATH,
            json_encode($valueToWrite),
        );
    }

    // Leer archivo json. Siempre devuelve un array (vacío si no hay nada)
    public function read(): array {
        if (!file_exists(self::DB_PATH)) {
            $this->createFile();
        }
        // true = array asociativo. Si el archivo está vacío o roto, json_decode da null
        return json_decode(file_get_contents(self::DB_PATH), true) ?? [];
    }

    public function delete(): void {
        if (file_exists(self::DB_PATH)) {
            unlink(self::DB_PATH);
        }
    }
    public function createTask(Task $task): void {
        $tasksArray = $this->read();
        $tasksArray[] = $task->getArray();
        $this->write($tasksArray);
    }

    // Devuelve la tarea como array asociativo, o null si no existe
    public function getTask(string $name): ?array {
        foreach ($this->read() as $task) {
            if ($task['name'] === $name) {
                return $task;
            }
        }
        return null;
    }

    // $changes = ['campo' => 'nuevo valor']. Devuelve false si no existe la tarea
    public function update(string $name, array $changes): bool {
        $tasks = $this->read();

        foreach ($tasks as $i => $task) {
            if ($task['name'] !== $name) {
                continue;
            }

            foreach ($changes as $field => $value) {
                if (!in_array($field, self::EDITABLE_FIELDS, true)) {
                    throw new InvalidArgumentException("Campo no válido: $field");
                }
                if ($field === 'status' && (!is_string($value) || TaskStatus::tryFrom($value) === null)) {
                    throw new InvalidArgumentException("Estado no válido");
                }
                $tasks[$i][$field] = $value;
            }

            $this->write($tasks);
            return true;
        }

        return false;
    }

    private function createFile(): void {
        // Si la carpeta storage no existe, se crea
        if (!is_dir(self::STORAGE_PATH)) {
            mkdir(self::STORAGE_PATH);
        }
        if (!file_exists(self::DB_PATH)) {
            file_put_contents(self::DB_PATH, '[]');
        }
    }
}
