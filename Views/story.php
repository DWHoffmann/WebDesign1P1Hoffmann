<?php
session_start();
require_once '../models/database.php';
require_once '../Controllers/auth.php';
require_once '../logger.php';

// Check if user is logged in, redirect to login page if not
if (!isLoggedIn()) {
    logMessage("Unauthorized access attempt to story.php");
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$step = isset($_POST['step']) ? intval($_POST['step']) : 1;



// Story content
$story = [
    1 => [
        'question' => 'Choose a name for the adventurer:',
        'choices' => ['Bob', 'Lizzy']
    ],
    2 => [
        'question' => 'Hello ' . (isset($_SESSION['name']) ? $_SESSION['name'] : 'Adventurer') . ', choose a destination:',
        'choices' => ['Walmart', 'Festival'],
        'narrative' => 'As they set off on their adventure, they think about what lies ahead.'
    ],
    3 => [
        'question' => 'Now that they have arrived at ' . (isset($_SESSION['destination']) ? $_SESSION['destination'] : 'their destination') . ', choose what they should purchase:',
        'choices' => ['Carrots', 'Lettuce'],
        'narrative' => 'With their destination decided, they walk through the aisles wondering what to buy.'
    ]
];

// Endings
$endings = [
    'Lettuce' => 'After grabbing some Lettuce, ' . (isset($_SESSION['name']) ? $_SESSION['name'] : 'the adventurer') . ' recalls their earlier choice of going to ' . (isset($_SESSION['destination']) ? $_SESSION['destination'] : 'the Festival') . '. They head home feeling satisfied and spend the evening petting their cat.',
    'Carrots' => 'With Carrots in hand, ' . (isset($_SESSION['name']) ? $_SESSION['name'] : 'the adventurer') . ' thinks back on your visit to ' . (isset($_SESSION['destination']) ? $_SESSION['destination'] : 'Walmart') . '. They return home to their loyal dog, who greets them with excitement.'
];

// Handle user choices and start over action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If the user chooses to start over, reset progress
    if (isset($_POST['start_over'])) {
        //becone
        $step = 1;
    } else {
        // Save user choice and progress through the story
        $choice = $_POST['choice'];
        if ($step == 1) {
            $_SESSION['name'] = $choice;
        } elseif ($step == 2) {
            $_SESSION['destination'] = $choice;
        } elseif ($step == 3) {
            $_SESSION['purchase'] = $choice;
        }
        
        $step++;
    }
}

// Max steps and handling ending
$max_steps = count($story);
if ($step > $max_steps) {
    $step = $max_steps + 1; // Go to ending state
}

// Show the current story step or ending
$current_step = $story[$step] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Story</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Interactive Story</h1>
        <?php if ($step <= $max_steps): ?>
            <h2><?php echo htmlspecialchars($current_step['question']); ?></h2>
            <form method="POST" action="story.php"> 
                <input type="hidden" id="step" name="step" value="<?php echo htmlspecialchars($step); ?>"> 
                <?php foreach ($current_step['choices'] as $choice): ?>
                    <button type="submit" name="choice" value="<?php echo htmlspecialchars($choice); ?>"><?php echo htmlspecialchars($choice); ?></button>
                <?php endforeach; ?>
            </form>
        <?php elseif ($step == $max_steps + 1): ?>
            <h2>Story Ending</h2>
            <?php
            if (isset($_SESSION['purchase']) && isset($endings[$_SESSION['purchase']])) {
                echo '<p>' . htmlspecialchars($endings[$_SESSION['purchase']]) . '</p>';
            } else {
                echo '<p>Thank you for playing! Your adventure has come to an end.</p>';
            }
            ?>
            <form method="POST" action="story.php">
                <button type="submit" name="start_over" value="1">Start Over</button>
            </form>
            <?php
            // Clear session on story end or start over
            //session_destroy();
            ?>
        <?php endif; ?>
        <a href="../logout.php">Logout</a>
    </div>
    <script src="script.js"></script>
</body>
</html>
