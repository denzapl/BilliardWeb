<?php
namespace App\Services;

use App\DAO\ReviewDAO;

class ReviewService {
    private $dao;

    public function __construct() {
        $this->dao = new ReviewDAO();
    }

    public function getAllReviews() {
        // This method will later fetch reviews joined with user & club names
        return $this->dao->getAll();
    }

    public function createReview($data) {
        if (empty($data['club_id']) || empty($data['user_id'])) {
            throw new \Exception("Club ID and User ID are required.");
        }
        if (isset($data['rating']) && ($data['rating'] < 1 || $data['rating'] > 5)) {
            throw new \Exception("Rating must be between 1 and 5.");
        }
        return $this->dao->create($data);
    }
}
