<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Context\Common\Domain\Models\InviteCode;
use App\Context\User\Domain\Model\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/personal';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param mixed[] $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'code'     => 'required|exists:invite_codes'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array<string,string> $data
     * @return User
     * @throws Exception
     */
    protected function create(array $data): User
    {
        InviteCode::where('code', $data['code'])->delete();
        /** @var User $user */
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),
            'api_token' => Str::random(60),
        ]);
        return $user;
    }
}
