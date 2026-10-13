## custom panel for forms (alternative for bootstrap modal)

## css

<style>
.example-wrapper {
  min-height: 500px;   /* or 100vh if you want full feel */
}

.example-panel {
  position: absolute;
  inset: 0;
  z-index: 999;

  opacity: 0;
  pointer-events: none;

  background: rgba(255,255,255,0.0);
  transition: opacity 0.2s ease, background 0.2s ease;

  display: flex;
}

.example-panel-card {
  width: 100%;
  height: 100%;   /* now works because parent has height */

  display: flex;
  flex-direction: column;

  transform: scale(0.98);
  transition: transform 0.2s ease;
}

.example-panel-card .card-body {
  flex: 1;
  overflow-y: auto;
}

/* ACTIVE */
.example-panel.active {
  opacity: 1;
  pointer-events: auto;
  background: rgba(255,255,255,0.95);
}

.example-panel.active .example-panel-card {
  transform: scale(1);
}
</style>

## html

<button class="btn btn-success" onclick="openexamplePanel()">
    Open Panel
</button>
<div class="card-body position-relative example-wrapper">
  <div id="examplePanel" class="example-panel">
    <div class="card shadow-sm example-panel-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Panel Name</h5>
        <button type="button" class="btn-close" onclick="closeexamplePanel()"></button>
      </div>
      <div class="card-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input class="form-control">
          </div>
        </form>
      </div>
      <div class="card-footer text-end">
        <button class="btn btn-secondary" onclick="closeexamplePanel()">Close</button>
      </div>
    </div>
  </div>
</div>

## javascript

<script>
    function openexamplePanel() {
        document.getElementById('examplePanel').classList.add('active');
    }
    function closeexamplePanel() {
        document.getElementById('examplePanel').classList.remove('active');
    }
<script>
