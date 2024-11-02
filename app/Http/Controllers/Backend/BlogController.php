<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\BlogRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\BlogService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class BlogController extends Controller
    {
        use SystemTrait;

        protected $blogService;

        public function __construct(BlogService $blogService)
        {
            $this->blogService = $blogService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/Blog/Index',
                [
                    'pageTitle' => fn () => 'Blog List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Blog Manage'],
                        ['link' => route('backend.blog.index'), 'title' => 'Blog List'],
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
            ['fieldName' => 'image', 'class' => 'text-center'],
			['fieldName' => 'date', 'class' => 'text-center'],
			['fieldName' => 'title', 'class' => 'text-center'],
			['fieldName' => 'posted_by', 'class' => 'text-center'],
			['fieldName' => 'category', 'class' => 'text-center'],
			['fieldName' => 'posted_on', 'class' => 'text-center'],
			['fieldName' => 'description', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Image',
			'Date',
			'Title',
			'Posted By',
			'Category',
			'Posted On',
			'Description',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->blogService->list();

        if(request()->filled('image'))
				$query->where('image', 'like', request()->image .'%');

			if(request()->filled('date'))
				$query->where('date', 'like', request()->date .'%');

			if(request()->filled('title'))
				$query->where('title', 'like', request()->title .'%');

			if(request()->filled('posted_by'))
				$query->where('posted_by', 'like', request()->posted_by .'%');

			if(request()->filled('category'))
				$query->where('category', 'like', request()->category .'%');

			if(request()->filled('posted_on'))
				$query->where('posted_on', 'like', request()->posted_on .'%');

			if(request()->filled('description'))
				$query->where('description', 'like', request()->description .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            // $customData->image = $data->image;
            $customData->image = '<img src="' . $data->image . '" height="60" width="70"/>';
			$customData->date = $data->date;
			$customData->title = $data->title;
			$customData->posted_by = $data->posted_by;
			$customData->category = $data->category;
			$customData->posted_on = $data->posted_on;
			$customData->description = $data->description;


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.blog.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.blog.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.blog.destroy', $data->id),
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
                'Backend/Blog/Form',
                [
                    'pageTitle' => fn () => 'Blog Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Blog Manage'],
                        ['link' => route('backend.blog.create'), 'title' => 'Blog Create'],
                    ],
                ]
            );
        }


        public function store(BlogRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();
                if ($request->hasFile('image'))
                $data['image'] = $this->imageUpload($request->file('image'), 'blog');

                $dataInfo = $this->blogService->create($data);

                if ($dataInfo) {
                    $message = 'Blog created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'blogs', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Blog.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'BlogController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $blog = $this->blogService->find($id);

            return Inertia::render(
                'Backend/Blog/Form',
                [
                    'pageTitle' => fn () => 'Blog Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Blog Manage'],
                        ['link' => route('backend.blog.edit', $id), 'title' => 'Blog Edit'],
                    ],
                    'blog' => fn () => $blog,
                    'id' => fn () => $id,
                ]
            );
        }

        public function update(BlogRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();

                if ($request->hasFile('image'))
                $data['image'] = $this->imageUpload($request->file('image'), 'blog');
                $Blog = $this->blogService->find($id);


                $dataInfo = $this->blogService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Blog updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'blogs', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Blog.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Blogcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->blogService->delete($id)) {
                    $message = 'Blog deleted successfully';
                    $this->storeAdminWorkLog($id, 'blogs', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Blog.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Blogcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->blogService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Blog ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'blogs', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Blog.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'BlogController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
