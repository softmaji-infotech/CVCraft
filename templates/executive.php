<?php
/** @var array $resume */
?>
<div style="font-family:Arial, sans-serif; color:#111827; background:#fff; line-height:1.6;">
    <h1 style="font-size:30px;margin:0;"><?= $helper::e($resume['name'] ?: 'Your Name') ?></h1>
    <p style="font-size:14px;color:#6b7280;margin:0 0 12px;"><?= $helper::e($resume['title']) ?></p>
    <p style="font-size:12px;"><?= $helper::e(implode(' | ', array_values(array_filter([$resume['email'], $resume['phone'], $resume['linkedin']])))) ?></p>
    <?php if (!empty($resume['summary'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Executive Summary</h3><p><?= nl2br($helper::e($resume['summary'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['experience'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Professional Experience</h3><?php foreach ($resume['experience'] as $exp): ?><div style="margin-bottom:10px;"><strong><?= $helper::e($exp['role'] ?? '') ?></strong> | <?= $helper::e($exp['company'] ?? '') ?> | <?= $helper::e($exp['duration'] ?? '') ?><br><?= nl2br($helper::e($exp['description'] ?? '')) ?></div><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['skills'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Core Skills</h3><p><?= $helper::e(implode(', ', $resume['skills'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['education'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Education</h3><?php foreach ($resume['education'] as $edu): ?><p><?= $helper::e($edu['degree'] ?? '') ?>, <?= $helper::e($edu['institution'] ?? '') ?> (<?= $helper::e($edu['duration'] ?? '') ?>)</p><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['projects'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Strategic Projects</h3><?php foreach ($resume['projects'] as $pro): ?><p><strong><?= $helper::e($pro['name'] ?? '') ?></strong><br><?= nl2br($helper::e($pro['description'] ?? '')) ?></p><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['certifications'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Certifications</h3><p><?= $helper::e(implode(', ', $resume['certifications'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['languages'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Languages</h3><p><?= $helper::e(implode(', ', $resume['languages'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['achievements'])): ?><h3 style="border-bottom:1px solid #e5e7eb;">Achievements</h3><ul><?php foreach ($resume['achievements'] as $a): ?><li><?= $helper::e($a) ?></li><?php endforeach; ?></ul><?php endif; ?>
</div>
