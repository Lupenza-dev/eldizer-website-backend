<?php

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MinServiceController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\CoreValueController;
use App\Http\Controllers\AboutUsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Services
    Route::apiResource('services', ServiceController::class);
    
    // Min Services
    Route::apiResource('min-services', MinServiceController::class);
    
    // Testimonials
    Route::apiResource('testimonials', TestimonialController::class);
    
    // News Categories
    Route::apiResource('news-categories', NewsCategoryController::class);
    
    // News
    Route::apiResource('news', NewsController::class);
    
    // FAQ Categories
    Route::apiResource('faq-categories', FaqCategoryController::class);
    
    // FAQs
    Route::apiResource('faqs', FaqController::class);
    
    // Sliders
    Route::apiResource('sliders', SliderController::class);
    
    // Team Members
    Route::apiResource('team-members', TeamMemberController::class);
    
    // Journey
    Route::apiResource('journeys', JourneyController::class);
    
    // Core Values
    Route::apiResource('core-values', CoreValueController::class);
    
    // About Us
    Route::apiResource('about-us', AboutUsController::class)->only(['index', 'update']);
});
