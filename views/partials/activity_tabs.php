<?php
$activityUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>
<div class="activity-tab-pills">
    <a href="/interests" class="activity-tab-pill <?= str_starts_with($activityUri, '/interests') ? 'active' : '' ?>">💌 Interests</a>
    <a href="/shortlist" class="activity-tab-pill <?= str_starts_with($activityUri, '/shortlist') ? 'active' : '' ?>">💖 Shortlist</a>
    <a href="/visitors" class="activity-tab-pill <?= str_starts_with($activityUri, '/visitors') ? 'active' : '' ?>">👁️ Visitors</a>
    <a href="/shortlisted-by" class="activity-tab-pill <?= str_starts_with($activityUri, '/shortlisted-by') ? 'active' : '' ?>">👥 Shortlisted Me</a>
</div>
