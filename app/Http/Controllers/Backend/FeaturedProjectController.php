<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeaturedProjectRequest;
use Illuminate\Support\Facades\DB;
use App\Services\FeaturedProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Traits\SystemTrait;
use Exception;

class FeaturedProjectController extends Controller
{
    use SystemTrait;

    protected $featuredprojectService;

    public function __construct(FeaturedProjectService $featuredprojectService)
    {
        $this->featuredprojectService = $featuredprojectService;
    }

    public function index()
    {
        return Inertia::render(
            'Backend/FeaturedProject/Index',
            [
                'pageTitle' => fn() => 'Featured Project List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Featured Project Manage'],
                    ['link' => route('backend.featuredproject.index'), 'title' => 'Featured Project List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->getDataFields(),
                'datas' => fn() => $this->getDatas(),
            ]
        );
    }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'project_name', 'class' => 'text-center'],
            ['fieldName' => 'live_link', 'class' => 'text-center'],
            ['fieldName' => 'image', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Project Name',
            'Live Link',
            'Image',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->featuredprojectService->list();

        if (request()->filled('project_name'))
            $query->where('project_name', 'like', request()->project_name . '%');

        if (request()->filled('live_link'))
            $query->where('live_link', 'like', request()->live_link . '%');

        if (request()->filled('image'))
            $query->where('image', 'like', request()->image . '%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->project_name = $data->project_name;
            $customData->live_link = $data->live_link;
            // $customData->image = '<img src="' . $data->image . '" height="60" width="70"/>';

            $customData->image = '<div style="display: flex;">';
            foreach ($data->image as $imageUrl) {
                $customData->image .= '<img src="' . ($imageUrl) . '" height="50" width="50" style="margin-right: 2px;"/>';
            }
            $customData->image.='</div>';

            // $customData->image = '';
            // foreach ($data->image as $imageUrl) {
            //     $customData->image .= '<img src="' . $imageUrl . '" height="60" width="70"/>';
            // }





            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.featuredproject.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.featuredproject.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.featuredproject.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    public function create()
    {
        return Inertia::render(
            'Backend/FeaturedProject/Form',
            [
                'pageTitle' => fn() => 'Featured Project Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Featured Project Manage'],
                    ['link' => route('backend.featuredproject.create'), 'title' => 'Featured Project Create'],
                ],
            ]
        );
    }


    // public function store(FeaturedProjectRequest $request)
    // {

    //     DB::beginTransaction();
    //     try {

    //         $data = $request->validated();
    //         if ($request->hasFile('image'))
    //         $data['image'] = $this->imageUpload($request->file('image'), 'featured_project');



    //         $dataInfo = $this->featuredprojectService->create($data);

    //         if ($dataInfo) {
    //             $message = 'Featured Project created successfully';
    //             $this->storeAdminWorkLog($dataInfo->id, 'featured_projects', $message);

    //             DB::commit();

    //             return redirect()
    //                 ->back()
    //                 ->with('successMessage', $message);
    //         } else {
    //             DB::rollBack();

    //             $message = "Failed To create Featured Project.";
    //             return redirect()
    //                 ->back()
    //                 ->with('errorMessage', $message);
    //         }
    //     } catch (Exception $err) {

    //         DB::rollBack();
    //         $this->storeSystemError('Backend', 'FeaturedProjectController', 'store', substr($err->getMessage(), 0, 1000));

    //         DB::commit();
    //         $message = "Server Errors Occur. Please Try Again.";

    //         return redirect()
    //             ->back()
    //             ->with('errorMessage', $message);
    //     }
    // }



    public function store(FeaturedProjectRequest $request)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            $data = $request->validated();
            // Handle image uploads
            if ($request->hasFile('image')) {
                $data['image'] = [];

                foreach ($request->file('image') as $file) {

                    $data['image'][] = $this->imageUpload($file, 'featured_project');
                }
                $data['image'] = json_encode($data['image']);
            }



            $dataInfo = $this->featuredprojectService->create($data);

            if ($dataInfo) {
                $message = 'Featured Project created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'featured_projects', $message);

                DB::commit();
                return redirect()->back()->with('successMessage', $message);
            } else {
                DB::rollBack();
                return redirect()->back()->with('errorMessage', 'Failed to create Featured Project.');
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FeaturedProjectController', 'store', substr($err->getMessage(), 0, 1000));
            return redirect()->back()->with('errorMessage', 'Server Errors Occur. Please Try Again.');
        }
    }



    public function edit($id)
    {
        $featuredproject = $this->featuredprojectService->find($id);

        return Inertia::render(
            'Backend/FeaturedProject/Form',
            [
                'pageTitle' => fn() => 'Featured Project Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Featured Project Manage'],
                    ['link' => route('backend.featuredproject.edit', $id), 'title' => 'Featured Project Edit'],
                ],
                'featuredproject' => fn() => $featuredproject,
                'id' => fn() => $id,
            ]
        );
    }

    public function update(FeaturedProjectRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = [];

                foreach ($request->file('image') as $file) {

                    $data['image'][] = $this->imageUpload($file, 'featured_project');
                }
                // Convert the image array to JSON before storing
                $data['image'] = json_encode($data['image']);
            }


            $FeaturedProject = $this->featuredprojectService->find($id);
            $dataInfo = $this->featuredprojectService->update($data, $id);

            if ($dataInfo->save()) {
                $message = 'Featured Project updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'featured_projects', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Featured Project.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FeaturedProjectcontroller', 'update', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }



    // public function update(FeaturedProjectRequest $request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $data = $request->validated();
    //         $FeaturedProject = $this->featuredprojectService->find($id);

    //         if ($request->hasFile('images')) {
    //             $images = [];
    //             foreach ($request->file('images') as $image) {
    //                 $images[] = $this->imageUpload($image, 'featured_project');
    //             }
    //             $data['images'] = json_encode($images);
    //         }

    //         $dataInfo = $this->featuredprojectService->update($data, $id);

    //         if ($dataInfo->save()) {
    //             $message = 'Featured Project updated successfully';
    //             $this->storeAdminWorkLog($dataInfo->id, 'featured_projects', $message);

    //             DB::commit();
    //             return redirect()->back()->with('successMessage', $message);
    //         } else {
    //             DB::rollBack();
    //             return redirect()->back()->with('errorMessage', "Failed to update Featured Project.");
    //         }
    //     } catch (Exception $err) {
    //         DB::rollBack();
    //         $this->storeSystemError('Backend', 'FeaturedProjectController', 'update', substr($err->getMessage(), 0, 1000));
    //         return redirect()->back()->with('errorMessage', "Server error. Please try again.");
    //     }
    // }



    public function destroy($id)
    {

        DB::beginTransaction();

        try {

            if ($this->featuredprojectService->delete($id)) {
                $message = 'Featured Project deleted successfully';
                $this->storeAdminWorkLog($id, 'featured_projects', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To Delete Featured Project.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FeaturedProjectcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function changeStatus()
    {
        DB::beginTransaction();

        try {
            $dataInfo = $this->featuredprojectService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'FeaturedProject ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'featured_projects', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " FeaturedProject.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FeaturedProjectController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors(['error' => $message]);
        }
    }
}
