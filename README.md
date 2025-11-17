🚀 Flight Micro-API Backend

Project Overview

This repository hosts a high-performance, lightweight Micro-API built using the Flight PHP Micro-Framework. The project is designed to provide rapid prototyping and efficient routing for backend services.

The entire application relies on a single entry point (index.php) and is configured to handle RESTful routing, including the serving of static documentation files.

💻 Setup and Installation

Prerequisites

To run this API locally, you need the following installed on your machine:

PHP (Version 7.4 or higher)

Composer (PHP dependency manager)

A local web server (e.g., Apache, Nginx, or using PHP's built-in server for quick testing).

Step-by-Step Installation

Clone the Repository:

git clone [your-repository-url]
cd flight-micro-api


Install Dependencies (using Composer):
If you are using Composer to manage Flight and other packages, run:

composer install


(If using the core Flight distribution without Composer, ensure the flight/ directory is present.)

Configure Web Server:
Ensure your web server (or virtual host) is configured to use the project root as the document root and utilizes a rewrite rule (like .htaccess) to direct all non-file requests to index.php.

Start the Server (Local Testing):
You can use PHP's built-in development server for a quick start:

php -S 127.0.0.1:8080


🗺️ Key Routes and Usage

All API logic is contained within index.php. The following are the core routes available:

Method

Path

Description

GET

/

Default index page / simple status check.

GET

/api/users

Fetches a list of all registered users.

POST

/api/users

Creates a new user record.

GET

/documentation.html

Interactive API Documentation (Swagger UI).

📚 API Documentation (Swagger/OpenAPI)

The interactive documentation is automatically served by a dedicated Flight::route command within the index.php file, ensuring it loads correctly even when the framework is handling requests.

The documentation interface is available at:

http://127.0.0.1:8080/flightphp_project/backend/documentation.html

This link provides a full reference for all available endpoints, required parameters, and response schemas, generated via your OpenAPI/Swagger definition (which should be referenced by documentation.html).