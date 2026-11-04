<?php
$role = session('role');
$path = '/' . trim(service('request')->getUri()->getPath(), '/');
$menu = [
    'admin' => [
        'Master Data' => [
            ['/admin/mahasiswa', 'fa-user-graduate', 'Mahasiswa'],
            ['/admin/dosen', 'fa-chalkboard-teacher', 'Dosen'],
            ['/admin/matakuliah', 'fa-book', 'Matakuliah'],
        ],
    ],
    'dosen' => [
        'Perkuliahan' => [['/nilai', 'fa-pen', 'Input Nilai']],
    ],
    'mahasiswa' => [
        'Akademik' => [
            ['/krs', 'fa-list-alt', 'KRS'],
            ['/khs', 'fa-file-alt', 'KHS'],
        ],
    ],
][$role] ?? [];
$active = static fn (string $url) => $path === $url || str_starts_with($path, $url . '/') ? ' active' : '';
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('dashboard') ?>">
        <div class="sidebar-brand-icon"><i class="fas fa-university"></i></div>
        <div class="sidebar-brand-text mx-3">SIAKAD</div>
    </a>

    <hr class="sidebar-divider my-0">
    <li class="nav-item<?= $active('/dashboard') ?>">
        <a class="nav-link" href="<?= site_url('dashboard') ?>"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
    </li>

    <?php foreach ($menu as $heading => $items): ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading"><?= esc($heading) ?></div>
        <?php foreach ($items as [$url, $icon, $label]): ?>
            <li class="nav-item<?= $active($url) ?>">
                <a class="nav-link" href="<?= site_url(ltrim($url, '/')) ?>"><i class="fas fa-fw <?= $icon ?>"></i><span><?= esc($label) ?></span></a>
            </li>
        <?php endforeach ?>
    <?php endforeach ?>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Ciutkan sidebar"></button>
    </div>
</ul>
