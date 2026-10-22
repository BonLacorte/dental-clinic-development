// No packages/build step. These are SIMULATED DOM/AJAX contract tests, not browser evidence.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const cp = require('node:child_process');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const view = fs.readFileSync(path.join(root, 'app/views/module_hris/employees.php'), 'utf8');
const script = text => text.match(/<script>([\s\S]*?)<\/script>/)[1].replace(/<\?=[\s\S]*?\?>/g, '{"view":true,"edit":true,"delete":true}');
const values = new Map(), events = new Map(), requests = [];
const ready = [];
function $(selector) {
    if (typeof selector === 'function') { ready.push(selector); return; }
    const key = typeof selector === 'string' ? selector : 'element';
    const chain = {length: 0, on(event, ...args) { events.set(`${key}|${event}`, args.at(-1)); return chain; },
        has() { return chain; }, each() { return chain; }, find() { return chain; }, closest() { return chain; },
        filter() { return chain; }, toArray() { return []; }, is() { return typeof selector === 'object' && selector?.visible !== false; }, trigger() { return chain; },
        removeAttr() { return chain; }, removeClass() { return chain; }, addClass() { return chain; }, data() { return undefined; }};
    for (const method of ['text', 'html', 'val', 'prop', 'attr']) chain[method] = function (...args) {
        const property = method === 'prop' || method === 'attr' ? args[0] : method;
        const id = `${key}|${property}`;
        if (args.length === (method === 'prop' || method === 'attr' ? 1 : 0)) return values.get(id);
        values.set(id, args.at(-1)); return chain;
    };
    return chain;
}
$.ajax = config => {
    const callbacks = {};
    const req = {config, abort() { callbacks.fail?.({}, 'abort'); callbacks.always?.(); },
        done(fn) { callbacks.done = fn; return req; }, fail(fn) { callbacks.fail = fn; return req; }, always(fn) { callbacks.always = fn; return req; },
        succeed(data) { callbacks.done?.(data); callbacks.always?.(); }, reject() { callbacks.fail?.({}, 'error'); callbacks.always?.(); }};
    requests.push(req); return req;
};
const context = vm.createContext({$, console, document: {activeElement: {}, contains: () => false, getElementById: () => ({focus() {}})},
    window: {matchMedia: () => ({matches: false, addEventListener() {}})}, MonteroHris: {statusClass: () => 'hris-status--neutral'}});
