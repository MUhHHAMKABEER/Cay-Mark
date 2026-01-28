<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuyerController extends Controller
{
  public function markFirstLogin(Request $request)
{
    $user = auth()->user();
    $user->first_login = 1; // mark walkthrough as completed
    $user->save();

    return response()->json(['status' => 'success']);
}

}
