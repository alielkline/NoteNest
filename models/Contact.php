<?php

class Contact {
    public function save($name, $email, $message) {
        // Log it or store it in DB. For now, just write to a file.
        $log = "From: $name <$email>\nMessage: $message\n---\n";
        file_put_contents(__DIR__ . '/../storage/contact.log', $log, FILE_APPEND);
    }
}
