<?php

namespace App\Http\Controllers\website;

use App\Models\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    /**
     * Display the about page.
     */
    public function index()
    {
        $about = About::getFirst();
        
        return view('website.about.index', compact('about'));
    }
}

