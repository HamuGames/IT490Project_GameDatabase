<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container">
<!--First clickable website name that redirects to HomePage no matter where user is.  will create icon later-->    
<a class="navbar-brand fw-bold text-success" href="HomePage.php">🎮 GAMER'S DUNGEON</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
	<li class="nav-item">
<!--next btn takes user to their library. backend logic takes session to see what user's library to show-->
          <a class="nav-link fs-5" href="myLibrary.php">My Library</a>
	</li>
<li class="nav-item">
 <a class="nav-link fs-5 text-warning" href="preferences.php">⚙️ settings</a>
</li>
      </ul>
      <!--seach bar takes user input and sends it over to search_results which will process the request.-->
      <form class="d-flex" action="search_results.php" method="GET">
        <input class="form-control me-2" type="search" name="search_query" placeholder="Search for a game..." required>
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
      
      <ul class="navbar-nav ms-4">
	<li class="nav-item">
<!--logout button takes user back to index, removes their current session as well.-->
          <a class="nav-link btn btn-danger text-white px-3" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
