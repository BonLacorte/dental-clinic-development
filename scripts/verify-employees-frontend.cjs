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
        filter() { return chain; }, toArray() { return []; }, is() { return false; }, trigger() { return chain; },
        removeAttr() { return chain; }, data() { return undefined; }};
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
const fixture = {id: 71, employee_no: '000071', firstname: 'Ana María', middlename: '', lastname: '例 <img src=x onerror=alert(1)>', branch_name: 'Fictional clinic & team', date_hired: '2026-10-01', employment_status: 'Probationary', status: 'INTERNAL-DO-NOT-DISPLAY'};
context.fixture = fixture;
const record = evaluate('employeePresentation(fixture)');
context.record = record;
const row = evaluate('employeeTableRow(record)'), card = evaluate('employeeCard(record)');
for (const markup of [row, card]) {
    assert(markup.includes('000071') && markup.includes('Ana María') && markup.includes('例'));
    assert(markup.includes('Fictional clinic &amp; team') && markup.includes('2026-10-01') && markup.includes('Probationary'));
    assert(!markup.includes('<img') && !markup.includes('INTERNAL-DO-NOT-DISPLAY'));
    assert.equal((markup.match(/data-id="71"/g) || []).length, 4);
    assert.equal((markup.match(/\sid="/g) || []).length, 0);
    for (const action of ['View', 'Edit', 'Compensation', 'Delete']) assert(markup.includes(`>${action}</span>`));
}
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
for (const field of ['employee_no', 'name', 'branch_name', 'date_hired', 'employment_status', 'created_at']) {
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
for (const name of ['dialogValues','dialogIsDirty','dialogReadStatus','populateChoices','loadDialogLookup','loadDialogRecord','focusDialog','openEmployeeDialog','mutationFeedback','employeeMutation','submitEmployee','renderPagination']) {
    assert.equal(evaluate(`${name}.toString()`), vm.runInContext(`${name}.toString()`, original), `Protected function changed: ${name}`);
}
for (const id of ['addEmployeeForm, #editEmployeeForm', 'confirmDeleteEmployeeBtn', 'compensationForm']) {
    const start = `        $('#${id}').on(`;
    const chunk = source => source.slice(source.indexOf(start), source.indexOf('\n        });', source.indexOf(start)) + 12);
    assert.equal(chunk(view), chunk(baseline), `Mutation event changed: ${id}`);
}
assert.equal((view.match(/\.on\('click', '\.viewEmployeeBtn/g) || []).length, 1);
console.log('PASS: simulated frontend rendering/XSS/action parity, legacy permissions, shared applied state, explicit Search/Clear, all mobile sorts/directions, pagination, stale-response protection, one list request per interaction and protected mutation/dialog functions.');
