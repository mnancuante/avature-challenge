<?php

namespace App\Repositories;

use App\JobOfferFormatter\JobOfferDTO;
use Exception;

final class ExternalJobRepository
{
    private $url;

    public function __construct($api_url)
    {
        $this->url = $api_url;
    }

    public function getExternalJobOffers(mixed $params)
    {
        $query_params = http_build_query($params);
        $url = $this->url . '?' . $query_params;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code !== 200) {
            throw new Exception("Error fetching external job offers: HTTP code $http_code");
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding external job offers JSON");
        }
        $external_id_counter = 1;
        $job_offers = [];
        foreach ($data as $country => $offers) {
            foreach ($offers as $offer) {
                $job_offers[] = new JobOfferDTO(
                    'ext_' . $external_id_counter++, // temporary id
                    $offer[0], // title
                    $offer[1], // salary
                    $country,
                    "",
                    $this->extractSkills($offer[2]) // skills
                );
            }
        }

        return $job_offers;
    }

    private function extractSkills(mixed $skillsXml): string
    {
        $skills = [];
        $xml = simplexml_load_string($skillsXml);
        foreach ($xml->skill as $skill) {
            $skills[] = (string)$skill;
        }
        
        $response = implode(", ", $skills);
        return $response;
    }
}
