<?php
session_start();
if (!isset($_SESSION['logged_in']) && $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit(0);
}

// If this is not a new registration, redirect to homepage
if (!isset($_SESSION['new_registration']) || $_SESSION['new_registration'] !== true) {
    header("Location: HomePage.php");
    exit(0);
}
?>
<!DOCTYPE html>
<html>
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
        .genre-tag {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: 2px solid #dee2e6;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
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
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Progress Bar -->
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">Step 1/3</div>
                </div>

                <!-- Step 1: Favorite Genres -->
                <div id="step1" class="preference-card shadow-sm">
                    <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h3>
                    <h4 class="mb-3">Step 1: What game genres do you enjoy?</h4>
                    <p class="text-muted mb-4">Select all that apply (you can change these later)</p>
                    
                    <div class="genre-container mb-4">
                        <span class="genre-tag" data-genre="Action">Action</span>
                        <span class="genre-tag" data-genre="Adventure">Adventure</span>
                        <span class="genre-tag" data-genre="RPG">RPG</span>
                        <span class="genre-tag" data-genre="Strategy">Strategy</span>
                        <span class="genre-tag" data-genre="Sports">Sports</span>
                        <span class="genre-tag" data-genre="Racing">Racing</span>
                        <span class="genre-tag" data-genre="FPS">FPS</span>
                        <span class="genre-tag" data-genre="Battle Royale">Battle Royale</span>
                        <span class="genre-tag" data-genre="MOBA">MOBA</span>
                        <span class="genre-tag" data-genre="Fighting">Fighting</span>
                        <span class="genre-tag" data-genre="Horror">Horror</span>
                        <span class="genre-tag" data-genre="Puzzle">Puzzle</span>
                        <span class="genre-tag" data-genre="Simulation">Simulation</span>
                        <span class="genre-tag" data-genre="Platformer">Platformer</span>
                        <span class="genre-tag" data-genre="Open World">Open World</span>
                    </div>
                    
                    <button class="btn btn-primary" onclick="nextStep(1)">Next →</button>
                </div>

                <!-- Step 2: Upcoming Games (Initially Hidden) -->
                <div id="step2" class="preference-card shadow-sm" style="display: none;">
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" style="width: 66%;" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100">Step 2/3</div>
                    </div>
                    
                    <h4 class="mb-3">Step 2: What upcoming games are you excited about?</h4>
                    <p class="text-muted mb-4">List the games you're looking forward to (separate with commas)</p>
                    
                    <div class="mb-4">
                        <textarea id="upcomingGames" class="form-control" rows="3" placeholder="e.g., GTA VI, Elder Scrolls VI, New Assassin's Creed..."></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary" onclick="prevStep(2)">← Back</button>
                        <button class="btn btn-primary" onclick="nextStep(2)">Next →</button>
                    </div>
                </div>

                <!-- Step 3: Additional Preferences -->
                <div id="step3" class="preference-card shadow-sm" style="display: none;">
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Step 3/3</div>
                    </div>
                    
                    <h4 class="mb-3">Step 3: Almost done! Any additional preferences?</h4>
                    
                    <div class="mb-4">
                        <label class="form-label">Preferred Gaming Platform(s)</label>
                        <select class="form-select" id="platforms" multiple>
                            <option value="PC">PC</option>
                            <option value="PlayStation">PlayStation</option>
                            <option value="Xbox">Xbox</option>
                            <option value="Nintendo Switch">Nintendo Switch</option>
                            <option value="Mobile">Mobile</option>
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">How often do you play games?</label>
                        <select class="form-select" id="playFrequency">
                            <option value="">Select frequency...</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Several times a week</option>
                            <option value="weekend">Weekends only</option>
                            <option value="occasional">Occasionally</option>
                            <option value="new">Just getting started</option>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary" onclick="prevStep(3)">← Back</button>
                        <button class="btn btn-success" onclick="submitPreferences()">Complete Setup →</button>
                    </div>
                </div>

                <div id="textResponse" class="alert alert-info text-center" style="display: none;"></div>
            </div>
        </div>
    </div>

    <script>
    let selectedGenres = [];

    // Genre selection handling
    document.querySelectorAll('.genre-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const genre = this.dataset.genre;
            if (this.classList.contains('selected')) {
                this.classList.remove('selected');
                selectedGenres = selectedGenres.filter(g => g !== genre);
            } else {
                this.classList.add('selected');
                selectedGenres.push(genre);
            }
        });
    });

    function nextStep(currentStep) {
        if (currentStep === 1 && selectedGenres.length === 0) {
            alert('Please select at least one genre!');
            return;
        }
        
        document.getElementById('step' + currentStep).style.display = 'none';
        document.getElementById('step' + (currentStep + 1)).style.display = 'block';
        window.scrollTo(0, 0);
    }

    function prevStep(currentStep) {
        document.getElementById('step' + currentStep).style.display = 'none';
        document.getElementById('step' + (currentStep - 1)).style.display = 'block';
        window.scrollTo(0, 0);
    }

    function submitPreferences() {
        const upcomingGames = document.getElementById('upcomingGames').value;
        const platforms = Array.from(document.getElementById('platforms').selectedOptions).map(opt => opt.value);
        const playFrequency = document.getElementById('playFrequency').value;

        // Validate at least some data is provided
        if (!upcomingGames && platforms.length === 0 && !playFrequency) {
            if (!confirm('You haven\'t filled much. Skip preferences and go to homepage?')) {
                return;
            }
        }

        // Send preferences to server
        const request = new XMLHttpRequest();
        request.open('POST', 'save_preferences.php', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        request.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                // Clear the new registration flag and redirect to homepage
                window.location.href = 'HomePage.php';
            }
        };

        const data = 'type=save_preferences&genres=' + encodeURIComponent(JSON.stringify(selectedGenres)) +
                    '&upcoming_games=' + encodeURIComponent(upcomingGames) +
                    '&platforms=' + encodeURIComponent(JSON.stringify(platforms)) +
                    '&play_frequency=' + encodeURIComponent(playFrequency);

        request.send(data);
    }
    </script>
