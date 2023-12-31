<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index()
    {
        return view('user.index', [
            'users' => User::whereNotNull('email_verified_at')->latest()->get(),
            'member' => User::where('role', 'Anggota')
                ->whereNotNull('email_verified_at')
                ->get(),
            'officer' => User::where('role', 'Petugas')
                ->whereNotNull('email_verified_at')
                ->get(),
        ]);
    }
    public function store(UserRequest $request)
    {
        $password = Carbon::parse($request->birthdate)->format('dmY');

        $data = $request->validated();
        $data['slug'] = Str::slug($request->name) . Str::random(5);
        $data['email_verified_at'] = now();
        $data['password'] = bcrypt($password);

        User::create($data);

        return back()->with('success', 'Proses penambahan data telah berhasil dilakukan.');
    }
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validated();
        $data['slug'] = Str::slug($request->name) . Str::random(5);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Proses penambahan data telah berhasil dilakukan.');
    }
    public function show($slug)
    {

        return view('user.show', [
            'user' => User::whereSlug($slug)->first()
        ]);
    }
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Proses penghapusan data telah berhasil dilakukan.');
    }
}
