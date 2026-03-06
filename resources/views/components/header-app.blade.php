<header class="header header-app">
    <h1 class="title">HOUSEHOLD BUDGET</h1>
    
    <div>
        <div>
            Insights
        </div>     
        <div>
            Settings
        </div>
        <div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-link">Logout</button>
            </form>
        </div>
    </div>
</header>