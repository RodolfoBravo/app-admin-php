<?php
class User
{
    public static function getAllUsers()
    {
        $conn = Database::getConnection();
        $result = $conn->query('SELECT * FROM users');
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
