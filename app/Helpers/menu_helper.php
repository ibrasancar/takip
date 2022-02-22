<?php

function get_menu(string $user_type = 'su_admin')
{
  $menu = [
    'Yönetim Paneli' => [
      'homepage' => [
        'icon' => ['name' => 'home', 'two_tone' => true],
        'name' => 'Anasayfa',
        'min_level' => 3
      ],
    ],
    'Ürünler & Siparişler' => [
      'products' => [
        'icon' => ['name' => 'inventory_2', 'two_tone' => true],
        'name' => 'Ürünler',
        'min_level' => 3,
        'sub-menu' => [
          'products' => 'Listele',
          'add_product' => 'Ekle',
          'product_categories' => 'Kategoriler',
        ],
      ],
      'orders' => [
        'icon' => ['name' => 'receipt_long', 'two_tone' => true],
        'name' => 'Siparişler',
        'min_level' => 3,
        'sub-menu' => [
          'orders' => 'Listele',
          'add_order' => 'Ekle',
        ],
      ],
      'tickets' => [
        'icon' => ['name' => 'help', 'two_tone' => true],
        'name' => 'SSH',
        'min_level' => 3,
        'sub-menu' => [
          'tickets' => 'Listele',
          'add_ticket' => 'Ekle',
        ],
      ]
    ],
    'Kullanıcılar' => [
      'users' => [
        'icon' => ['name' => 'verified_user', 'two_tone' => true],
        'name' => 'Yöneticiler',
        'min_level' => 3,
        'sub-menu' => [
          'users' => 'Listele',
          'add_user' => 'Ekle',
        ],
      ],
      'salesmans' => [
        'icon' => ['name' => 'work', 'two_tone' => true],
        'name' => 'Satış Danışmanları',
        'min_level' => 3,
        'sub-menu' => [
          'salesmans' => 'Listele',
          'add_salesman' => 'Ekle',
        ],
        'ajax' => [
          'edit_salesman' => true,
          'delete_salesman' => true,
          'get_salesmans' => true,
        ]
      ],
      'technics' => [
        'icon' => ['name' => 'local_shipping', 'two_tone' => true],
        'name' => 'Teknik Servis',
        'min_level' => 3,
        'sub-menu' => [
          'technics' => 'Listele',
          'add_technic' => 'Ekle',
        ],
      ],
    ],
    'Kişiler' => [
      'customers' => [
        'icon' => ['name' => 'local_mall', 'two_tone' => true],
        'name' => 'Müşteriler',
        'min_level' => 3,
        'sub-menu' => [
          'customers' => 'Listele',
          'add_customer' => 'Ekle',
        ],
      ],
      'manufacturers' => [
        'icon' => ['name' => 'settings', 'two_tone' => true],
        'name' => 'Üreticiler',
        'min_level' => 3,
        'sub-menu' => [
          'manufacturers' => 'Listele',
          'add_manufacturer' => 'Ekle',
        ],
      ],
    ],
    'Profilim' => [
      'account' => [
        'icon' => ['name' => 'manage_accounts', 'two_tone' => true],
        'name' => 'Hesabımı Düzenle',
        'min_level' => 3,
      ],
      'info_page' => [
        'icon' => ['name' => 'design_services', 'two_tone' => true],
        'name' => 'Extra Takip',
        'min_level' => 3,
      ],
    ],
  ];

  if ($user_type == 'salesman') {
    unset($menu['Ürünler & Siparişler']['products']['sub-menu']['add_product']);
    unset($menu['Ürünler & Siparişler']['products']['sub-menu']['product_categories']);
    unset($menu['Kişiler']['manufacturers']);
    unset($menu['Kullanıcılar']);
    unset($menu['Profilim']['info_page']);
  }

  if ($user_type == 'technic') {
    unset($menu['Ürünler & Siparişler']['products']['sub-menu']['add_product']);
    unset($menu['Ürünler & Siparişler']['products']['sub-menu']['product_categories']);
    unset($menu['Ürünler & Siparişler']['orders']);
    unset($menu['Kişiler']['manufacturers']);
    unset($menu['Kişiler']['customers']['sub-menu']['add_customer']);
    unset($menu['Kullanıcılar']);
    unset($menu['Profilim']['info_page']);
  }

  return $menu;
}

function groupArray(array $array, string $key = 'id')
{
  $group = array();

  foreach ($array as $value) {
    $group[$value[$key]][] = $value;
  }
  return $group;
}
function multiKeyExists(array $arr, $key)
{

  // is in base array?
  if (array_key_exists($key, $arr)) {
    return true;
  }

  // check arrays contained in this array
  foreach ($arr as $element) {
    if (is_array($element)) {
      if (multiKeyExists($element, $key)) {
        return true;
      }
    }
  }

  return false;
}

function beautifyUserType(string $user_type)
{
  if ($user_type == 'su_admin') {
    return 'Super Admin';
  } elseif ($user_type == 'admin') {
    return 'Yönetici';
  } elseif ($user_type == 'technic') {
    return 'Teknik Destek';
  } elseif ($user_type == 'salesman') {
    return 'Satış Danışmanı';
  } else {
    return 'Kullanıcı';
  }
}

function isAdmin(): bool
{
  return session()->get('user_type') == 'su_admin' || session()->get('user_type') == 'admin';
}
