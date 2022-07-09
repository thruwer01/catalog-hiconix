@push('head')
    <link
        href="/favicon.ico"
        id="favicon"
        rel="icon"
    >
@endpush

<p class="n-m v-center">
    @auth
        <img src="/img/logo_cab_white.png" width="100%">
    @endauth
    
    @guest
        <img src="/img/logo_cab_standart.png">
    @endguest
</p>