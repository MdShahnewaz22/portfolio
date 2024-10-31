<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\EducationRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\EducationService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class EducationController extends Controller
    {
        use SystemTrait;

        protected $educationService;

        public function __construct(EducationService $educationService)
        {
            $this->educationService = $educationService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/Education/Index',
                [
                    'pageTitle' => fn () => 'Education List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Education Manage'],
                        ['link' => route('backend.education.index'), 'title' => 'Education List'],
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
            ['fieldName' => 'institution_name', 'class' => 'text-center'],
			['fieldName' => 'year_to_year', 'class' => 'text-center'],
			['fieldName' => 'certificate_name', 'class' => 'text-center'],
			['fieldName' => 'group', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Institution Name',
			'Year To Year',
			'Certificate Name',
			'Group',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->educationService->list();

        if(request()->filled('institution_name'))
				$query->where('institution_name', 'like', request()->institution_name .'%');

			if(request()->filled('year_to_year'))
				$query->where('year_to_year', 'like', request()->year_to_year .'%');

			if(request()->filled('certificate_name'))
				$query->where('certificate_name', 'like', request()->certificate_name .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->institution_name = $data->institution_name;
			$customData->year_to_year = $data->year_to_year;
			$customData->certificate_name = $data->certificate_name;
			$customData->group = $data->group;


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.education.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.education.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.education.destroy', $data->id),
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
                'Backend/Education/Form',
                [
                    'pageTitle' => fn () => 'Education Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Education Manage'],
                        ['link' => route('backend.education.create'), 'title' => 'Education Create'],
                    ],
                ]
            );
        }


        public function store(EducationRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                $dataInfo = $this->educationService->create($data);

                if ($dataInfo) {
                    $message = 'Education created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'education', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Education.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'EducationController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $education = $this->educationService->find($id);

            return Inertia::render(
                'Backend/Education/Form',
                [
                    'pageTitle' => fn () => 'Education Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Education Manage'],
                        ['link' => route('backend.education.edit', $id), 'title' => 'Education Edit'],
                    ],
                    'education' => fn () => $education,
                    'id' => fn () => $id,
                ]
            );
        }

        public function update(EducationRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                $Education = $this->educationService->find($id);


                $dataInfo = $this->educationService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Education updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'education', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Education.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Educationcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->educationService->delete($id)) {
                    $message = 'Education deleted successfully';
                    $this->storeAdminWorkLog($id, 'education', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Education.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Educationcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->educationService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Education ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'education', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Education.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'EducationController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
