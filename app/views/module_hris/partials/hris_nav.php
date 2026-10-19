<?php
$hrisGroups = [
    'Organization' => $navHrisHome ? [
        '/hris/branches' => 'Branches',
        '/hris/offices' => 'Offices',
        '/hris/positions' => 'Positions',
        '/hris/appointments' => 'Employment Types',
    ] : [],
    'Attendance' => array_merge($navHris ? ['/hris/dtr' => 'DTR'] : [], $navHrisHome ? [
        '/hris/leaves' => 'Leaves',
        '/hris/holidays' => 'Holidays',
        '/hris/shifts' => 'Shifts',
    ] : []),
];
?>
<nav class="module-navigation hris-navigation" aria-label="HRIS navigation">
    <ul class="nav nav-underline">
        <?php if ($navHrisHome): ?>
        <li class="nav-item">
            <a class="nav-link <?= $navIsCurrent('/hris') ? 'active' : '' ?>" href="/hris"<?= $navCurrentAttribute('/hris') ?>>Home</a>
        </li>
        <?php endif; ?>
        <?php if ($navHrisEmployees): ?>
        <li class="nav-item">
            <a class="nav-link <?= $navIsSection('/hris/employees') ? 'active' : '' ?>" href="/hris/employees"<?= $navCurrentAttribute('/hris/employees') ?>>Employees</a>
        </li>
        <?php endif; ?>
        <?php foreach ($hrisGroups as $label => $links): ?>
            <?php if ($links): ?>
            <?php $groupActive = (bool) array_filter(array_keys($links), $navIsSection); ?>
            <li class="nav-item dropdown">
                <button class="nav-link dropdown-toggle <?= $groupActive ? 'active' : '' ?>" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?= e($label) ?></button>
                <ul class="dropdown-menu">
                    <?php foreach ($links as $href => $linkLabel): ?>
                    <li><a class="dropdown-item <?= $navIsCurrent($href) ? 'active' : '' ?>" href="<?= e($href) ?>"<?= $navCurrentAttribute($href) ?>><?= e($linkLabel) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
<script>
// HRIS presentation only. Returned values, requests and business rules stay intact.
window.MonteroHris = Object.freeze({
    escape(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[character]));
    },
    statusClass(value, kind) {
        const key = String(value ?? '').trim().toUpperCase();
        const palettes = {
            employment: {ACTIVE: 'positive', RESIGNED: 'neutral'},
            leave: {PENDING: 'attention', APPROVED: 'positive', REJECTED: 'negative', CANCELLED: 'neutral'},
            attendance: {PRESENT: 'positive'}
        };
        return 'hris-status--' + (palettes[kind]?.[key] || 'neutral');
    },
    createdSort(button, sortBy, sortDir) {
        if (!button.classList.contains('hris-created-sort')) return false;
        const active = sortBy === 'created_at';
        button.textContent = active ? `Date added: ${sortDir === 'ASC' ? 'oldest first' : 'newest first'}` : 'Date added';
        button.setAttribute('aria-pressed', String(active));
        button.setAttribute('aria-label', `${button.textContent}. Sort ${active && sortDir === 'ASC' ? 'newest' : 'oldest'} first`);
        button.title = button.getAttribute('aria-label');
        return true;
    }
});
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.hris-page .hris-status[data-hris-kind]').forEach(badge => {
        badge.classList.add(MonteroHris.statusClass(badge.textContent, badge.dataset.hrisKind));
    });
});
</script>
