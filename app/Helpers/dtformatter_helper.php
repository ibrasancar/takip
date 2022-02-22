<?php

use CodeIgniter\I18n\Time;

// dt beautifier
function dtBeautifyPrice($data, int $float_number = 2)
{
  helper('number');
  return $data != null && $data != '' ? number_to_currency($data, 'TRY', 'tr', $float_number) : '';
}

function dtBeautifyDate($data, string $message = '')
{
  return $data != null && $data != '' ? "<span class='badge badge-style-bordered badge-secondary'>" . Time::parse($data)->toLocalizedString('d/MM/YYYY HH:mm') . "</span>" : $message;
}

function dtGetImage($data)
{
  return '<a data-lightbox="product-gallery" href="' . $data . '"><img src="' . $data . '" class="img-thumbnail w-xs"></a>';
}


function dtAdminType($data)
{
  $userTypes = config('UserHierarchy');
  return '<span class="badge badge-' . $userTypes->getUserTypeNameByID($data)['class'] . '">' . $userTypes->getUserTypeNameByID($data)['name'] . '</span>';
}
