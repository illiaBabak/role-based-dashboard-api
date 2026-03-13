<?php

declare(strict_types=1);

namespace App\Models;

use mysqli;
use Core\MySQLConnect;

final class SessionsModel
{
    private ?mysqli $connect = null;

    public function __construct()
    {
        $this->connect = new MySQLConnect()->getConnection();
    }

    public function createSession(int $user_id, string $token, string $expires_at)
    {
        $stmt = $this->connect->prepare("INSERT INTO user_sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $token, $expires_at);
        $result = $stmt->execute();

        return $result;
    }

    public function getSessionByToken(string $token)
    {
        $stmt = $this->connect->prepare("SELECT * FROM user_sessions WHERE token = ? LIMIT 1");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $result = $stmt->get_result();

        $session = $result->fetch_assoc();

        return $session ?? null;
    }

    public function deleteSessionByUserId(int $user_id)
    {
        $stmt = $this->connect->prepare("DELETE FROM user_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);

        return $stmt->execute();
    }
}
