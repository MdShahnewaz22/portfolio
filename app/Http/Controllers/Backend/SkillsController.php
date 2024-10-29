<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\SkillsRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\SkillsService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class SkillsController extends Controller
    {
        use SystemTrait;

        protected $skillsService;

        public function __construct(SkillsService $skillsService)
        {
            $this->skillsService = $skillsService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/Skills/Index',
                [
                    'pageTitle' => fn () => 'Skills List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Skills Manage'],
                        ['link' => route('backend.skills.index'), 'title' => 'Skills List'],
                    ],
                    'tableHeaders' => fn () => $this->getTableHeaders(),
                    'dataFields' => fn () => $this->getDataFields(),
                    'datas' => fn () => $this->getDatas(),
                ]
            );
        }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'title', 'class' => 'text-center'],
			['fieldName' => 'percent', 'class' => 'text-center'],
			['fieldName' => 'image', 'class' => 'text-center'],
			['fieldName' => 'file', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Title',
			'Percent',
			'Image',
			'File',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->skillsService->list();

        if(request()->filled('title'))
				$query->where('title', 'like', request()->title .'%');

			if(request()->filled('percent'))
				$query->where('percent', 'like', request()->percent .'%');

			if(request()->filled('image'))
				$query->where('image', 'like', request()->image .'%');

			if(request()->filled('file'))
				$query->where('file', 'like', request()->file .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->title = $data->title;
			$customData->percent = $data->percent;
			// $customData->image = $data->image;
            $customData->image = '<img src="' . $data->image . '" height="60" width="70"/>';
			// $customData->file = $data->file;
            $customData->file ='<br> <a href="' . strstr($data->file, '/storage/') . '" target="_blank"><span class="font-bold text-green-600">Show</span></a>';


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.skills.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.skills.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.skills.destroy', $data->id),
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
                'Backend/Skills/Form',
                [
                    'pageTitle' => fn () => 'Skills Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Skills Manage'],
                        ['link' => route('backend.skills.create'), 'title' => 'Skills Create'],
                    ],
                ]
            );
        }


        public function store(SkillsRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                // if ($request->hasFile('image') && $request->hasFile('file')) {
                //     $data['image'] = $this->imageUpload($request->file('image'), 'skill');
                //     $data['file'] = $this->fileUpload($request->file('file'), 'file');
                // }


                if ($request->hasFile('image'))
                $data['image'] = $this->imageUpload($request->file('image'), 'skill');

                // if ($request->hasFile('file'))
                // $data['file'] = $this->fileUpload($request->file('file'), 'file');



                $dataInfo = $this->skillsService->create($data);

                if ($dataInfo) {
                    $message = 'Skills created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'skills', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Skills.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'SkillsController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $skills = $this->skillsService->find($id);

            return Inertia::render(
                'Backend/Skills/Form',
                [
                    'pageTitle' => fn () => 'Skills Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Skills Manage'],
                        ['link' => route('backend.skills.edit', $id), 'title' => 'Skills Edit'],
                    ],
                    'skills' => fn () => $skills,
                    'id' => fn () => $id,
                ]
            );
        }

        public function update(SkillsRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();

                if ($request->hasFile('image'))
                $data['image'] = $this->imageUpload($request->file('image'), 'skill');

                $Skills = $this->skillsService->find($id);


                $dataInfo = $this->skillsService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Skills updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'skills', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Skills.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Skillscontroller', 'update', substr($err->getMessage(), 0, 1000));
                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function destroy($id)
        {

            DB::beginTransaction();

            try {

                if ($this->skillsService->delete($id)) {
                    $message = 'Skills deleted successfully';
                    $this->storeAdminWorkLog($id, 'skills', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Skills.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Skillscontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->skillsService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Skills ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'skills', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Skills.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'SkillsController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }


}
