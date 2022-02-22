<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class OrdersModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'orders';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['slug', 'customer_id', 'products', 'order_note', 'total_price', 'deposit', 'discount', 'salesman_id', 'customer_confirm', 'admin_confirm', 'completed_at', 'created_at'];

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
    protected $afterDelete          = ['deleteAllMeta'];


    protected $orderStatuses = [
        'processing' => [
            'name' => 'İşleniyor',
            'list_style' => '<span class="badge badge-dark">İşleniyor</span>',
        ],
        'shipped' => [
            'name' => 'Hazırlandı',
            'list_style' => '<span class="badge badge-warning">Hazırlandı</span>',
        ],
        'ontheway' => [
            'name' => 'Yolda',
            'list_style' => '<span class="badge badge-info">Yolda</span>',
        ],
        'delivered' => [
            'name' => 'Teslim edildi',
            'list_style' => '<span class="badge badge-success">Teslim edildi</span>',
        ],
        'canceled' => [
            'name' => 'İptal Edildi',
            'list_style' => '<span class="badge badge-danger">İptal</span>',
        ]
    ];

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

    public function createSlug()
    {
        $slug = strtoupper(bin2hex(random_bytes(5)));
        if ($this->where('slug', $slug)->countAllResults() > 0)
            $this->createSlug();
        else
            return $slug;
    }

    public function fixPrice(array $data)
    {
        $data['price'] = str_replace('.', ',', $data['price']);
        return $data;
    }

    public function beautifyPrice($price, string $column_name = null)
    {
        helper(['number']);

        if ($column_name == null)
            return isset($price) && $price != null && $price != ''  ? number_to_currency($price, 'TRY', 'tr', 2) : '';

        return array_map(function ($item) use ($column_name) {
            $item[$column_name] = isset($item[$column_name]) && $item[$column_name] != null && $item[$column_name] != '' ? number_to_currency($item[$column_name], 'TRY', 'tr', 2) : '';
            return $item;
        }, $price);
    }

    public function beautifyDate($date, string $column_name = null, string $date_format = 'dd/MM/YYYY')
    {
        helper(['number']);

        if ($column_name == null)
            return isset($date) && $date != null && $date != ''  ? Time::parse($date)->toLocalizedString($date_format) : '';

        return array_map(function ($item) use ($column_name, $date_format) {
            $item[$column_name] = isset($item[$column_name]) && $item[$column_name] != null && $item[$column_name] != '' ? Time::parse($item[$column_name])->toLocalizedString($date_format) : '';
            return $item;
        }, $date);
    }

    public function deleteAllMeta($data)
    {
        $model = new OrderProductsModel();
        $model->where('order_id', $data['id'])->delete();
    }

    public function calcMonthlySales()
    {
        helper('dtformatter');

        $lastMonth = new Time('-2 months');
        $thisMonth = new Time('-1 month');

        $data['thisMonth'] = $this
            ->select('SUM(total_price) as monthly_total, COUNT(id) as total_sales')
            ->where('created_at >', $thisMonth)
            ->first();
        $data['lastMonth'] = $this
            ->select('SUM(total_price) as monthly_total, COUNT(id) as total_sales')
            ->where('created_at >', $lastMonth)
            ->where('created_at <', $thisMonth)
            ->first();


        $increment = $data['thisMonth']['monthly_total'] - $data['lastMonth']['monthly_total'];
        $data['percentage'] = $data['lastMonth']['monthly_total'] != 0 ? number_format(($increment / ($data['lastMonth']['monthly_total'])) * 100, 2) : '100';
        $data['sign'] = $increment >= 0 ? true : false;

        $data['thisMonth']['total_price'] = dtBeautifyPrice($data['thisMonth']['monthly_total'], 0);

        return $data;
    }

    public function getOrderByID(int $order_id = null)
    {
        $data = $this
            ->select('orders.id, orders.slug, orders.status, orders.total_price, orders.total_price, orders.deposit, orders.discount, orders.salesman_id, orders.created_at, customers.id as customer_id, customers.name, customers.email')
            ->where('orders.id', $order_id)
            ->join('customers', 'customers.id = orders.customer_id')
            ->first();
        $data['created_at'] = $this->beautifyDate($data['created_at']);

        $data['deposit'] = $this->beautifyPrice($data['deposit']);
        $data['discount'] = $this->beautifyPrice($data['discount']);
        $data['total_price'] = $this->beautifyPrice($data['total_price']);

        return $data;
    }

    public function getOrderBySlug(string $slug = null)
    {
    }

    public function getOrders(array $ordersID = null, bool $isAdminConfirm = true, int $limit = null)
    {
        $this->select('orders.slug, u.full_name, c.name, orders.total_price, orders.created_at, orders.completed_at, orders.id');
        $this->join('users as u', 'orders.salesman_id = u.id', 'left');
        $this->join('customers as c', 'orders.customer_id = c.id');

        if ($ordersID)
            $this->whereIn('orders.id', $ordersID);
        if ($isAdminConfirm)
            $this->where('admin_confirm IS NULL');
        if ($limit)
            $this->limit($limit);

        $this->orderBy('orders.created_at', 'ASC');

        $data['data'] = $this->find();

        $data['count'] = count($data['data']) >= 5 ? '+5' : count($data['data']);

        return $data;
    }
}
