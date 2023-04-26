<?php

require "vendor/autoload.php";

session_start();

use App\QuestionManager;

$score = null;
$answers = [];
$results = [];

try {
    $manager = new QuestionManager;
    $manager->initialize();

    if (!isset($_SESSION['answers'])) {
        throw new Exception('Missing answers');
    }

    $answers = $_SESSION['answers'];
    $score = $manager->computeScore($answers);
    $results = $manager->results($answers);

    $file = "results.txt";
    $txt = fopen($file, "w") or die("Unable to open file!");

    $output = "Complete Name: ".$_SESSION['user_fullname']."\n";
    $output .= "Email: ".$_SESSION['user_email']."\n";
    $output .= "Birthdate: ".$_SESSION['user_birthdate']."\n";
    $output .= "Score: ".$score." out of ".$manager->getQuestionSize()."\n";
    $output .= "Answers:\n\n";

    foreach ($results as $number => $answer) {
        if ($answer[1] == 1) {
            $output .= $number + 1 . ") " . $answer[0] . " (correct) \n";
        } else {
            $output .= $number + 1 . ") " . $answer[0] . " (incorrect) \n";
        }
    }

    fwrite($txt, $output);
    fclose($txt);

    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    header('Content-Type: text/plain');
    readfile($file);
    exit;

} catch (Exception $e) {
    echo '<h1>An error occurred:</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
    exit;
}