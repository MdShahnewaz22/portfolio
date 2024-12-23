<?php

namespace App\Services;

use App\Models\Skills;

class SkillsService
{
    protected $skillsModel;

    public function __construct(Skills $skillsModel)
    {
        $this->skillsModel = $skillsModel;
    }

    public function list()
    {
        return $this->skillsModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->skillsModel->whereNull('deleted_at')->get();
    }

    // public function latest()
    // {
    //     return $this->skillsModel->whereNull('deleted_at')->latest()->firstOrFail();
    // }
    public function latest()
    {
        return $this->skillsModel
            ->whereNull('deleted_at')
            ->first()
            ->take(5) // Limit to 5 records
            ->get();  // Retrieve all 5 records
    }



    public function find($id)
    {
        return $this->skillsModel->find($id);
    }

    public function create(array $data)
    {
        return $this->skillsModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->skillsModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->skillsModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->skillsModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->skillsModel->whereNull('deleted_at')->where('status', 'Active')->get();
    }
}
