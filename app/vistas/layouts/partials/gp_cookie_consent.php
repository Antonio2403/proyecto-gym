<?php if (($_COOKIE['gp_cookie_consent'] ?? '') !== 'accepted'): ?>
<div class="gp-cookie-consent" data-gp-cookie-consent data-cookie-url="<?= htmlspecialchars(url('/cookies/aceptar')) ?>" role="dialog" aria-modal="true" aria-labelledby="gpCookieTitle">
    <div class="gp-cookie-consent__card">
        <h2 id="gpCookieTitle" class="text-xl mb-2">Cookies de Spartum</h2>
        <p class="text-sm text-slate-500 mb-4">Usamos cookies técnicas para mantener tu sesión y recordar esta preferencia.</p>
        <button type="button" class="btn btn-primary gp-btn-plain gp-cookie-consent__accept w-full" data-gp-cookie-accept>Aceptar cookies</button>
    </div>
</div>
<script>
(function () {
    var box = document.querySelector('[data-gp-cookie-consent]');
    if (!box) {
        return;
    }
    var key = 'gp_cookie_consent';
    function readStore(store) {
        try {
            return store.getItem(key) === 'accepted';
        } catch (e) {
            return false;
        }
    }
    if (document.documentElement.classList.contains('gp-cookies-ok') || readStore(localStorage) || readStore(sessionStorage)) {
        box.hidden = true;
        return;
    }
    document.body.classList.add('gp-cookie-open');
    var btn = box.querySelector('[data-gp-cookie-accept]');
    if (!btn) {
        return;
    }
    btn.addEventListener('click', function () {
        var secure = location.protocol === 'https:' ? '; Secure' : '';
        var expires = new Date(Date.now() + 365 * 86400000).toUTCString();
        document.cookie = key + '=accepted; Expires=' + expires + '; Max-Age=31536000; Path=/; SameSite=Lax' + secure;
        try { localStorage.setItem(key, 'accepted'); } catch (e) {}
        try { sessionStorage.setItem(key, 'accepted'); } catch (e) {}
        document.documentElement.classList.add('gp-cookies-ok');
        document.body.classList.remove('gp-cookie-open');
        box.hidden = true;
        var url = box.getAttribute('data-cookie-url') || '';
        if (!url) {
            return;
        }
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = meta ? (meta.getAttribute('content') || '') : '';
        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-Token': token, 'Accept': 'application/json' }
        }).catch(function () {});
    });
})();
</script>
<?php endif; ?>
