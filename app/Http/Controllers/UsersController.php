<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{

    public function __construct()
    {
        # 过滤以下指定页面不要登录即可访问
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        # 只让未登录用户访问注册页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    // 创建用户
    public function create()
    {
        return view('users.create');
    }

    // 获取所有用户列表
    public function index()
    {
        $users = User::orderby('id', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }


    // 显示微博
    public function show(User $user)
    {
        $statuses = $user->statuses()
            ->orderby('created_at', 'desc')
            ->paginate(30);

        return view('users.show', compact('user', 'statuses'));
    }

    // 登录
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|max:50',
            'email'    => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = user::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'password'=> bcrypt($request->password),

        ]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

        // Auth::login($user);
        // session()->flash('success', '欢迎，您将在这里开启一段新的旅程');
        // return redirect()->route('users.show', [$user]);
    }

    // 发送账户激活邮件
    protected function sendEmailConfirmationTo($user)
    {
        $view    = 'emails.confirm';
        $data    = compact('user');
        $from    = 'aufree@yoursails.com';
        $name    = 'Aufree';
        $to      = $user->email;
        $subject = "感谢注册 Sample 应用！ 请确认你的邮箱。";

        Mail::send($view, $data, function($message) use ($from, $name, $to , $subject) {
            $message->to($to)->subject($subject);
        });
    }

    // 修改
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    // 更新个人资料
    public function update(User $user, Request $request)
    {
        $this->validate($request,[
            'name'    => 'required|max:50',
            'password'=> 'nullable|confirmed|min:6'
        ]);

         # 授权验证
        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;

        // 验证密码
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功');

        return redirect()->route('users.show', $user->id);
    }

    // 删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);

        $user->delete();

        session()->flash('success', '成功删除用户!');

        return back();
    }

    // 激活成功
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated        = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

}