<?php
// index.php - Flight bootstrap for club routes
require 'vendor/autoload.php';
require_once 'db.php';
require_once 'dao/BilliardClubDAO.php';

Flight::map('db', function() {
    return DB::getConnection();
});

// Simple CORS for local dev
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

// GET list with member counts
Flight::route('GET /api/clubs', function() {
    $dao = new BilliardClubDAO();
    $list = $dao->findAllWithMemberCount();
    Flight::json($list);
});

// GET single club
Flight::route('GET /api/clubs/@id', function($id) {
    $dao = new BilliardClubDAO();
    $club = $dao->findById($id);
    if (!$club) Flight::halt(404, json_encode(['error'=>'Not found']));
    Flight::json($club);
});

// CREATE club (POST) - later protect with JWT
Flight::route('POST /api/clubs', function() {
    $data = Flight::request()->data->getData();
    $dao = new BilliardClubDAO();
    $id = $dao->create($data);
    Flight::json(['id' => $id], 201);
});

// UPDATE club (PUT)
Flight::route('PUT /api/clubs/@id', function($id) {
    $data = Flight::request()->data->getData();
    $dao = new BilliardClubDAO();
    $ok = $dao->update($id, $data);
    Flight::json(['success' => (bool)$ok]);
});

// DELETE club
Flight::route('DELETE /api/clubs/@id', function($id) {
    $dao = new BilliardClubDAO();
    $ok = $dao->delete($id);
    Flight::json(['success' => (bool)$ok]);
});

Flight::start();
