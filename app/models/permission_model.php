<?php
class PermissionModel {
    private $id;
    private $code;
    private $description;

    // Constructor
    public function __construct(
        $id = null,
        $code = null,
        $description = null
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->description = $description;
    }

    // Static method to create an object from array
    static public function fromArray($row): PermissionModel {
        return new PermissionModel(
            $row['id'] ?? null,
            $row['code'] ?? null,
            $row['description'] ?? null
        );
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    // Validation function
    public function isValid(): bool {
        return empty($this->getErreur());
    }

    public function getErreur(): array {
        $errors = [];
        if (empty($this->code) || strlen($this->code) < 3) {
            $errors['code'] = 'Le code est obligatoire et doit contenir au moins 3 caractères.';
        }
        if (empty($this->description)) {
            $errors['description'] = 'La description est obligatoire.';
        }
        return $errors;
    }
}