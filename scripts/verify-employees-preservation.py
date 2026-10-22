"""Read-only source gates; these are not PHP lint, SQL execution or browser tests."""
from pathlib import Path
import re
import subprocess

ROOT = Path(__file__).resolve().parent.parent
BASELINE = 'b5526bb4a7999bbd3c253fcdda1c8419a171314d'


def original(path):
    return subprocess.check_output(['git', 'show', BASELINE + ':' + path], cwd=ROOT, text=True)


def methods(text):
    # Repository convention: an outer method closes at four spaces.
    return {m.group(1): m.group(0) for m in re.finditer(r'^    (?:public|private|protected) function (\w+)\b.*?^    }', text, re.M | re.S)}


for path in ['app/controllers/hris/EmployeeController.php', 'app/models/hris/EmployeeModel.php']:
    before, after = methods(original(path)), methods((ROOT / path).read_text())
    if path.endswith('EmployeeModel.php'):
        # v2's explicitly authorized backward-compatible Position projection/join.
        after['getAllEmployees'] = after['getAllEmployees'].replace('                p.name AS position,\n', '').replace('            LEFT JOIN hris_positions p ON p.id = e.position_id AND p.tenant_id = e.tenant_id\n', '')
    protected = [name for name in before if name != 'index']
    assert all(before[name] == after.get(name) for name in protected), path + ': protected method changed'
    print('PASS:', path, len(protected), 'protected existing methods byte-identical')

path = 'app/views/module_hris/employees.php'
before, after = original(path), (ROOT / path).read_text()
start = '    <?php\n    // Existing field names'
assert before[before.index(start):before.index('</main>')] == after[after.index(start):after.index('</main>')]
assert "$navCanPermission('hris.employee.create')" in after
print('PASS: complete dialog/form markup, Add predicate and modal target preserved')

path = 'public/assets/css/style.css'
assert (ROOT / path).read_text().startswith(original(path))
print('PASS: complete accepted Premium CSS unchanged; component rules appended')

path = 'routes/hris-routes.php'
addition = "    $app->post('/hris/employees/export', 'hris/EmployeeController@exportEmployees', [\n        'PermissionMiddleware:permission=hris.employee.view',\n        'PermissionMiddleware:permission=hris.view'\n    ]);\n"
assert (ROOT / path).read_text().replace(addition, '') == original(path)
print('PASS: existing routes/middleware byte-identical; new export inherits Auth/Tenant/HRIS and both permissions')

allowlist = {
    'app/controllers/hris/EmployeeController.php', 'app/models/hris/EmployeeModel.php',
    'app/services/hris/EmployeeExportService.php', 'app/views/module_hris/employees.php',
    'routes/hris-routes.php', 'public/assets/css/style.css',
    'scripts/verify-employees-frontend.cjs', 'scripts/verify-employee-export.php',
    'scripts/verify-employees-preservation.py', 'docs/work-items/hris-employees-ux-modernization.md',
    'scripts/verify-employees-position.py', 'docs/work-items/hris-employees-complete-ux.md',
}
changes = set(subprocess.check_output(['git', 'diff', '--name-only', BASELINE], cwd=ROOT, text=True).splitlines())
untracked = set(subprocess.check_output(['git', 'ls-files', '--others', '--exclude-standard'], cwd=ROOT, text=True).splitlines())
assert (changes | untracked) <= allowlist, (changes | untracked) - allowlist
assert not any(p.startswith(('app/views/module_payroll/', 'app/models/payroll/', 'app/controllers/payroll/')) or p in ('composer.json', 'composer.lock') for p in changes)
print('PASS: exact implementation allowlist; Payroll, dependency, schema, policy and unrelated files unchanged')

controller = (ROOT / 'app/controllers/hris/EmployeeController.php').read_text()
export = methods(controller)['exportEmployees']
assert export.index('authorizedContext') < export.index('Csrf::validate') < export.index('getEmployeesForExport') < export.index('createFile')
assert "Csrf::validate($token, 'keep')" in export and 'finally' in export and 'register_shutdown_function' in export
model = methods((ROOT / 'app/models/hris/EmployeeModel.php').read_text())['getEmployeesForExport']
assert 'e.tenant_id = :tenant_id AND e.deleted_at IS NULL' in model
assert 'e.branch_id IN' in model and 'ub.user_id = :export_user_id' in model
assert 'b.tenant_id = e.tenant_id' in model and 'u.tenant_id = e.tenant_id' in model
assert '$maxRows + 1' in model and 'count($rows) > $maxRows' in model
projection = model.split('SELECT e.employee_no,', 1)[1].split('FROM hris_employees e', 1)[0]
assert all(field not in projection for field in ['e.id', 'e.email', 'e.contact_no', 'e.status', 'salary', 'compensation'])
print('PASS: static export authorization/CSRF order, parameterized tenant/branch/current-assignment restrictions, minimal projection, limit sentinel and cleanup gates')
