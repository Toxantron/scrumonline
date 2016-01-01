<?php
// Execute incoming request and return json reponse
$result = $controller->execute();

echo json_encode($result);
?>