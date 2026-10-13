<?php
// AJAX Locations
$app->get('/getAllLocations', 'LocationController@getAllLocations');
$app->get('/getLocationCodes', 'LocationController@getLocationCodes');
$app->get('/getLocationIDs', 'LocationController@getLocationIDs');
$app->get('/getRegions', 'LocationController@getRegions');
$app->get('/getProvinces', 'LocationController@getProvinces');
$app->get('/getMunicipalities', 'LocationController@getMunicipalities');
$app->get('/getBarangays', 'LocationController@getBarangays');
?>