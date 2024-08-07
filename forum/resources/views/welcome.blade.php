<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Laravel Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Link to your CSS file -->
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
</head>
<body>
    <div class="container">
        <header class="p-3">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="{{ url('/') }}">Home</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('posts.index') }}">Posts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Categories</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="#">{{ Auth::user()->name }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </nav>
        </header>
        <main class="mt-4">
            <div class="welcome-content">
                <h1>Welcome to Laravel Forum</h1>
                <p>This is your application’s welcome page.</p>
                <p>To get started, you can edit this view file located at <code>resources/views/welcome.blade.php</code>.</p>
                <p>For more information, visit the <a href="https://laravel.com/docs">Laravel Documentation</a>.</p>
            </div>
            <div class="search-bar">
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search posts" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Latest Posts</h5>
                            <p class="card-text">Check out the latest discussions in the forum.</p>
                            <a href="{{ route('posts.index') }}" class="btn btn-primary">View Posts</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Popular Categories</h5>
                            <p class="card-text">Explore the most popular categories in the forum.</p>
                            <a href="{{ route('categories.index') }}" class="btn btn-primary">View Categories</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">User Profiles</h5>
                            <p class="card-text">View and manage user profiles.</p>
                            <a href="{{ route('users.index') }}" class="btn btn-primary">View Profiles</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="mt-4 text-center">
            <p>&copy; {{ date('Y') }} Your Application Name. All rights reserved.</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
