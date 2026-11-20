<?php
require_once(__DIR__ . '/../models/JSONModel.php');
require_once(__DIR__ . '/../models/Task.php');

class IndexController extends Controller {
    private JSONModel $model;
    public function __construct() {
        $this->model = new JSONModel();
    }

    public function indexAction() {
        $this->view->tasks = $this->model->read();
    }

    public function createTaskAction() {
        $name   = $this->_getParam('task-name');
        $status = $this->_getParam('task-status');
        $user   = $this->_getParam('user-name');
        try {
            if (!$this->isFieldValid($status)) {
                throw new Exception('Not a valid task name');
            }
            if (!$this->isFieldValid($user)) {
                throw new Exception('Not a valid user');
            }
            $this->model->createTask(new Task(trim($name), TaskStatus::from($status), $user));
        } catch (Exception $err) {
            $_SESSION['error'] = $err->getMessage();
        }

        header('Location: ' . $this->_baseUrl() . '/');
        exit;
    }

    public function updateTaskAction() {
        $original = $this->_getParam('original-name');
        $name     = $this->_getParam('task-name');
        $user     = $this->_getParam('user-name');
        $status   = $this->_getParam('task-status');

        try {
            if (!$this->isFieldValid($original)) {
                throw new Exception('Task not found');
            }
            if (!$this->isFieldValid($name)) {
                throw new Exception('Not a valid task name');
            }
            if (!$this->isFieldValid($user)) {
                throw new Exception('Not a valid user');
            }

            $updated = $this->model->update($original, [
                'name'   => trim($name),
                'user'   => trim($user),
                'status' => $status,
            ]);
            if (!$updated) {
                throw new Exception('Task not found');
            }
        } catch (Exception $err) {
            $_SESSION['error'] = $err->getMessage();
        }

        header('Location: ' . $this->_baseUrl() . '/');
        exit;
    }

    // Valida que sea string, no vacío y con menos de $limit caracteres
    private function isFieldValid($text): bool {
        // is_string() ya descarta null, números, arrays...
        if (!is_string($text)) {
            return false;
        }
        $text = trim($text);

        return $text !== '' && mb_strlen($text) < 20;
    }
}
