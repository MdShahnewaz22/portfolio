<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\AboutRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\AboutService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class AboutController extends Controller
    {
        use SystemTrait;

        protected $aboutService;

        public function __construct(AboutService $aboutService)
        {
            $this->aboutService = $aboutService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/About/Index',
                [
                    'pageTitle' => fn () => 'About List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'About Manage'],
                        ['link' => route('backend.about.index'), 'title' => 'About List'],
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
            ['fieldName' => 'phone', 'class' => 'text-center'],
			['fieldName' => 'gmail', 'class' => 'text-center'],
			['fieldName' => 'github', 'class' => 'text-center'],
			['fieldName' => 'skype', 'class' => 'text-center'],
			['fieldName' => 'language', 'class' => 'text-center'],
			['fieldName' => 'years_experience', 'class' => 'text-center'],
			['fieldName' => 'handled_project', 'class' => 'text-center'],
			['fieldName' => 'open_source', 'class' => 'text-center'],
			['fieldName' => 'awards', 'class' => 'text-center'],
			['fieldName' => 'description', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Phone',
			'Gmail',
			'Github',
			'Skype',
			'Language',
			'Years Experience',
			'Handled Project',
			'Open Source',
			'Awards',
			'Description',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->aboutService->list();

        if(request()->filled('phone'))
				$query->where('phone', 'like', request()->phone .'%');

			if(request()->filled('gmail'))
				$query->where('gmail', 'like', request()->gmail .'%');

			if(request()->filled('github'))
				$query->where('github', 'like', request()->github .'%');

			if(request()->filled('skype'))
				$query->where('skype', 'like', request()->skype .'%');

			if(request()->filled('language'))
				$query->where('language', 'like', request()->language .'%');

			if(request()->filled('years_experience'))
				$query->where('years_experience', 'like', request()->years_experience .'%');

			if(request()->filled('handled_project'))
				$query->where('handled_project', 'like', request()->handled_project .'%');

			if(request()->filled('open_source'))
				$query->where('open_source', 'like', request()->open_source .'%');

			if(request()->filled('awards'))
				$query->where('awards', 'like', request()->awards .'%');

			if(request()->filled('description'))
				$query->where('description', 'like', request()->description .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->phone = $data->phone;
			$customData->gmail = $data->gmail;
			$customData->github = $data->github;
			$customData->skype = $data->skype;
			$customData->language = $data->language;
			$customData->years_experience = $data->years_experience;
			$customData->handled_project = $data->handled_project;
			$customData->open_source = $data->open_source;
			$customData->awards = $data->awards;
			$customData->description = $data->description;
			

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.about.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.about.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.about.destroy', $data->id),
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
                'Backend/About/Form',
                [
                    'pageTitle' => fn () => 'About Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'About Manage'],
                        ['link' => route('backend.about.create'), 'title' => 'About Create'],
                    ],
                ]
            );
        }


        public function store(AboutRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                $dataInfo = $this->aboutService->create($data);

                if ($dataInfo) {
                    $message = 'About created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'abouts', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create About.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'AboutController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $about = $this->aboutService->find($id);

            return Inertia::render(
                'Backend/About/Form',
                [
                    'pageTitle' => fn () => 'About Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'About Manage'],
                        ['link' => route('backend.about.edit', $id), 'title' => 'About Edit'],
                    ],
                    'about' => fn () => $about,
                    'id' => fn () => $id,
                ]
            );
        }

        public function update(AboutRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                $About = $this->aboutService->find($id);


                $dataInfo = $this->aboutService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'About updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'abouts', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update About.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Aboutcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->aboutService->delete($id)) {
                    $message = 'About deleted successfully';
                    $this->storeAdminWorkLog($id, 'abouts', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete About.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Aboutcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->aboutService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'About ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'abouts', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " About.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'AboutController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
