<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArraySweeper Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }

        .game-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="number"],
        input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .game-message {
            margin-bottom: 10px;
        }

        .game-outcome {
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <?php
    session_start();

    // Check if form is submitted for player names and bomb locations
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['player1']) && isset($_POST['player2']) && isset($_POST['bomb1']) && isset($_POST['bomb2'])) {
        // Set player names and bomb locations
        $_SESSION['player1'] = $_POST['player1'];
        $_SESSION['player2'] = $_POST['player2'];
        $_SESSION['bomb1'] = $_POST['bomb1'];
        $_SESSION['bomb2'] = $_POST['bomb2'];
        $_SESSION['player1Hits'] = 0;
        $_SESSION['player2Hits'] = 0;
        $_SESSION['player1Attempts'] = 0;
        $_SESSION['player2Attempts'] = 0;
        $_SESSION['currentPlayer'] = 'player1'; // Start with player 1
    }

    // Check if form is submitted for game turns
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['index'])) {
        $currentPlayer = $_SESSION['currentPlayer'];
        $selectedIndex = $_POST['index'];

        // Check if the selected index hits the opponent's bomb
        if ($currentPlayer == 'player1' && $selectedIndex == $_SESSION['bomb2']) {
            $_SESSION['player1Hits']++;
            $message = $_SESSION['player1'] . ", Boom! You hit the bomb. You're dead!";
        } else if ($currentPlayer == 'player2' && $selectedIndex == $_SESSION['bomb1']) {
            $_SESSION['player2Hits']++;
            $message = $_SESSION['player2'] . ", Boom! You hit the bomb. You're dead!";
        } else {
            $message = $_SESSION['currentPlayer'] . ", Congratulations! You're alive.";
        }

        // Increase attempts for the current player
        if ($_SESSION['currentPlayer'] == 'player1') {
            $_SESSION['player1Attempts']++;
        } else {
            $_SESSION['player2Attempts']++;
        }

        // Switch to the next player or end the game
        if ($_SESSION['player1Attempts'] < 3 || $_SESSION['player2Attempts'] < 3) {
            $_SESSION['currentPlayer'] = $currentPlayer == 'player1' ? 'player2' : 'player1';
        } else {
            // Determine the game outcome
            if ($_SESSION['player1Hits'] > $_SESSION['player2Hits']) {
                $outcome = $_SESSION['player1'] . " wins!";
            } else if ($_SESSION['player2Hits'] > $_SESSION['player1Hits']) {
                $outcome = $_SESSION['player2'] . " wins!";
            } else {
                $outcome = "It's a draw!";
            }
            // Reset game variables
            session_unset();
            session_destroy();
        }
    }
    ?>

    <!-- HTML Form for Player Names and Bomb Locations -->
    <?php if (!isset($_SESSION['player1']) || !isset($_SESSION['player2'])) { ?>
        <form action="" method="POST" class="game-form">
            <label for="player1">Player 1 Name:</label>
            <input type="text" id="player1" name="player1" required><br>

            <label for="player2">Player 2 Name:</label>
            <input type="text" id="player2" name="player2" required><br>

            <label for="bomb1">Player 1 Bomb Location (0-8):</label>
            <input type="number" id="bomb1" name="bomb1" min="0" max="8" required><br>

            <label for="bomb2">Player 2 Bomb Location (0-8):</label>
            <input type="number" id="bomb2" name="bomb2" min="0" max="8" required><br>

            <input type="submit" value="Start Game">
        </form>
    <?php } ?>

    <!-- Display Game Messages with CSS class -->
    <?php if (isset($message)) { ?>
        <p class="game-message">
            <?php echo $message; ?>
        </p>
    <?php } ?>

    <?php if (isset($outcome)) { ?>
        <p class="game-outcome">
            <?php echo $outcome; ?>
        </p>
    <?php } ?>

    <!-- HTML Form for Game Turns -->
    <?php if (isset($_SESSION['player1']) && isset($_SESSION['player2'])) { ?>
        <form action="" method="POST" class="game-form">
            <input type="hidden" name="currentPlayer" value="<?php echo $_SESSION['currentPlayer']; ?>">
            <label for="index">Enter Index to Avoid Bomb (
                <?php echo $_SESSION['currentPlayer'] == 'player1' ? $_SESSION['player1'] : $_SESSION['player2']; ?>):
            </label>

            <input type="number" id="index" name="index" min="0" max="8" required>
            <input type="submit" value="Submit">
        </form>
    <?php } ?>

</body>

</html>