<?php
// app/core/Database.php

class Database
{
    private static $pdo = null;

    public static function getConnection()
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=localhost;port=3307;dbname=notenestdb';
            $username = 'root';
            $password = '';

            try {
                self::$pdo = new PDO($dsn, $username, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed :( " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
