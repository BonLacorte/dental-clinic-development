#!/usr/bin/env python3
"""Real HTTP/PDO acceptance. Requires ONLY the named, labelled tmpfs test stack.
Never point this runner at the ordinary demo or a remote server. No third-party packages.
"""
import hashlib
import http.cookiejar
import json
import re
import subprocess
import sys
import urllib.error
import urllib.parse
import urllib.request

BASE = 'http://127.0.0.1:8018'
COUNT = 0

def check(ok, name):
    global COUNT
    if not ok:
        raise AssertionError('FAIL ' + name)
    COUNT += 1

def inspect(name):
    return json.loads(subprocess.check_output(['docker', 'inspect', name], text=True))[0]

def guard():
    db, app = inspect('cemp-test-db'), inspect('cemp-test-app')
    for obj in (db, app):
        if obj['Config']['Labels'].get('montero.cemp.disposable') != 'true':
            raise RuntimeError('Refusing unowned fixtures')
        expected = {'cemp-test-net'} if obj is db else {'cemp-test-net', 'cemp-test-web'}
        if set(obj['NetworkSettings']['Networks']) != expected:
            raise RuntimeError('Refusing unexpected network')
    if db['Mounts'] or '/var/lib/mysql' not in db['HostConfig']['Tmpfs'] or db['HostConfig']['PortBindings']:
        raise RuntimeError('Refusing persistent or exposed database')
    ports = app['HostConfig']['PortBindings']['8000/tcp']
    if ports != [{'HostIp': '127.0.0.1', 'HostPort': '8018'}]:
        raise RuntimeError('Refusing unexpected application transport')
    network = json.loads(subprocess.check_output(['docker', 'network', 'inspect', 'cemp-test-net'], text=True))[0]
    if not network['Internal'] or network['Labels'].get('montero.cemp.disposable') != 'true':
        raise RuntimeError('Refusing unowned or non-internal DB network')
    if set(network['Containers']) != {db['Id'], app['Id']}:
        raise RuntimeError('Refusing shared fixture network')
    web = json.loads(subprocess.check_output(['docker','network','inspect','cemp-test-web'], text=True))[0]
    if web['Labels'].get('montero.cemp.disposable') != 'true' or set(web['Containers']) != {app['Id']}:
        raise RuntimeError('Refusing shared or unowned frontend network')
    if sql('SELECT @@hostname').strip() != db['Config']['Hostname']:
        raise RuntimeError('Wrong database instance')
    config_check = subprocess.check_output(['docker', 'exec', 'cemp-test-app', 'php', '-r',
        "$c=(require '/workspace/config/local.php')['default']; echo $c['host']==='db' && $c['dbname']==='monterodental_demo' && $c['user']==='montero_demo' ? gethostbyname('db') : 'unsafe';"], text=True)
    if config_check != db['NetworkSettings']['Networks']['cemp-test-net']['IPAddress']:
        raise RuntimeError('Application does not target the owned test DB')
    print('PASS fixture guard: dedicated labelled tmpfs DB, no persistent DB mount/host port, internal network')

def sql(query):
    return subprocess.check_output(['docker', 'exec', '-i', 'cemp-test-db', 'sh', '-c',
        'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" --batch --skip-column-names monterodental_demo'],
        input=query, text=True)

