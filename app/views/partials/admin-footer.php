        </div> <!-- end content -->

        <footer class="admin-footer">
            &copy; <?= e(date('Y')) ?> Montero Dental
        </footer>
    </div> <!-- end content-wrapper -->
</div> <!-- end wrapper -->

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js?v=<?= filemtime(BASE_PATH . '/public/assets/js/main.js') ?>"></script>

<!-- Optional JS Scripts Here (DataTables etc.) -->
    <!-- Optional DataTables -->
<?php if ($useDataTables ?? false): ?>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
<?php endif; ?>



<!-- JS/Ajax scripts after this -->
