<?php
// public/ajax/get_subjects.php
require_once __DIR__ . '/../../controllers/ClassroomController.php';

$controller = new ClassroomController();
$controller->getSubjectsAjax();
