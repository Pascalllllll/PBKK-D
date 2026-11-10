<?php

/** Template paginasi Bootstrap 5, didaftarkan sebagai 'bs5' di Config/Pager.php. @var \CodeIgniter\Pager\PagerRenderer $pager */
$pager->setSurroundCount(2);
if ($pager->getPageCount() <= 1) {
    return;
}
?>
<nav aria-label="Navigasi halaman">
    <ul class="pagination pagination-sm mb-0">
        <li class="page-item<?= $pager->hasPrevious() ? '' : ' disabled' ?>"><a class="page-link" href="<?= $pager->getPrevious() ?? '#' ?>" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></a></li>
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item<?= $link['active'] ? ' active' : '' ?>"><a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a></li>
        <?php endforeach ?>
        <li class="page-item<?= $pager->hasNext() ? '' : ' disabled' ?>"><a class="page-link" href="<?= $pager->getNext() ?? '#' ?>" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></a></li>
    </ul>
</nav>
