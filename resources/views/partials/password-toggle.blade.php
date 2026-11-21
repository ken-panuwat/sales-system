{{-- เพิ่มปุ่มลูกตาเปิด/ปิดการมองเห็นรหัสผ่าน ให้ทุกช่อง input[type=password] อัตโนมัติ (เริ่มต้น = ปิดลูกตา/ซ่อนรหัสผ่าน) --}}
<script>
(function () {
    var ICON_HIDDEN = '<i class="bi bi-eye-slash"></i>';
    var ICON_SHOWN = '<i class="bi bi-eye"></i>';

    document.querySelectorAll('input[type="password"]').forEach(function (input) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pw-toggle';
        btn.setAttribute('aria-label', 'แสดงรหัสผ่าน');
        btn.setAttribute('aria-pressed', 'false');
        btn.title = 'แสดง/ซ่อนรหัสผ่าน';
        btn.innerHTML = ICON_HIDDEN;

        if (input.parentElement.classList.contains('input-group')) {
            btn.classList.add('input-group-text');
            input.parentElement.appendChild(btn);
        } else {
            var wrap = document.createElement('div');
            wrap.className = 'pw-wrap';
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);
            wrap.appendChild(btn);
        }

        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.innerHTML = show ? ICON_SHOWN : ICON_HIDDEN;
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', show ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน');
        });
    });
})();
</script>
