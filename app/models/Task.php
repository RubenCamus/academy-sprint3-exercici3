<?php

enum TaskStatus: string {
    case PENDING = 'pending';
    case DOING = 'doing';
    case DONE = 'done';
}

class Task {
    public function __construct(
        public string $taskName,
        public TaskStatus $taskStatus = TaskStatus::PENDING,
        public ?DateTime $startDate = null,
        public ?DateTime $endDate = null
    ) {
        $this->startDate ??= new DateTime();
    }

    public function getArray(): array {
        return [
            'name'      => $this->taskName,
            'status'    => $this->taskStatus->value,
            'startDate' => $this->startDate->format('Y-m-d H:i:s'),
            'endDate'   => $this->endDate?->format('Y-m-d H:i:s'),
        ];
    }
}
