<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('taikhoan.edit', [
            'title' => 'Tài khoản của tôi',
            'user'  => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $request->user()->update($data);

        return redirect()->route('taikhoan.edit')
            ->with('success', 'Cập nhật tài khoản thành công!');
    }
}
