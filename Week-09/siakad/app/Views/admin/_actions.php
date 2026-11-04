<a href="<?= site_url("admin/{$uri}/{$id}/edit") ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
<?= form_open("admin/{$uri}/{$id}/delete", ['class' => 'd-inline', 'data-confirm' => "Hapus {$label}?"]) ?>
    <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
<?= form_close() ?>
