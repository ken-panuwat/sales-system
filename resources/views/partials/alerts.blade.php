{{--
    ป๊อบอัพ (SweetAlert2) สำหรับ:
    1) ข้อความสำเร็จ / ไม่สำเร็จ / ข้อมูลไม่ถูกต้อง จาก session (with('success'), with('error'), $errors)
    2) หน้าต่างยืนยันก่อนส่งฟอร์ม: ใส่ data-confirm="ข้อความ" ที่ <form> หรือที่ปุ่ม submit
       ตัวเลือกเสริม: data-title, data-icon (warning|question|info), data-ok (ข้อความปุ่มยืนยัน)
--}}
@php
    $swalErrors = $errors->any()
        ? '<div style="text-align:center;line-height:1.7">'.collect($errors->all())->map(fn ($e) => '<div>'.e($e).'</div>')->implode('').'</div>'
        : null;
@endphp
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.all.min.js"></script>
<script>
(function () {
    var Pop = Swal.mixin({
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true,
        // ป๊อบอัพไม่ปิดเองตามเวลา: ปิดได้เฉพาะปุ่ม "ตกลง" / "ยกเลิก" หรือปุ่มปิด (X) เท่านั้น
        showCloseButton: true,
        closeButtonAriaLabel: 'ปิด',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    window.Pop = Pop;

    var flashSuccess = @json(session('success'));
    var flashError = @json(session('error'));
    var flashErrors = @json($swalErrors);

    if (flashError) {
        Pop.fire({ icon: 'error', title: 'ไม่สำเร็จ', text: flashError });
    } else if (flashErrors) {
        Pop.fire({ icon: 'error', title: 'ข้อมูลไม่ถูกต้อง', html: flashErrors });
    } else if (flashSuccess) {
        Pop.fire({ icon: 'success', title: 'สำเร็จ', text: flashSuccess });
    }

    // หน้าต่างยืนยันก่อนส่งฟอร์ม
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.dataset.confirmed === '1') { return; }

        var submitter = e.submitter;
        var src = (submitter && submitter.dataset && submitter.dataset.confirm) ? submitter : form;
        if (!src.dataset || !src.dataset.confirm) { return; }

        e.preventDefault();

        Pop.fire({
            icon: src.dataset.icon || 'warning',
            title: src.dataset.title || 'ยืนยันการทำรายการ',
            text: src.dataset.confirm,
            showCancelButton: true,
            confirmButtonText: src.dataset.ok || 'ยืนยัน',
            focusCancel: true
        }).then(function (r) {
            if (!r.isConfirmed) { return; }
            form.dataset.confirmed = '1';
            if (submitter && form.requestSubmit) { form.requestSubmit(submitter); } else { form.submit(); }
        });
    });
})();
</script>
