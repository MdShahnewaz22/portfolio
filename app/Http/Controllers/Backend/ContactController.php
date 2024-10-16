<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\ContactRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\ContactService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class ContactController extends Controller
    {
        use SystemTrait;

        protected $contactService;

        public function __construct(ContactService $contactService)
        {
            $this->contactService = $contactService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/Contact/Index',
                [
                    'pageTitle' => fn () => 'Contact List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Contact Manage'],
                        ['link' => route('backend.contact.index'), 'title' => 'Contact List'],
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
			['fieldName' => 'gmail', 'class' => 'text-center'],
			['fieldName' => 'message', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Name',
			'Gmail',
			'Message',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->contactService->list();

        if(request()->filled('name'))
				$query->where('name', 'like', '%'.request()->name .'%');

			if(request()->filled('gmail'))
				$query->where('gmail', 'like', request()->gmail .'%');

			if(request()->filled('message'))
				$query->where('message', 'like', request()->massage .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->name = $data->name;
			$customData->gmail = $data->gmail;
			$customData->message = $data->message;


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.contact.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.contact.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.contact.destroy', $data->id),
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
                'Backend/Contact/Form',
                [
                    'pageTitle' => fn () => 'Contact Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Contact Manage'],
                        ['link' => route('backend.contact.create'), 'title' => 'Contact Create'],
                    ],
                ]
            );
        }


        public function store(ContactRequest $request)
        {

            // dd($request);

            DB::beginTransaction();
            try {

                $data = $request->validated();

                $dataInfo = $this->contactService->create($data);

                if ($dataInfo) {
                    $message = 'Contact created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'contacts', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Contact.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'ContactController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $contact = $this->contactService->find($id);

            return Inertia::render(
                'Backend/Contact/Form',
                [
                    'pageTitle' => fn () => 'Contact Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Contact Manage'],
                        ['link' => route('backend.contact.edit', $id), 'title' => 'Contact Edit'],
                    ],
                    'contact' => fn () => $contact,
                    'id' => fn () => $id,
                ]
            );
        }

        public function update(ContactRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                $Contact = $this->contactService->find($id);


                $dataInfo = $this->contactService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Contact updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'contacts', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Contact.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Contactcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->contactService->delete($id)) {
                    $message = 'Contact deleted successfully';
                    $this->storeAdminWorkLog($id, 'contacts', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Contact.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Contactcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->contactService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Contact ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'contacts', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Contact.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'ContactController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
