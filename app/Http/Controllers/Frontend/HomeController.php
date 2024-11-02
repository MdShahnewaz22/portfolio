<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ContactService;
use App\Services\Parsonal_InfoService;
use App\Services\SkillsService;
use App\Services\AboutService;
use App\Services\WorkExperienceService;
use App\Services\EducationService;
use App\Services\FeaturedProjectService;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;


class HomeController extends Controller
{
    protected $contactService,$Parsonal_InfoService,$skillsService,$AboutService,$WorkExperienceService,$educationService,$featuredProjectService,$blogService;

    public function __construct(ContactService $contactService,Parsonal_InfoService $Parsonal_InfoService,SkillsService $skillsService,AboutService $AboutService,WorkExperienceService $WorkExperienceService,EducationService $educationService,FeaturedProjectService $featuredProjectService,BlogService $blogService)
    {
        $this->contactService = $contactService;
        $this->Parsonal_InfoService = $Parsonal_InfoService;
        $this->skillsService = $skillsService;
        $this->AboutService = $AboutService;
        $this->WorkExperienceService = $WorkExperienceService;
        $this->educationService = $educationService;
        $this->featuredProjectService = $featuredProjectService;
        $this->blogService = $blogService;

    }

    public function index()
    {
        $parsonal_info = $this->Parsonal_InfoService->latest();
        $skills = $this->skillsService->latest();
        $Advantages = $this->skillsService->all();
        $about = $this->AboutService->latest();
        $works = $this->WorkExperienceService->all();
        $educations = $this->educationService->all();
        $featureds = $this->featuredProjectService->latest();
        $blogs = $this->blogService->latest();

        //  dd($skills);
        return view('frontend.home',compact('parsonal_info','skills','about','works','educations','Advantages','featureds','blogs'));

    }

    public function moreproject()
    {
        $parsonal_info = $this->Parsonal_InfoService->latest();
        $skills = $this->skillsService->latest();
        $featureds = $this->featuredProjectService->all();
        // dd($featureds);
        return view('frontend.more_project', compact('featureds','parsonal_info','skills'));
    }

    public function moreblog()
    {
        $parsonal_info = $this->Parsonal_InfoService->latest();
        $skills = $this->skillsService->latest();
        $blogs = $this->blogService->all();
        // dd($featureds);
        return view('frontend.more_blog', compact('blogs','parsonal_info','skills'));
    }

    public function blogdetails($id)
    {
        $parsonal_info = $this->Parsonal_InfoService->latest();
        $skills = $this->skillsService->latest();
        $blogs = $this->blogService->find($id);
        // dd($featureds);
        return view('frontend.blog_details', compact('blogs','parsonal_info','skills'));
    }


    public function contact(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gmail' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        // dd( $validatedData);

        // Create a new contact record using the validated data
        Contact::create($validatedData);

        // Optional: Redirect or return a response
        return redirect()->back()->with('success', 'Contact information saved successfully!');
    }

}
