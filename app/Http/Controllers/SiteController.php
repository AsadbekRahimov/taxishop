<?php

namespace App\Http\Controllers;

use App\Services\SiteService;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    protected $siteService;

    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    public function index()
    {
        $data = $this->siteService->getHomePageData();
        
        return view('site.home', $data);
    }

    public function category($slug = null)
    {
        $data = $this->siteService->getCategoryPageData($slug);
        
        return view('site.category', $data);
    }

    public function product($id = null)
    {
        $data = $this->siteService->getProductPageData($id);
        
        return view('site.product', $data);
    }

    public function cart()
    {
        $data = $this->siteService->getCartData();
        
        return view('site.cart', $data);
    }

    public function checkout()
    {
        $data = $this->siteService->getCheckoutData();
        
        return view('site.checkout', $data);
    }

    public function thanks()
    {
        $data = $this->siteService->getThanksPageData();
        
        return view('site.thanks', $data);
    }

    public function login()
    {
        return view('site.login');
    }

    public function addToCart(Request $request)
    {
        $result = $this->siteService->addToCart($request->all());
        
        return response()->json($result);
    }

    public function removeFromCart(Request $request)
    {
        $result = $this->siteService->removeFromCart($request->all());
        
        return response()->json($result);
    }

    public function updateCart(Request $request)
    {
        $result = $this->siteService->updateCart($request->all());
        
        return response()->json($result);
    }

    public function placeOrder(Request $request)
    {
        $result = $this->siteService->placeOrder($request->all());
        
        if ($result['success']) {
            return redirect()->route('thanks')->with('order', $result['order']);
        }
        
        return back()->withErrors($result['errors']);
    }
}
