# avature-challenge

Avature Challenge

Description

This repository contains an API that allows querying and creating job offers in a proprietary database. When querying job offers, the API simultaneously retrieves data from an external resource and merges the results from both sources. Additionally, the API integrates with MailTrap to send email notifications containing job offer details every time a new job offer is created.

Project Structure

/avature/
├── api/
│   ├── index.php
│
├── config/
│   ├── config.php
│   ├── dependencies.php
│
├── sql/
│   ├── setup.sql
│
├── src/
│   ├── Controllers/
│   │   ├── JobController.php
│   │   ├── ResponseController.php
│   │
│   ├── Services/
│   │   ├── JobService.php
│   │   ├── ExternalJobService.php
│   │   ├── MailService.php
│   │
│   ├── Repositories/
│   │   ├── JobRepository.php
│   │   ├── ExternalJobRepository.php
│   │
│   ├── Database/
│   │   ├── Database.php
│   │
│   ├── JobOfferFormatter/
│   │   ├── JobOfferDTO.php
│
├── vendor/
│   ├── autoload.php
│
├── .htaccess
├── composer.json
├── composer.lock
├── README.md

- Requirements

To run the project, you need the following:

XAMPP (Apache, PHP, MySQL/MariaDB)

Composer

External Resource: Jobberwocky Extra Source v2: https://github.com/avatureta/jobberwocky-extra-source-v2

PHPmailer (for sending emails)

PHP DI (dependency injection)

API Testing Platform (e.g., Thunder Client for Visual Studio Code, Postman, or any other API testing tool)

- Installation & Setup

Clone the repository into Applications/XAMPP/xamppfiles/htdocs/

git clone https://github.com/mnancuante/avature-challenge.git

cd Applications/XAMPP/xamppfiles/htdocs/

Install dependencies

composer install

- Set up the database

Import setup.sql into your MySQL/MariaDB instance.

Configure environment variables

Edit config/config.php to set database credentials and external API configurations if needed.

Run the external API

Start Apache and MySQL using XAMPP.

Ensure your PHP environment is correctly set up.

Test the API

Use Thunder Client, Postman, or any API testing tool to make requests.

Usage

Retrieve Job Offers: Make a GET request to /api/index.php

You can filter using the optional parameters: title, salary_min, salary_max and country. 

Create a Job Offer: Send a POST request to /api/index.php with job offer details in the request body, with this format:

The description parameter is optional, and you must insert at least 1 skill.

{
  "title": "Fullstack Web Developer",
  "country": "Argelia",
  "salary": "70000",
  "description": "Designs and creates web apps"
  "skills": ["PHP", "SQL", "Laravel"]
}

Upon creation, an email notification is sent using MailTrap (You must login into MailTrap in order to receive the notification). 

