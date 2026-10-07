<?php

$img = $_POST['signature'];
$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img));
file_put_contents('storage/signature.png', $data);
