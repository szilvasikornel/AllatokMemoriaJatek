<?php
session_start();

// Reset the game if "reset" is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Initialize the game
if (!isset($_SESSION['cards'])) {
    $animals = [
        'Panda' => 'img/_Panda.JPG',
        'Tiger' => 'img/images.jpg',
        'Elephant' => 'img/1033551-elephant.jpg',
        'Rhino' => 'img/3yuabfu3jq_white_rhino_42993643.jpg',
        'Turtle' => 'img/hawksbill_sea_turtle.jpg',
        'Penguin' => 'img/pinguin.jpg',
    ];    

    $names = array_keys($animals);
    $images = array_values($animals);

    // Shuffle the cards, mixing names and images
    $cards = array_merge($names, $images); 
    shuffle($cards);

    $_SESSION['cards'] = $cards;
    $_SESSION['flipped'] = [];
    $_SESSION['matched'] = [];
}

// Handle card flip
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_index'])) {
    $index = (int)$_POST['card_index'];

    if (!in_array($index, $_SESSION['flipped']) && !in_array($index, $_SESSION['matched'])) {
        $_SESSION['flipped'][] = $index;

        // Check for match if two cards are flipped
        if (count($_SESSION['flipped']) === 2) {
            $first = $_SESSION['flipped'][0];
            $second = $_SESSION['flipped'][1];

            $firstCard = $_SESSION['cards'][$first];
            $secondCard = $_SESSION['cards'][$second];

            // Check for matching pairs (name and corresponding image)
            define('IMG_PATH', 'img/');
            $animals = [
                'Panda' => IMG_PATH . '_Panda.JPG',
                'Tiger' => IMG_PATH . 'images.jpg',
                'Elephant' => IMG_PATH . '1033551-elephant.jpg',
                'Rhino' => IMG_PATH . '3yuabfu3jq_white_rhino_42993643.jpg',
                'Turtle' => IMG_PATH . 'hawksbill_sea_turtle.jpg',
                'Penguin' => IMG_PATH . 'pinguin.jpg',
            ];

            // Check for matching pairs (name and corresponding image)
            if ((isset($animals[$firstCard]) && $animals[$firstCard] === $secondCard) ||
                (isset($animals[$secondCard]) && $animals[$secondCard] === $firstCard)) {
                $_SESSION['matched'] = array_merge($_SESSION['matched'], $_SESSION['flipped']);
            }

            // Reset flipped cards after the game logic
            $_SESSION['flipped'] = [];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Veszélyeztetett állatok memóri játék</title>
</head>
<body>
    <h1>Veszélyeztetett állatok memóri játéka</h1>
    <h4>Párosítsd a képeket a hozzájuk tartozó nevekkel!</h4>
    <form method="post">
        <div class="grid">
            <?php foreach ($_SESSION['cards'] as $index => $card): ?>
                <button type="submit" name="card_index" value="<?= $index ?>"
                        class="card <?= in_array($index, $_SESSION['matched']) ? 'matched' : '' ?>"
                        <?= in_array($index, $_SESSION['matched']) ? 'disabled' : '' ?>>
                    <?php if (in_array($index, $_SESSION['flipped']) || in_array($index, $_SESSION['matched'])): ?>
                        <?php if (str_ends_with($card, '.jpg') || str_ends_with($card, '.JPG')): ?>
                            <img src="<?= $card ?>" alt="Image of <?= $card ?>">
                        <?php else: ?>
                            <?= $card ?>
                        <?php endif; ?>
                    <?php else: ?>
                        ?
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="actions">
            <button type="submit" name="reset" class="gomb">Újraindítás</button>
        </div>
    </form>

    <script>
        let flippedCards = <?= json_encode($_SESSION['flipped']) ?>;

        // Check if the game is finished and display winner message until user clicks reset
        <?php if (count($_SESSION['matched']) === count($_SESSION['cards'])): ?>
            // Show winning message for a longer time
            document.addEventListener('DOMContentLoaded', function() {
                const winMessage = document.createElement('h2');
                winMessage.innerHTML = 'Szép munka! Minden állatot összepárosított!';
                document.body.appendChild(winMessage);

                // Display the message until reset button is clicked
                const resetButton = document.querySelector('button[name="reset"]');
                resetButton.addEventListener('click', function() {
                    // Hide the win message when reset is clicked
                    winMessage.style.display = 'none';
                });
            });
        <?php endif; ?>
    </script>

</body>
</html>
