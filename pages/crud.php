<?php

$errors = [];
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editing = null;
$canModify = is_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (!$canModify) {
        flash('warning', 'A táblázat módosításához be kell jelentkezni.');
        redirect_to('belepes');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = db()->prepare('DELETE FROM suti WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'A sütemény törlése sikerült.');
        redirect_to('crud');
    }

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = request_value('nev');
        $type = request_value('tipus');
        $awarded = isset($_POST['dijazott']) ? 1 : 0;

        if (mb_strlen($name) < 2) $errors[] = 'A név legalább 2 karakter legyen.';
        if (mb_strlen($type) < 2) $errors[] = 'A típus legalább 2 karakter legyen.';

        if (!$errors) {
            if ($id > 0) {
                $stmt = db()->prepare('UPDATE suti SET nev = ?, tipus = ?, dijazott = ? WHERE id = ?');
                $stmt->execute([$name, $type, $awarded, $id]);
                flash('success', 'A sütemény módosítása sikerült.');
            } else {
                $nextId = (int) db()->query('SELECT COALESCE(MAX(id), 0) + 1 FROM suti')->fetchColumn();
                $stmt = db()->prepare('INSERT INTO suti (id, nev, tipus, dijazott) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nextId, $name, $type, $awarded]);
                flash('success', 'Az új sütemény létrejött.');
            }
            redirect_to('crud');
        }
    }
}

if ($editId > 0 && !$canModify) {
    flash('warning', 'A szerkesztéshez be kell jelentkezni.');
    redirect_to('belepes');
}

if ($editId > 0) {
    $stmt = db()->prepare('SELECT * FROM suti WHERE id = ?');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

$stmt = db()->query(
    "SELECT suti.*,
            GROUP_CONCAT(DISTINCT CONCAT(ar.ertek, ' Ft / ', ar.egyseg) ORDER BY ar.ertek SEPARATOR ', ') AS arak,
            GROUP_CONCAT(DISTINCT tartalom.mentes ORDER BY tartalom.mentes SEPARATOR ', ') AS mentes
     FROM suti
     LEFT JOIN ar ON ar.sutiid = suti.id
     LEFT JOIN tartalom ON tartalom.sutiid = suti.id
     GROUP BY suti.id, suti.nev, suti.tipus, suti.dijazott
     ORDER BY suti.nev"
);
$cakes = $stmt->fetchAll();
?>

<section class="section">
    <p class="eyebrow">CRUD</p>
    <h1>Cukrászda adatbázis - sütemények</h1>
    <div class="split">
        <div class="form-panel">
            <?php if ($canModify): ?>
                <h2><?= $editing ? 'Sütemény módosítása' : 'Új sütemény' ?></h2>
                <?php if ($errors): ?>
                    <div class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <p><?= h($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="post" class="form-grid">
                    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?= h($editing['id'] ?? 0) ?>">
                    <div class="field full">
                        <label for="nev">Név</label>
                        <input id="nev" name="nev" value="<?= h($_POST['nev'] ?? ($editing['nev'] ?? '')) ?>">
                    </div>
                    <div class="field full">
                        <label for="tipus">Típus</label>
                        <input id="tipus" name="tipus" value="<?= h($_POST['tipus'] ?? ($editing['tipus'] ?? '')) ?>">
                    </div>
                    <label class="checkline field full">
                        <input name="dijazott" type="checkbox" <?= (!empty($_POST['dijazott']) || (!$_POST && !empty($editing['dijazott']))) ? 'checked' : '' ?>>
                        Díjazott sütemény
                    </label>
                    <div class="form-actions full">
                        <button class="primary" type="submit"><?= $editing ? 'Módosítás' : 'Létrehozás' ?></button>
                        <?php if ($editing): ?>
                            <a class="button secondary" href="<?= h(route_url('crud')) ?>">Mégsem</a>
                        <?php endif; ?>
                    </div>
                </form>
            <?php else: ?>
                <h2>Adatmódosítás</h2>
                <p>A sütemények listája vendégként is megtekinthető, de létrehozást, módosítást és törlést csak bejelentkezett felhasználó végezhet.</p>
                <a class="button primary" href="<?= h(route_url('belepes')) ?>">Bejelentkezés</a>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Importált forrás</h2>
            <p>A táblák a beadandó adatforrásai közül a Cukrászda témából származnak: `suti`, `ar`, `tartalom`.</p>
            <p>A kávézó kínálata ezekre a süteményekre épül, a hajnali 30% kedvezmény pedig a kávéitalokra vonatkozik.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="table-panel">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Név</th>
                <th>Típus</th>
                <th>Díjazott</th>
                <th>Árak</th>
                <th>Mentes jelölések</th>
                <?php if ($canModify): ?>
                    <th>Műveletek</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cakes as $cake): ?>
                <tr>
                    <td><?= h($cake['id']) ?></td>
                    <td><?= h($cake['nev']) ?></td>
                    <td><?= h($cake['tipus']) ?></td>
                    <td><?= $cake['dijazott'] ? 'Igen' : 'Nem' ?></td>
                    <td><?= h($cake['arak'] ?: '-') ?></td>
                    <td><?= h($cake['mentes'] ?: '-') ?></td>
                    <?php if ($canModify): ?>
                        <td>
                            <div class="actions">
                                <a class="button secondary" href="<?= h(route_url('crud', ['edit' => $cake['id']])) ?>">Szerkesztés</a>
                                <form method="post" onsubmit="return confirm('Biztosan törlöd?')">
                                    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= h($cake['id']) ?>">
                                    <button class="danger" type="submit">Törlés</button>
                                </form>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
