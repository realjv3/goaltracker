<?php
// Routes

// Render goals view
$app->get('/', function ($request, $response, $args) {
    $db = $this->get('db');
    $stmt = $db->prepare("
            SELECT
                goals.goal_name,
                goals.due_date
            FROM
                users
            JOIN
                goals ON users.id = goals.user_id
            WHERE
                goals.user_id = '$_SESSION[logged_in]'
        ");
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $this->renderer->render($response, 'goals.html', array('title' => 'Goal Tracker', 'goals' => $result));
});

// Render registration form view
$app->get('/register', function ($request, $response, $args) {
    return $this->renderer->render($response, 'register.html', array('title' => 'Create new user account'));
});

// Register a new user
// @TODO sanitize/validate formdata, implement CSRF protection, flash errors to view
$app->post('/reg_user', function ($request, $response, $args) {

    $db = $this->get('db');
    try {
        $db->beginTransaction();

        $stmt = $db->prepare('
            INSERT INTO
                users(email, password)
            VALUES
                ("'. $_POST['email'].'", "'.md5($_POST['password']).'");
        ');
        $stmt->execute() or die(print_r($stmt->errorInfo(), true));
        $db->commit();
    } catch(Exception $e) {
        $db->rollBack();
        die("Error inserting new user: $e->getMessage()");
    }
    foreach($db->query("SELECT id FROM users WHERE email='$_POST[email]' AND password='".md5($_POST['password'])."'") as $row)
        $_SESSION['logged_in'] = $row['id'];


    return $this->renderer->render($response, 'goals.html', array('title' => 'Goal Tracker'));
});

//Sign in User
// @TODO sanitize/validate formdata, implement CSRF protection, flash errors to view
$app->post('/login', function($request, $response, $args) {
    $db = $this->get('db');
    $count = $db->query("SELECT COUNT(*) FROM users WHERE email='$_POST[email]' AND password='".md5($_POST['password'])."'");
    if($count && $count->fetchColumn() > 0) {
        foreach($db->query("SELECT id FROM users WHERE email='$_POST[email]' AND password='".md5($_POST['password'])."'") as $row) {
            $_SESSION['logged_in'] = $row['id'];
        }
        $stmt = $db->prepare("
            SELECT
                goals.goal_name,
                goals.due_date
            FROM
                users
            JOIN
                goals ON users.id = goals.user_id
            WHERE
                goals.user_id = '$_SESSION[logged_in]'
        ");
        $stmt->execute();
        $result = $stmt->fetchAll();
    } else die("<p>Wrong email or password. <a href='/'>Try again.</a> </p>");

    return $this->renderer->render($response, 'goals.html', array('title' => 'Goal Tracker', 'goals' => $result));
});

//Sign out

//Save Goal
// @TODO sanitize/validate formdata, implement CSRF protection, flash errors to view
$app->post('/save_goal', function($request, $response, $args) {
    $db = $this->get('db');
    try {
        $db->beginTransaction();

        $stmt = $db->prepare('
            INSERT INTO
                goals(user_id, goal_name, due_date)
            VALUES
                ("'. $_SESSION['logged_in'].'", "'. $_POST['goal_name'].'", "'. $_POST['due_date'].'");
        ');
        $stmt->execute() or die(print_r($stmt->errorInfo(), true));
        $db->commit();
    } catch(Exception $e) {
        $db->rollBack();
        die("Error inserting new user: $e->getMessage()");
    }

    $stmt = $db->prepare("
            SELECT
                goals.goal_name,
                goals.due_date
            FROM
                users
            JOIN
                goals ON users.id = goals.user_id
            WHERE
                goals.user_id = '$_SESSION[logged_in]'
        ");
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $this->renderer->render($response, 'goals.html', array('title' => 'Goal Tracker', 'goals' => $result));
});

//Edit Goal

//Delete Goal