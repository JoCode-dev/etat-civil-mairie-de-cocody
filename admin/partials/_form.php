<?php
/**
 * resources/views/admin/admins/partials/_form.php
 * Formulaire partagé création/édition
 */
?>
<div class="mb-3">
    <label for="prenom" class="form-label">Prénom</label>
    <input type="text" 
           id="prenom" 
           name="prenom" 
           class="form-control" 
           value="<?= isset($admin) ? htmlspecialchars($admin['prenom']) : '' ?>" 
           required>
</div>

<div class="mb-3">
    <label for="nom" class="form-label">Nom</label>
    <input type="text" 
           id="nom" 
           name="nom" 
           class="form-control" 
           value="<?= isset($admin) ? htmlspecialchars($admin['nom']) : '' ?>" 
           required>
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" 
           id="email" 
           name="email" 
           class="form-control" 
           value="<?= isset($admin) ? htmlspecialchars($admin['email']) : '' ?>" 
           required>
</div>

<div class="mb-3">
    <label for="role_id" class="form-label">Rôle</label>
    <select id="role_id" name="role_id" class="form-select" required>
        <?php foreach ($roles as $role): ?>
        <option value="<?= $role['id'] ?>" 
            <?= (isset($admin) && $admin['role_id'] == $role['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($role['titre']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>