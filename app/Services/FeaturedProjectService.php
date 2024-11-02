<?php

namespace App\Services;

use App\Models\FeaturedProject;

class FeaturedProjectService
{
    protected $featuredprojectModel;

    public function __construct(FeaturedProject $featuredprojectModel)
    {
        $this->featuredprojectModel = $featuredprojectModel;
    }

    public function list()
    {
        return $this->featuredprojectModel->whereNull('deleted_at');
    }

    // public function all()
    // {
    //     return $this->featuredprojectModel->whereNull('deleted_at')->all();
    // }
    public function all()
    {
        return $this->featuredprojectModel->whereNull('deleted_at')->get();
    }


    public function latest()
    {
        return $this->featuredprojectModel
            ->whereNull('deleted_at')
            ->first()
            ->take(4) // Limit to 5 records
            ->get();  // Retrieve all 5 records
    }



    public function find($id)
    {
        return $this->featuredprojectModel->find($id);
    }

    public function create(array $data)
    {
        return $this->featuredprojectModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->featuredprojectModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->featuredprojectModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->featuredprojectModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->featuredprojectModel->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
