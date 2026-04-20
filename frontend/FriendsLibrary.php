<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit(0);
}

/* MOCK DATA (replace with backend later) */
$friends = [
    [
        'username' => 'PixelKnight',
        'status' => 'Online',
        'favorite_game' => 'Elden Ring',
        'games' => ['Elden Ring', 'Hades', 'Helldivers 2'],
        'count' => 42
    ],
    [
        'username' => 'ShadowMage',
        'status' => 'Offline',
        'favorite_game' => 'Baldur\'s Gate 3',
        'games' => ['BG3', 'Cyberpunk', 'Hollow Knight'],
        'count' => 27
    ],
    [
        'username' => 'RetroFox',
        'status' => 'Online',
        'favorite_game' => 'Stardew Valley',
        'games' => ['Stardew Valley', 'Celeste', 'Dead Cells'],
        'count' => 58
    ]
];
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

        .status-online {
            background: #198754;
            color: white;
        }

        .status-offline {
            background: #6c757d;
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

    <!-- Search -->
    <input type="text" id="search" class="form-control mb-4" placeholder="Search friends...">

    <div class="row" id="friendsContainer">

        <?php foreach ($friends as $f): ?>
            <div class="col-md-4 mb-4 friend-item"
                 data-name="<?php echo strtolower($f['username']); ?>">

                <div class="card friend-card shadow-sm p-3">

                    <h5 class="fw-bold">
                        <?php echo htmlspecialchars($f['username']); ?>
                    </h5>

                    <span class="badge mb-2
                        <?php echo $f['status'] === 'Online' ? 'status-online' : 'status-offline'; ?>">
                        <?php echo $f['status']; ?>
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
                        <?php foreach ($f['games'] as $g): ?>
                            <span class="game-pill"><?php echo htmlspecialchars($g); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <button class="btn btn-primary w-100">
                        View Full Library
                    </button>

                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <div id="empty" class="alert alert-warning text-center d-none">
        No results found.
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
