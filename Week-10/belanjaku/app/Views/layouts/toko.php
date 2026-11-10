<!doctype html>
<html lang="id">
<head>
    <?= $this->include('layouts/_head') ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?= $this->include('layouts/_navbar') ?>

<main class="flex-grow-1 py-4">
    <div class="container">
        <?= $this->include('layouts/_alerts') ?>
        <?= $this->renderSection('content') ?>
    </div>
</main>

<?= $this->include('layouts/_footer') ?>
<?= $this->include('layouts/_scripts') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
