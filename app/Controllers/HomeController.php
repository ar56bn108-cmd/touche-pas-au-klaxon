<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Agence;
use PDO;

class HomeController
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function index(): void
    {
        $agenceModel = new Agence($this->pdo);
        $agences = $agenceModel->getAll();

        require __DIR__ . '/../Views/home.php';
    }
}