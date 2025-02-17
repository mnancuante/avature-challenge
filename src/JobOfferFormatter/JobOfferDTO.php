<?php

namespace App\JobOfferFormatter;

class JobOfferDTO {
    public $id;
    public $title;
    public $salary;
    public $country;
    public $description;
    public $skills;

    public function __construct($id, $title, $salary, $country, $description, $skills) {
        $this->id = $id;
        $this->title = $title;
        $this->salary = $salary;
        $this->country = $country;
        $this->description = $description;
        $this->skills = $skills;
    }

    // Método para convertir el DTO a un array (opcional)
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'salary' => $this->salary,
            'country' => $this->country,
            'description' => $this->description,
            'skills' => $this->skills
        ];
    }
}