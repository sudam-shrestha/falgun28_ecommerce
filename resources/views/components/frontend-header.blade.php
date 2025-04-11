<header>
    <div class="bg-primary">
        <div class="text-center text-white py-1 relative container">
            <p>
                Lorem ipsum, dolor sit amet consectetur adipisicing elit.
            </p>

            <button type="button" class="absolute right-0 top-[50%] translate-y-[-50%] cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <nav class="shadow-lg">
        <div class="container flex flex-col md:flex-row gap-5 items-center justify-between py-3">
            <div>
                <a href="{{ route('homepage') }}">
                    <img class="h-12" src="https://codeit.com.np/storage/01JJ6HWH8RP35HYNCEBFXVYZKF.png" alt="Logo">
                </a>
            </div>
            <div>
                <form action="{{ route('compare') }}" method="get">
                    <div class="flex items-center">
                        <input type="search" name="q" id="search" placeholder="Search">
                        <button type="submit" class="bg-[#ababab] border border-[#ababab] text-white px-4 py-2">compare
                            <i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>
            </div>
            <div class="flex gap-2 items-center">
                <a href="{{ route('login') }}" class="btn-primary">SignIn</a>
                <a href="{{ route('register') }}" class="btn-secondary">SignUp</a>
            </div>
        </div>
    </nav>
</header>
