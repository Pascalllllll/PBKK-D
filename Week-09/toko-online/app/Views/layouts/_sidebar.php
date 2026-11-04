<?php
$path   = '/' . trim(service('request')->getUri()->getPath(), '/');
$active = static fn (string $url) => ($url === '/admin' ? $path === $url : ($path === $url || str_starts_with($path, $url . '/'))) ? ' active' : '';
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('admin') ?>">
        <div class="sidebar-brand-icon"><i class="fas fa-store"></i></div>
        <div class="sidebar-brand-text mx-3">Toko Admin</div>
    </a>

    <hr class="sidebar-divider my-0">
    <li class="nav-item<?= $active('/admin') ?>">
        <a class="nav-link" href="<?= site_url('admin') ?>"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Katalog</div>
    <li class="nav-item<?= $active('/admin/kategori') ?>">
        <a class="nav-link" href="<?= site_url('admin/kategori') ?>"><i class="fas fa-fw fa-tags"></i><span>Kategori</span></a>
    </li>
    <li class="nav-item<?= $active('/admin/produk') ?>">
        <a class="nav-link" href="<?= site_url('admin/produk') ?>"><i class="fas fa-fw fa-box"></i><span>Produk</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Penjualan</div>
    <li class="nav-item<?= $active('/admin/pesanan') ?>">
        <a class="nav-link" href="<?= site_url('admin/pesanan') ?>"><i class="fas fa-fw fa-receipt"></i><span>Pesanan</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= site_url('/') ?>" target="_blank" rel="noopener"><i class="fas fa-fw fa-external-link-alt"></i><span>Lihat Toko</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Ciutkan sidebar"></button>
    </div>
</ul>
