<?php
/** @var array $resume */
?>
<div style="font-family:Arial, sans-serif; color:#111827; background:#fff;">
    <h1 style="font-size:28px;margin-bottom:0;"><?= $helper::e($resume['name'] ?: 'Your Name') ?></h1>
    <p style="margin-top:4px;color:#2563eb;font-size:16px;"><?= $helper::e($resume['title'] ?: 'Professional Title') ?></p>
    <p style="font-size:12px;color:#4b5563;"><?= $helper::e(implode(' | ', array_values(array_filter([$resume['email'], $resume['phone'], $resume['address']])))) ?></p>
    <?php if (!empty($resume['summary'])): ?><h3>Summary</h3><p><?= nl2br($helper::e($resume['summary'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['skills'])): ?><h3>Skills</h3><p><?= $helper::e(implode(', ', $resume['skills'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['experience'])): ?><h3>Experience</h3><?php foreach ($resume['experience'] as $exp): ?><div><strong><?= $helper::e($exp['role'] ?? '') ?></strong> - <?= $helper::e($exp['company'] ?? '') ?> (<?= $helper::e($exp['duration'] ?? '') ?>)<br><?= nl2br($helper::e($exp['description'] ?? '')) ?></div><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['education'])): ?><h3>Education</h3><?php foreach ($resume['education'] as $edu): ?><div><strong><?= $helper::e($edu['degree'] ?? '') ?></strong> - <?= $helper::e($edu['institution'] ?? '') ?> (<?= $helper::e($edu['duration'] ?? '') ?>)</div><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['projects'])): ?><h3>Projects</h3><?php foreach ($resume['projects'] as $pro): ?><div><strong><?= $helper::e($pro['name'] ?? '') ?></strong><?= !empty($pro['url']) ? ' - ' . $helper::e($pro['url']) : '' ?><br><?= nl2br($helper::e($pro['description'] ?? '')) ?></div><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['certifications'])): ?><h3>Certifications</h3><p><?= $helper::e(implode(', ', $resume['certifications'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['languages'])): ?><h3>Languages</h3><p><?= $helper::e(implode(', ', $resume['languages'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['achievements'])): ?><h3>Achievements</h3><ul><?php foreach ($resume['achievements'] as $a): ?><li><?= $helper::e($a) ?></li><?php endforeach; ?></ul><?php endif; ?>
</div>
