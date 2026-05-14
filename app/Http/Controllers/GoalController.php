<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
     public function index()
    {
        return Goal::where(
            'user_id',
           Auth::id()
        )->latest()->get();
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $goal = Goal::create([
            'user_id' =>Auth::id(),

            'goal_name' =>
                $request->goal_name,

            'target_amount' =>
                $request->target_amount,

            'current_amount' =>
                $request->current_amount ?? 0,

            'deadline' =>
                $request->deadline,

            'status' => 'active',

            'icon' =>
                $request->icon,

            'image' =>
                $request->image,

            'color' =>
                $request->color,

            'description' =>
                $request->description,

            'product_link' =>
                $request->product_link,
        ]);

        return response()->json($goal);
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        Goal::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }

    public function addSaving(Request $request, $id)
{
    $goal = Goal::findOrFail($id);

    $goal->current_amount += $request->amount;

    $goal->save();

    return response()->json($goal);
}

    
}
