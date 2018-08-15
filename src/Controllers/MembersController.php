<?php

namespace PanicHD\PanicHD\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PanicHD\PanicHD\Models;

class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
	public function index(Request $request)
	{
		$a_members = Models\Member::withCount(['userTickets', 'agentTotalTickets'])->orderBy('name')->get();
		
		return view('panichd::admin.member.index', compact('a_members'));
	}
	
	/**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
	public function store(Request $request)
    {
		$rules = [
			'name' => 'required',
			'email' => 'bail|required|unique:panichd_members|email',
			'password' => 'required|confirmed',
			'password_confirmation' => 'required'
		];
		
        $this->validate($request, $rules);

        $member = new User;
		$member->name = $request->name;
		$member->email = $request->email;
		$member->password = bcrypt($request->password);
		$member->save();
				
        \Session::flash('status', trans('panichd::admin.member-added-ok', ['name' => $member->name]));

        return redirect()->back();
    }
	
	public function update(Request $request, $id)
	{
		$member = User::findOrFail($id);
		
		$rules = [
			'id' => 'exists:users',
			'name' => 'required',
			'email' => [
				'bail',
				'required',
				Rule::unique('users')->ignore($id),
				'email'
			]
		];
		if ($request->password != ""){
			$rules['password'] = 'required|confirmed';
			$rules['password_confirmation'] = 'required';
		}
		
        $this->validate($request, $rules);
		
		$member->name = $request->name;
		$member->email = $request->email;
		if ($request->password != ""){
			$member->password = bcrypt($request->password);
		}
		$member->save();
		
		\Session::flash('status', trans('panichd::admin.member-updated-ok', ['name' => $member->name]));

        return redirect()->back();
	}
	
	public function destroy(Request $request, $id)
	{
		$user = User::findOrFail($id);
		$member = Models\Member::findOrFail($id);
		if (auth()->user()->id == $id){
			\Session::flash('warning', trans('panichd::admin.member-delete-own-user-error'));
			return redirect()->back();
		}
		
		if ($member->userTickets()->count() > 0 or $member->agentTotalTickets()->count() > 0){
			\Session::flash('warning', trans('panichd::admin.member-with-tickets-delete'));
			return redirect()->back();
		}
		
		$user->delete();
		
		\Session::flash('status', trans('panichd::admin.member-deleted', ['name' => $member->name]));

        return redirect()->back();
	}
}