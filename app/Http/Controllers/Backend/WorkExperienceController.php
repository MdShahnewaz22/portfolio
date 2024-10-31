<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\WorkExperienceRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\WorkExperienceService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class WorkExperienceController extends Controller
    {
        use SystemTrait;

        protected $workexperienceService;

        public function __construct(WorkExperienceService $workexperienceService)
        {
            $this->workexperienceService = $workexperienceService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/WorkExperience/Index',
                [
                    'pageTitle' => fn () => 'Work Experience List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Work Experience Manage'],
                        ['link' => route('backend.workexperience.index'), 'title' => 'Work Experience List'],
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
            ['fieldName' => 'company_name', 'class' => 'text-center'],
			['fieldName' => 'year_to_year', 'class' => 'text-center'],
			['fieldName' => 'designation', 'class' => 'text-center'],
			['fieldName' => 'description', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Company Name',
			'Year To Year',
			'Designation',
			'Description',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->workexperienceService->list();

        if(request()->filled('company_name'))
				$query->where('company_name', 'like','%'. request()->company_name .'%');

			if(request()->filled('year_to_year'))
				$query->where('year_to_year', 'like', request()->year_to_year .'%');

			if(request()->filled('designation'))
				$query->where('designation', 'like', request()->designation .'%');

			if(request()->filled('description'))
				$query->where('description', 'like', request()->description .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->company_name = $data->company_name;
			$customData->year_to_year = $data->year_to_year;
			$customData->designation = $data->designation;
			$customData->description = $data->description;


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.workexperience.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.workexperience.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.workexperience.destroy', $data->id),
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
                'Backend/WorkExperience/Form',
                [
                    'pageTitle' => fn () => 'Work Experience Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Work Experience Manage'],
                        ['link' => route('backend.workexperience.create'), 'title' => 'Work Experience Create'],
                    ],
                ]
            );
        }


        public function store(WorkExperienceRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                $dataInfo = $this->workexperienceService->create($data);

                if ($dataInfo) {
                    $message = 'Work Experience created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'work_experiences', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Work Experience.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'WorkExperienceController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $workexperience = $this->workexperienceService->find($id);

            return Inertia::render(
                'Backend/WorkExperience/Form',
                [
                    'pageTitle' => fn () => 'Work Experience Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Work Experience Manage'],
                        ['link' => route('backend.workexperience.edit', $id), 'title' => 'Work Experience Edit'],
                    ],
                    'workexperience' => fn () => $workexperience,
                    'id' => fn () => $id,
                ]
            );
        }

        public function update(WorkExperienceRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                $WorkExperience = $this->workexperienceService->find($id);


                $dataInfo = $this->workexperienceService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Work Experience updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'work_experiences', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Work Experience.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'WorkExperiencecontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->workexperienceService->delete($id)) {
                    $message = 'Work Experience deleted successfully';
                    $this->storeAdminWorkLog($id, 'work_experiences', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Work Experience.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'WorkExperiencecontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->workexperienceService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Work Experience ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'work_experiences', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " WorkExperience.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'WorkExperienceController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
