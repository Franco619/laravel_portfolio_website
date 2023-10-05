<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Image;
use Illuminate\Support\Carbon;
class PortfolioController extends Controller
{
    public function AllPortfolio(){

        $portfolio = Portfolio::latest()->get();
        return view('admin.portfolio.portfolio_all',compact('portfolio'));
    } // End Method

    public function AddPortfolio(){

        return view('admin.portfolio.protfolio_add');  
    }//end method

      public function StorePortfolio(Request $request){
     $request->validate([
            'portfolio_name'=>'required',
            'portfolio_title'=>'required',
            'portfolio_image'=>'required',
     ],
     [
        'portfolio_name.required'=>"Portfolio name is required",
        'portfolio_title.required'=>"Portfolio title is required",
     ]);

        $image = $request->file('portfolio_image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();  // 3434343443.jpg

            Image::make($image)->resize(1020,519)->save('upload/portfolio/'.$name_gen);
            $save_url = 'upload/portfolio/'.$name_gen;

            Portfolio::insert([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,
                'portfolio_image' => $save_url,
                'created_at' => Carbon::now(),

            ]); 
            $notification = array(
            'message' => 'Portfolio Inserted Successfully', 
            'alert-type' => 'success'
        );

        return redirect()->route('all.portfolio')->with($notification);
    }// End Method

      public function EditPortfolio($id){

        $portfolio = Portfolio::findOrFail($id);
        return view('admin.portfolio.protfolio_edit',compact('portfolio'));
    }// End Method

    public function UpdatePortfolio(Request $request){
         $portfolio_id = $request->id;

        if ($request->file('portfolio_image')) {
            $image = $request->file('portfolio_image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();  // 3434343443.jpg

            Image::make($image)->resize(1020,519)->save('upload/portfolio/'.$name_gen);
            $save_url = 'upload/portfolio/'.$name_gen;

            Portfolio::findOrFail($portfolio_id)->update([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,
                'portfolio_image' => $save_url,

            ]); 
            $notification = array(
            'message' => 'Portfolio Updated with Image Successfully', 
            'alert-type' => 'success'
        );

        return redirect()->route('all.portfolio')->with($notification);

        } else{

            Portfolio::findOrFail($portfolio_id)->update([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,


            ]); 
            $notification = array(
            'message' => 'Portfolio Updated without Image Successfully', 
            'alert-type' => 'success'
        );

       return redirect()->route('all.portfolio')->with($notification);

        } // end Else

    }//end method

    
     public function DeletePortfolio($id){

        $portfolio = Portfolio::findOrFail($id);
        $img = $portfolio->portfolio_image;
        @unlink($img);


        Portfolio::findOrFail($id)->delete();

         $notification = array(
            'message' => 'Portfolio Image Deleted Successfully', 
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);       

     }// End Method 

       public function PortfolioDetails($id){

        $portfolio = Portfolio::findOrFail($id);
        return view('admin.frontend.protfolio_details',compact('portfolio'));
     }// End Method 

     public function HomePortfolio(){
       $portfolio = Portfolio::latest()->get();
       return view('admin.frontend.portfolio', compact('portfolio'));  
     }//end method
}
