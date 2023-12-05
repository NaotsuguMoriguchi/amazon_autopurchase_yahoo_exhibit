<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use DateTime;
use DateTimeZone;

use App\Models\YahooStore;
use App\Models\YahooSetting;
use App\Models\YahooOrderItem;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user_id = Auth::id();
		$yahoo_store = YahooStore::where('user_id', $user_id)->get();
        $all_order = YahooOrderItem::where('user_id', $user_id)->get();

        return view('dashboard', ['yahoo_stores' => $yahoo_store, 'order_count' => count($all_order)]);
    }

    public function progress(Request $request)
    {
        $user = Auth::user();
        
        return $user;
    }
}
