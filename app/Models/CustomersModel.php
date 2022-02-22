<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class CustomersModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'customers';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['name', 'birthday', 'email', 'phone', 'address', 'created_at', 'salesman_id'];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ['clearEmptyString'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = ['clearEmptyString'];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function clearEmptyString(array $data)
    {
        if (isset($data['data'])) {
            $data['data'] = array_map(function ($item) {
                return $item != '' ? $item : null;
            }, $data['data']);
        } else {
            $data = array_map(function ($item) {
                return $item != '' ? $item : null;
            }, $data);
        }
        return $data;
    }

    public function calcActiveCustomer()
    {

        $lastMonth = new Time('-2 months');
        $thisMonth = new Time('-1 month');

        $data['activeCustomer']['allTime'] = $this->countAllResults();
        $data['activeCustomer']['twoMonths'] = $this->where(['created_at >' => $lastMonth])->countAllResults();
        $data['activeCustomer']['thisMonth'] = $this->where(['created_at >' => $thisMonth])->countAllResults();

        // calculate percentage
        $data['activeCustomer']['lastMonth'] = $data['activeCustomer']['twoMonths'] - $data['activeCustomer']['thisMonth'];
        $increment = $data['activeCustomer']['thisMonth'] - $data['activeCustomer']['lastMonth'];
        $data['activeCustomer']['sign'] = $increment >= 0 ? true : false;
        $data['activeCustomer']['percentage'] = $data['activeCustomer']['thisMonth'] != '0' ? number_format((($increment) / $data['activeCustomer']['thisMonth']) * 100, 2) : '&#8734; ';

        return $data['activeCustomer'];
    }

    public function getBirthdays()
    {
        $customers = $this->select('id, name, birthday, email, phone')->findAll();

        $time = new Time('now');
        $today = $time->format('m-d');
        $m = [];
        foreach ($customers as $k => $v) {
            $currentTime = substr($v['birthday'], -5);
            if ($currentTime === $today)
                $m[] = $v;
        }
        return $m;
    }
}
