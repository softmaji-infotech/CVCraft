<?php
/** @var array $resume */
?>
<div style="font-family:Arial, sans-serif; color:#111827; background:#fff; line-height:1.5;">
    <h1 style="font-size:26px;margin-bottom:0;"><?= $helper::e($resume['name'] ?: 'Your Name') ?></h1>
    <p style="margin-top:2px;color:#2563eb;"><?= $helper::e($resume['title'] ?: 'Fresher Candidate') ?></p>
    <p style="font-size:12px;"><?= $helper::e(implode(' | ', array_values(array_filter([$resume['email'], $resume['phone'], $resume['portfolio']])))) ?></p>
    <?php if (!empty($resume['summary'])): ?><h3>Career Objective</h3><p><?= nl2br($helper::e($resume['summary'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['education'])): ?><h3>Education</h3><?php foreach ($resume['education'] as $edu): ?><p><strong><?= $helper::e($edu['degree'] ?? '') ?></strong> - <?= $helper::e($edu['institution'] ?? '') ?> (<?= $helper::e($edu['duration'] ?? '') ?>)</p><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['skills'])): ?><h3>Technical Skills</h3><p><?= $helper::e(implode(', ', $resume['skills'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['projects'])): ?><h3>Academic/Personal Projects</h3><?php foreach ($resume['projects'] as $pro): ?><p><strong><?= $helper::e($pro['name'] ?? '') ?></strong><br><?= nl2br($helper::e($pro['description'] ?? '')) ?></p><?php endforeach; ?><?php endif; ?>
    <?php if (!empty($resume['certifications'])): ?><h3>Certifications</h3><p><?= $helper::e(implode(', ', $resume['certifications'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['languages'])): ?><h3>Languages</h3><p><?= $helper::e(implode(', ', $resume['languages'])) ?></p><?php endif; ?>
    <?php if (!empty($resume['achievements'])): ?><h3>Achievements</h3><ul><?php foreach ($resume['achievements'] as $a): ?><li><?= $helper::e($a) ?></li><?php endforeach; ?></ul><?php endif; ?>
</div>
