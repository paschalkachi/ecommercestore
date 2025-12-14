<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // public function index() 
    // {
    //     // dd(DB::connection()->getPdo());
    //     $slides = Slide::where('status',1)->get()->take(3); 
        
    //     // To display categories on the home page
    //     $categories = Category::orderBy('name')->get();

    //     // To display on sale products on the home page
    //     $sproducts = Product::whereNotNull('sale_price')->where('sale_price','<>','')->inRandomOrder()->get()->take(8); //remember to browse te meaning of this line later

    //     // To display featured products on the home page
    //     $fproducts = Product::where('featured',1)->get()->take(8);
             
    // //      $user = Auth::user();
    // //     if ($user->utype === 'ADM') {
    // //     // Redirect admin to the dashboard route
    // //     return route('admin.index');
    // // }
    //     return view("index",compact('slides','categories','sproducts','fproducts'));
    // }


    public function index()
{
    $slides = Slide::where('status', 1)
        ->limit(3)
        ->get();

    $categories = Category::orderBy('name')->get();

    $sproducts = Product::whereNotNull('sale_price')
        ->where('sale_price', '>', 0)
        ->inRandomOrder()
        ->limit(8)
        ->get();

    $fproducts = Product::where('featured', 1)
        ->limit(8)
        ->get();

    return view("index", compact(
        'slides',
        'categories',
        'sproducts',
        'fproducts'
    ));
}

    // Function to view cotact us page
    public function contact()
    {
        return view('contact');
    }

    // Function to store user details on visiting the contact us page
    public function contact_store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:10',
            'comment' => 'required'
        ]);

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();

    return redirect()->back()->with('success','Your message has been sent successfully');
    }    

    // public function search(Request $request)
    // {
    //     $query = $request->input('query');
    //     $results = Product::where('name','LIKE',"%{$query}%")->get()->take(8);
    //     return response()->json($results);
    // }

}
