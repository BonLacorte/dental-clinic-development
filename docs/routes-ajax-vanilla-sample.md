```php
$app->get('/', 'HomeController@index', [ 'AuthMiddleware' ]);
$app->get('/logout', 'AuthController@logout');
$app->get('/about', 'HomeController@about', [ 'AuthMiddleware' ]);
$app->get('/users', 'UserController@index', [ 'AuthMiddleware' ]);
$app->get('/user', 'UserController@user', [ 'AuthMiddleware' ]);


## SAMPLE Role Middleware usage

// Minimum role hierarchy (ENCODER and above)
$app->get('/encoder/dashboard', 'EncoderController@index', [
    'AuthMiddleware',
    'RoleMiddleware:requiredRole=ENCODER'
]);

// Specific roles only
$app->get('/special/report', 'ReportController@index', [
    'AuthMiddleware',
    'RoleMiddleware:allowedRoles=VALIDATOR,REGION'
]);


# AJAX Example
// from view file
const postData = {
    name: "Test",
    email: "test@example.com"
};
$.ajax({
    url: '/save-data',
    type: 'POST',
    data: postData,
    success: function(res) {
        const data = JSON.parse(res);
        alert(data.message); // show confirmation
    },
    error: function() {
        alert('Something went wrong!');
    }
});
//
$app->post('/save-data', 'DataController@saveData', ['AuthMiddleware']);

///////////////////////// REST-Style Routes and Controller Example //////////////////////////
// Route
$app->get('/farmers', 'FarmerController@index');
$app->post('/farmers', 'FarmerController@create');
$app->patch('/farmers/{id}', 'FarmerController@update');
$app->delete('/farmers/{id}', 'FarmerController@delete');

$app->get('/farmers/{id}', 'FarmerController@show');

// Routes with middleware
$app->group(['RoleMiddleware:requiredRole=REGION'], function($app) {

    $app->get('/users', 'UserController@index');

    $app->get('/getAllUsers', 'UserController@getAllUsers');
    $app->get('/getUsersDataTables', 'UserController@getUsersDataTables');

    // REST-style example
    $app->patch('/users/{id}', 'UserController@update');
});

// Controler
public function update($id)
{
    echo "Updating farmer: " . $id;
}

// AJAX for $app->patch('/users/{id}', 'UserController@update');
let id = "123";
$.ajax({
    url: '/users/'+id,
    type: 'PATCH',
    data: {
        name: 'Juan Dela Cruz'
    }
});

// or use instead
let id = "123";
$.ajax({
    url: '/farmers/' + id,
    type: 'POST',
    data: {
        _method: 'PATCH',
        name: 'Juan Dela Cruz'
    }
});
// then in php controller
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

## vanilla js (alternative to jQuery)
# GET single param
fetch('/api/users?id=1')
  .then(res => res.json())
  .then(data => console.log(data))
  .catch(err => console.error(err));

# GET multi param
const params = new URLSearchParams({
  id: 1,
  name: 'John',
  status: 'active'
});

fetch('/api/users?' + params)
  .then(res => res.json())
  .then(data => console.log(data));

# POST (JSON body)
fetch('/api/users', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    id: 1,
    name: 'John',
    status: 'active'
  })
})
.then(res => res.json())
.then(data => console.log(data));

# POST (form-style like PHP $_POST)
const formData = new URLSearchParams();
formData.append('id', 1);
formData.append('name', 'John');

fetch('/api/users', {
  method: 'POST',
  body: formData
})
.then(res => res.json())
.then(data => console.log(data));