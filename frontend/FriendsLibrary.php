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
$flashType = '';
$flashMessage = '';

$client = new rabbitMQClient("../backend/testRabbitMQ.ini", "testServer");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['type'] ?? '') === 'add_friend') {
    $friendUsername = trim($_POST['friend_username'] ?? '');

    if ($friendUsername === '') {
        $flashType = 'danger';
        $flashMessage = 'Please enter a username.';
    } else {
        $addRequest = [
            'type' => 'add_friend',
            'session_key' => $_SESSION['session_key'],
            'friend_username' => $friendUsername
        ];

        $addResponse = $client->send_request($addRequest);
        if (isset($addResponse['returnCode']) && $addResponse['returnCode'] === '1') {
            $flashType = 'success';
            $flashMessage = $addResponse['message'] ?? 'Friend added.';
        } else {
            $flashType = 'danger';
            $flashMessage = $addResponse['message'] ?? 'Could not add friend.';
        }
    }
}

$listRequest = [
    'type' => 'get_friends_library',
    'session_key' => $_SESSION['session_key']
];

$listResponse = $client->send_request($listRequest);
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

    <!-- Search -->
    <input type="text" id="search" class="form-control mb-4" placeholder="Search friends...">

    <div class="row" id="friendsContainer">

        <?php foreach ($friends as $f): ?>
            <div class="col-md-4 mb-4 friend-item"
                 data-name="<?php echo htmlspecialchars(strtolower($f['username'])); ?>">

                <div class="card friend-card shadow-sm p-3">

                    <h5 class="fw-bold">
                        <?php echo htmlspecialchars($f['username']); ?>
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
