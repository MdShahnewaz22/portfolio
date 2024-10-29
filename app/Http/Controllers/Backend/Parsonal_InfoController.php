<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Parsonal_InfoRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\Parsonal_InfoService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class Parsonal_InfoController extends Controller
    {
        use SystemTrait;

        protected $parsonal_infoService;

        public function __construct(Parsonal_InfoService $parsonal_infoService)
        {
            $this->parsonal_infoService = $parsonal_infoService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/Parsonal_Info/Index',
                [
                    'pageTitle' => fn () => 'Parsonal_Info List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Parsonal_Info Manage'],
                        ['link' => route('backend.parsonal_info.index'), 'title' => 'Parsonal_Info List'],
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
            ['fieldName' => 'name', 'class' => 'text-center'],
			['fieldName' => 'designation', 'class' => 'text-center'],
			['fieldName' => 'residence', 'class' => 'text-center'],
			['fieldName' => 'city', 'class' => 'text-center'],
			['fieldName' => 'age', 'class' => 'text-center'],
			['fieldName' => 'image', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Name',
			'Designation',
			'Residence',
			'City',
			'Age',
			'Image',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->parsonal_infoService->list();

        if(request()->filled('name'))
				$query->where('name', 'like', request()->name .'%');

			if(request()->filled('designation'))
				$query->where('designation', 'like', request()->designation .'%');

			if(request()->filled('residence'))
				$query->where('residence', 'like', request()->residence .'%');

			if(request()->filled('city'))
				$query->where('city', 'like', request()->city .'%');

			if(request()->filled('age'))
				$query->where('age', 'like', request()->age .'%');

			if(request()->filled('image'))
				$query->where('image', 'like', request()->image .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->name = $data->name;
			$customData->designation = $data->designation;
			$customData->residence = $data->residence;
			$customData->city = $data->city;
			$customData->age = $data->age;
			// $customData->image = $data->image;
            $customData->image = '<img src="' . $data->image . '" height="60" width="70"/>';


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.parsonal_info.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.parsonal_info.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.parsonal_info.destroy', $data->id),
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
                'Backend/Parsonal_Info/Form',
                [
                    'pageTitle' => fn () => 'Parsonal_Info Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Parsonal_Info Manage'],
                        ['link' => route('backend.parsonal_info.create'), 'title' => 'Parsonal_Info Create'],
                    ],
                ]
            );
        }


        public function store(Parsonal_InfoRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                if ($request->hasFile('image'))
                $data['image'] = $this->imageUpload($request->file('image'), 'parsonal_info');

                $dataInfo = $this->parsonal_infoService->create($data);

                if ($dataInfo) {
                    $message = 'Parsonal_Info created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'parsonal__infos', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Parsonal_Info.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'Parsonal_InfoController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $parsonal_info = $this->parsonal_infoService->find($id);

            return Inertia::render(
                'Backend/Parsonal_Info/Form',
                [
                    'pageTitle' => fn () => 'Parsonal_Info Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Parsonal_Info Manage'],
                        ['link' => route('backend.parsonal_info.edit', $id), 'title' => 'Parsonal_Info Edit'],
                    ],
                    'parsonal_info' => fn () => $parsonal_info,
                    'id' => fn () => $id,
                ]
            );
        }

        public function update(Parsonal_InfoRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                
                if ($request->hasFile('image'))
                $data['image'] = $this->imageUpload($request->file('image'), 'parsonal_info');

                $Parsonal_Info = $this->parsonal_infoService->find($id);


                $dataInfo = $this->parsonal_infoService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Parsonal_Info updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'parsonal__infos', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Parsonal_Info.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Parsonal_Infocontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->parsonal_infoService->delete($id)) {
                    $message = 'Parsonal_Info deleted successfully';
                    $this->storeAdminWorkLog($id, 'parsonal__infos', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Parsonal_Info.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Parsonal_Infocontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->parsonal_infoService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Parsonal_Info ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'parsonal__infos', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Parsonal_Info.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'Parsonal_InfoController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
