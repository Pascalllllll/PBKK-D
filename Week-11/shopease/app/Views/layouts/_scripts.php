<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    // Tombol mata di samping input kata sandi
    document.querySelectorAll('[data-intip]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.intip);
            var tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            btn.querySelector('i').className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });

    // Indikator kekuatan sandi. Hanya petunjuk; aturan sebenarnya dicek di server.
    document.querySelectorAll('input[data-meter]').forEach(function (input) {
        var meter = document.getElementById('meter-' + input.id);
        var tingkat = [['', ''], ['lemah', 'Lemah'], ['lemah', 'Lemah'], ['sedang', 'Cukup'], ['kuat', 'Kuat'], ['kuat', 'Sangat kuat']];
        input.addEventListener('input', function () {
            var v = input.value, skor = 0;
            if (v.length >= 8) skor++;
            if (v.length >= 12) skor++;
            if (/[a-z]/i.test(v) && /\d/.test(v)) skor++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) skor++;
            if (/[^A-Za-z0-9]/.test(v)) skor++;
            if (v && !(/[a-z]/i.test(v) && /\d/.test(v) && v.length >= 8)) skor = Math.min(skor, 1);
            meter.className = 'meter-sandi mt-2 ' + tingkat[skor][0];
            meter.dataset.skor = skor;
            meter.querySelector('span').textContent = v ? tingkat[skor][1] : '';
        });
    });

    // Kolom konfirmasi sandi di profil muncul saat email diubah
    var email = document.querySelector('[data-email-awal]');
    if (email) email.addEventListener('input', function () {
        document.getElementById('kolomKonfirmasi').classList.toggle('d-none', email.value.trim().toLowerCase() === email.dataset.emailAwal);
    });
</script>
<script>
    // Form dengan data-confirm meminta konfirmasi sebelum dikirim (hapus, batal, dll.)
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) { if (!confirm(form.dataset.confirm)) e.preventDefault(); });
    });

    // Tombol mata di samping input kata sandi
    document.querySelectorAll('[data-intip]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.intip);
            var tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            btn.querySelector('i').className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });

    // Indikator kekuatan sandi. Hanya petunjuk; aturan sebenarnya dicek di server.
    document.querySelectorAll('input[data-meter]').forEach(function (input) {
        var meter = document.getElementById('meter-' + input.id);
        var tingkat = [['', ''], ['lemah', 'Lemah'], ['lemah', 'Lemah'], ['sedang', 'Cukup'], ['kuat', 'Kuat'], ['kuat', 'Sangat kuat']];
        input.addEventListener('input', function () {
            var v = input.value, skor = 0;
            if (v.length >= 8) skor++;
            if (v.length >= 12) skor++;
            if (/[a-z]/i.test(v) && /\d/.test(v)) skor++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) skor++;
            if (/[^A-Za-z0-9]/.test(v)) skor++;
            if (v && !(/[a-z]/i.test(v) && /\d/.test(v) && v.length >= 8)) skor = Math.min(skor, 1);
            meter.className = 'meter-sandi mt-2 ' + tingkat[skor][0];
            meter.dataset.skor = skor;
            meter.querySelector('span').textContent = v ? tingkat[skor][1] : '';
        });
    });

    // Kolom konfirmasi sandi di profil muncul saat email diubah
    var email = document.querySelector('[data-email-awal]');
    if (email) email.addEventListener('input', function () {
        document.getElementById('kolomKonfirmasi').classList.toggle('d-none', email.value.trim().toLowerCase() === email.dataset.emailAwal);
    });
</script>
