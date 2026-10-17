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
<nav class="module-navigation" aria-label="HRIS navigation">
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