class Client:
    def __init__(self, email='admin@example.test', password='DemoDental2026!'):
        self.opener = urllib.request.build_opener(urllib.request.ProxyHandler({}), urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
        _, text = self.request('/')
        token = re.search(r'name="csrf_token" value="([^"]+)"', text).group(1)
        self.request('/', {'email': email, 'password': password, 'csrf_token': token})
        _, text = self.request('/hris/employees')
        token = re.search(r'name="csrf_token" value="([^"]+)"', text)
        self.token = token.group(1) if token else None

    def request(self, path, data=None):
        req = urllib.request.Request(BASE + path, data=urllib.parse.urlencode(data).encode() if data is not None else None,
            headers={'X-Requested-With': 'XMLHttpRequest'} if path.startswith('/hris/') else {})
        try:
            res = self.opener.open(req, timeout=15)
        except urllib.error.HTTPError as error:
            res = error
        return res.status, res.read().decode()

    def json(self, path, data=None):
        status, text = self.request(path, data)
        try:
            result = json.loads(text)
        except ValueError:
            raise AssertionError('Non-JSON response: ' + path) from None
        return status, result


def employee(client, **changes):
    return dict(employee_no='', firstname='CempFictional', lastname='Fixture', branch_id='1',
                employment_type_id='1', date_hired='2026-01-01', csrf_token=client.token, **changes)


def seed_security():
    # Explicit fictional fixtures, created only after guard() verifies tmpfs ownership.
    sql("""
    INSERT IGNORE INTO tenants (id, name, code) VALUES
        (8001, 'CEMP Fictional Tenant Two', 'CEMP_TWO'), (8002, 'CEMP No Module', 'CEMP_NONE');
    INSERT IGNORE INTO tenant_modules (tenant_id, module_id)
        SELECT 8001, id FROM modules WHERE code = 'HRIS';
    INSERT IGNORE INTO tenant_branches (id, tenant_id, code, name) VALUES
        (8001, 8001, 'CEMP_FOREIGN', 'CEMP Foreign Branch'),
        (8002, 1, 'CEMP_DELETED', 'CEMP Deleted Branch');
    UPDATE tenant_branches SET deleted_at = NOW() WHERE id = 8002;
    INSERT IGNORE INTO hris_positions (id, tenant_id, name) VALUES (8001, 8001, 'CEMP Foreign Position');
    INSERT IGNORE INTO hris_org_units (id, tenant_id, name) VALUES (8001, 8001, 'CEMP Foreign Unit');
    INSERT IGNORE INTO hris_employment_types (id, tenant_id, code, name) VALUES
        (8001, 8001, 'CEMP_FOREIGN', 'CEMP Foreign Type'), (8002, NULL, 'CEMP_GLOBAL', 'CEMP Global Type');
    INSERT IGNORE INTO hris_employment_statuses (id, tenant_id, code, name) VALUES
        (8001, 8001, 'CEMP_FOREIGN', 'CEMP Foreign Status'), (8002, NULL, 'CEMP_GLOBAL', 'CEMP Global Status');
    INSERT IGNORE INTO hris_employees (id, tenant_id, employee_no, firstname, lastname, branch_id, employment_type_id, date_hired)
        VALUES (8001, 8001, 'CEMP-8001', 'ForeignFictional', 'Fixture', 8001, 8001, '2026-01-01');
    INSERT IGNORE INTO payroll_employee_compensations (id, tenant_id, employee_id, salary_type, payroll_frequency, salary_rate, effective_from)
        VALUES (8001, 8001, 8001, 'DAILY', 'SEMI_MONTHLY', 123.45, '2026-01-01');
    INSERT IGNORE INTO roles (id, tenant_id, code, name) VALUES
        (8001, 1, 'CEMP_LIMITED', 'CEMP Limited'), (8002, 1, 'CEMP_BROAD', 'CEMP Broad Only'),
        (8003, 1, 'CEMP_VIEW', 'CEMP View Only'), (8004, 1, 'CEMP_SUPER', 'Super Admin'),
        (8006, 8002, 'CEMP_NOMODULE', 'CEMP No Module'), (8007, 8001, 'CEMP_FOREIGN', 'CEMP Foreign');
    INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT 8001, id FROM permissions WHERE code IN
        ('hris.view', 'hris.employee.view', 'hris.employee.create', 'hris.employee.edit', 'hris.employee.delete');
    INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT 8006, permission_id FROM role_permissions WHERE role_id = 8001;
    INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT 8007, permission_id FROM role_permissions WHERE role_id = 8001;
    INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT 8002, id FROM permissions WHERE code = 'hris.view';
    INSERT IGNORE INTO role_permissions (role_id, permission_id) SELECT 8003, id FROM permissions WHERE code IN ('hris.view', 'hris.employee.view');
    """)
    for uid, tenant, role, branch, slug in [(8001,1,8001,1,'limited'), (8002,1,8002,1,'broad'),
            (8003,1,8003,1,'view'), (8004,1,8001,None,'empty'), (8005,1,8004,1,'super'),
            (8006,8002,8006,None,'nomodule'), (8007,8001,8007,8001,'foreign')]:
        # Copy only the known fictional seed's password hash inside the disposable DB.
        sql(f"INSERT IGNORE INTO users (id, tenant_id, name, email, password) SELECT {uid}, {tenant}, 'CEMP {slug}', 'cemp-{slug}@example.test', password FROM users WHERE id = 1;"
            f"INSERT IGNORE INTO user_roles (user_id, role_id) VALUES ({uid}, {role});" +
            (f"INSERT IGNORE INTO user_branches (user_id, branch_id) VALUES ({uid}, {branch});" if branch else ''))


def fingerprints():
    tables = sql('SHOW TABLES').splitlines()
    return {table: hashlib.sha256(sql(f'SELECT * FROM `{table}` ORDER BY id').encode()).hexdigest()
            for table in tables if table not in ('hris_employees', 'payroll_employee_compensations')}


def security(client):
    seed_security()
    invariant = fingerprints()
    limited = Client('cemp-limited@example.test')
    _, res = limited.json('/hris/employees/getallemployees')
    check(res.get('success') is True and all(int(row['branch_id']) == 1 for row in res['data']), 'restricted list branch scope')
    for eid in (3, 8001, 999999):
        for endpoint in ('getemployee', 'getcompensation'):
            _, res = limited.json(f'/hris/employees/{endpoint}/{eid}')
            check(res.get('success') is False and res.get('message') != 'Data not found.', 'scoped detail failure')
        _, res = limited.json(f'/hris/employees/updateemployee/{eid}', employee(limited))
        check(res.get('success') is False, 'scoped update failure')
        _, res = limited.json(f'/hris/employees/deleteemployee/{eid}', {'csrf_token': limited.token})
        check(res.get('success') is False, 'scoped delete failure')
    _, res = limited.json('/hris/employees/getallemployees?search=Clara')
    check(res['data'] == [] and res['totalPages'] == 1, 'search/count cannot expose other branch')
    for field in ('branch_id', 'position_id', 'org_unit_id', 'employment_type_id', 'employment_status_id'):
        data = employee(limited); data[field] = '8001'
        before = sql('SELECT COUNT(*) FROM hris_employees')
        _, res = limited.json('/hris/employees/create', data)
        check(res.get('success') is False and sql('SELECT COUNT(*) FROM hris_employees') == before, 'foreign reference ' + field)
    for branch in ('2', '8002'):
        data = employee(limited); data['branch_id'] = branch
        _, res = limited.json('/hris/employees/create', data)
        check(res.get('success') is False, 'unassigned/deleted branch create')
    data = employee(limited); data.update(employment_type_id='8002', employment_status_id='8002')
    _, res = limited.json('/hris/employees/create', data)
    check(res.get('success') is True, 'global employment catalogs remain usable')
    target = res['id']
    data = employee(limited); data['branch_id'] = '2'
    _, res = limited.json(f'/hris/employees/updateemployee/{target}', data)
    check(res.get('success') is False, 'cannot move employee outside assigned branches')
    _, res = client.json('/hris/employees/updateemployee/8001', employee(client))
    check(res.get('success') is False, 'foreign employee fails for tenant-wide administrator')
    for slug in ('empty', 'super'):
        fallback = Client('cemp-' + slug + '@example.test')
        _, res = fallback.json('/hris/employees/getemployee/3')
        check(res.get('success') is True, 'established tenant-wide fallback ' + slug)
        _, res = fallback.json('/hris/employees/getemployee/8001')
        check(res.get('success') is False, 'fallback never bypasses tenant ' + slug)
    foreign = Client('cemp-foreign@example.test')
    _, res = foreign.json('/hris/employees/getallemployees')
    check([int(row['id']) for row in res['data']] == [8001], 'second tenant sees only its employee')
    for slug in ('broad', 'nomodule'):
        denied = Client('cemp-' + slug + '@example.test')
        status, res = denied.json('/hris/employees/getallemployees')
        check(status == 403 and 'error' in res, 'list permission/module gate ' + slug)
    viewer = Client('cemp-view@example.test')
    for endpoint, data in [('create', employee(viewer)), ('updateemployee/1', employee(viewer)), ('deleteemployee/1', {'csrf_token': viewer.token})]:
        status, res = viewer.json('/hris/employees/' + endpoint, data)
        check(status == 403 and 'error' in res, 'existing employee action permission ' + endpoint)
    rate = dict(id='', employee_id=str(target), salary_type='DAILY', payroll_frequency='BI_WEEKLY', salary_rate='123.45', effective_from='2026-01-01', effective_to='', csrf_token=limited.token)
    before = sql('SELECT COUNT(*) FROM payroll_employee_compensations')
    for changed in ({'csrf_token': 'invalid'}, {'employee_id':'3'}, {'employee_id':'8001'}, {'salary_rate':'abc'}, {'effective_from':'2026-02-30'}, {'tenant_id':'2'}):
        _, res = limited.json('/hris/employees/createcompensation', dict(rate, **changed))
        check(res.get('success') is False, 'invalid/foreign compensation create')
    check(sql('SELECT COUNT(*) FROM payroll_employee_compensations') == before, 'rejected compensation did not persist')
    _, res = limited.json('/hris/employees/createcompensation', rate)
    check(res.get('success') is True and res.get('id',0) > 0, 'real compensation create')
    cid = res['id']; rate['id'] = str(cid)
    for _ in range(2):
        _, res = limited.json(f'/hris/employees/updatecompensation/{cid}', rate)
        check(res.get('success') is True, 'compensation unchanged update')
    _, res = limited.json(f'/hris/employees/getcompensation/{target}')
    check(res.get('success') is True and res['data']['salary_rate'] == '123.45' and res['data']['payroll_frequency'] == 'BI_WEEKLY', 'compensation exact persistence')
    for endpoint, changed in [(str(cid), {'csrf_token':'bad'}), (str(cid), {'employee_id':'1'}), (str(cid), {'employee_id':'3'}), ('8001', {'id':'8001'}), ('999999', {'id':'999999'}), (str(cid), {'id':'999999'})]:
        _, res = limited.json('/hris/employees/updatecompensation/' + endpoint, dict(rate, **changed))
        check(res.get('success') is False, 'tampered compensation update fails')
    _, res = limited.json(f'/hris/employees/deleteemployee/{target}', {'csrf_token':limited.token})
    check(res.get('success') is True, 'delete owned isolated employee fixture')
    _, res = limited.json(f'/hris/employees/getcompensation/{target}')
    check(res.get('success') is False and res.get('message') != 'Data not found.', 'deleted employee compensation cannot become create path')
    _, res = limited.json(f'/hris/employees/updatecompensation/{cid}', rate)
    check(res.get('success') is False, 'deleted employee compensation cannot update')
    _, legacy = viewer.json('/hris/employees/getcompensation/1')
    status, payroll_legacy = viewer.json('/payroll/compensations/getcompensationbyid/3')
    check(legacy.get('success') is True and status == 200 and payroll_legacy.get('success') is True,
          'documented legacy compensation access observed without assigning a new policy')
    print('BLOCKED compensation authorization certification: HRIS viewer can read rates; PAYROLL route remains tenant-wide without action permissions/branch scope')
    # Legacy inconsistent references must not leak foreign display labels through joins.
    sql("INSERT IGNORE INTO hris_employees (id,tenant_id,employee_no,firstname,lastname,branch_id,position_id,org_unit_id,employment_type_id,employment_status_id,date_hired) VALUES (7002,1,'CEMP-7002','LegacyFictional','Fixture',1,8001,8001,8001,8001,'2026-01-01')")
    _, res = client.json('/hris/employees/getemployee/7002')
    check(all(res['data'][field] is None for field in ('position','org_unit','employment_type','employment_status')), 'foreign reference labels excluded by join predicates')
    check(fingerprints() == invariant, 'all 35 other table fingerprints unchanged during mutation/security tests')

def run():
    guard()
    anonymous = urllib.request.build_opener(urllib.request.ProxyHandler({}))
    for body in (None, b'firstname=NoPersistence'):
        res = anonymous.open(urllib.request.Request(BASE + '/hris/employees/create', data=body) if body else BASE + '/hris/employees/getallemployees')
        check('Access denied. Please login.' in res.read().decode(), 'unauthenticated request denied before employee action')
    client = Client()
    check(bool(client.token), 'ordinary login and employee page token')
    initial = int(sql('SELECT COUNT(*) FROM hris_employees'))
    for field, value in [('firstname', ''), ('lastname', ' '), ('date_hired', '2026-02-30'),
                         ('branch_id', 'x'), ('email', 'bad'), ('csrf_token', 'bad'),
                         ('firstname[]', 'array'), ('tenant_id', '2')]:
        data = employee(client); data[field] = value
        _, res = client.json('/hris/employees/create', data)
        check(res.get('success') is False, 'rejected create ' + field)
    check(int(sql('SELECT COUNT(*) FROM hris_employees')) == initial, 'invalid creates did not persist')
    data = employee(client); data['employee_no'] = 'CLIENT-TAMPERED'
    status, res = client.json('/hris/employees/create', data)
    check(status == 200 and res.get('success') is True and res.get('id', 0) > 0, 'real employee create')
    eid = res['id']
    _, res = client.json(f'/hris/employees/getemployee/{eid}')
    check(res.get('success') is True and res['data']['employee_no'] != 'CLIENT-TAMPERED', 'generated employee number persisted')
    check(res['data']['email'] is None and res['data']['position_id'] is None, 'blank optional values persisted as null')
    data['employment_status_id'] = res['data']['employment_status_id']
    for _ in range(2):
        _, res = client.json(f'/hris/employees/updateemployee/{eid}', data)
        check(res.get('success') is True, 'accessible no-op edit succeeds')
    for path in ['/hris/employees/updateemployee/999999', '/hris/employees/deleteemployee/999999']:
        _, res = client.json(path, data if 'update' in path else {'csrf_token': client.token})
        check(res.get('success') is False, 'missing-row mutation fails')
    bad = dict(data, date_resigned='2025-01-01')
    _, res = client.json(f'/hris/employees/updateemployee/{eid}', bad)
    check(res.get('success') is False, 'resignation order rejected')
    _, res = client.json(f'/hris/employees/getemployee/{eid}')
    check(res['data']['date_resigned'] is None, 'invalid update did not persist')
    _, res = client.json(f'/hris/employees/getcompensation/{eid}')
    check(res.get('success') is False and res.get('message') == 'Data not found.', 'accessible absent compensation preserves legacy shape')
    _, res = client.json('/hris/employees/getcompensation/999999')
    check(res.get('success') is False and res.get('message') != 'Data not found.', 'missing employee never treated as absent compensation')
    _, res = client.json(f'/hris/employees/deleteemployee/{eid}', {'csrf_token': client.token})
    check(res.get('success') is True, 'real soft delete')
    for path, data_or_none in [(f'/hris/employees/getemployee/{eid}', None),
                               (f'/hris/employees/updateemployee/{eid}', data),
                               (f'/hris/employees/deleteemployee/{eid}', {'csrf_token': client.token})]:
        _, res = client.json(path, data_or_none)
        check(res.get('success') is False, 'deleted row not read or mutated')
    # Real fixture-only SQL fault, removed in finally; verify generic failure and no write.
    before = sql('SELECT COUNT(*) FROM hris_employees')
    try:
        sql("CREATE TRIGGER cemp_fail_employee BEFORE INSERT ON hris_employees FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='CEMP synthetic SQL detail'")
        status, res = client.json('/hris/employees/create', employee(client))
        check(status == 500 and res.get('success') is False and 'SQL' not in res.get('message',''), 'SQL exception returns generic failure')
        check(sql('SELECT COUNT(*) FROM hris_employees') == before, 'fault-injected insert did not persist')
    finally:
        sql('DROP TRIGGER IF EXISTS cemp_fail_employee')
    _, res = client.json('/hris/employees/getallemployees?search[]=invalid')
    check(res.get('success') is False, 'malformed list query returns failure JSON')
    security(client)
    print(f'PASS {COUNT} real HTTP/persistence/security assertions')

if __name__ == '__main__':
    guard() if '--guard' in sys.argv else run()
