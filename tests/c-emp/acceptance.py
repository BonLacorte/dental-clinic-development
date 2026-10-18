#!/usr/bin/env python3
"""Real HTTP/PDO acceptance. Requires ONLY the named, labelled tmpfs test stack.
Never point this runner at the ordinary demo or a remote server. No third-party packages.
"""
import http.cookiejar
import json
import re
import subprocess
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
    if not network['Internal']:
        raise RuntimeError('Refusing non-internal DB network')
    if sql('SELECT @@hostname').strip() != db['Config']['Hostname']:
        raise RuntimeError('Wrong database instance')
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


def run():
    guard()
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
    print(f'PASS {COUNT} real HTTP/persistence assertions')

if __name__ == '__main__':
    run()
