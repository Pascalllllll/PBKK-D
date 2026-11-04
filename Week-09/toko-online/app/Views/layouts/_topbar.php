<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" aria-label="Menu">
        <i class="fa fa-bars"></i>
    </button>
    <span class="d-none d-sm-inline text-gray-600 small">Panel admin toko</span>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= esc(user('nama')) ?></span>
                <span class="badge badge-primary text-uppercase"><?= esc(user('role')) ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <span class="dropdown-item-text small text-gray-600">Login sebagai <b><?= esc(user('username')) ?></b></span>
                <div class="dropdown-divider"></div>
                <?= form_open('logout', ['class' => 'px-3']) ?>
                    <button class="btn btn-sm btn-outline-danger btn-block"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-1"></i>Logout</button>
                <?= form_close() ?>
            </div>
        </li>
    </ul>
</nav>
