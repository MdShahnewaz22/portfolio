<?php

namespace App\Services;

use App\Models\Education;

class EducationService
{
    protected $educationModel;

    public function __construct(Education $educationModel)
    {
        $this->educationModel = $educationModel;
    }

    public function list()
    {
        return $this->educationModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->educationModel->whereNull('deleted_at')->get();
    }

    public function find($id)
    {
        return $this->educationModel->find($id);
    }

    public function create(array $data)
    {
        return $this->educationModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->educationModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->educationModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->educationModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->educationModel->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
