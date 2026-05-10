<?php
session_start();
require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit(0);
}

// If no user, redirect to homepage
if (!isset($_SESSION['new_registration']) || $_SESSION['new_registration'] !== true) {
    header("Location: HomePage.php");
    exit(0);
}

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
$request = [
    'type' => "get_preferences",
    'session_key' => $_SESSION['session_key']
];
$response = $client->send_request($request);

$evryPlatform = $response['data']['all_platforms'] ?? [];
$evryGenre = $response['data']['all_genres'] ?? [];

?>
<!DOCTYPE html>
<html>
<!--THIS PAGE IS ONLY ACCESSED ONCE after user clicks REGISTER. does the same as change preferences but it looks more like a part of registration than settings page. -->
<head>
    <title>Game Preferences - GAMERS DUNGEON</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .preference-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .genre-tag.selected {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        .genre-tag:hover {
            background-color: #e9ecef;
        }
        .genre-tag.selected:hover {
            background-color: #0b5ed7;
        }
        .progress {
            height: 10px;
            margin-bottom: 30px;
        }
        .progress-bar {
            transition: width 0.3s ease;
	}
.btn-outline-primary { border-radius: 25px; margin: 5px; padding: 10px 20px; font-weight: 500;}
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
     <form action="savePreferences.php" method="POST" id="onboardingForm">
                <div class="progress">
                    <div class="progress-bar" id="progressbar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">Step 1/2</div>
                </div>

                <!--Favorite Genres. Instead of hardcodign each genre or platform down below, there is a loop that creates a clickable button PER entry in the game_platforms and game_genres table in sql. -->
                <div id="step1" class="preference-card shadow-sm">
                    <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h3>
                    <h4 class="mb-3">Step 1: What game genres do you enjoy?</h4>
                    <p class="text-muted mb-4">Select all that apply (you can change these later)</p>
                    
                    <div class="genre-container mb-4">
     <?php foreach ($evryGenre as $g) : ?>
     <input type="checkbox" class="btn-check" name="genres[]" id="genre_<?php echo $g['id']; ?>" value="<?php echo $g['id']; ?>">
<label class="btn btn-outline-primary" for="genre_<?php echo $g['id']; ?>">
<?php echo htmlspecialchars($g['name']); ?>
</label>
<?php endforeach; ?>
                    </div>
                    
                    <button type="button" class="btn btn-primary" onclick="nextStep(1)">Next →</button>
                </div>
                   <div class="progress mb-4">
                    </div>
<div id="step2" class="preference-card shadow-sm" style="display: none;">
 <h4 class="mb-3">Step 2: which platforms do you play on?</h4>
 <p class="text-muted mb-4">Select the devices you own</p>
<div class="mb-4">
<?php foreach ($evryPlatform as $p): ?>
<input type="checkbox" class="btn-check" name="platforms[]" id="plat_<?php echo $p['id']; ?>" value="<?php echo $p['id']; ?>">
<label class="btn btn-outline-primary" for="plat_<?php echo $p['id']; ?>">
<?php echo htmlspecialchars($p['name']); ?>
</label>
<?php endforeach; ?>
</div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary px-4" onclick="prevStep(2)">← Back</button>
                        <button type="submit" class="btn btn-success px-4">Complete Setup →</button>
                    </div>
                </div>
</form>
                <div id="textResponse" class="alert alert-info text-center" style="display: none;"></div>
            </div>
        </div>
    </div>

  <script>
    function nextStep(currentStep) {
        const genresChecked = document.querySelectorAll('input[name="genres[]"]:checked').length;
        if (currentStep === 1 && genresChecked === 0) {
            alert('Please select at least one genre!');
            return;
        }

        document.getElementById('step' + currentStep).style.display = 'none';
        document.getElementById('step' + (currentStep + 1)).style.display = 'block';
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('progressBar').innerText = 'Step 2/2';
        window.scrollTo(0, 0);
    }

    function prevStep(currentStep) {
        document.getElementById('step' + currentStep).style.display = 'none';
        document.getElementById('step' + (currentStep - 1)).style.display = 'block';
        document.getElementById('progressBar').style.width = '50%';
        document.getElementById('progressBar').innerText = 'Step 1/2';
        window.scrollTo(0, 0);
    }
    </script>
</body>
</html>
