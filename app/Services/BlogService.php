<?php

namespace App\Services;

use App\Models\Blog;

class BlogService
{
    protected $blogModel;

    public function __construct(Blog $blogModel)
    {
        $this->blogModel = $blogModel;
    }

    public function list()
    {
        return $this->blogModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->blogModel->whereNull('deleted_at')->get();
    }


    public function latest()
    {
        return $this->blogModel
            ->whereNull('deleted_at')
            ->first()
            ->take(2) // Limit to 5 records
            ->get();  // Retrieve all 5 records
    }

    public function find($id)
    {
        return $this->blogModel->find($id);
    }

    public function create(array $data)
    {
        return $this->blogModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->blogModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->blogModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->blogModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->blogModel->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
