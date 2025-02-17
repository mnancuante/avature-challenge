<?php

// src/Services/JobService.php
namespace App\Services;

use App\Repositories\JobRepository;
use App\Services\ExternalJobService;
use App\JobOfferFormatter\JobOfferDTO;
use Exception;
use App\Services\MailService;

class JobService
{
    private $job_repository;
    private $external_job_service;
    public function __construct(JobRepository $job_repository, ExternalJobService $external_job_service)
    {
        $this->job_repository = $job_repository;
        $this->external_job_service = $external_job_service;
    }

    public function searchJobOffers(array $request) :array
    {
        $this->validateRequestParameters($request, ['title', 'salary_min', 'salary_max', 'country']);

        $title = $this->sanitizeAndValidateTextField($request, 'title', 'Title');
        $country = $this->sanitizeAndValidateTextField($request, 'country', 'Country');
        [$salary_min, $salary_max] = $this->validateAndSanitizeSalaries($request);

        $local_jobs = $this->job_repository->searchJobOffers($title, $salary_min, $salary_max, $country);
        $local_job_dtos = [];
        foreach ($local_jobs as $job) {
            $local_job_dtos[] = new JobOfferDTO(
                $job['id'],
                $job['title'],
                $job['salary'],
                $job['country'],
                $job['description'],
                $job['skills']
            );
        }

        $external_job_dtos = $this->external_job_service->getExternalJobOffers($title, $salary_min, $salary_max, $country);

        $all_job_offers = array_merge($local_job_dtos, $external_job_dtos);

        $response = [];
        foreach ($all_job_offers as $job_offer) {
            $response[] = $job_offer->toArray();
        }

        return $response;
    }

    public function createJobOffer(mixed $body): array
    {
        $this->validateRequest($body);
        // Sanitize and validate title
        $title = $this->sanitizeAndValidateTextField($body, 'title', 'Title');

        // Sanitize and validate country
        $country = $this->sanitizeAndValidateTextField($body, 'country', 'Country');

        // Validate salary
        $salary = $this->validateSalary($body);

        // Validate skills
        $skills = $this->validateSkills($body);

        $description = isset($body['description']) ? $body['description'] : null;

        // Check if job offer already exists
        if ($this->job_repository->jobOfferExists($title, $salary, $country, $skills, $description)) {
            throw new Exception('Job offer already exists');
        }

        // Call the repository
        $jobOffer = $this->job_repository->createJobOffer($title, $salary, $country, $skills, $description);
        
        if ($jobOffer) {
            $this->notifyUsers($jobOffer['data']);
        }
        return $jobOffer;
    }

    // Private methods to sanitize and validate
    private function validateRequestParameters($request, $valid_params)
    {
        $invalid_params = array_diff(array_keys($request), $valid_params);
        if (!empty($invalid_params)) {
            throw new Exception('Invalid parameter: ' . implode(', ', $invalid_params) . '. Valid parameters must be: ' . implode(', ', $valid_params));
        }
    }

    private function sanitizeAndValidateTextField($data, $field, $field_name)
    {
        if (isset($data[$field])) {
            $value = trim($data[$field]);
            if ($value === '') {
                throw new Exception("$field_name cannot be empty");
            }

            if (is_numeric($value)) {
                throw new Exception("$field_name cannot be numeric");
            }

            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            if (!preg_match('/^[a-zA-Z0-9\s\-.,áéíóúÁÉÍÓÚñÑ]+$/', $value)) {
                throw new Exception("$field_name contains invalid characters");
            }

            return $value;
        }

        return null;
    }

    private function validateAndSanitizeSalaries($request)
    {
        $salary_min = isset($request['salary_min']) ? filter_var($request['salary_min'], FILTER_VALIDATE_FLOAT) : null;
        $salary_max = isset($request['salary_max']) ? filter_var($request['salary_max'], FILTER_VALIDATE_FLOAT) : null;

        if (($salary_min !== null && !is_numeric($salary_min)) || ($salary_max !== null && !is_numeric($salary_max))) {
            throw new Exception('Invalid salary: Must be a number');
        }

        if ($salary_min < 0 || $salary_max < 0) {
            throw new Exception('Salary must be a positive number.');
        }

        if ($salary_min !== null && $salary_max !== null && $salary_min > $salary_max) {
            throw new Exception('Invalid salary range: Minimum salary cannot be greater than the maximum');
        }

        return [$salary_min, $salary_max];
    }

    private function validateSalary($body)
    {
        if (!isset($body['salary']) || trim($body['salary']) === '') {
            throw new Exception('Salary is required');
        }

        $salary = filter_var($body['salary'], FILTER_VALIDATE_FLOAT);

        if ($salary === false || $salary < 0) {
            throw new Exception('Salary must be a positive number');
        }

        return $salary;
    }

    private function validateSkills($body)
    {
        if (!isset($body['skills']) || !is_array($body['skills']) || empty($body['skills'])) {
            throw new Exception('At least one skill is required');
        }

        return array_map(function ($skill) {
            $skill = htmlspecialchars(trim($skill), ENT_QUOTES, 'UTF-8');
            if (empty($skill)) {
                throw new Exception('Skill cannot be empty');
            }
            return $skill;
        }, $body['skills']);
    }

    private function validateRequest($request)
    {
        if (!isset($request['title']) || !isset($request['country']) || !isset($request['salary'])) {
            throw new Exception('Title, salary and country are required fields');
        }
    }

    private function notifyUsers($job)
    {

        try {
            $mailService = new MailService();
    
            $destinatario = "destinatario@ejemplo.com";
            $asunto = "Nuevo trabajo insertado";
            $cuerpo = "<p>Hay una nueva alerta de trabajo disponible:</p>
                        <ul>
                        <li><strong>Titulo:</strong>{$job['title']}</li>
                        <li><strong>Salario:</strong>{$job['salary']} </li>
                        <li><strong>Descripcion:</strong>{$job['description']}</li>
                        </ul>";

            $mailService->sendMail($destinatario, $asunto, $cuerpo);
            } catch (Exception $e) {
            throw new Exception('Something went wrong ' . $e->getMessage());
        }
    }
}