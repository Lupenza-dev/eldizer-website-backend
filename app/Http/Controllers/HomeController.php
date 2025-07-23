<?php

namespace App\Http\Controllers;

use App\Http\Resources\AboutUsResource;
use App\Http\Resources\CoreValueResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\HomePageAboutResource;
use App\Http\Resources\MinServiceResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\TeamMemberResource;
use App\Http\Resources\TestimonialResource;
use App\Models\AboutUs;
use App\Models\CoreValue;
use App\Models\Faq;
use App\Models\HomePageAbout;
use App\Models\MinService;
use App\Models\News;
use App\Models\Service;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function homePage(){
        try {
            $sliders = Slider::with(['creator'])->latest()->get();
            $minServices = HomePageAbout::with(['creator'])->limit(4)->latest()->first();
            $services = Service::latest()->get();
            // $minServices = MinService::with(['creator'])->limit(4)->latest()->get();
            $testimonials = Testimonial::with(['creator'])->limit(3)->get();
            $news = News::with(['category', 'creator'])->limit(3)->get();

            return response()->json([
                'sliders'    =>SliderResource::collection($sliders),
                'home_about' =>new HomePageAboutResource($minServices),
                'services'   =>ServiceResource::collection($services),
                // 'min_services' =>MinServiceResource::collection($minServices),
                'testmonials'  =>TestimonialResource::collection($testimonials),
                'news'         =>NewsResource::collection($news)
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' =>false,
                'message' =>'Failed to fetch data',
                'error' =>$th->getMessage()
            ],422);
        }
    }

    public function aboutUs(){
        try {
            $aboutUs = AboutUs::first();
            $coreValues = CoreValue::with(['creator'])->latest()->get();
            $teamMembers = TeamMember::with(['creator'])->oldest()->get();

            return response()->json([
                'about'       =>new AboutUsResource($aboutUs->load('creator')),
                'core_values' =>CoreValueResource::collection($coreValues),
                'team_members' =>TeamMemberResource::collection($teamMembers),
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' =>false,
                'message' =>'Failed to fetch data',
                'error' =>$th->getMessage()
            ],422);
        }
    }

    public function getAllNews(){
        try {
            $news = News::with(['category', 'creator'])->latest()->get();
            return response()->json([
                'news'         =>NewsResource::collection($news)
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' =>false,
                'message' =>'Failed to fetch data',
                'error' =>$th->getMessage()
            ],422);
        }
    }

    public function getFaq(){
        try {
            $faqs = Faq::with(['category', 'creator'])->latest()->get();

            return response()->json([
                'faqs'         =>FaqResource::collection($faqs)
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' =>false,
                'message' =>'Failed to fetch data',
                'error' =>$th->getMessage()
            ],422);
        }
    }

    public function getServices(){
        try {
            $minServices = MinService::with(['creator'])->latest()->get();

            return response()->json([
                'min_services' =>MinServiceResource::collection($minServices),
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' =>false,
                'message' =>'Failed to fetch data',
                'error' =>$th->getMessage()
            ],422);
        }
    }
}
