<?php
namespace App\Controllers;

use Mark\MjdCore\Http\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $userModel = new User();

        $allActivity = [
                ['date' => '2026-02-04 12:45', 'type' => 'USER_REGISTER', 'status' => 'SUCCESS'],
                ['date' => '2026-02-04 12:38', 'type' => 'DB_MIGRATION', 'status' => 'ERROR'],
        ];
        $perPage = 5;
        $totalItems = count($allActivity);
        $totalPages = ceil($totalItems / $perPage);

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;


        $offset = ($currentPage - 1) * $perPage;
        $pagedActivity = array_slice($allActivity, $offset, $perPage);

        return $this->view('dashboard', [
            'title'    => 'System Dashboard',
            'stats'    => [
                'user_count' => count($userModel->all()),
            ],
            'activity' => $pagedActivity,
            'pagination' => [
                'current' => $currentPage,
                'total'   => $totalPages,
                'has_next' => $currentPage < $totalPages,
                'has_prev' => $currentPage > 1
            ]
        ]);
    }
}