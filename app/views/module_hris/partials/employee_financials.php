<?php
require_once __DIR__ . '/../../../services/EmployeeFinancials.php';
$financialDefinitions = EmployeeFinancials::clientDefinitions();
$canManageFinancials = $canViewFinancials && $canEdit;
?>
<section class="profile-financials" aria-labelledby="financialsHeading">
    <div class="profile-content-heading">
        <h2 id="financialsHeading">Financials</h2>
        <p>View and manage payroll items for <?= e($fullName) ?>.</p>
    </div>
    <?php if (!$canManageFinancials): ?><div class="alert alert-info">You can view these records. Managing financial records requires employee edit permission.</div><?php endif; ?>
    <div class="card profile-section">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-3">
            <div><label class="form-label" for="financialCategory">Payroll item</label><select id="financialCategory" class="form-select">
                <?php foreach ($financialDefinitions as $key => $definition): ?><option value="<?= e($key) ?>"><?= e($definition['title']) ?></option><?php endforeach; ?>
            </select></div>
            <?php if ($canManageFinancials): ?><button id="financialAdd" type="button" class="btn btn-primary" disabled>Add Compensation</button><?php endif; ?>
        </div>
        <p id="financialListFeedback" role="status" aria-live="polite" class="mb-3">Loading financial records…</p>
        <button id="financialRetry" type="button" class="btn btn-outline-primary mb-3" hidden>Retry loading</button>
        <div class="table-responsive" role="region" aria-label="Employee financial records, scroll horizontally for more columns" tabindex="0">
            <table class="table table-hover align-middle" id="financialRecords">
                <caption id="financialCaption" class="visually-hidden">Employee payroll items</caption>
                <thead></thead><tbody></tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mt-3">
            <p id="financialPageInfo" role="status" class="mb-0"></p>
            <nav aria-label="Financial records pages"><div class="d-flex gap-2"><button type="button" class="btn btn-outline-secondary" id="financialPrevious" disabled>Previous</button><button type="button" class="btn btn-outline-secondary" id="financialNext" disabled>Next</button></div></nav>
        </div>
    </div>
</section>
<?php if ($canManageFinancials): ?>
<div class="modal fade" id="financialRecordModal" tabindex="-1" aria-labelledby="financialRecordTitle" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down"><div class="modal-content">
        <form id="financialRecordForm">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
            <input type="hidden" name="employee_id" value="<?= (int) $employee['id'] ?>">
            <div class="modal-header"><h2 class="modal-title h5" id="financialRecordTitle">Payroll item</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button></div>
            <div class="modal-body">
                <p>Employee: <strong><?= e($fullName) ?></strong> <span class="text-muted">(<?= e($employee['employee_no']) ?>)</span></p>
                <p id="financialRecordFeedback" role="status" aria-live="polite"></p>
                <p class="profile-caption">Fields marked * are required.</p>
                <fieldset id="financialFields" class="row g-3" disabled><legend class="visually-hidden">Payroll item details</legend></fieldset>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="financialSave" disabled>Save</button></div>
        </form>
    </div></div>
</div>
<div class="modal fade" id="financialDeleteModal" tabindex="-1" aria-labelledby="financialDeleteTitle" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h2 class="modal-title h5" id="financialDeleteTitle">Delete payroll item</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button></div>
        <div class="modal-body"><p>Employee: <strong><?= e($fullName) ?></strong> (<?= e($employee['employee_no']) ?>)</p><p id="financialDeleteContext"></p><p id="financialDeleteFeedback" role="status" aria-live="polite"></p></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="financialDeleteCancel">Cancel</button><button type="button" class="btn btn-danger" id="financialDeleteConfirm">Delete</button></div>
    </div></div>
</div>
<?php endif; ?>
