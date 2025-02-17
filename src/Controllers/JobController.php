<?php

namespace App\Controllers;

use App\Services\JobService;
use App\Controllers\ResponseController;
use Exception;
use JsonSerializable;

class JobController extends ResponseController
{
    private $job_service;

    public function __construct(JobService $job_service) 
    {
        $this->job_service = $job_service;
    }

    public function searchJobOffers($request)
    {
        try {
            if (!is_array($request)) {
                throw new Exception("Invalid request parameters");
            }

            $result = $this->job_service->searchJobOffers($request);
            if (empty($result)) {
                self::responseSuccess("No job offers found.");
            } else {
                self::responseSuccess("Job offers found successfully:", $result);
            }
        } catch (Exception $e) {
            self::responseError(500, "Error searching job offers: " . $e->getMessage());           
        }
    }

    public function createJobOffer($body) {
        try {
            if (!is_array($body) || empty($body)) {
                throw new Exception("Invalid job offer data");
            }

            $createdJob = $this->job_service->createJobOffer($body);
            self::responseSuccess("Job offer created successfully", $createdJob['data']);
        } catch (Exception $e) {
            self::responseError(500, "Error creating job offer: " . $e->getMessage());
        }
    }
}
