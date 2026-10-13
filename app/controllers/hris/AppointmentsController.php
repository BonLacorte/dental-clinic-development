<?php

require_once __DIR__ . '/../../models/hris/AppointmentsModel.php';

class AppointmentsController extends Controller
{
    protected $appointmentModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentsModel();
    }

    public function index()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();
        $data = [
            'title' => 'Appointments',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => $csrf_token
        ];

        $this->view('module_hris/appointments', $data);
    }

    public function getAllAppointments()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->appointmentModel->getAllAppointments($tenant_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getAppointmentById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $appointment = $this->appointmentModel->getAppointmentById($id, $tenant_id);

        if (!$appointment) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment not found.'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $appointment
        ]);
    }

    
    //future reference 
    //system defaults can only be added/delete/updated by super admin
    //tenants can create their own appointments but cannot delete/update system defaults
    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'code' => ['type' => 'string', 'required' => true, 'max_length' => 50],
            'name' => ['type' => 'string', 'required' => true, 'max_length' => 100],
            'description' => ['type' => 'string', 'required' => false, 'max_length' => 255],
            'is_active' => ['type' => 'int', 'required' => false, 'default' => 1, 'enum' => [0, 1]],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $data = $result['data'];

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        $data['is_active'] = $data['is_active'] ?? 1;

        $newId = $this->appointmentModel->createAppointment($tenant_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Appointment created successfully.',
            'id' => $newId
        ]);
    }

    public function update(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'code' => ['type' => 'string', 'required' => true, 'max_length' => 50],
            'name' => ['type' => 'string', 'required' => true, 'max_length' => 100],
            'description' => ['type' => 'string', 'required' => false, 'max_length' => 255],
            'is_active' => ['type' => 'int', 'required' => false, 'default' => 1, 'enum' => [0, 1]],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $data = $result['data'];

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        $data['is_active'] = $data['is_active'] ?? 1;

        if (!$this->appointmentModel->existsById($id, $tenant_id)) {
            $existing = $this->appointmentModel->getAppointmentById($id, $tenant_id);
            je([
                'success' => false,
                'message' => $existing
                    ? 'System default appointment types cannot be edited.'
                    : 'Appointment not found.'
            ], 'json');
        }

        $success = $this->appointmentModel->updateAppointment($id, $tenant_id, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Appointment updated successfully.'
                : 'Failed to update appointment.'
        ]);
    }

    public function delete(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $data = $result['data'];

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        if (!$this->appointmentModel->existsById($id, $tenant_id)) {
            $existing = $this->appointmentModel->getAppointmentById($id, $tenant_id);
            je([
                'success' => false,
                'message' => $existing
                    ? 'System default appointment types cannot be deleted.'
                    : 'Appointment not found.'
            ], 'json');
        }

        $success = $this->appointmentModel->deleteAppointment($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Appointment deleted successfully.'
                : 'Failed to delete appointment.'
        ]);
    }
}