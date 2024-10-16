<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;


class HomeController extends Controller
{
    protected $contactService;
    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;

    }

    public function index()
    {
        return view('frontend.home');
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
