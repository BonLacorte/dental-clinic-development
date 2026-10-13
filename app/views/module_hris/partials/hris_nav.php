<div>
    <ul class="nav nav-underline">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="/hris">Home</a>
        </li>
        <li class="nav-item">
            <!-- Add employee, assign branch, shifts, position and other employee info here..  -->
            <a class="nav-link" href="/hris/employees">Employees</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Organization</a>
            <ul class="dropdown-menu">
                <!-- create/update branches -->
                <li><a class="dropdown-item" href="/hris/branches">Branches</a></li>

                <!-- create/update offices (HRD, Property, etc.) -->
                <li><a class="dropdown-item" href="/hris/offices">Offices</a></li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <!-- create/update positions (manager, staff, dentist, etc.) -->
                <li><a class="dropdown-item" href="/hris/positions">Positions</a></li>

                <!-- create/update appointments (permanent, contractual, JO) -->
                <li><a class="dropdown-item" href="/hris/appointments">Appointments</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Attendance</a>
            <ul class="dropdown-menu">

                <!-- view/print employee attendance -->
                <li><a class="dropdown-item" href="/hris/dtr">DTR</a></li>

                <!-- create/update/approve/assign leaves -->
                <li><a class="dropdown-item" href="/hris/leaves">Leaves</a></li>

                <!-- create/update/delete holidays (will reflect in the DTR module)-->
                <li><a class="dropdown-item" href="/hris/holidays">Holidays</a></li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <!-- create/update/delete shifts (assign employee shifts in the employee page) -->
                <li><a class="dropdown-item" href="/hris/shifts">Shifts</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link disabled" href="/hris/reports" tabindex="-1" aria-disabled="true">Reports</a>
        </li>
    </ul>
</div>