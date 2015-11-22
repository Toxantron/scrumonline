<?php
require_once "controller.php";

$post = file_get_contents('php://input');
$data = json_decode($post, true);

$controller->placeVote($data["sessionId"], $data["memberId"], $data["vote"]);