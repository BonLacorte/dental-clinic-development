"""Execute extracted SELECTs with fictional CTEs. No DDL/DML, app bootstrap or real DB connection."""
from pathlib import Path
import re
import sqlite3
import subprocess

ROOT = Path(__file__).resolve().parent.parent
text = (ROOT / 'app/models/hris/EmployeeModel.php').read_text()
baseline = subprocess.check_output(['git', 'show', '681f84e0bb3bbcd02cfb92b20a042e032a766d53:app/models/hris/EmployeeModel.php'], cwd=ROOT, text=True)

def query(source):
    method = re.search(r'public function getAllEmployees\(.*?^    }', source, re.S | re.M).group()
    return re.search(r'prepare\("(.*?)"\)', method, re.S).group(1)

old, new = query(baseline), query(text)
assert new.replace('                p.name AS position,\n', '').replace('            LEFT JOIN hris_positions p ON p.id = e.position_id AND p.tenant_id = e.tenant_id\n', '') == old
assert 'position' not in re.search(r'private function sortColumn.*?^    }', text, re.S | re.M).group().split('$columns = [', 1)[1]
fields = ['id','tenant_id','employee_no','firstname','middlename','lastname','email','contact_no','branch_id','employment_type_id','employment_status_id','position_id','date_hired','date_resigned','status','created_at','deleted_at']
fixtures = []
for ident, tenant, position, deleted in [(1,1,100,None),(2,1,None,None),(3,1,200,None),(4,2,200,None),(5,1,100,'2026-10-20')]:
    fixtures.append([ident,tenant,f'000{ident}','Fictional',None,f'Sample {ident}',None,None,10,1,1,position,'2026-10-01',None,'ACTIVE',f'2026-10-{ident:02}',deleted])
params = {}
values = []
for index, row in enumerate(fixtures):
    names = [f'fixture_{index}_{column}' for column in range(len(fields))]
    params.update(zip(names,row));values.append('('+','.join(':'+n for n in names)+')')
prefix = 'WITH hris_employees('+','.join(fields)+') AS (VALUES '+','.join(values)+'''),
tenant_branches(id,name) AS (VALUES (10,'Fictional clinic')),
hris_employment_types(id,name) AS (VALUES (1,'Regular')),
hris_employment_statuses(id,name) AS (VALUES (1,'Active')),
hris_positions(id,tenant_id,name) AS (VALUES (100,1,'Dentist'),(200,2,'Other tenant position'))
'''
db = sqlite3.connect(':memory:')
db.row_factory = sqlite3.Row
db.create_function('CONCAT_WS', -1, lambda sep,*parts: sep.join(str(p) for p in parts if p is not None))
search_sql = re.search(r'private function employeeSearchSql.*?return "(.*?)";', text, re.S).group(1)
columns = {'name':'e.lastname','branch_name':'b.name','employment_type':'et.name','employment_status':'es.name','employee_no':'e.employee_no','date_hired':'e.date_hired','created_at':'e.created_at'}
for field, expression in columns.items():
    for direction in ['ASC','DESC']:
        for search in ['', 'Sample 1', 'no match']:
            bound = {**params,'tenant_id':1,'limit':10,'offset':0,'search':'%'+search+'%'}
            def execute(sql):
                sql = sql.replace('{$whereSearch}', search_sql if search else '').replace('{$orderBy}',expression).replace('{$direction}',direction)
                return [dict(r) for r in db.execute(prefix + sql,bound)]
            previous, current = execute(old), execute(new)
            assert [{k:v for k,v in r.items() if k!='position'} for r in current] == previous
            assert len({r['id'] for r in current}) == len(current)
            for row in current:
                assert row['position'] == ('Dentist' if row['id']==1 else None)
print('PASS: 42 read-only SELECT fixture comparisons preserve all existing list fields, search/order/page semantics, null position, employee tenant/deletion filtering and Position tenant isolation; no database/schema writes.')
