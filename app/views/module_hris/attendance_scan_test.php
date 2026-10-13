<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    :root {
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: #212529;
    }

    * {
        box-sizing: border-box;
    }

    body {
        min-height: 100vh;
        margin: 0;
        display: grid;
        place-items: center;
        padding: 20px;
        background: #f5f6f8;
    }

    .scanner {
        width: min(100%, 600px);
        padding: 32px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
    }

    .eyebrow {
        margin: 0 0 12px;
        color: #6c757d;
        font-size: .9rem;
        font-weight: 600;
    }

    #clock {
        margin: 0;
        font-size: clamp(2.5rem, 10vw, 4.5rem);
        font-weight: 600;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    #date {
        margin: 10px 0 30px;
        color: #6c757d;
        font-size: .95rem;
    }

    label {
        display: block;
        margin-bottom: 9px;
        color: #212529;
        font-size: .9rem;
        font-weight: 700;
    }

    .row {
        display: flex;
        gap: 12px;
    }

    input[type="number"] {
        width: 100%;
        min-width: 0;
        padding: 12px 14px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        outline: none;
        background: #fff;
        color: #212529;
        font: inherit;
        font-size: 1.1rem;
    }

    input[type="number"]:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, .15);
    }

    button {
        flex: 0 0 auto;
        padding: 0 24px;
        border: 0;
        border-radius: 6px;
        background: #0d6efd;
        color: #fff;
        cursor: pointer;
        font: inherit;
        font-weight: 600;
    }

    button:disabled {
        cursor: wait;
        opacity: .65;
    }

    .hint {
        margin: 11px 0 0;
        color: #6c757d;
        font-size: .82rem;
    }

    #result {
        display: none;
        margin-top: 24px;
        padding: 16px 18px;
        border-radius: 6px;
        line-height: 1.45;
    }

    #result.show {
        display: block;
    }

    #result.success {
        border: 1px solid #badbcc;
        background: #d1e7dd;
        color: #0f5132;
    }

    #result.error {
        border: 1px solid #f5c2c7;
        background: #f8d7da;
        color: #842029;
    }

    .result-title {
        font-weight: 700;
    }

    .result-meta {
        margin-top: 4px;
        font-size: .86rem;
    }

    @media (max-width: 520px) {
        .scanner {
            padding: 24px 18px;
        }

        .row {
            flex-direction: column;
        }

        button {
            min-height: 46px;
        }
    }
</style>

<body>
    <main class="scanner">
        <p class="eyebrow">Attendance test terminal</p>
        <h1 id="clock">--:--:--</h1>
        <p id="date">Loading Manila time…</p>

        <form id="attendanceForm" autocomplete="off">
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?= e($csrf_token); ?>">
            <input type="hidden" id="branchId" name="branch_id" value="<?= $branchId ?>">
            <input type="hidden" id="deviceId" name="device_id">


            <label for="employeeId">Employee ID</label>
            <div class="row">
                <input id="employeeId" name="employee_id" type="number" min="1" inputmode="numeric"
                    placeholder="Enter or scan employee ID" required autofocus>
                <button id="submitButton" type="submit">Record scan</button>
            </div>



            <p class="hint">Press Enter to submit. The server records the official time.</p>
        </form>

        <section id="result" role="status" aria-live="polite"></section>
    </main>



    <script>
        const csrfToken = document.getElementById('csrf_token');
        const deviceInput = document.getElementById('deviceId');
        const form = document.getElementById('attendanceForm');
        const submitButton = document.getElementById('submitButton');
        const resultBox = document.getElementById('result');
        const employeeInput = document.getElementById('employeeId');

        function updateClock() {
            const now = new Date();
            const timeOptions = {
                timeZone: 'Asia/Manila',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const dateOptions = {
                timeZone: 'Asia/Manila',
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            document.getElementById('clock').textContent =
                new Intl.DateTimeFormat('en-PH', timeOptions).format(now);
            document.getElementById('date').textContent =
                new Intl.DateTimeFormat('en-PH', dateOptions).format(now) + ' · Asia/Manila';
        }

        function getDeviceId() {
            const storageKey = 'attendance_device_id';
            let id = localStorage.getItem(storageKey);

            if (!id) {
                id = window.crypto?.randomUUID?.()
                    ?? 'device-' + Date.now() + '-' + Math.random().toString(16).slice(2);
                localStorage.setItem(storageKey, id);
            }

            return id;
        }

        function showResult(ok, message, data = null) {
            const action = data?.action
                ? data.action.replaceAll('_', ' ')
                : '';

            let recordedTime = '';

            if (data?.action === 'TIME_IN') {
                recordedTime = data.time_in || '';
            } else if (data?.action === 'TIME_OUT') {
                recordedTime = data.time_out || '';
            }

            resultBox.className = 'show ' + (ok ? 'success' : 'error');
            resultBox.innerHTML = '';

            const title = document.createElement('div');
            title.className = 'result-title';
            title.textContent = message;
            resultBox.appendChild(title);

            if (action || recordedTime) {
                const meta = document.createElement('div');
                meta.className = 'result-meta';
                meta.textContent = [action, recordedTime]
                    .filter(Boolean)
                    .join(' · ');

                resultBox.appendChild(meta);
            }
        }

        deviceInput.value = getDeviceId();
        updateClock();
        setInterval(updateClock, 1000);

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            submitButton.disabled = true;
            submitButton.textContent = 'Recording…';

            try {
                const formData = new FormData(form);

                const response = await fetch('/hris/attendance/scan', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const payload = await response.json().catch(() => ({}));

                showResult(
                    response.ok && payload.success,
                    payload.message || `Request failed (${response.status}).`,
                    payload.data
                );

                if (response.ok && payload.success) {
                    employeeInput.value = '';
                }
            } catch (error) {
                console.error(error);
                showResult(false, 'Cannot connect to the attendance endpoint.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Record scan';
                employeeInput.focus();
            }
        });

    </script>

</body>

</html>