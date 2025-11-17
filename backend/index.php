<?php
// Define the ROOT path of your project (D:/xampp/htdocs/flightphp_project)
define('PROJECT_ROOT', dirname(__DIR__)); 

require PROJECT_ROOT . '/vendor/autoload.php';

// --- CONFIGURATION LOADING ---
$config = require __DIR__ . '/config.php';
$dbConfig = $config['db'];

// --- INCLUDES ---
require __DIR__ . '/dao/BilliardClubDAO.php';
require __DIR__ . '/services/BilliardClubService.php';
require __DIR__ . '/dao/CategoryDAO.php'; 
require __DIR__ . '/services/CategoryService.php';
require __DIR__ . '/dao/RoleDAO.php';      // NEW
require __DIR__ . '/services/RoleService.php';  // NEW
require __DIR__ . '/dao/UserDAO.php';      // NEW
require __DIR__ . '/services/UserService.php';  // NEW

use App\Services\BilliardClubService;
use App\DAO\BilliardClubDAO;
use App\Services\CategoryService;
use App\DAO\CategoryDAO;
use App\Services\UserService; // NEW
use App\DAO\UserDAO;       // NEW
use App\Services\RoleService; // NEW
use App\DAO\RoleDAO;       // NEW


// --- API CONFIGURATION ---
Flight::before('start', function(){
    header('Content-Type: application/json');
});

// Set the base URL for routing
Flight::set('flight.base_url', '/flightphp_project/backend'); 


// 1. MAP THE CLUB DAO & SERVICE 
Flight::map('clubDao', function() use ($dbConfig) {
    try {
        return new BilliardClubDAO($dbConfig);
    } catch (\Exception $e) {
        Flight::halt(500, "Database Connection Error (Club DAO): " . $e->getMessage());
    }
});

Flight::map('clubService', function() {
    $dao = Flight::clubDao(); 
    return new BilliardClubService($dao); 
});

// 2. MAP THE CATEGORY DAO & SERVICE
Flight::map('categoryDao', function() use ($dbConfig) {
    try {
        return new CategoryDAO($dbConfig);
    } catch (\Exception $e) {
        Flight::halt(500, "Database Connection Error (Category DAO): " . $e->getMessage());
    }
});

Flight::map('categoryService', function() {
    $dao = Flight::categoryDao(); 
    return new CategoryService($dao); 
});

// 3. NEW: MAP THE ROLE DAO & SERVICE
Flight::map('roleDao', function() use ($dbConfig) {
    try {
        return new RoleDAO($dbConfig);
    } catch (\Exception $e) {
        Flight::halt(500, "Database Connection Error (Role DAO): " . $e->getMessage());
    }
});

Flight::map('roleService', function() {
    $dao = Flight::roleDao(); 
    return new RoleService($dao); 
});

// 4. NEW: MAP THE USER DAO & SERVICE
Flight::map('userDao', function() use ($dbConfig) {
    try {
        return new UserDAO($dbConfig);
    } catch (\Exception $e) {
        Flight::halt(500, "Database Connection Error (User DAO): " . $e->getMessage());
    }
});

Flight::map('userService', function() {
    $dao = Flight::userDao(); 
    return new UserService($dao); 
});


// --- API ENDPOINTS ---

// GET /api/v1/clubs (Read All Clubs)
Flight::route('GET /api/v1/clubs', function(){
    try {
        $clubs = Flight::clubService()->getAllClubs(); 
        Flight::json(['success' => true, 'data' => $clubs], 200);
    } catch (\Exception $e) {
        error_log("API Error: " . $e->getMessage()); 
        Flight::json(['success' => false, 'message' => 'Internal Server Error.'], 500);
    }
});

// POST /api/v1/clubs (Create New Club)
Flight::route('POST /api/v1/clubs', function(){
    $requestData = json_decode(Flight::request()->getBody(), true);
    if (empty($requestData)) {
        $requestData = Flight::request()->data;
    }

    try {
        $clubId = Flight::clubService()->createClub($requestData);
        Flight::json(['success' => true, 'id' => $clubId, 'message' => 'Club created successfully.'], 201); 
    } catch (\Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
    }
});

// GET /api/v1/clubs/{id} (Read Single Club)
Flight::route('GET /api/v1/clubs/@id', function($id){
    try {
        $club = Flight::clubService()->getClubById((int)$id);
        Flight::json(['success' => true, 'data' => $club], 200);
    } catch (\Exception $e) {
        $httpCode = $e->getCode() === 400 || $e->getCode() === 404 ? $e->getCode() : 500;
        Flight::json(['success' => false, 'message' => $e->getMessage()], $httpCode); 
    }
});

// PUT /api/v1/clubs/{id} (Update Club)
Flight::route('PUT /api/v1/clubs/@id', function($id){
    $requestData = json_decode(Flight::request()->getBody(), true);
    if (empty($requestData)) {
        $requestData = Flight::request()->data;
    }
    
    try {
        Flight::clubService()->updateClub((int)$id, $requestData);
        Flight::json(['success' => true, 'message' => "Club ID {$id} updated successfully."], 200); 
    } catch (\Exception $e) {
        $httpCode = $e->getCode() === 400 || $e->getCode() === 404 ? $e->getCode() : 500;
        Flight::json(['success' => false, 'message' => $e->getMessage()], $httpCode); 
    }
});


