<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Services\FirebaseService;

class NotificationController extends BaseController
{
    public function sendTest()
    {
        $firebase = new FirebaseService();

        $deviceToken = "ca7zBGDgRWa1785Ku593R9:APA91bGQDInBqOn_6b40bfE878PQ7WirkUpb1_3jLqsKYgXGvdxie26V82MzsPsDiHglXp97187RHcEzm54c86qYllK3oj1RBFxGTmDwutIj8qzx4F9hdFQ";

        $result = $firebase->sendNotification(
            $deviceToken,
            "Hello 🚀",
            "This is a test notification",
            ["order_id" => "123 sparta"]
        );

        return response()->json($result);
    }
}