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

    $stmt = $db->prepare('SELECT * FROM users ORDER BY active DESC, name ASC');
    $stmt->execute();

    $users = array();
    while($u = $stmt->fetch()) {
        $users[$u['id']] = $u;
    };

    $slots = array(
        'morning' => '9:00',
        'afternoon' => '12:30',
        'evening' => '18:00'
    );

    $daysOfWeek = array(
        'Dimanche',
        'Lundi',
        'Mardi',
        'Mercredi',
        'Jeudi',
        'Vendredi',
        'Samedi',
    );

    $months = array(
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre'
    );
?>