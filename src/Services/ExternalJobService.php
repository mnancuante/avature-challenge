<?php

namespace App\Services;

use App\Repositories\ExternalJobRepository;
use Exception;

class ExternalJobService
{
    private $external_job_repository;

    public function __construct(ExternalJobRepository $external_job_repository)
    {
        $this->external_job_repository = $external_job_repository;
    }

    public function getExternalJobOffers($title, $salary_min, $salary_max, $country)
    {
        $params = [];

        if (isset($title)) {
            $params['name'] = $title;
        }

        if (isset($salary_min)) {
            $params['salary_min'] = $salary_min;
        }

        if (isset($salary_max)) {
            $params['salary_max'] = $salary_max;
        }

        if (isset($country)) {
            $params['country'] = $country;
        }

        return $this->external_job_repository->getExternalJobOffers($params);

    }
}
