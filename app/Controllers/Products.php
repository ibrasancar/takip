<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductsModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Products extends BaseController
{

    protected $productValidationRules = [
        'name'  => [
            'rules' => 'required|min_length[3]',
            'label' => 'İsim',
        ],
        'price' =>  [
            'rules' => 'required',
            'label' => 'Fiyat'
        ],
        'image' => [
            "rules" => "uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/gif,image/png]",
            "label" => "Ürün görseli",
        ]
    ];

    protected $productFormElements = [
        'name' => [
            'type' => 'text',
            'column' => 'col-md-8',
            'label' => 'Ürün adı *',
            'placeholder' => 'Ürününüzün adı',
        ],
        'price' => [
            'type' => 'text',
            'column' => 'col-md-4',
            'label' => 'Fiyat *',
            'placeholder' => 'Lütfen geçerli bir fiyat giriniz',
        ],
        'category' => [
            'type' => 'select',
            'label' => 'Ürün Kategorisi',
            'column' => 'col-md-6',
            'data' => [],
            'empty_label' => 'Lütfen bir kategori seçiniz',
            'show_value' => 'name',
            'is_jsselect' => true,
            'custom_html' => [
                '<button class="btn btn-outline btn-outline-dark mt-2 w-100" id="add_product"><i class="material-icons-outlined">add</i> Kategori Ekle</button>'
            ]
        ],
        'manufacturer_id' => [
            'type' => 'select',
            'label' => 'Üretici',
            'column' => 'col-md-6',
            'data' => [],
            'empty_label' => 'Lütfen bir üretici seçiniz',
            'show_value' => 'name',
            'is_jsselect' => true,
            'custom_html' => [
                '<button class="btn btn-outline btn-outline-dark mt-2 w-100" id="add_manufacturer"><i class="material-icons-outlined">add</i> Üretici Ekle</button>'
            ]
        ],
        'description' => [
            'type' => 'textarea',
            'column' => 'col-md-12',
            'label' => 'Ürün açıklaması',
            'rows' => 2,
            'placeholder' => 'Ürününüzü tanımlayan kısa bir açıklama...',
        ]
    ];

    protected $pageButtons = [
        'add_product' => [
            'route' => 'add_product',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'Ürün Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'products' => [
            'route' => 'products',
            'class' => 'btn btn-info',
            'text' => 'Geri Dön',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'arrow_back',
            ]
        ],
    ];

    protected $uploadPath = 'uploads/product-image';

    public function index()
    {
        $data = [
            'page_title' => 'Ürünler',
            'active' => ['products', 'products'],
            'page_buttons' => [$this->pageButtons['add_product']],
        ];

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        return view('pages/products/list', $data);
    }

    public function add()
    {
        helper(['form', 'url', 'text', 'post', 'inflector', 'formElement']);
        $data = [
            'page_title' => 'Ürün ekle',
            'page_desc' => 'Aşağıdaki formu doldurarak ürününüzü ekleyebilirsiniz.',
            'active' => ['products', 'add_product'],
            'page_buttons' => [$this->pageButtons['products']],
        ];


        $product_model = new ProductsModel();
        if ($this->request->getMethod() == 'post') {
            $input = $this->validate($this->productValidationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $full_img_url = $this->uploadProductImage('image');
                $product_model->save([
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'price' => clear_price_mask($this->request->getVar('price')),
                    'description' => $this->request->getVar('description', FILTER_SANITIZE_STRING),
                    'category'  => $this->request->getVar('category', FILTER_SANITIZE_NUMBER_INT),
                    'manufacturer_id' => $this->request->getVar('manufacturer_id', FILTER_SANITIZE_NUMBER_INT),
                    'image' => $full_img_url,
                ]);
                $session = session();
                $session->setFlashData('success', 'Ürün başarıyla eklendi');
                return redirect()->route('edit_product', [$product_model->getInsertID()]);
            }
        }

        $productForm = $this->productFormElements;

        $data['form_elements'] = makeFormElement($productForm, $data['validation'] ?? null);

        $manufacturerController = new Manufacturers();
        $data['manufacturer_form_elements'] = makeFormElement($manufacturerController->manufacturerForm);

        return view('pages/products/add', $data);
    }

    public function edit(int $product_id)
    {
        helper(['post', 'text', 'form', 'inflector', 'formElement']);
        $data = [
            'page_title' => 'Ürünü düzenle',
            'page_desc' => 'Aşağıdaki formu doldurarak ürününüzü düzenleyebilirsiniz.',
            'active' => ['products', 'products'],
            'page_buttons' => [$this->pageButtons['products'], $this->pageButtons['add_product']],
        ];

        // get models
        $product_model = new ProductsModel();

        $data['product'] = $product_model
            ->select('products.id, products.category, pm.name as category_name, products.name, products.price, products.description, products.image, products.manufacturer_id, m.name as manufacturer_name, products.created_at')
            ->join('manufacturers as m', 'm.id = products.manufacturer_id', 'left')
            ->join('product_categories as pm', 'pm.id = products.category', 'left')
            ->where('products.id', $product_id)
            ->first();

        if (!$data['product']) {
            session()->setFlashData('error', 'Ürün bulunamadı!');
            return redirect()->route('products');
        }

        $session = session();
        $data['success'] = $session->getFlashData('success');


        // fix price (₺ to float)
        $data['product'] = $product_model->fixPrice($data['product']);

        // check if post
        if ($this->request->getMethod() == 'post') {
            $file_exist = $this->request->getFile('image')->getError();

            $validationRules = [
                'name' => $this->productValidationRules['name'],
                'price' => $this->productValidationRules['price'],
            ];

            if ($file_exist === 0)
                $validationRules['image'] = $this->productValidationRules['image'];

            $input = $this->validate($validationRules);

            if (!$input) {
                $data['validation'] = $this->validator;
            } else {

                $full_img_url = $this->uploadProductImage('image') ?? false;

                $product_model->update($product_id, [
                    'name'  => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    'category'  => $this->request->getVar('category', FILTER_SANITIZE_NUMBER_INT),
                    'price' => clear_price_mask($this->request->getVar('price')),
                    'description' => $this->request->getVar('description', FILTER_SANITIZE_STRING),
                    'manufacturer_id' => $this->request->getVar('manufacturer_id', FILTER_SANITIZE_NUMBER_INT),
                    'image' => $full_img_url != false ? $full_img_url : $data['product']['image'],
                ]);

                $data['success'] = 'Ürün başarıyla düzenlendi.';

                $data['product'] = $product_model
                    ->select('products.id, products.category, pm.name as category_name, products.name, products.price, products.description, products.image, products.manufacturer_id, m.name as manufacturer_name, products.created_at')
                    ->join('manufacturers as m', 'm.id = products.manufacturer_id', 'left')
                    ->join('product_categories as pm', 'pm.id = products.category', 'left')
                    ->where('products.id', $product_id)
                    ->first();

                // fix price (₺ to float)
                $data['product'] = $product_model->fixPrice($data['product']);
            }
        }

        $productForm = $this->productFormElements;
        $productForm['manufacturer_id']['data'] = [
            'id' => $data['product']['manufacturer_id'],
            'show_value' => $data['product']['manufacturer_name']
        ];

        $productForm['category']['data'] = [
            'id' => $data['product']['category'],
            'show_value' => $data['product']['category_name']
        ];

        $data['form_elements'] = makeFormElement($productForm, $data['validation'] ?? null, $data['product']);

        $manufacturerController = new Manufacturers();
        $data['manufacturer_form_elements'] = makeFormElement($manufacturerController->manufacturerForm);

        return view('pages/products/edit', $data);
    }

    public function delete(int $product_id)
    {
        $product_model = new ProductsModel();

        $check = $product_model->where('id', $product_id)->first();

        if (!$check) {
            session()->setFlashData('error', 'Ürün bulunamadı!');
        } else {
            $product_model->delete($product_id);
            session()->setFlashData('success', 'Ürün başarıyla silindi');
        }

        return redirect()->route('products');
    }

    public function getProducts()
    {
        helper(['number', 'dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter);
        $default_query = "SELECT p.image, pm.name AS category_name, p.name, p.price, m.name AS manufacturer_name, p.created_at, p.id 
        FROM products p
        LEFT JOIN manufacturers m ON m.id = p.manufacturer_id AND m.deleted_at IS NULL
        LEFT JOIN product_categories pm ON pm.id = p.category AND pm.deleted_at IS NULL";

        if (isset($_GET['deleted']) && $_GET['deleted'] != '') {
            $default_query .= ' WHERE p.deleted_at IS NOT NULL';
        } else {
            $default_query .= ' WHERE p.deleted_at IS NULL';
        }

        $dt->query($default_query);

        $minDate = $this->request->getVar('minDate');
        $maxDate = $this->request->getVar('maxDate');
        $minPrice = $this->request->getVar('minPrice');
        $maxPrice = $this->request->getVar('maxPrice');

        if ($minDate && !$maxDate) {
            $minDate .= ' 00:00:00';
            $dt->query($default_query . ' AND p.created_at >= "' . $minDate . '"');
        }

        if ($maxDate && !$minDate) {
            $maxDate .= ' 23:59:59';
            $dt->query($default_query . ' AND p.created_at <= "' . $maxDate . '"');
        }

        if ($maxDate && $minDate) {
            $maxDate .= ' 23:59:59';
            $minDate .= ' 00:00:00';
            if (strtotime($maxDate) > strtotime($minDate))
                $dt->query($default_query . ' AND ( p.created_at BETWEEN "' . $minDate . '" AND "' . $maxDate . '" )');
            else
                $dt->query($default_query . ' AND p.created_at <= "' . $maxDate . '"');
        }

        if ($minPrice && !$maxPrice) {
            $dt->filter('price', function () use ($minPrice) {
                return $this->greaterThan($minPrice);
            });
        }
        if ($maxPrice && !$minPrice) {
            $dt->filter('price', function () use ($maxPrice) {
                return $this->lessThan($maxPrice);
            });
        }
        if ($maxPrice && $minPrice) {
            $dt->filter('price', function () use ($maxPrice, $minPrice) {
                if ($maxPrice > $minPrice)
                    return $this->between($minPrice, $maxPrice);
                else
                    return  $this->lessThan($maxPrice);
            });
        }

        $dt->edit('image', function ($data) {
            return dtGetImage($data['image']);
        });

        $dt->edit('created_at', function ($data) {
            return dtBeautifyDate($data['created_at']);
        });

        $dt->edit('id', function ($data) {
            return '<div class="btn-group"><a href="' . site_url(route_to('edit_product', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_product', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
        });

        echo $dt->generate();
    }

    protected function uploadProductImage(string $fieldName)
    {
        // get file
        $file = $this->request->getFile($fieldName);

        // check file is uploaded
        if ($file->getError() > 0)
            return false;

        $uploadPath = ROOTPATH . 'public/' . $this->uploadPath . '/' . date('Y/m/d');

        // check directory is exist => if is not exist create a new one
        if (!file_exists($uploadPath))
            mkdir(ROOTPATH . 'public/' . $this->uploadPath . '/' . date('Y/m/d'), 0755, true);

        // urlPath for database
        $urlPath = $this->uploadPath . '/' . date('Y/m/d');

        // file upload!
        $file->move($uploadPath, convert_accented_characters(underscore($file->getName())));

        // get last name of file
        $name = $file->getName();

        // resize and optimize image
        \Config\Services::image()
            ->withFile($uploadPath . '/' . $name)
            ->resize(600, 400, true, 'width')
            ->save($uploadPath . '/' . $name, 70);

        // return image full url for database
        return site_url($urlPath . '/' . $name);
    }

    public function getProductsForSelect()
    {
        $data = [];

        $products_model = new ProductsModel();

        $products_model->select(['id', 'name as text', 'price', 'image']);

        $id = $this->request->getVar('id');
        if ($id != '') {
            return $this->response->setJSON($products_model->find($id));
        }

        $page = $this->request->getVar('page');

        $search = $this->request->getVar('search');
        if ($search != '') {
            $products_model->like('name', "$search");
        }

        $data['results'] = $products_model->paginate(20, 'default', $page);
        $data['total'] = count($data['results']);
        $data['pagination']['more'] = $data['total'] < 20 ? false : true;

        return $this->response->setJSON($data);
    }
}
