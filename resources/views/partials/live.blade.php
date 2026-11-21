{{--
    ระบบเรียลไทม์ (polling ทุก 5 วินาที):
    ดึงหน้าปัจจุบันซ้ำเงียบๆ แล้วอัปเดตเฉพาะส่วนที่มี data-live="..." ถ้าข้อมูลเปลี่ยน
    ไม่อัปเดตตอนผู้ใช้กำลังพิมพ์/เลือกในส่วนนั้น หรือมีป๊อบอัพเปิดอยู่
--}}
<script>
(function () {
    var INTERVAL = 5000, busy = false;
    if (!document.querySelector('[data-live]') && !window.onLiveDoc) { return; }

    function editing(el) {
        var a = document.activeElement;
        return a && el.contains(a) && /^(INPUT|TEXTAREA|SELECT)$/.test(a.tagName);
    }

    function tick() {
        if (busy || document.hidden || (window.Swal && Swal.isVisible())) { return; }
        busy = true;
        fetch(location.href, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok || r.redirected) { throw 0; } return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                document.querySelectorAll('[data-live]').forEach(function (cur) {
                    var next = doc.querySelector('[data-live="' + cur.getAttribute('data-live') + '"]');
                    if (!next || editing(cur)) { return; }
                    if (cur.innerHTML !== next.innerHTML) { cur.innerHTML = next.innerHTML; }
                });
                if (window.onLiveDoc) { window.onLiveDoc(doc); }
            })
            .catch(function () {})
            .then(function () { busy = false; });
    }
    setInterval(tick, INTERVAL);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) { tick(); } });
})();
</script>
