<?php

$routes = array(
	'newsletter' => '_other/newsletter',
	'newsletter/thank-you' => '_other/newsletter',
  
	'data/([^/]+)/([^/]+)' => 'data/index',

	'recaptcha' => '_other/recaptcha',
);

$bookingsRoutes = array(

	'reports/classes' => 'bookings/reports/classes',
	'reports/instructors' => 'bookings/reports/instructors',
	'reports/sales' => 'bookings/reports/sales',
	'reports/sales/(?P<year>\d{4})' => 'bookings/reports/sales',
	'reports/sales/(?P<year>\d{4})/(?P<month>(?:0?[1-9]|1[012]))' => 'bookings/reports/sales',
	'reports/fetch' => 'bookings/reports/fetch.json',
	'reports/fetch/([^/]+)/([^/]+)/([^/]+)' => 'bookings/reports/fetch.json',
	'modal/waitinglist-email/([^/]+)' => 'bookings/modals/waitinglist-email',

	'products/masterclass/add' => 'bookings/products/add',
	'products/masterclass/edit/([^/]+)' => 'bookings/products/edit',
	'products/masterclass/remove/([^/]+)' => 'bookings/products/remove',
	'products/masterclass/([^/]+)' => 'bookings/products/masterclass',
	'products/masterclass/([^/]+)/([^/]+)' => 'bookings/products/masterclass',


	'products/class/add' => 'bookings/products/add',
	'products/class/edit/([^/]+)' => 'bookings/products/edit',
	'products/class/remove/([^/]+)' => 'bookings/products/remove',
	'products/class/([^/]+)' => 'bookings/products/class',
	'products/class/([^/]+)/([^/]+)' => 'bookings/products/class',

	'orders/ip-address' => 'bookings/orders/ip-address',
	
);

if (strpos(CRAFT_ENVIRONMENT, 'bookings') !== false) {
    $routes = array_merge($routes, $bookingsRoutes);
}

return $routes;