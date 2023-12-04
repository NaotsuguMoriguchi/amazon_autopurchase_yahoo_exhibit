<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\User;
use App\Models\AmazonSetting;
use App\Models\YahooSetting;


class UserController extends Controller
{
    public function save_registered_item(Request $request)
    {
		dd($request);
		
		return;
	}
}
