<?php
require_once('Task.php');
interface IPersistance {
    // Como tipo las funciones? Que devuelve read en una DB? Y en JSON?
    // Delete deberia de devolver lo que ha eliminado? O simplemente un mensaje y void?
    public function read(): mixed;
    public function write(Task $value): void;
    public function delete(): void;
}

?>
