<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use GuzzleHttp\Promise\Create;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    // Show all listings
    public function index() {
        return view('listings.index', [
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(5)
        ]);
    }

    // Show single listing
    public function show(Listing $listing) {
        return view('listings.show', [
            'listing' => $listing
        ]);
    }

    // Show create Form
    public function create() {
        return view('listings.create');
    }

    // Store Listing Data
    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required',
            'location' => 'required',
            'website' => 'required'
        ]);

        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
            $formFields['logo'] = 'storage/' . $formFields['logo'];
        }

        Listing::create($formFields);

        return redirect('/')->with('message', 'Listing Created Successfully!');
    }

    // Show Edit Form
    public function edit(Listing $listing) {
        return view('listings.edit', ['listing' => $listing]);
    }

    // Update Listing Data
    public function update(Request $request, Listing $listing ) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required',
            'location' => 'required',
            'website' => 'required',
        ]);

        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
            $formFields['logo'] = 'storage/' . $formFields['logo'];
        }

        $listing->update($formFields);

        return back()->with('message', 'Listing Updated Successfully!');
    }

    // Deleting a Listing
    public function destroy(Listing $listing) {
        $listing->delete();
        return redirect('/')->with('message', 'Listing Deleted Successfully');
    }
}