// DELETE /api/v1/clubs/{id} (Delete Club)
Flight::route('DELETE /api/v1/clubs/@id', function($id){
    try {
        Flight::clubService()->deleteClub((int)$id);
        // Return 204 No Content for a successful deletion
        Flight::json(['success' => true, 'message' => "Club ID {$id} deleted successfully."], 200); 
    } catch (\Exception $e) {
        $httpCode = $e->getCode() === 400 || $e->getCode() === 404 ? $e->getCode() : 500;
        Flight::json(['success' => false, 'message' => $e->getMessage()], $httpCode); 
    }
});


// ----------------------------------------------------
// CATEGORY ENDPOINTS 
// ----------------------------------------------------

// GET /api/v1/categories (Read All Categories)
Flight::route('GET /api/v1/categories', function() { 
    try {
        $categories = Flight::categoryService()->getAllCategories();
        Flight::json(['success' => true, 'data' => $categories], 200);
    } catch (\Exception $e) {
        error_log("Category API Error: " . $e->getMessage());
        // Fallback or actual error
        Flight::json(['success' => false, 'message' => 'Failed to load categories.'], 500); 
    }
});

// POST /api/v1/categories (Create New Category)
Flight::route('POST /api/v1/categories', function(){
    $requestData = json_decode(Flight::request()->getBody(), true);
    if (empty($requestData)) {
        $requestData = Flight::request()->data;
    }
    
    try {
        $categoryId = Flight::categoryService()->createCategory($requestData);
        Flight::json(['success' => true, 'id' => $categoryId, 'message' => 'Category created successfully.'], 201);
    } catch (\Exception $e) {
        Flight::json(['success' => false, 'message' => $e->getMessage()], 400); 
    }
});

// GET /api/v1/categories/{id} (Read Single Category)
Flight::route('GET /api/v1/categories/@id', function($id){
    try {
        $category = Flight::categoryService()->getCategoryById((int)$id);
        Flight::json(['success' => true, 'data' => $category], 200);
    } catch (\Exception $e) {
        $httpCode = $e->getCode() === 400 || $e->getCode() === 404 ? $e->getCode() : 500;
        Flight::json(['success' => false, 'message' => $e->getMessage()], $httpCode); 
    }
});

// PUT /api/v1/categories/{id} (Update Category)
Flight::route('PUT /api/v1/categories/@id', function($id){
    $requestData = json_decode(Flight::request()->getBody(), true);
    if (empty($requestData)) {
        $requestData = Flight::request()->data;
    }
    
    try {
        Flight::categoryService()->updateCategory((int)$id, $requestData);
        Flight::json(['success' => true, 'message' => "Category ID {$id} updated successfully."], 200); 
    } catch (\Exception $e) {
        $httpCode = $e->getCode() === 400 || $e->getCode() === 404 ? $e->getCode() : 500;
        Flight::json(['success' => false, 'message' => $e->getMessage()], $httpCode); 
    }
});


// DELETE /api/v1/categories/{id} (Delete Category)
Flight::route('DELETE /api/v1/categories/@id', function($id){
    try {
        Flight::categoryService()->deleteCategory((int)$id);
        // Return 204 No Content for a successful deletion
        Flight::json(['success' => true, 'message' => "Category ID {$id} deleted successfully."], 200); 
    } catch (\Exception $e) {
        $httpCode = $e->getCode() === 400 || $e->getCode() === 404 ? $e->getCode() : 500;
        Flight::json(['success' => false, 'message' => $e->getMessage()], $httpCode); 
    }
});

// ----------------------------------------------------
// NEW: USER ENDPOINTS 
// ----------------------------------------------------

/**
 * Endpoint to fetch the current user's profile. 
 * Since we don't have authentication, we will hardcode the ID to 1.
 */
Flight::route('GET /api/v1/user/profile', function(){
    // In a real app, the ID would come from the session/token. Here, we mock the primary admin user ID 1.
    $userId = 1; 
    try {
        $userProfile = Flight::userService()->getUserProfile($userId);
        Flight::json(['success' => true, 'data' => $userProfile], 200);
    } catch (\Exception $e) {
        $httpCode = $e->getCode() === 404 ? 404 : 500;
        Flight::json(['success' => false, 'message' => $e->getMessage()], $httpCode); 
    }
});

Flight::route('/documentation.html', function() {
    // We assume 'documentation.html' is located in the same directory as 'index.php'.
    $filePath = __DIR__ . '/documentation.html';

    // The Flight::file() method reads the file and outputs it with the correct
    // MIME type (text/html) automatically.
    Flight::file($filePath, 'text/html');

    // Optionally, if the documentation depends on an openapi.json file 
    // (which is common for Swagger/OpenAPI docs), you might need a route for that too:
    // Flight::file(__DIR__ . '/openapi.json', 'application/json'); 
});
// Fallback for unmatched API routes
Flight::route('/api/v1/*', function(){
    Flight::json(['success' => false, 'message' => 'API endpoint not found.'], 404);
});


Flight::start();