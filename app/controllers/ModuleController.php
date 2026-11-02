<?php

require_once __DIR__ . '/../models/ModuleDashboardModel.php';

class ModuleController extends Controller
{
    public function index()
    {
        $data = [
            'name' => $_SESSION['user']['name'],
            'is_super_admin' => $_SESSION['user']['name'],
            'title' => 'Home'
        ];
        $this->view('home', $data);
    }

    public function about()
    {
        $data = [
            'name' => $_SESSION['user']['name'],
            'title' => 'About'
        ];
        $this->view('about', $data);
    }

    // public function users()
    // {
    //     $data = [
    //         'name' => $_SESSION['user']['name'],
    //         'title' => 'Users',
    //     ];
    //     $this->view('users', $data);
    // }


    /////////// module pages here /////////
    public function hris()
    {
        $this->view('module_hris/hris_index', [
            'name' => $_SESSION['user']['name'] ?? '',
            'title' => 'HRIS Home',
            'dashboard' => $this->readDashboard('hris'),
            'visuals' => $this->readVisuals('hris'),
        ]);
    }

    public function payroll()
    {
        $this->view('module_payroll/payroll_index', [
            'name' => $_SESSION['user']['name'] ?? '',
            'title' => 'Payroll Home',
            'dashboard' => $this->readDashboard('payroll'),
            'visuals' => $this->readVisuals('payroll'),
        ]);
    }


    protected function visualFilters(array $input, DateTimeImmutable $now): array
    {
        $today = $now->setTimezone(new DateTimeZone('Asia/Manila'))->format('Y-m-d');
        $notices = [];
        $start = $input['start'] ?? $today;
        $date = is_string($start) && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $start) ? DateTimeImmutable::createFromFormat('!Y-m-d', $start, new DateTimeZone('Asia/Manila')) : false;
        if (!$date || $date->format('Y-m-d') !== $start || $date->format('Y') < '1000' || strlen($date->modify('+29 days')->format('Y-m-d')) !== 10) {
            $start = $today; $notices[] = 'The calendar start date was invalid. Showing today’s 30-day window.';
        }
        $group = $input['group'] ?? 'department';
        if (!in_array($group, ['department', 'position'], true)) {
            $group = 'department'; $notices[] = 'The grouping was invalid. Showing departments.';
        }
        $rawId = $input['run_id'] ?? '';
        $runId = null; $runInvalid = false;
        if ($rawId !== '') {
            $validId = is_string($rawId) && preg_match('/^[1-9][0-9]*$/D', $rawId) && filter_var($rawId, FILTER_VALIDATE_INT) !== false;
            if ($validId) $runId = (int) $rawId;
            else { $runInvalid = true; $notices[] = 'The selected run is unavailable. Choose a run from the list.'; }
        }
        $search = $input['run_q'] ?? '';
        if (!is_string($search) || !mb_check_encoding($search, 'UTF-8') || mb_strlen($search) > 100) {
            $search = ''; $notices[] = 'Run search must contain at most 100 characters.';
        }
        return ['start' => $start, 'group' => $group, 'run_id' => $runId, 'run_invalid' => $runInvalid,
            'run_q' => trim($search), 'notices' => $notices];
    }

    protected function readVisuals(string $module): array
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
        $result = ['filters' => $this->visualFilters($_GET, $now), 'as_of' => $now->format('j M Y, g:i A'), 'panels' => []];
        $tenantId = (int) ($_SESSION['user']['tenant_id'] ?? 0);
        if ($tenantId <= 0) return $result;
        try {
            $model = $this->dashboardModel();
            $result['panels'] = $module === 'hris'
                ? $model->getHrisVisuals($tenantId, $result['filters'], $now)
                : $model->getPayrollVisuals($tenantId, $result['filters'], $now);
        } catch (Throwable $error) {
            error_log('Dashboard visual setup failed [' . $module . ']: ' . get_class($error));
        }
        return $result;
    }

    protected function dashboardModel(): ModuleDashboardModel
    {
        return new ModuleDashboardModel();
    }

    protected function readDashboard(string $module): array
    {
        $unavailable = ['available' => false, 'notice' => 'Summary could not be loaded. Refresh this page to retry.'];
        $tenantId = (int) ($_SESSION['user']['tenant_id'] ?? 0);
        if ($tenantId <= 0) {
            $unavailable['notice'] = 'A current organization is required to load this overview.';
            return $unavailable;
        }
        try {
            $model = $this->dashboardModel();
            $data = $module === 'hris'
                ? ['summary' => $model->getHrisSummary($tenantId)]
                : $model->getPayrollOverview($tenantId);
            return ['available' => true, 'as_of' => date('j M Y, g:i A')] + $data;
        } catch (Throwable $error) {
            error_log('Home summary read failed [' . $module . ']: ' . get_class($error));
            return $unavailable;
        }
    }
}
