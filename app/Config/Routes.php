<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardController::index');

//testing
$routes->get('test', 'TestController::testModels');

//category
$routes->group('categories', function($routes) {
    $routes->get('/', 'CategoryController::index');
    $routes->get('create', 'CategoryController::create');
    $routes->post('store', 'CategoryController::store');
    $routes->get('edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('update/(:num)', 'CategoryController::update/$1');
    $routes->delete('delete/(:num)', 'CategoryController::delete/$1');
});

//product
$routes->group('products', function($routes) {
    $routes->get('/', 'ProductController::index');
    $routes->get('create', 'ProductController::create');
    $routes->get('show/(:num)', 'ProductController::show/$1');
    $routes->post('store', 'ProductController::store');
    $routes->get('edit/(:num)', 'ProductController::edit/$1');
    $routes->post('update/(:num)', 'ProductController::update/$1');
    $routes->delete('delete/(:num)', 'ProductController::delete/$1');
    $routes->post('generate-sku', 'ProductController::generateSKU');
});

//Stock Management
$routes->group('stock', function($routes) {
    $routes->get('in', 'StockController::stockIn');
    $routes->post('in/store', 'StockController::storeStockIn');
    $routes->get('out', 'StockController::stockOut');
    $routes->post('out/store', 'StockController::storeStockOut');
    $routes->get('history', 'StockController::history');
    $routes->get('history/product/(:num)', 'StockController::historyByProduct/$1');
});

//Reports
$routes->group('reports', function($routes) {
    $routes->get('stock', 'ReportController::stock');
    $routes->get('movements', 'ReportController::movements');
    $routes->get('export/stock', 'ReportController::exportStock');
    $routes->get('export/movements', 'ReportController::exportMovements');
});


//Api routes untuk ajax
$routes->group('api', function($routes) {
    $routes->get('products/search', 'Api\ProductController::search');
    $routes->get('categories/active', 'Api\CategoryController::getActive');
});

// Products API routes
$routes->group('api/products', function($routes) {
    $routes->get('search', 'Api\ProductController::search');
    $routes->get('stock-status/(:num)', 'Api\ProductController::getStockStatus/$1');
    $routes->get('by-category/(:num)', 'Api\ProductController::getByCategory/$1');
});

// Products export routes
$routes->group('products', function($routes) {
    // Existing CRUD routes...
    $routes->get('export/excel', 'ProductController::exportExcel');
    $routes->get('export/pdf', 'ProductController::exportPDF');
    $routes->get('export/(:num)', 'ProductController::exportSingle/$1');
});

// Dashboard API for real-time updates
$routes->get('api/dashboard/stats', 'Api\DashboardController::getStats');
