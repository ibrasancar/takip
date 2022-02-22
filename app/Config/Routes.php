<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// $routes->get('/', 'Auth::login', ['as' => 'login', 'filter' => 'noAuth']);


$routes->group('panel', ['namespace' => 'App\Controllers', 'filter' => 'authGuard'], function ($routes) {
    $routes->add('/', 'Panel::index', ['as' => 'homepage']);

    $routes->group('products', function ($routes) {
        $routes->get('/', 'Products::index', ['as' => 'products']);
        $routes->match(['get', 'post'], 'add', 'Products::add', ['as' => 'edg', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'Products::edit/$1', ['as' => 'edit_product', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'delete/(:num)', 'Products::delete/$1', ['as' => 'delete_product', 'filter' => 'userTypeFilter:salesman,technic']);

        // get all products
        $routes->get('getproducts', 'Products::getProducts', ['as' => 'products_get']);
        $routes->get('getproductsselect', 'Products::getProductsForSelect', ['as' => 'get_products_select']);

        // product categories
        $routes->get('category', 'ProductCategories::index', ['as' => 'product_categories', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->get('getcategories', 'ProductCategories::getCategories', ['as' => 'get_p_categories', 'filter' => 'userTypeFilter:salesman,technic']);

        $routes->get('getcategoriesselect', 'ProductCategories::getCategoriesForSelect', ['as' => 'get_product_categories', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->post('addcategory', 'ProductCategories::addCategory', ['as' => 'add_product_category', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->post('deletecategory', 'ProductCategories::deleteCategory', ['as' => 'delete_category', 'filter' => 'userTypeFilter:salesman,technic']);
    });

    $routes->group('manufacturers', function ($routes) {
        $routes->get('/', 'Manufacturers::index', ['as' => 'manufacturers', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'add', 'Manufacturers::add', ['as' => 'add_manufacturer', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'Manufacturers::edit/$1', ['as' => 'edit_manufacturer', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'delete/(:num)', 'Manufacturers::delete/$1', ['as' => 'delete_manufacturer', 'filter' => 'userTypeFilter:salesman,technic']);

        // get all manufacturers 
        $routes->get('getmanufacturers', 'Manufacturers::getManufacturers', ['as' => 'get_manufacturers']);
        $routes->get('getmanufselect', 'Manufacturers::getManufacturersForSelect', ['as' => 'get_manuf_select']);
        $routes->post('addmanufacturer', 'Manufacturers::addManufacturer', ['as' => 'add_manufacturer_ajax', 'filter' => 'userTypeFilter:salesman,technic']);
    });

    $routes->group('customers', function ($routes) {
        $routes->get('/', 'Customers::index', ['as' => 'customers']);
        $routes->match(['get', 'post'], 'add', 'Customers::add', ['as' => 'add_customer', 'filter' => 'userTypeFilter:technic']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'Customers::edit/$1', ['as' => 'edit_customer', 'filter' => 'userTypeFilter:technic']);
        $routes->match(['get', 'post'], 'delete/(:num)', 'Customers::delete/$1', ['as' => 'delete_customer', 'filter' => 'userTypeFilter:technic,salesman']);

        // get all customers
        $routes->get('getcustomers', 'Customers::getCustomers', ['as' => 'get_customers']);
        $routes->get('getcustomerselect', 'Customers::getCustomersForSelect', ['as' => 'get_customers_select']);
        $routes->post('addcustomer', 'Customers::addCustomer', ['as' => 'add_customer_ajax', 'filter' => 'userTypeFilter:technic']);
    });

    $routes->group('users', function ($routes) {
        $routes->get('/', 'Users::index', ['as' => 'users', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'add', 'Users::add', ['as' => 'add_user', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'Users::edit/$1', ['as' => 'edit_user', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'delete/(:num)', 'Users::delete/$1', ['as' => 'delete_user', 'filter' => 'userTypeFilter:salesman,technic']);

        // get all users
        $routes->get('getusers', 'Users::getUsers', ['as' => 'get_users', 'filter' => 'userTypeFilter:salesman,technic']);
    });

    $routes->group('salesmans', function ($routes) {
        $routes->get('/', 'Salesmans::index', ['as' => 'salesmans', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'add', 'Salesmans::add', ['as' => 'add_salesman', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'Salesmans::edit/$1', ['as' => 'edit_salesman', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'delete/(:num)', 'Salesmans::delete/$1', ['as' => 'delete_salesman', 'filter' => 'userTypeFilter:salesman,technic']);

        // get all salesmans
        $routes->get('getsalesmans', 'Salesmans::getSalesmans', ['as' => 'get_salesmans', 'filter' => 'userTypeFilter:salesman,technic']);
    });

    $routes->group('technics', function ($routes) {
        $routes->get('/', 'Technics::index', ['as' => 'technics', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'add', 'Technics::add', ['as' => 'add_technic', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'Technics::edit/$1', ['as' => 'edit_technic', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->match(['get', 'post'], 'delete/(:num)', 'Technics::delete/$1', ['as' => 'delete_technic', 'filter' => 'userTypeFilter:salesman,technic']);

        // get all technics
        $routes->get('gettechnics', 'Technics::getTechnics', ['as' => 'get_technics', 'filter' => 'userTypeFilter:salesman,technic']);
        $routes->get('gettechnicsforselect', 'Technics::getTechnicsForSelect', ['as' => 'get_technic_for_select']);
    });

    $routes->group('tickets', function ($routes) {
        $routes->get('/', 'Tickets::index', ['as' => 'tickets']);
        $routes->match(['get', 'post'], 'add', 'Tickets::add', ['as' => 'add_ticket']);
        $routes->match(['get', 'post'], 'edit/(:num)', 'Tickets::edit/$1', ['as' => 'edit_ticket']);
        $routes->match(['get', 'post'], 'delete/(:any)', 'Tickets::delete/$1', ['as' => 'delete_ticket', 'filter' => 'userTypeFilter:salesman,technic']);

        // get all tickets
        $routes->get('gettickets', 'Tickets::getTickets', ['as' => 'get_tickets']);
        $routes->get('get_ticket', 'Tickets::getTicket', ['as' => 'get_ticket']);
        $routes->post('add_update_to_ticket', 'Tickets::addUpdateToTicket', ['as' => 'add_update_to_ticket']);
        $routes->get('delete_ticket_update', 'Tickets::deleteTicketUpdate', ['as' => 'delete_ticket_update']);
    });

    $routes->group('orders', function ($routes) {
        $routes->add('/', 'Orders::index', ['as' => 'orders', 'filter' => 'userTypeFilter:technic']);
        $routes->add('add', 'Orders::add', ['as' => 'add_order', 'filter' => 'userTypeFilter:technic']);
        $routes->add('edit/(:num)', 'Orders::edit/$1', ['as' => 'edit_order', 'filter' => 'userTypeFilter:technic']);
        $routes->add('delete/(:num)', 'Orders::delete/$1', ['as' => 'delete_order', 'filter' => 'userTypeFilter:salesman,technic']);

        $routes->add('getorder', 'Orders::getOrder', ['as' => 'get_order', 'filter' => 'userTypeFilter:technic']);
        $routes->get('getorderselect', 'Orders::getOrdersForSelect', ['as' => 'get_orders_select']);

        // order ajaxs
        $routes->add('updateorder', 'Orders::updateOrder', ['as' => 'update_order', 'filter' => 'userTypeFilter:technic']);

        // order product ajaxs
        $routes->add('add-order-product', 'Orders::addOrderProduct', ['as' => 'add_order_product', 'filter' => 'userTypeFilter:technic']);
        $routes->add('get-order-product', 'Orders::getOrderProduct', ['as' => 'get_order_product', 'filter' => 'userTypeFilter:technic']);
        $routes->add('delete-order-product', 'Orders::deleteOrderProduct', ['as' => 'delete_order_product', 'filter' => 'userTypeFilter:technic']);

        // get all orders
        $routes->add('getorders', 'Orders::getOrders', ['as' => 'get_orders', 'filter' => 'userTypeFilter:technic']);
    });

    $routes->group('account', function ($routes) {
        $routes->add('/', 'Account::index', ['as' => 'account']);
    });
});

$routes->add('login', 'Auth::login', ['as' => 'login', 'filter' => 'noAuth']);
$routes->get('logout', 'Auth::logout', ['as' => 'logout', 'filter' => 'authGuard']);

$routes->get('order/(:alphanum)', 'Orders::show/$1', ['as' => 'show_order']);
$routes->post('order/confirm', 'Orders::confirmCustomer', ['as' => 'confirm_order']);

$routes->get('order/(:num)/(:alphanum)', 'Manufacturers::showOrder/$1/$2', ['as' => 'show_manufacturer_order']);
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
