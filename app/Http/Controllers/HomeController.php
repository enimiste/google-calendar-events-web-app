<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Business\Calendar\Provider\EventStoreFactory;
use App\Business\Calendar\Provider\ShouldAuthException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(EventStoreFactory $factory)
    {
        try{
            $events = $factory->get(EventStoreFactory::GOOGLE)->loadEvents(auth()->user());
            return view('home', compact('events'));
        } catch(ShouldAuthException $sae){
            return redirect($sae->getAuthUrl());
        }
    }

    /**
     * 
     */
    public function googOAuthCallback(Request $request, EventStoreFactory $factory) {
        try{
            $factory->get(EventStoreFactory::GOOGLE)->onOAuthCallback(auth()->user(), $request->only(['code', 'error']));
            return redirect(route('home'));
        } catch(ShouldAuthException $sae){
            return redirect($sae->getAuthUrl());
        }
    }
}
