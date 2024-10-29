<?php

namespace App\Services;

use App\Models\Parsonal_Info;

class Parsonal_InfoService
{
    protected $parsonal_infoModel;

    public function __construct(Parsonal_Info $parsonal_infoModel)
    {
        $this->parsonal_infoModel = $parsonal_infoModel;
    }

    public function list()
    {
        return $this->parsonal_infoModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->parsonal_infoModel->whereNull('deleted_at')->all();
    }
    public function latest()
    {
        return $this->parsonal_infoModel->whereNull('deleted_at')->latest()->firstOrFail();
    }

    public function find($id)
    {
        return $this->parsonal_infoModel->find($id);
    }

    public function create(array $data)
    {
        return $this->parsonal_infoModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->parsonal_infoModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->parsonal_infoModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->parsonal_infoModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->parsonal_infoModel->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
