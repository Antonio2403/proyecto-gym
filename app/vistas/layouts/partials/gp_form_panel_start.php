<?php
$gpFormTitle = $gpFormTitle ?? 'Formulario';
$gpFormBackUrl = $gpFormBackUrl ?? null;
$gpFormSubtitle = $gpFormSubtitle ?? null;
$gpFormBadge = $gpFormBadge ?? null;
$gpFormHasToolbar = !empty($gpFormBadge) || !empty($gpFormBackUrl);
?>
<div class="content-wrapper">
    <div class="container-fluid gp-form-page py-2">
        <article class="gp-form-panel gp-motion-item">
            <header class="gp-form-panel__head">
                <?php if ($gpFormHasToolbar): ?>
                    <div class="gp-form-panel__toolbar">
                        <?php if (!empty($gpFormBadge)): ?>
                            <span class="gp-form-panel__badge gp-badge"><?= htmlspecialchars((string) $gpFormBadge) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($gpFormBackUrl)): ?>
                            <a href="<?= htmlspecialchars((string) $gpFormBackUrl) ?>" class="btn btn-outline-secondary btn-sm gp-form-panel__back">
                                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Volver
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <h1 class="gp-form-panel__title h4 mb-1"><?= htmlspecialchars((string) $gpFormTitle) ?></h1>
                <?php if (!empty($gpFormSubtitle)): ?>
                    <p class="gp-form-panel__lead text-muted small mb-0"><?= htmlspecialchars((string) $gpFormSubtitle) ?></p>
                <?php endif; ?>
            </header>
            <div class="gp-form-panel__body">
