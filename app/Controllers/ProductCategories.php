<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductCategoriesModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class ProductCategories extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'Ürün kategorileri',
            'active' => ['products', 'categories'],
        ];

        return view('pages/products/categories', $data);
    }

    public function getCategoriesForSelect()
    {
        $data = [];

        $categories_model = new ProductCategoriesModel();

        $categories_model->select(['id', 'name as text']);

        $id = $this->request->getVar('id');
        if ($id != '') {
            return $this->response->setJSON($categories_model->find($id));
        }

        $page = $this->request->getVar('page');

        $search = $this->request->getVar('search');
        if ($search != '') {
            $categories_model->like('name', "$search");
        }

        $data['results'] = $categories_model->paginate(20, 'default', $page);
        $data['total'] = count($data['results']);
        $data['pagination']['more'] = $data['total'] < 20 ? false : true;

        return $this->response->setJSON($data);
    }

    public function addCategory()
    {
        helper('post');

        $data = [];

        if ($this->request->getMethod() == 'post') {
            $rules = [
                'name' => [
                    'rules' => 'required',
                    'label' => 'Ürün',
                ],
            ];
            $input = $this->validate($rules);
            if (!$input) {
                $data = [
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                ];
                $this->response->setStatusCode('400');
            } else {
                $model = new ProductCategoriesModel();
                $id = $this->request->getVar('id');
                if (!$id) {
                    $model->save([
                        'name' => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    ]);
                } else {
                    $model->update($id, [
                        'name' => $this->request->getVar('name', FILTER_SANITIZE_STRING),
                    ]);
                }
                $data = [
                    'success' => true,
                    'message' => 'Kategori başarıyla eklendi!',
                    'id' => $model->getInsertID(),
                ];
            }
        } else {
            $data = [
                'success' => false,
                'errors' => [
                    "Geçersiz istek!"
                ],
            ];
            $this->response->setStatusCode('400');
        }

        $data['csrfName'] = csrf_token();
        $data['csrfHash'] = csrf_hash();

        return $this->response->setJSON($data);
    }

    public function getCategories()
    {
        helper(['number', 'dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter());
        $default_query = "SELECT `name`, id FROM product_categories WHERE deleted_at IS NULL";

        $dt->query($default_query);

        $dt->edit('id', function ($data) {
            return '<div class="btn-group">
            <a href="#" class="btn btn-outline-primary d-flex" id="edit_category"  data-id="' . $data['id'] . '"><i class="material-icons">edit</i> Düzenle</a>
            <a href="#" class="btn btn-outline-danger delete d-flex" id="remove_category" data-id="' . $data['id'] . '"><i class="material-icons">delete_outline</i> Sil</a>
            </div>';
        });

        echo $dt->generate();
    }

    public function deleteCategory()
    {
        $data = [];
        if ($this->request->getMethod() == 'post') {
            if ($id = $this->request->getVar('id')) {
                $categories_model = new ProductCategoriesModel();
                $category = $categories_model->find($id);
                if (!empty($category)) {
                    $categories_model->delete($id);
                    $data = [
                        'success' => 'Kategori başarıyla silindi',
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash(),
                    ];
                } else {
                    $this->response->setStatusCode(400);
                    $data = ['error' => 'Böyle bir kategori bulunamadı.'];
                }
            } else {
                $this->response->setStatusCode('400');
                $data = ['error' => 'Lütfen ID bilgisi gönderin.'];
            }
        }

        return $this->response->setJSON($data);
    }
}
