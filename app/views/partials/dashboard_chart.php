<?php
$key = $card['key']; $kind = $card['kind']; $panel = $card['panel'] ?? [];
$available = ($panel['available'] ?? false) === true;
$rows = $available ? ($panel['rows'] ?? []) : [];
$total = array_sum(array_column($rows, 'value'));
$maximum = max(array_merge([1], array_column($rows, 'value')));
$run = $panel['run'] ?? null;
?>
<section class="dashboard-chart<?= $kind === 'prerequisite' ? ' dashboard-chart--prerequisite' : ''; ?>" id="visual-<?= e($key); ?>" aria-labelledby="visual-<?= e($key); ?>-title" data-visual-state="<?= $kind === 'prerequisite' ? 'prerequisite' : ($available ? 'available' : 'unavailable'); ?>">
    <header class="dashboard-chart-heading"><h3 id="visual-<?= e($key); ?>-title"><?= e($card['title']); ?></h3>
    <span class="dashboard-chart-scope"><?= $kind === 'prerequisite' ? 'Awaiting prerequisites' : 'Current organization'; ?></span></header>
    <p class="dashboard-chart-help"><?= e($card['help']); ?></p>

    <?php if (in_array($key, ['leave-calendar', 'timeline'], true)): ?>
    <form method="get" action="<?= e($visualAction); ?>#visual-<?= e($key); ?>" class="dashboard-chart-controls">
        <div class="dashboard-filter-field"><label for="visual-<?= e($key); ?>-start">Calendar start date</label><input class="form-control" type="date" id="visual-<?= e($key); ?>-start" name="start" value="<?= e($visualFilters['start']); ?>" required></div>
        <?php if ($visualAction === '/hris'): ?><input type="hidden" name="group" value="<?= e($visualFilters['group']); ?>"><?php else: ?>
        <?php if ($visualFilters['run_id'] !== null): ?><input type="hidden" name="run_id" value="<?= (int) $visualFilters['run_id']; ?>"><?php endif; ?>
        <input type="hidden" name="run_q" value="<?= e($visualFilters['run_q']); ?>"><?php endif; ?>
        <button class="btn btn-outline-primary" type="submit">Show 30 days</button>
    </form>
    <p class="dashboard-chart-range"><?= e($visualDate($visualFilters['start'])); ?> – <?= e($visualDate($visualEnd)); ?> · Manila</p>
    <?php elseif ($key === 'composition'): ?>
    <form method="get" action="/hris#visual-composition" class="dashboard-chart-controls">
        <input type="hidden" name="start" value="<?= e($visualFilters['start']); ?>">
        <div class="dashboard-filter-field"><label for="visual-group">Group employees by</label><select class="form-select" name="group" id="visual-group"><option value="department"<?= $visualFilters['group'] === 'department' ? ' selected' : ''; ?>>Department</option><option value="position"<?= $visualFilters['group'] === 'position' ? ' selected' : ''; ?>>Position</option></select></div>
        <button class="btn btn-outline-primary" type="submit">Apply grouping</button>
    </form>
    <?php elseif ($key === 'progress'): ?>
    <form method="get" action="/payroll#visual-progress" class="dashboard-chart-controls dashboard-run-controls">
        <input type="hidden" name="start" value="<?= e($visualFilters['start']); ?>">
        <?php if ($run): ?><input type="hidden" name="run_id" value="<?= (int) $run['id']; ?>"><?php endif; ?>
        <div class="dashboard-filter-field"><label for="visual-run-search">Find a run by code</label><input class="form-control" type="search" id="visual-run-search" name="run_q" maxlength="100" value="<?= e($visualFilters['run_q']); ?>" placeholder="Search run codes"></div>
        <button class="btn btn-outline-primary" type="submit">Find runs</button>
    </form>
    <?php if ($available): ?>
    <form method="get" action="/payroll#visual-progress" class="dashboard-chart-controls">
        <input type="hidden" name="start" value="<?= e($visualFilters['start']); ?>"><input type="hidden" name="run_q" value="<?= e($visualFilters['run_q']); ?>">
        <div class="dashboard-filter-field"><label for="visual-run-select">Payroll run</label><select class="form-select" id="visual-run-select" name="run_id" required>
            <option value="" disabled<?= !$run ? ' selected' : ''; ?>>Choose a payroll run</option>
            <?php if ($run && !in_array((int) $run['id'], array_map('intval', array_column($panel['options'], 'id')), true)): ?>
            <option value="<?= (int) $run['id']; ?>" selected><?= e($run['run_code']); ?> · <?= e($run['status'] ?? 'Unknown'); ?> · <?= e($run['pay_date'] ?? 'No pay date'); ?></option>
            <?php endif; ?>
            <?php foreach ($panel['options'] as $option): ?><option value="<?= (int) $option['id']; ?>"<?= $run && (int) $run['id'] === (int) $option['id'] ? ' selected' : ''; ?>><?= e($option['run_code']); ?> · <?= e($option['status'] ?? 'Unknown'); ?> · <?= e($option['pay_date'] ?? 'No pay date'); ?></option><?php endforeach; ?>
        </select></div><button class="btn btn-outline-primary" type="submit"<?= !$panel['options'] && !$run ? ' disabled' : ''; ?>>View calculation</button>
    </form>
    <p class="dashboard-chart-help"><?= $panel['options'] ? ($panel['more'] ? 'Showing the latest 50 matching runs. Refine the code search to find older runs.' : 'Matching runs are ordered by pay date, then run ID, newest first.') : 'No runs match the code search.'; ?> Searching does not change the selected run.</p>
    <?php endif; ?>
    <?php endif; ?>

    <?php if ($kind === 'prerequisite'): ?>
    <div class="dashboard-chart-state"><i class="bi bi-info-circle" aria-hidden="true"></i><p><strong>Not available until prerequisites are resolved</strong><span>No chart values are shown. The deferred calculations or historical data remain unchanged.</span></p></div>
    <?php elseif (!$available): ?>
    <div class="dashboard-chart-state" role="status"><i class="bi bi-exclamation-circle" aria-hidden="true"></i><p><strong>Visual unavailable</strong><span><?= e($panel['notice'] ?? 'This visual could not be loaded. Refresh this page to retry.'); ?></span></p></div>
    <?php elseif ($kind === 'calendar'): ?>
    <?php $days = $panel['days']; $peak = $days ? max($days) : 0; ?>
    <p class="dashboard-chart-total"><?= number_format($peak); ?> <span>maximum employees on approved leave on a single day</span></p>
    <div class="dashboard-calendar-week" aria-hidden="true"><?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday): ?><span><?= e($weekday); ?></span><?php endforeach; ?></div>
    <ol class="dashboard-calendar" aria-label="Approved leave by date; each value counts employees">
        <?php $first = true; foreach ($days as $date => $count): $day = new DateTimeImmutable($date); ?>
        <li class="dashboard-calendar-day dashboard-calendar-day--<?= $count >= 2 ? 'many' : ($count === 1 ? 'one' : 'zero'); ?>"<?= $first ? ' style="grid-column-start:' . (int) $day->format('N') . '"' : ''; ?>>
            <time datetime="<?= e($date); ?>" aria-label="<?= e($visualDate($date)); ?>"><?= e($day->format('j')); ?></time><strong><span class="visually-hidden">Employees on approved leave: </span><?= number_format($count); ?></strong>
        </li><?php $first = false; endforeach; ?>
    </ol>
    <p class="dashboard-calendar-legend"><span><i class="dashboard-legend-zero" aria-hidden="true"></i>0 employees</span><span><i class="dashboard-legend-one" aria-hidden="true"></i>1 employee</span><span><i class="dashboard-legend-many" aria-hidden="true"></i>2+ employees</span></p>
    <?php if (!$peak): ?><p class="dashboard-chart-empty">No approved leave overlaps this window for current employee records.</p><?php endif; ?>
    <?php if ($panel['invalid']): ?><p class="dashboard-chart-help"><?= number_format($panel['invalid']); ?> overlapping request(s) had invalid date ranges and were excluded. Review Leave Management.</p><?php endif; ?>
    <details class="dashboard-chart-details"><summary>View exact daily values</summary><table class="dashboard-values"><caption class="visually-hidden">Employees on approved leave per date</caption><thead><tr><th scope="col">Date</th><th scope="col">Employees</th></tr></thead><tbody><?php foreach ($days as $date => $count): ?><tr><th scope="row"><?= e($visualDate($date)); ?></th><td><?= number_format($count); ?></td></tr><?php endforeach; ?></tbody></table></details>
    <?php elseif ($kind === 'timeline'): ?>
    <?php foreach (['events' => 'Saved dates in this 30-day window', 'review' => 'Past saved dates — review status'] as $listKey => $heading): ?>
    <h4 class="dashboard-timeline-heading"><?= e($heading); ?></h4>
    <?php if ($listKey === 'review'): ?><p class="dashboard-chart-help">Dates in the 30 days before today on runs not saved as Finalized or Cancelled. This list does not establish unpaid or overdue payroll.</p><?php endif; ?>
    <?php if (!$panel[$listKey]): ?><p class="dashboard-chart-empty"><?= $listKey === 'events' ? 'No saved cutoff or pay dates fall within this window.' : 'No past saved dates need review in this scope.'; ?></p>
    <?php else: ?><div class="dashboard-timeline-scroll" role="region" aria-label="<?= e($heading); ?>" tabindex="0"><ol class="dashboard-timeline">
        <?php foreach ($panel[$listKey] as $event): ?><li><time datetime="<?= e($event['date']); ?>"><?= e($visualDate($event['date'])); ?></time><div><strong><?= e($event['label']); ?></strong><span><?= e($event['run_code']); ?> · <?= e($event['status']); ?></span></div><?php if ($card['can_view_run']): ?><a class="dashboard-chart-link" href="/payroll/runs/view/<?= (int) $event['id']; ?>" aria-label="View payroll run <?= e($event['run_code']); ?>">View Run</a><?php endif; ?></li><?php endforeach; ?>
    </ol></div><?php endif; ?>
    <?php endforeach; ?>
    <?php else: ?>
    <?php if ($kind === 'progress' && !$run): ?><p class="dashboard-chart-empty" role="status"><?= e($panel['notice']); ?></p>
    <?php else: ?>
    <?php if ($run): ?><p class="dashboard-chart-run"><strong><?= e($run['run_code']); ?></strong><span>Saved run status: <?= e($run['status'] ?? 'Unknown'); ?> · Pay date: <?= e($run['pay_date'] ?? 'Not set'); ?></span></p><?php endif; ?>
    <p class="dashboard-chart-total"><?= number_format($total); ?> <span><?= $kind === 'progress' ? 'loaded employees in this run' : ($key === 'statuses' ? ($total === 1 ? 'saved run · all dates' : 'saved runs · all dates') : ($key === 'leave-age' ? ($total === 1 ? 'pending leave request · all dates' : 'pending leave requests · all dates') : ($total === 1 ? 'employee record · current assignments' : 'employee records · current assignments'))); ?></span></p>
    <?php if (!$total): ?><p class="dashboard-chart-empty"><?= $kind === 'progress' ? 'No employees are loaded in this run.' : 'No saved records in this scope.'; ?></p><?php endif; ?>
    <?php if ($kind === 'progress' && $total): ?>
    <div class="dashboard-stacked" aria-hidden="true"><?php foreach ($rows as $row): if (!$row['value']) continue; ?><span class="dashboard-tone-<?= e($row['tone']); ?>" style="width:<?= number_format(100 * $row['value'] / $total, 6, '.', ''); ?>%"></span><?php endforeach; ?></div>
    <p class="dashboard-chart-help">Segment lengths show each state’s share of the loaded employees. Exact counts are listed below.</p>
    <?php endif; ?>
    <table class="dashboard-bars dashboard-values"><caption class="visually-hidden"><?= e($card['title']); ?> — exact counts</caption><thead><tr><th scope="col">Category</th><th scope="col">Count</th><th scope="col"><span class="visually-hidden">Relative count</span></th></tr></thead><tbody>
        <?php foreach ($rows as $row): ?><tr><th scope="row"><?= e($row['label']); ?></th><td><?= number_format($row['value']); ?></td><td class="dashboard-bar-track dashboard-tone-<?= e($row['tone']); ?>"><svg viewBox="0 0 100 10" preserveAspectRatio="none" aria-hidden="true" focusable="false"><rect x="0" y="0" width="<?= number_format(100 * $row['value'] / $maximum, 6, '.', ''); ?>" height="10" rx="2" fill="currentColor"/></svg></td></tr><?php endforeach; ?>
    </tbody></table>
    <?php if ($kind === 'progress' && !empty($card['link']) && $run): ?><a class="dashboard-chart-link" href="/payroll/runs/view/<?= (int) $run['id']; ?>">View selected run</a><?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($card['link'])): ?><footer class="dashboard-chart-footer"><a class="dashboard-chart-link" href="<?= e($card['link']); ?>"><?= e($card['link_label']); ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a><span>Opens the existing page; dashboard filters are not applied there.</span></footer><?php endif; ?>
</section>
