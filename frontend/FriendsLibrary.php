<?php
session_start();

require_once('../backend/path.inc');
require_once('../backend/get_host_info.inc');
require_once('../backend/rabbitMQLib.inc');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit(0);
}

$friends = [];
$userResults = [];
$searchUsersInput = '';
$flashType = '';
$flashMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['type'] ?? '') === 'add_friend') {
    $friendUsername = trim($_POST['friend_username'] ?? '');

    if ($friendUsername === '') {
        $flashType = 'danger';
        $flashMessage = 'Please enter a username.';
    } else if (!isset($_SESSION['session_key']) || empty($_SESSION['session_key'])) {
        $flashType = 'danger';
        $flashMessage = 'Session error: Please log in again.';
    } else {
        $addRequest = [
            'type' => 'add_friend',
            'session_key' => $_SESSION['session_key'],
            'friend_username' => $friendUsername
	];
	$addClient = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
	$addResponse = $addClient->send_request($addRequest);
        
        // Debug: log response
        error_log("Add friend response: " . json_encode($addResponse));
        
        if (isset($addResponse['returnCode']) && $addResponse['returnCode'] === '1') {
            $flashType = 'success';
            $flashMessage = $addResponse['message'] ?? 'Friend added successfully!';
        } else {
            $flashType = 'danger';
            // Show exact backend error message
            $flashMessage = $addResponse['message'] ?? 'Failed to add friend. Please try again.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_search'])) {
    $searchUsersInput = trim($_GET['user_search']);

    if ($searchUsersInput !== '') {
        if (!isset($_SESSION['session_key']) || empty($_SESSION['session_key'])) {
            $flashType = 'danger';
            $flashMessage = 'Session error: Please log in again.';
        } else {
            $userSearchRequest = [
                'type' => 'search_users',
                'session_key' => $_SESSION['session_key'],
                'query' => $searchUsersInput
            ];
	    $searchClient = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
            $userSearchResponse = $searchClient->send_request($userSearchRequest);
            
            // Debug: log response
            error_log("Search users response: " . json_encode($userSearchResponse));
            
            if (isset($userSearchResponse['returnCode']) && $userSearchResponse['returnCode'] === '1' && !empty($userSearchResponse['data'])) {
                $userResults = $userSearchResponse['data'];
            }
        }
    }
}

$listRequest = [
    'type' => 'get_friends_library',
    'session_key' => $_SESSION['session_key']
];
$listClient = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");
$listResponse = $listClient->send_request($listRequest);
if (isset($listResponse['returnCode']) && $listResponse['returnCode'] === '1' && !empty($listResponse['data'])) {
    $friends = $listResponse['data'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Friends Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .friend-card {
            transition: 0.2s;
            border-radius: 12px;
        }

        .friend-card:hover {
            transform: translateY(-5px);
        }

        .status-friend {
            background: #0d6efd;
            color: white;
        }

        .game-pill {
            display: inline-block;
            background: #f1f3f5;
            border-radius: 999px;
            padding: 5px 10px;
            margin: 3px;
            font-size: 12px;
        }
    </style>
</head>

<body class="bg-light">

<?php include('navBar.php'); ?>

<div class="container mt-5">

    <h1 class="mb-4 fw-bold">Friends Library</h1>

    <!-- Debug Panel -->
    <div class="card mb-3 bg-light border-secondary" style="font-size: 12px;">
        <div class="card-body">
            <strong>Debug Info:</strong><br>
            Session Key: <?php echo isset($_SESSION['session_key']) && !empty($_SESSION['session_key']) ? 'SET ✓' : 'NOT SET ✗'; ?><br>
            Username: <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'N/A'; ?><br>
        </div>
    </div>

    <?php if ($flashMessage !== ''): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flashType); ?>">
            <?php echo htmlspecialchars($flashMessage); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="row g-2 mb-3">
        <input type="hidden" name="type" value="add_friend">
        <div class="col-md-9">
            <input
                type="text"
                name="friend_username"
                class="form-control"
                placeholder="Enter username to add"
                required
            >
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-success">Add Friend</button>
        </div>
    </form>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-9">
            <input
                type="text"
                name="user_search"
                class="form-control"
                placeholder="Find users in database"
                value="<?php echo htmlspecialchars($searchUsersInput); ?>"
            >
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-outline-primary">Find People</button>
        </div>
    </form>

    <?php if ($searchUsersInput !== ''): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white fw-bold">Search Results</div>
            <div class="list-group list-group-flush">
                <?php if (empty($userResults)): ?>
                    <div class="list-group-item text-muted">No users found.</div>
                <?php else: ?>
                    <?php foreach ($userResults as $u): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?php echo htmlspecialchars($u['username']); ?></span>
                            <form method="POST" class="m-0">
                                <input type="hidden" name="type" value="add_friend">
                                <input type="hidden" name="friend_username" value="<?php echo htmlspecialchars($u['username']); ?>">
                                <button type="submit" class="btn btn-sm btn-success">Add</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search -->
    <input type="text" id="search" class="form-control mb-4" placeholder="Search friends...">

    <div class="row" id="friendsContainer">

        <?php foreach ($friends as $f): ?>
            <div class="col-md-4 mb-4 friend-item"
                 data-name="<?php echo htmlspecialchars(strtolower($f['username'])); ?>">

                <div class="card friend-card shadow-sm p-3">

		    <h5 class="fw-bold">
			<a href="myLibrary.php?friend=<?php echo urlencode($f['username']); ?>" class="text-decoration-none text-dark">
			<?php echo htmlspecialchars($f['username']); ?>
			</a>
                    </h5>

                    <span class="badge mb-2 status-friend">
                        <?php echo htmlspecialchars($f['status']); ?>
                    </span>

                    <p class="mb-1">
                        <strong>Favorite:</strong>
                        <?php echo htmlspecialchars($f['favorite_game']); ?>
                    </p>

                    <p class="mb-2">
                        <strong>Library Size:</strong>
                        <?php echo $f['count']; ?>
                    </p>

                    <div class="mb-3">
                        <?php if (empty($f['games'])): ?>
                            <span class="text-muted small">No shared library data yet.</span>
                        <?php endif; ?>
                        <?php foreach ($f['games'] as $g): ?>
                            <span class="game-pill"><?php echo htmlspecialchars($g); ?></span>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <div id="empty" class="alert alert-warning text-center <?php echo empty($friends) ? '' : 'd-none'; ?>">
        <?php echo empty($friends) ? 'No friends added yet. Add a username above.' : 'No results found.'; ?>
    </div>

</div>

<script>
const search = document.getElementById('search');
const items = document.querySelectorAll('.friend-item');
const empty = document.getElementById('empty');

search.addEventListener('input', () => {
    let value = search.value.toLowerCase();
    let visible = 0;

    items.forEach(item => {
        let name = item.dataset.name;

        if (name.includes(value)) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    empty.classList.toggle('d-none', visible !== 0);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
