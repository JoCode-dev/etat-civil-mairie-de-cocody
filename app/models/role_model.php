<?php
class RoleModel {
    private $id;
    private $titre;
    private $description;
    private $is_active;
    private $created_at;

    // Constructor
    public function __construct(
        $id = null,
        $titre = null,
        $description = null,
        $is_active = true,
        $created_at = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->is_active = $is_active;
        $this->created_at = $created_at;
    }

    // Static method to create an object from array
    static public function fromArray($row): RoleModel {
        return new RoleModel(
            $row['id'] ?? null,
            $row['titre'] ?? null,
            $row['description'] ?? null,
            $row['is_active'] ?? true,
            $row['created_at'] ?? null
        );
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function setTitre($titre) {
        $this->titre = $titre;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getIsActive() {
        return $this->is_active;
    }

    public function setIsActive($is_active) {
        $this->is_active = $is_active;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

  
}