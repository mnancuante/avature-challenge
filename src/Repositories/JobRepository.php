<?php
// src/Repositories/JobRepository.php
namespace App\Repositories;

use App\Database\Database;
use PDO;
use Exception;

final class JobRepository
{
    private $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function searchJobOffers(string|null $title, float|null $salary_min, float|null $salary_max, string|null $country)
    {
        $sql = "SELECT 
                    job_offers.*, 
                    GROUP_CONCAT(job_skill.name SEPARATOR ', ') AS skills
                FROM job_offers
                LEFT JOIN job_offer_skills ON job_offers.id = job_offer_skills.job_offer_id
                LEFT JOIN job_skill ON job_offer_skills.skill_id = job_skill.id
                WHERE 1=1";

        $params = [];

        if ($title) {
            $sql .= " AND job_offers.title LIKE :title";
            $params[':title'] = "%$title%";
        }
        if ($salary_min) {
            $sql .= " AND job_offers.salary >= :salary_min";
            $params[':salary_min'] = $salary_min;
        }
        if ($salary_max) {
            $sql .= " AND job_offers.salary <= :salary_max";
            $params[':salary_max'] = $salary_max;
        }
        if ($country) {
            $sql .= " AND job_offers.country LIKE :country";
            $params[':country'] = "%$country%";
        }

        $sql .= " GROUP BY job_offers.id;";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if (!$stmt->execute()) {
            throw new Exception('Error searching job offers: ' . implode(', ', $stmt->errorInfo()));
        }

        $jobs = $stmt->fetchAll();
        return $jobs;
    }

    public function createJobOffer($title, $salary, $country, $skills, $description)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO job_offers (title, salary, country, description) VALUES (:title, :salary, :country, :description)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':salary', $salary);
            $stmt->bindValue(':country', $country);
            $stmt->bindValue(':description', $description);

            if (!$stmt->execute()) {
                throw new Exception('Error creating job offer: ' . implode(', ', $stmt->errorInfo()));
            }

            $job_offer_id = $this->conn->lastInsertId();

            $existing_skills = $this->getExistingSkills($skills);

            foreach ($skills as $skill_name) {
                if (isset($existing_skills[$skill_name])) {
                    $skill_id = $existing_skills[$skill_name];
                } else {

                    $sql_insert_skill = "INSERT INTO job_skill (name) VALUES (:name)";
                    $stmt = $this->conn->prepare($sql_insert_skill);
                    $stmt->bindValue(':name', $skill_name);

                    if (!$stmt->execute()) {
                        throw new Exception('Error inserting skill: ' . implode(', ', $stmt->errorInfo()));
                    }

                    $skill_id = $this->conn->lastInsertId();
                    $existing_skills[$skill_name] = $skill_id;
                }
                // Insert the relationship between the job offer and the skill using the pivot table job_offer_skills
                $sql_insert_job_skill = "INSERT INTO job_offer_skills (job_offer_id, skill_id) VALUES (:job_offer_id, :skill_id)";
                $stmt = $this->conn->prepare($sql_insert_job_skill);
                $stmt->bindValue(':job_offer_id', $job_offer_id);
                $stmt->bindValue(':skill_id', $skill_id);

                if (!$stmt->execute()) {
                    throw new Exception('Error adding skills to job offer: ' . implode(', ', $stmt->errorInfo()));
                }
            }

            $this->conn->commit();
            return ['message' => 'Job offer created successfully',
                    'data' => [
                        'title' => $title,
                        'salary' => $salary,
                        'country' => $country,
                        'description' => $description,
                        'skills' => $skills
                    ]];
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception('Error creating job offer: ' . $e->getMessage());
        }
    }

    private function getExistingSkills($skills)
    {
        $placeholders = implode(',', array_fill(0, count($skills), '?'));
        $sql = "SELECT id, name FROM job_skill WHERE name IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);

        // Bind each skill name to the query
        foreach ($skills as $index => $skill_name) {
            $stmt->bindValue($index + 1, $skill_name);
        }

        $stmt->execute();

        $existing_skills = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $existing_skills[$row['name']] = $row['id'];
        }

        return $existing_skills;
    }

    public function jobOfferExists($title, $salary, $country, $skills)
    {

        $sql = "SELECT id FROM job_offers WHERE title = :title AND salary = :salary AND country = :country";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':salary', $salary);
        $stmt->bindValue(':country', $country);
        $stmt->execute();

        $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($offers)) {
            return false;
        }

        foreach ($offers as $offer) {
            $offer_id = $offer['id'];

            $sql_skills = "SELECT js.name 
                       FROM job_offer_skills jos
                       JOIN job_skill js ON jos.skill_id = js.id
                       WHERE jos.job_offer_id = :job_offer_id";
            $stmt_skills = $this->conn->prepare($sql_skills);
            $stmt_skills->bindValue(':job_offer_id', $offer_id);
            $stmt_skills->execute();

            $existing_skills = $stmt_skills->fetchAll(PDO::FETCH_COLUMN, 0);

            if (empty(array_diff($skills, $existing_skills)) && empty(array_diff($existing_skills, $skills))) {
                return true;
            }
        }
        return false;
    }
}
