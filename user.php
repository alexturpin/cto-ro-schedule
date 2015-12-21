<?php
    session_start();

    $user = false;
    if (isset($_SESSION['user'])) {
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(array(
            'id' => $_SESSION['user']
        ));

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
        }
    }
?>