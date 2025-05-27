<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wishlist = $user ? $user->wishlist : session()->get('wishlist', []);
        return response()->json(['items' => $wishlist]);
    }

    public function store(Request $request)
    {
        $items = $request->input('items', []);
        $user = Auth::user();
        if ($user) {
            $user->wishlist = $items;
            $user->save();
        } else {
            session()->put('wishlist', $items);
        }
        return response()->json(['message' => 'Wishlist updated']);
    }
}