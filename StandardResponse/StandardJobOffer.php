<?php
// src/StandardResponse/StandardJobOffer.php

namespace App\StandardResponse;

class StandardJobOffer
{
    public $title;
    public $salary;
    public $country;
    public $description;
    public $skills;

    public static function fromExternalSourceArray(array $data): self
    {
        $job_offer = new self();
        $job_offer->title = $data['job_title'] ?? $data['title'] ?? '';
        $job_offer->salary = $data['salary'] ?? 0;
        $job_offer->country = $data['location'] ?? $data['country'] ?? '';
        $job_offer->description = $data['job_description'] ?? $data['description'] ?? '';
        $job_offer->skills = $data['required_skills'] ?? $data['skills'] ?? [];
        return $job_offer;
    }

    public static function extractSkills(string $skillsXml): array
    {
        $skills = [];
        try {
            $xml = simplexml_load_string("<root>$skillsXml</root>");
            if ($xml !== false && isset($xml->skills)) {
                foreach ($xml->skills->skill as $skill) {
                    $skills[] = (string) $skill;
                }
            }
        } catch (\Exception $e) {
            // Manejar el error si el XML no es válido
            error_log("Error parsing XML: " . $e->getMessage());
        }
        return $skills;
    }

    public function dataToArray(): array
    {
        return [
            'title' => $this->title,
            'salary' => $this->salary,
            'country' => $this->country,
            'description' => $this->description,
            'skills' => $this->skills,
        ];
    }

}
