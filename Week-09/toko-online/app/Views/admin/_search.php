<form method="get" class="form-inline">
    <div class="input-group input-group-sm">
        <input type="search" name="q" class="form-control" placeholder="<?= esc($placeholder) ?>" value="<?= esc($q) ?>" aria-label="Cari">
        <div class="input-group-append">
            <button class="btn btn-primary"><i class="fas fa-search fa-sm"></i></button>
        </div>
    </div>
    <?php if ($q !== ''): ?><a href="?" class="btn btn-sm btn-link">Reset</a><?php endif ?>
</form>