vm.runInContext(script(view), context);
const evaluate = code => vm.runInContext(code, context);
const fixture = {id: 71, employee_no: '000071', firstname: 'Ana María', middlename: '', lastname: '例 <img src=x onerror=alert(1)>', position: 'Dentist', employment_type: 'Regular', branch_name: 'Fictional clinic & team', date_hired: '2026-10-01', employment_status: 'Probationary', status: 'INTERNAL-DO-NOT-DISPLAY'};
context.fixture = fixture;
const record = evaluate('employeePresentation(fixture)');
context.record = record;
const row = evaluate('employeeTableRow(record)'), card = evaluate('employeeCard(record)');
for (const markup of [row, card]) {
    assert(markup.includes('000071') && markup.includes('Ana María') && markup.includes('例'));
    assert(markup.includes('Fictional clinic &amp; team') && markup.includes('Dentist') && markup.includes('Regular') && markup.includes('Probationary'));
    assert(!markup.includes('<img') && !markup.includes('INTERNAL-DO-NOT-DISPLAY'));
    assert.equal((markup.match(/data-id="71"/g) || []).length, 2, 'Identity and contextual trigger share the record');
    assert.equal((markup.match(/\sid="/g) || []).length, 1, 'Only unique menu trigger ID per representation');
    assert(markup.includes('aria-haspopup="menu"') && markup.includes('bi-three-dots'));
}
for (const action of ['View details', 'Edit employee', 'Manage compensation', 'Remove employee']) assert(record.actions.includes(`>${action}</span>`));
assert(row.includes('employeeActions-table-0') && card.includes('employeeActions-card-0'));
assert(card.includes('2026-10-01'), 'Previously approved card hire date preserved');
assert(!row.includes('2026-10-01'), 'Hire date remains in supplementary sorting/cards, not new directory columns');
assert.equal(evaluate('employeePresentation({...fixture, position:null}).position'), 'Not assigned');
assert.equal(evaluate('employeePresentation({id:1}).position'), 'Position unavailable', 'Missing API property never masquerades as unassigned');
evaluate('employeeActions.view = employeeActions.edit = employeeActions.delete = false');
const restricted = evaluate('employeePresentation(fixture).actions');
assert(!restricted.includes('viewEmployeeBtn') && !restricted.includes('editEmployeeBtn') && !restricted.includes('deleteEmployeeBtn'));
assert(restricted.includes('editCompensationBtn'), 'Preserve legacy Compensation visibility');
evaluate('employeeActions.view = employeeActions.edit = employeeActions.delete = true');
ready[0]();
assert.equal(requests.length, 1, 'One initial list request');
assert.equal(requests[0].config.url, '/hris/employees/getallemployees');
assert.deepEqual(JSON.parse(JSON.stringify(requests[0].config.data)), {page:1, search:'', sort_by:'created_at', sort_dir:'DESC'});
requests[0].succeed({success:true,data:[fixture],currentPage:1,totalPages:3});
assert(values.get('#employeeCards|html').includes('000071'));
assert(values.get('#employeesTable tbody|html').includes('000071'));
assert.equal(values.get('#employeePageSummary|text'), 'Page 1 of 3');
assert.equal(values.get('#employeeMobileSortBy|val'), 'created_at');
values.set('#employeeSearchInput|val', 'fictional query');
events.get('#employeeSearchForm|submit')({preventDefault(){}});
assert.equal(requests.length, 2, 'One request per explicit Search');
assert.equal(requests[1].config.data.search, 'fictional query');
requests[1].succeed({success:true,data:[],currentPage:1,totalPages:1});
assert(values.get('#employeeCards|html').includes('No employees match'));
assert(values.get('#employeesTable tbody|html').includes('colspan="6"'));
values.set('#employeeSearchInput|val', 'unsubmitted draft');
assert.equal(evaluate('loadedList.search'), 'fictional query', 'Unsubmitted input never changes applied context');
events.get('#clearEmployeeSearchBtn|click')();
assert.equal(requests.length, 3);
assert.equal(requests[2].config.data.search, '');
requests[2].reject();
assert.equal(evaluate('loadedList.search'), 'fictional query', 'Failed request retains previous successful context');
for (const field of ['employee_no', 'name', 'branch_name', 'employment_type', 'date_hired', 'employment_status', 'created_at']) {
    for (const dir of ['ASC', 'DESC']) {
        values.set('#employeeMobileSortBy|val', field); values.set('#employeeMobileSortDir|val', dir);
        const count = requests.length;
        events.get('#employeeMobileSortForm|submit')({preventDefault(){}});
        assert.equal(requests.length, count + 1, 'One request per mobile sort submission');
        const req = requests.at(-1);
        assert.equal(req.config.data.sort_by, field); assert.equal(req.config.data.sort_dir, dir);
        req.succeed({success:true,data:[fixture],currentPage:1,totalPages:3});
        assert(values.get('#employeeSortSummary|text').includes(field === 'created_at' ? (dir === 'ASC' ? 'oldest' : 'newest') : (dir === 'ASC' ? 'ascending' : 'descending')));
    }
}
evaluate('getAllEmployees(2)');
assert.equal(requests.at(-1).config.data.page, 2);
requests.at(-1).succeed({success:true,data:[fixture],currentPage:2,totalPages:3});
assert.equal(values.get('#employeePageSummary|text'), 'Page 2 of 3');
const oldReq = evaluate('getAllEmployees(1)'), countBefore = requests.length;
evaluate('getAllEmployees(3)');
requests.at(-2).succeed({success:true,data:[],currentPage:1,totalPages:1});
assert.equal(evaluate('loadedList.page'), 2, 'Stale response cannot overwrite current successful page');
requests.at(-1).succeed({success:true,data:[fixture],currentPage:3,totalPages:3});
assert.equal(evaluate('loadedList.page'), 3);
assert.equal(requests.length, countBefore + 1);
// Optional baseline file supports restricted executors that prohibit Node child processes.
const baseline = process.argv[2] ? fs.readFileSync(process.argv[2], 'utf8') : cp.execFileSync('git', ['show', 'b5526bb4a7999bbd3c253fcdda1c8419a171314d:app/views/module_hris/employees.php'], {cwd:root,encoding:'utf8'});
const original = vm.createContext({$: () => {}, console});
vm.runInContext(script(baseline), original);
for (const name of ['dialogValues','dialogIsDirty','dialogReadStatus','populateChoices','loadDialogLookup','focusDialog','mutationFeedback','employeeMutation','submitEmployee','renderPagination']) {
    assert.equal(evaluate(`${name}.toString()`), vm.runInContext(`${name}.toString()`, original), `Protected function changed: ${name}`);
}
for (const id of ['addEmployeeForm, #editEmployeeForm', 'confirmDeleteEmployeeBtn', 'compensationForm']) {
    const start = `        $('#${id}').on(`;
    const chunk = source => source.slice(source.indexOf(start), source.indexOf('\n        });', source.indexOf(start)) + 12);
    assert.equal(chunk(view), chunk(baseline), `Mutation event changed: ${id}`);
}
assert.equal((view.match(/\.on\('click\.employeeActions'/g) || []).length, 1);
console.log('PASS: simulated frontend rendering/XSS/action parity, legacy permissions, shared applied state, explicit Search/Clear, all mobile sorts/directions, pagination, stale-response protection, one list request per interaction and protected mutation/dialog functions.');

// Programmatic Bootstrap/portal/keyboard protocol, without layout or a browser.
{
    const doc = context.document;
    const parent = label => ({label,appendChild(node){node.parentElement=this;}});
    const body = parent('body'), host = parent('host'), cell = parent('table cell');
    const classSet = new Set(['dropdown-menu']);
    const items = Array.from({length:4},(_,index) => ({index,focus(){doc.activeElement=this;}}));
    const menu = {classList:{add:c=>classSet.add(c),remove:c=>classSet.delete(c)},setAttribute(){},querySelectorAll:()=>items,contains:target=>items.includes(target)};
    const trigger = {id:'employeeActions-table-0',dataset:{id:'71'},parentElement:cell,attributes:{},setAttribute(name,value){this.attributes[name]=value;},hasAttribute:n=>n==='data-employee-menu',contains:target=>target===trigger,focus(){doc.activeElement=this;}};
    doc.body=body;doc.contains=()=>true;
    doc.getElementById=id=>id==='employeeActionMenu'?menu:id==='employeeMenuHost'?host:{focus(){}};
    let constructed=0,disposed=0;
    context.bootstrap={Dropdown:class {
        constructor(button,config){
            constructed++;assert.equal(menu.parentElement,cell,'Menu sibling during Bootstrap construction');
            const popper=config.popperConfig({modifiers:[]});
            assert.equal(popper.strategy,'fixed');assert.equal(popper.placement,'bottom-end');
            assert(popper.modifiers.some(x=>x.name==='preventOverflow'&&x.options.rootBoundary==='viewport'));
        }
        show(){assert.equal(menu.parentElement,body,'Portal escapes the scrolling table');assert(!classSet.has('dropdown-menu'),'Avoid conflicting Bootstrap delegated sibling keyboard API');classSet.add('show');}
        hide(){classSet.delete('show');}
        dispose(){disposed++;}
    }};
    context.menuTrigger=trigger;
    evaluate('openEmployeeMenu(menuTrigger)');assert.equal(doc.activeElement,items[0]);
    const key=name=>({key:name,preventDefault(){},stopPropagation(){}});
    context.menuKey=key('End');evaluate('employeeMenuKeydown(menuKey)');assert.equal(doc.activeElement,items[3]);
    context.menuKey=key('ArrowDown');evaluate('employeeMenuKeydown(menuKey)');assert.equal(doc.activeElement,items[0]);
    context.menuKey=key('ArrowUp');evaluate('employeeMenuKeydown(menuKey)');assert.equal(doc.activeElement,items[3]);
    context.menuKey=key('Home');evaluate('employeeMenuKeydown(menuKey)');assert.equal(doc.activeElement,items[0]);
    context.menuKey=key('Escape');evaluate('employeeMenuKeydown(menuKey)');assert.equal(doc.activeElement,trigger);assert.equal(menu.parentElement,host);
    evaluate('openEmployeeMenu(menuTrigger,true)');assert.equal(doc.activeElement,items[3]);
    context.menuKey=key('Tab');evaluate('employeeMenuKeydown(menuKey)');assert.equal(doc.activeElement,trigger);
    evaluate('openEmployeeMenu(menuTrigger)');
    events.get('element|pointerdown.employeeMenu focusin.employeeMenu')({target:{}});
    assert.equal(evaluate('employeeMenu'),null,'Outside interaction closes the menu');
    assert.equal(constructed,disposed,'Every popup lifecycle disposes its positioning instance');
    console.log('PASS: simulated singleton-menu/portal positioning configuration, keyboard arrows/Home/End/Escape/Tab, outside close, focus return and disposal. Real menu geometry remains unverified.');
}

// Binary transport is SIMULATED. Workbook validity is tested separately by PHP read-back.
(async () => {
    const exports = [], downloads = [], revoked = [];
    const button = {dataset:{csrf:'fictional-test-token'}};
    context.document.getElementById = id => id === 'exportEmployeesBtn' ? button : {focus(){}};
    context.document.body = {appendChild(){}};
    context.document.createElement = () => ({click(){downloads.push(this.download);},remove(){}});
    context.window.setTimeout = callback => callback();
    context.URLSearchParams = URLSearchParams;
    context.URL = {createObjectURL:() => 'blob:fictional-test',revokeObjectURL:value => revoked.push(value)};
    const binary = {ok:true,headers:{get:header => header === 'Content-Type' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'attachment; filename="Montero_Dental_Employees_2026-10-22.xlsx"'},blob:async () => new Blob([new Uint8Array([80,75,3,4])])};
    let resolveResponse;
    context.fetch = (url, config) => {exports.push({url,config});return new Promise(resolve => {resolveResponse=resolve;});};
    evaluate('loadedList = {search:"applied fictional search", sortBy:"name", sortDir:"DESC"}');
    values.set('#employeeSearchInput|val','unsubmitted different search');
    const listCount = requests.length;
    const pending = evaluate('exportEmployees()');
    await evaluate('exportEmployees()');
    assert.equal(exports.length,1,'Double click cannot duplicate export');
    assert.equal(exports[0].url,'/hris/employees/export');
    assert.equal(exports[0].config.method,'POST');
    assert.equal(exports[0].config.body.get('search'),'applied fictional search');
    assert.equal(exports[0].config.body.get('sort_by'),'name');
    assert.equal(exports[0].config.body.get('sort_dir'),'DESC');
    assert.equal(exports[0].config.body.get('csrf_token'),'fictional-test-token');
    evaluate('loadedList = {search:"later list", sortBy:"date_hired", sortDir:"ASC"}');
    resolveResponse(binary); await pending;
    assert.equal(downloads.length,1); assert.equal(revoked.length,1);
    assert.equal(evaluate('employeeExportPending'),false);
    context.fetch = async () => ({ok:false,headers:{get:() => 'application/json'},json:async () => ({message:'Narrow your search: limit exceeded.'})});
    await evaluate('exportEmployees()');
    assert.equal(values.get('#employeeExportStatus|text'),'Narrow your search: limit exceeded.');
    assert.equal(downloads.length,1,'JSON errors never download a workbook');
    context.fetch = async () => ({ok:true,headers:{get:() => 'text/html'}});
    await evaluate('exportEmployees()');
    assert.equal(downloads.length,1,'Redirect/login HTML never downloads as Excel');
    context.fetch = async () => ({...binary,blob:async () => new Blob(['employee,name,csv'])});
    await evaluate('exportEmployees()');
    assert.equal(downloads.length,1,'Mislabeled non-ZIP bytes rejected');
    assert(values.get('#employeeExportStatus|text').includes('not a valid Excel download'));
    assert.equal(requests.length,listCount,'Export never refetches the list');
    assert.equal(evaluate('employeeExportPending'),false);
    console.log('PASS: simulated Excel transport uses successful applied criteria, one POST/CSRF, duplicate-click guard, error recovery, JSON/HTML/non-ZIP rejection, download and URL cleanup.');
})().catch(error => {console.error(error);process.exitCode=1;});
