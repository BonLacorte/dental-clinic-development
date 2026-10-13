# MVCLite Framework Guide

## Routing System

Routes are registered using the App class:

$app->get('/path', 'Controller@method');
$app->post('/path', 'Controller@method');

---

## Route Parameters (Optional)

Supports dynamic routes:

/users/{id}

Usage in controller:
$id = $params['id'];

---

## Middleware

Middleware can be attached globally or per route:

$app->group(['AuthMiddleware'], function($app) {
    $app->get('/home', 'HomeController@index');
});

---

## Services

Services are used in the controller:
public function viewPage()
    {
        $userid = $_SESSION['user']['userid'];
        $branchId = $_GET['branch_id'];

        $check = ScopeService::authorizeBranchAccess($userid, $branchId);

        if ($result['allowed'] === false) {
            $message = $result['message'] ?? 'Access denied';
            header('Content-Type: application/json');
            echo json_encode([
                'error' => $message,
                'type' => 'info'
            ]);
            exit;
        }

        .....
    }

---

## HTTP Methods

Supported:
- GET
- POST
- PATCH
- DELETE

---

## AJAX Usage

All endpoints are AJAX-friendly:

$.ajax({
    url: '/createUser',
    type: 'POST',
    data: {...}
});