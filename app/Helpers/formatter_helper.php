<?php

use App\Models\UsersModel;
use App\Models\CustomersModel;
use CodeIgniter\I18n\Time;

if (!function_exists('admin_type')) {
	function admin_type(string $value, array $row): string
	{
		$userTypes = config('UserHierarchy');
		return '<span class="badge badge-' . $userTypes->getUserTypeNameByID($value)['class'] . '">' . $userTypes->getUserTypeNameByID($value)['name'] . '</span>'; 
	}
}

if (!function_exists('customer_fullname')) {
	function customer_fullname(string $value, array $row): string
	{
		$user_model = new CustomersModel();
		$full_name = $user_model->where('id', $row['id'])->first()['surname'];
		return $value . ' ' . $full_name;
	}
}

if (!function_exists('get_image')) {
	function get_image(string $value, array $row): string
	{
		return '<a data-lightbox="product-gallery" href="' . $value . '"><img src="' . $value . '" class="img-thumbnail w-xs"></a>';
	}
}

if (!function_exists('get_producer')) {
	function get_producer(string $value, array $row): string
	{
		$user_model = new UsersModel();
		$name = $user_model->select('full_name')->where('id', $value)->first($value);
		return $name['full_name'];
	}
}

if (!function_exists('date_humanize')) {
	function date_humanize($value, array $row): string
	{
		return $value != null && $value != '' ? Time::parse($value)->humanize() : '<span class="badge badge-warning">Tamamlanmamış</span>';
	}
}

if (!function_exists('price_formatter')) {
	function price_formatter($value, array $row): string
	{
		helper(['number']);
		return isset($value) && $value != null ? number_to_currency($value, 'TRY', 'tr', '2') : '';
	}
}

if (!function_exists('customer_formatter')) {
	function customer_formatter($value, array $row): string
	{
		if(isset($value) && $value != null && $value != 0) {
			$customersModel = new CustomersModel();
			$customer = $customersModel->where('id', $value)->first();
			return $customer['name'];
		}
		return '';
	}
}

if (!function_exists('action_links')) {
	function action_links(string $value, array $row, array $route = null): string
	{
		return '<div class="btn-group"><a href="' . site_url(route_to($route['edit'], $value)) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
		<a href="' . site_url(route_to($route['delete'], $value)) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
	}
}
