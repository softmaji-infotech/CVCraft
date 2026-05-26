<?php
/** @var array $resume */
?>
<div style="font-family:Arial, sans-serif; color:#111827; background:#fff; font-size:13px; line-height:1.5;">
    <h1 style="font-size:24px;margin:0;"><?= $helper::e($resume['name'] ?: 'Your Name') ?></h1>
    <p style="margin:0 0 8px;color:#374151;"><?= $helper::e($resume['title']) ?></p>
    <p><?= $helper::e($resume['email']) ?> | <?= $helper::e($resume['phone']) ?> | <?= $helper::e($resume['address']) ?></p>
    <hr>
    <?php if (!empty($resume['summary'])): ?><h3>PROFESSIONAL SUMMARY</h3><p><?= nl2br($helper::e($resume['summary'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['skills'])): ?><h3>SKILLS</h3><p><?= $helper::e(implode(' • ', $resume['skills'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['experience'])): ?><h3>EXPERIENCE</h3><?php foreach ($resume['experience'] as $exp): ?><div style="margin-bottom:8px;"><strong><?= $helper::e($exp['role'] ?? '') ?></strong>, <?= $helper::e($exp['company'] ?? '') ?> (<?= $helper::e($exp['duration'] ?? '') ?>)<br><?= nl2br($helper::e($exp['description'] ?? '')) ?></div><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['education'])): ?><h3>EDUCATION</h3><?php foreach ($resume['education'] as $edu): ?><div><?= $helper::e($edu['degree'] ?? '') ?>, <?= $helper::e($edu['institution'] ?? '') ?> (<?= $helper::e($edu['duration'] ?? '') ?>)</div><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['projects'])): ?><h3>PROJECTS</h3><?php foreach ($resume['projects'] as $pro): ?><div><strong><?= $helper::e($pro['name'] ?? '') ?></strong><br><?= nl2br($helper::e($pro['description'] ?? '')) ?></div><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['certifications'])): ?><h3>CERTIFICATIONS</h3><p><?= $helper::e(implode(', ', $resume['certifications'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['languages'])): ?><h3>LANGUAGES</h3><p><?= $helper::e(implode(', ', $resume['languages'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['achievements'])): ?><h3>ACHIEVEMENTS</h3><p><?= $helper::e(implode(', ', $resume['achievements'])) ?></p><?php endif; ?>
</div>
