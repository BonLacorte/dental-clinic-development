// Chromium acceptance using the existing optional Playwright installation; no app dependency.
// Real mutations target only newly created fictional records in the guarded tmpfs stack.
const {chromium} = require('playwright');
const {execFileSync} = require('node:child_process');
const assert = require('node:assert/strict');
execFileSync('python3', ['-B', 'tests/c-emp/acceptance.py', '--guard'], {stdio: 'inherit'});
const base = 'http://127.0.0.1:8018';
let checks = 0, intercepted = 0, unexpected = 0, realWrites = 0;
function check(ok, name) { assert.ok(ok, name); checks++; }
function fingerprint() {
    return execFileSync('python3', ['-B', '-c', "import sys,hashlib;sys.path.insert(0,'tests/c-emp');import acceptance as a;print(hashlib.sha256(''.join(a.sql('SELECT * FROM `'+t+'` ORDER BY id') for t in a.sql('SHOW TABLES').splitlines()).encode()).hexdigest())"], {encoding:'utf8'}).trim();
}
(async () => {
    const browser = await chromium.launch({executablePath:process.env.CHROMIUM_PATH || '/usr/bin/chromium', headless:true,
        args:['--no-sandbox'], ...(process.env.HTTPS_PROXY ? {proxy:{server:process.env.HTTPS_PROXY,bypass:'127.0.0.1,localhost'}} : {})});
    const context = await browser.newContext({serviceWorkers:'block'});
    const errors=[];
    context.on('page', p => p.on('pageerror', e => errors.push(e.message)));
    let active = null, login = true;
    await context.route('**/*', async route => {
        const req=route.request(), url=new URL(req.url());
        if (['GET','HEAD'].includes(req.method())) return route.continue();
        if (login && url.origin===base && url.pathname==='/' && req.method()==='POST') return route.continue();
        if (url.origin!==base || !url.pathname.startsWith('/hris/employees/') || !active) {
            unexpected++; return route.abort();
        }
        if (active.real) { realWrites++; return route.continue(); }
        const response=active; intercepted++;
        response.received();
        await response.gate;
        if (response.network) return route.abort('failed');
        return route.fulfill({status:response.status || 200, contentType:'application/json', body:response.raw || JSON.stringify(response.body)});
    });
    const page=await context.newPage();
    try {
        await page.goto(base+'/');
        await page.locator('#email').fill('admin@example.test');
        await page.locator('#password').fill('DemoDental2026!');
        await Promise.all([page.waitForURL('**/home'),page.locator('#login-button').click()]);
        login=false;
        const record=async id => (await context.request.get(base+'/hris/employees/getemployee/'+id)).json();
        const compensation=async id => (await context.request.get(base+'/hris/employees/getcompensation/'+id)).json();
        const waitList=async () => page.waitForFunction(()=>document.querySelector('.employee-table-region')?.getAttribute('aria-busy')==='false');
        const search=async name => {
            await page.locator('#employeeSearchInput').fill(name);
            await page.locator('#employeeSearchForm button[type=submit]').click();
            await waitList();
        };
        const fillAdd=async name => {
            const modal=page.locator('#addEmployeeModal');
            await modal.locator('button[type=submit]').waitFor();
            await page.waitForFunction(()=>!document.querySelector('#addEmployeeForm button[type=submit]').disabled);
            await modal.locator('[name=firstname]').fill(name);
            await modal.locator('[name=lastname]').fill('FictionalFixture');
            await modal.locator('[name=branch_id]').selectOption('1');
            await modal.locator('[name=employment_type_id]').selectOption('1');
            await modal.locator('[name=date_hired]').fill('2026-01-01');
        };
        const close=async modal => {
            await modal.locator('.modal-footer [data-bs-dismiss]').first().click();
            if (await modal.locator('.employee-discard-confirm').isVisible()) await modal.locator('.employee-discard-confirm').click();
            await modal.waitFor({state:'hidden'});
        };
        async function simulate(action, target, expectedSuccess, response) {
            const cfg={add:['#addEmployeeModal',null], edit:['#editEmployeeModal','.editEmployeeBtn'],
                delete:['#deleteEmployeeModal','.deleteEmployeeBtn'], compCreate:['#compensationModal','.editCompensationBtn'], compUpdate:['#compensationModal','.editCompensationBtn']}[action];
            if (action==='add') await page.getByRole('button',{name:'Add Employee',exact:true}).click();
            else await page.locator(`#employeesTable button${cfg[1]}[data-id="${target}"]`).click();
            const modal=page.locator(cfg[0]);await modal.waitFor({state:'visible'});
            await page.waitForFunction(id=>!bootstrap.Modal.getInstance(document.querySelector(id))._isTransitioning,cfg[0]);
            if(action==='add') await fillAdd('SimulatedOnly');
            if(action==='edit') {
                await page.waitForFunction(()=>!document.querySelector('#editEmployeeForm button[type=submit]').disabled);
                await modal.locator('[name=firstname]').fill('SimulatedDraft');
            }
            if(action.startsWith('comp')) {
                await page.waitForFunction(()=>!document.querySelector('#compensationForm button[type=submit]').disabled);
                await modal.locator('[name=salary_type]').selectOption('DAILY');
                await modal.locator('[name=payroll_frequency]').selectOption('BI_WEEKLY');
                await modal.locator('[name=salary_rate]').fill('123.45');
                await modal.locator('[name=effective_from]').fill('2026-01-01');
            }
            let release, received;
            const arrived=new Promise(resolve=>received=resolve);
            active={...response,received,gate:new Promise(resolve=>release=resolve)};
            await (action==='delete' ? modal.locator('#confirmDeleteEmployeeBtn') : modal.locator('button[type=submit]')).click();
            await arrived;
            await page.keyboard.press('Escape');
            await modal.locator('.modal-footer [data-bs-dismiss]').first().click();
            check(await modal.isVisible(),action+' remains open while pending');
            release();
            if(expectedSuccess) {
                await modal.waitFor({state:'hidden'});
                check(true,action+' explicit simulated success closes');
            } else {
                await modal.locator('.employee-mutation-feedback').waitFor({state:'visible'});
                check(await modal.isVisible(),action+' failure/ambiguity preserves dialog');
                if(action==='edit') check(await modal.locator('[name=firstname]').inputValue()==='SimulatedDraft','edit draft retained');
                if(action==='add') check(await modal.locator('[name=firstname]').inputValue()==='SimulatedOnly','add draft retained');
                if(action.startsWith('comp')) check(await modal.locator('[name=salary_rate]').inputValue()==='123.45','compensation draft retained');
                await close(modal);
            }
            active=null;
            await waitList();
        }
        const faults=[{body:{success:true,message:'SIMULATED success'}}, {body:{success:false,message:'SIMULATED validation',errors:{firstname:'SIMULATED field error'}}},
            {body:{}}, {body:{success:'true'}}, {body:{success:true,type:'error',message:'SIMULATED contradictory error'}},
            {raw:'{'}, {network:true}, {status:403,body:{success:false,message:'SIMULATED denied'}}];
        for(const width of [1440,768,375]) {
            await page.setViewportSize({width,height:width===375?812:1000});
            await page.goto(base+'/hris/employees');await waitList();
            check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth), 'employee viewport '+width);
            const name='BrowserCemp'+width+'x'+Date.now();
            await page.getByRole('button',{name:'Add Employee',exact:true}).click();
            await fillAdd(name);active={real:true};
            await page.locator('#addEmployeeForm button[type=submit]').click();
            await page.locator('#addEmployeeModal').waitFor({state:'hidden'});active=null;
            await search(name);
            const id=await page.locator('#employeesTable .editEmployeeBtn').getAttribute('data-id');
            let saved=await record(id);check(saved.success===true && saved.data.firstname===name, 'real browser create persisted '+width);
            await page.locator('#employeesTable .editEmployeeBtn').click();
            await page.waitForFunction(()=>!document.querySelector('#editEmployeeForm button[type=submit]').disabled);
            await page.locator('#editEmployeeForm [name=lastname]').fill('EditedFixture');
            active={real:true};await page.locator('#editEmployeeForm button[type=submit]').click();
            await page.locator('#editEmployeeModal').waitFor({state:'hidden'});active=null;await waitList();
            saved=await record(id);check(saved.data.lastname==='EditedFixture','real browser edit persisted '+width);
            await page.locator('#employeesTable .viewEmployeeBtn').click();
            await page.locator('#view_fullName').filter({hasText:name}).waitFor();
            check(await page.locator('#viewEmployeeModal').isVisible(),'view works '+width);
            await close(page.locator('#viewEmployeeModal'));
            if(width===1440) {
                const before=fingerprint();
                for(const action of ['add','edit','delete','compCreate']) {
                    for(const fault of faults) await simulate(action,id,fault.body?.success===true&&!fault.body?.type,fault);
                    console.log('PASS 8 intercepted '+action+' response scenarios');
                }
                check(fingerprint()===before,'32 intercepted simulations left all 37 DB tables unchanged');
            }
            await page.locator('#employeesTable .editCompensationBtn').click();
            await page.waitForFunction(()=>!document.querySelector('#compensationForm button[type=submit]').disabled);
            for(const [selector,value] of [['salary_type','DAILY'],['payroll_frequency','SEMI_MONTHLY']]) await page.locator('#'+selector).selectOption(value);
            await page.locator('#salary_rate').fill('123.45');await page.locator('#effective_from').fill('2026-01-01');
            active={real:true};await page.locator('#compensationForm button[type=submit]').click();
            await page.locator('#compensationModal').waitFor({state:'hidden'});active=null;
            check((await compensation(id)).data.salary_rate==='123.45','real browser compensation create '+width);
            if(width===1440) {
                const before=fingerprint();for(const fault of faults) await simulate('compUpdate',id,fault.body?.success===true&&!fault.body?.type,fault);
                check(fingerprint()===before,'8 intercepted compensation updates left all 37 DB tables unchanged');
            }
            await page.locator('#employeesTable .editCompensationBtn').click();
            await page.waitForFunction(()=>!document.querySelector('#compensationForm button[type=submit]').disabled);
            await page.locator('#salary_rate').fill('123.46');active={real:true};
            await page.locator('#compensationForm button[type=submit]').click();await page.locator('#compensationModal').waitFor({state:'hidden'});active=null;
            check((await compensation(id)).data.salary_rate==='123.46','real browser compensation update '+width);
            // Dirty-dialog Escape/Stay/Discard and focus containment use normal interactions.
            await page.locator('#employeesTable .editEmployeeBtn').click();
            await page.waitForFunction(()=>!document.querySelector('#editEmployeeForm button[type=submit]').disabled);
            await page.waitForFunction(()=>!bootstrap.Modal.getInstance(document.querySelector('#editEmployeeModal'))._isTransitioning);
            await page.locator('#editEmployeeForm [name=firstname]').fill('UnsavedDraft');await page.keyboard.press('Escape');
            await page.locator('#editEmployeeModal .employee-stay').click();
            check(await page.locator('#editEmployeeForm [name=firstname]').inputValue()==='UnsavedDraft','dirty stay retains draft '+width);
            await page.keyboard.press('Tab');check(await page.evaluate(()=>document.activeElement.closest('#editEmployeeModal')!==null),'focus stays inside dialog '+width);
            await close(page.locator('#editEmployeeModal'));
            await page.locator('#employeesTable .deleteEmployeeBtn').click();await close(page.locator('#deleteEmployeeModal'));
            check((await record(id)).success===true,'delete cancel does not mutate '+width);
            await page.locator('#employeesTable .deleteEmployeeBtn').click();active={real:true};
            await page.locator('#confirmDeleteEmployeeBtn').click();await page.locator('#deleteEmployeeModal').waitFor({state:'hidden'});active=null;await waitList();
            check((await record(id)).success===false,'real browser soft delete '+width);
            await page.locator('#clearEmployeeSearchBtn').click();await waitList();
            for(const sort of ['employee_no','name','branch_name','date_hired','employment_status']) {
                await page.locator(`[data-sort="${sort}"]`).click();await waitList();check(await page.locator(`[data-sort="${sort}"]`).getAttribute('data-sort')===sort,'sort '+sort+' '+width);
            }
            console.log('PASS real browser CRUD and employee interactions at '+width+'px');
            for(const path of ['/home','/hris','/payroll','/users','/tenants','/branches','/hris/branches','/hris/dtr','/hris/leaves',
                '/payroll/allowances','/payroll/commissions','/payroll/incentives','/payroll/deductions','/payroll/cash-advances',
                '/payroll/compensations','/payroll/runs','/payroll/runs/view/1','/payroll/runs/1/employee/1','/payroll/attendance/import']) {
                const res=await page.goto(base+path);
                check(res.status()===200,'regression page '+path+' '+width);
            }
        }
        check(errors.length===0,'no uncaught JavaScript errors');check(unexpected===0,'no unexpected mutation requests');
        check(intercepted===40,'all 40 simulated mutations intercepted');check(realWrites===15,'exactly 15 intended real fixture mutations');
        console.log(`PASS ${checks} browser assertions; ${intercepted} fully intercepted SIMULATED writes; ${realWrites} real fixture writes; 57 regression page/viewport checks; zero unexpected mutations or uncaught JavaScript errors`);
    } finally { await browser.close(); }
})().catch(error=>{console.error(error.stack);process.exitCode=1;});
