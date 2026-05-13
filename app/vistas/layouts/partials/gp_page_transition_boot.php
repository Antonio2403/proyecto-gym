<script>
try {
    if (sessionStorage.getItem('gpPageTransition') === 'active') {
        document.documentElement.classList.add('gp-nav-curtain-hold', 'gp-transition-lock');
    }
} catch (e) {}
</script>
<style>
html.gp-nav-curtain-hold::before,
html.gp-nav-curtain-hold::after {
    content: '';
    position: fixed;
    left: 0;
    right: 0;
    width: 100%;
    height: 50%;
    z-index: 2147483646;
    background: linear-gradient(165deg, #2c3544 0%, #1a2230 50%, #2c3544 100%);
    background-size: 100% 200%;
    pointer-events: all;
}
html.gp-nav-curtain-hold::before {
    top: 0;
    background-position: center top;
}
html.gp-nav-curtain-hold::after {
    bottom: 0;
    background-position: center bottom;
}
html.gp-nav-curtain-hold,
html.gp-transition-lock {
    overflow: hidden !important;
    height: 100%;
    scrollbar-width: none;
}
html.gp-nav-curtain-hold::-webkit-scrollbar,
html.gp-transition-lock::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}
html.gp-transition-lock body {
    overflow: hidden !important;
    touch-action: none;
}
</style>
