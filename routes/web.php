<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\TaskCompleted;
use Illuminate\Support\Facades\Mail;
use App\Mail\testEmail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/registerRandom', function()
{
    //random firstname
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $length = 10;
    $rand = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);

    //create user
    $user = User::create([
        'name' => $rand,
        'email' => $rand .'@yahoo.com',
        'password' => Hash::make($rand .'takkesah'),
    ]);
    return $user;
});


Route::get('/testGetData', function(Request $request)
{
    //cara 1
    //return $request['ayam'];

    //cara 2
    //return $request->input('ayam');

    //cara 3 - display all
    $string = '';
    foreach ($request->all() as $key => $item)
    {
        $string .= $key . ' : ' . $item . '<br>';
    }
    return $string;
    //key point kat sini guna - $request->all()

});

Route::get('/loginistation', function(Request $request) {
    //cara 1
    //auth()->login($request);
    // if(auth()->login($usermodel))
    //     return 'OK';
    // return 'Failed';
    //not a good practice

    //cara 2
    //return $request;
    // if(Auth::attempt($request->all()))
    // //if(Auth::attempt(['email'=> $request->input('email'), 'password'=> $request->input('password')]))
    //     return 'OK';
    // return 'Failed';

    //much proper way
    if (Auth::attempt($request->all())) {
        return response()->json(['message' => 'Authentication successful']);
    } else {
        return response()->json(['message' => 'Authentication failed'], 401);
    }

    // this one use when inserting only
    // $validatedData = $request->validate([
    //     'email' => 'required|string|email|max:255|unique:users',
    //     'password' => 'required|string|min:8|confirmed',
    // ]);
});

//logout
Route::get('/logoutization', function()
{
    //return Auth::user();
    if(Auth::check())
    {
        $details = Auth::user();
        Auth::logout();
        return response()->json(['status' => 'succesfull', 'user' => $details]);
    } else {
        return response()->json(['status' => 'failed', 'message'=> 'probably you have no authenticate history']);
    }
});


//group of protected route
Route::middleware('auth')->group(function(){

    //notification part
    Route::get('/indexing', function(Request $request)
    {
        return '<center>
                <h1>mantap</h1>
                <h3 style="display : inline">In</h3> <h1 style="display : inline" id="counter">5</h1> <h3>second will redirect you to notify</h3>
                </center>
                <script>
                    let test = setInterval(()=>{
                        console.log(`redirecting`);
                        window.location.href = `' . route('noty') . '`;
                        clearInterval(test);
                    }, 5000);

                    let count = 5;
                    document.getElementById("counter").innerHTML = count;
                    let testTwo = setInterval(() => {
                        if(count <= 0)
                        {
                            clearInterval(testTwo);
                        }
                        count--;
                        document.getElementById("counter").innerHTML = count;
                    }, 1000);
                </script>';
    })->name('home');

    //display notification
    Route::get('/notify', function() {
        $tempArr = [];
        foreach(Auth::user()->notifications as $noti)
        {
            $tempArr[] = $noti;
        }
        return $tempArr;
    })->name('noty');

    //create notification
    Route::get('/sending', function(Request $request)
    {
        //related information related to notification is?
        //app\Notifications\TaskCompleted.php
        $validatedData = $request->validate([
            'message' => 'required|string|max:255',
        ]);
        Notification::send(User::all(), new TaskCompleted($validatedData['message']));
    });

    //send to specific
    Route::get('/sepific', function(Request $request)
    {
        $validatedData = $request->validate([
            'message' => 'required|string|max:255',
            'email' => 'required|string|email|max:255'
        ]);

        // return User::where('email', $validatedData['email'])->first();

        //lau letak lam condition dia always return false
        Notification::send(User::where('email', $validatedData['email'])->first(), new TaskCompleted($validatedData['message']));

        return 'ok';
    });

    //create dulu new mailable class
    //php artisan make:mail AnotherTestMail
    Route::get('/emailing', function (Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255'
        ]);

        Mail::to($validatedData['email'])->send(new testEmail());
    });

});

//authentication redirection information??
// app\Http\Middleware\RedirectIfAuthenticated.php  -> home
// app\Http\Middleware\Authenticate.php -> notallowed

// for guest only
Route::middleware('guest')->group(function(){

    //notification part
    Route::get('/notallowed', function(Request $request)
    {
        return 'no access here!';
    })->name('notallowed');

});