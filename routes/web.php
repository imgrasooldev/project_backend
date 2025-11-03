<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\NotificationController;
use Illuminate\Support\Facades\Mail;


/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider and all of them will

| be assigned to the "web" middleware group. Make something great!

|

*/



Route::get('/', function () {

    return view('welcome');

});



Route::get('/test-mail', function () {
    try {
        Mail::raw('Laravel direct web mail test', function ($msg) {
            $msg->from('kaam@yolger.com', 'Kaam');
            $msg->to('imgrasool@gmail.com');
            $msg->subject('Laravel direct test');
        });
        return '✅ Sent successfully — check inbox/spam.';
    } catch (\Exception $e) {
        return '❌ Failed: ' . $e->getMessage();
    }
});



Route::get('/send-email', function () {
    $to = "imgrasool@gmail.com";

    Mail::raw("This is a test email from Laravel!", function ($message) use ($to) {
        $message->to($to)
                ->subject("Test Email");
    });

    return "Email sent successfully!";
});


Route::get('/send-test', [NotificationController::class, 'sendTest']);


Route::get('/setup', function() {

    $credentials = [

        'email' => 'admin@admin.com',

        'password' => 'password'

    ];



    if (!Auth::attempt($credentials)) {

        $user = new \App\Models\User();



        $user->name = 'Admin';

        $user->email = $credentials['email'];

        $user->password = Hash::make($credentials['password']);

    

        $user->save();



        if (Auth::attempt($credentials)) {

            $user = Auth::user();



            $adminToken = $user->createToken('admin-token', ['create','update','delete']);

            $updateToken = $user->createToken('update-token', ['create','update']);

            $basicToken = $user->createToken('basic-token');



            return [

                'admin' => $adminToken->plainTextToken,

                'update' => $updateToken->plainTextToken,

                'basic' => $basicToken->plainTextToken,

            ];

        }

    }

});

