<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ContactService;
use App\Services\Parsonal_InfoService;
use App\Services\SkillsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;


class HomeController extends Controller
{
    protected $contactService,$Parsonal_InfoService,$skillsService;
    public function __construct(ContactService $contactService,Parsonal_InfoService $Parsonal_InfoService,SkillsService $skillsService)
    {
        $this->contactService = $contactService;
        $this->Parsonal_InfoService = $Parsonal_InfoService;
        $this->skillsService = $skillsService;

    }

    public function index()
    {
        $parsonal_info = $this->Parsonal_InfoService->latest();
        $skills = $this->skillsService->all();
        //  dd($skills);
        return view('frontend.home',compact('parsonal_info','skills'));

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
