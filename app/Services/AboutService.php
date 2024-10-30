<?php

namespace App\Services;

use App\Models\About;

class AboutService
{
    protected $aboutModel;

    public function __construct(About $aboutModel)
    {
        $this->aboutModel = $aboutModel;
    }

    public function list()
    {
        return $this->aboutModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->aboutModel->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->aboutModel->find($id);
    }

    public function create(array $data)
    {
        return $this->aboutModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->aboutModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->aboutModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->aboutModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->aboutModel->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
