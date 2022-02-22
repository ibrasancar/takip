<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class UserHierarchy extends BaseConfig
{
  public $userTypes = [
    [
      'id' => 'su_admin',
      'name' => 'Super Admin',
      'class' => 'primary'
    ],
    [
      'id' => 'admin',
      'name' => 'Yönetici',
      'class' => 'danger',
    ],
    [
      'id' => 'mod',
      'name' => 'Moderatör',
      'class' => 'info',
    ],
    [
      'id' => 'technic',
      'name' => 'Teknik Servis',
      'class' => 'warning',
    ],
    [
      'id' => 'salesman',
      'name' => 'Satış Danışmanları',
      'class' => 'success',
    ],
  ];

  public function getUserTypeNameByID(string $typeID)
  {
    foreach ($this->userTypes as $key => $value) {
      if ($typeID == $value['id'])
        return $value;
    }
  }

  public function getArrayKeyByTypeID(array $data, string $typeID)
  {
    foreach ($data as $key => $value) {
      if ($typeID == $value['id'])
        return $value['level'];
    }
  }

  public function getAdmins(bool $forEvent = false)
  {
    if ($forEvent == true)
      return [
        $this->userTypes[1]['id'],
        $this->userTypes[2]['id']
      ];

    return [
      $this->userTypes[1],
      $this->userTypes[2]
    ];
  }

  // TODO: fetch lower user levels by current user level
  public function getUserType(int $user_id)
  {
    # code...
  }
}
