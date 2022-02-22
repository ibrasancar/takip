<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class ProductsModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'products';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['name', 'category', 'price', 'image', 'manufacturer_id', 'description', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [
        'name'  => [
            'required' => 'Lütfen ürün adı giriniz.',
            'min_length' => 'En az 3 karakter giriniz.',
        ],
        'price' => [
            'required' => 'Lütfen fiyat değeri giriniz.',
            'decimal' => 'Lütfen geçerli bir fiyat değeri giriniz.'
        ],
        'image' => [
            'max_size' => 'Dosya boyutu izin verilenden büyük.',
            'is_image' => 'Lütfen geçerli bir görsel seçin.',
            'mime_in'  => 'Lütfen geçerli bir görsel seçin.',
        ]

    ];
    protected $skipValidation       = false;

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

    public function fixPrice($data)
    {
        if (isset($data['price']) && $data['price'] != null && $data['price'] != '')
            $data['price'] = str_replace('.', ',', $data['price']);
        return $data;
    }

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

    public function calcTotalProduct()
    {
        $thisMonth = new Time('-1 month');
        $data['total_product'] = $this->select('COUNT(id) as value')->first();
        $data['increment'] = $this
            ->select('COUNT(id) as value')
            ->where('created_at >', $thisMonth)
            ->first();

        return $data;
    }
}
